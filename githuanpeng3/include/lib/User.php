<?php
namespace lib;

use hp;
use \DBHelperi_huanpeng;
use lib\Task;
use \RedisHelp;
use lib\GiftTable;

//class User implements UserInterface
class User
{
	public $uid = null;//uid
	private $_db = null;//数据库对象
	private $_redis = null;//Redis对象
	const USER_INFO_BASE = 0; //用户基础信息
	const USER_INFO_DETAIL = 1;//用户详细信息
	const USER_INFO_ALL = 2;//用户所有信息
	const FREE_NICK = 1;//设置免费修改昵称标志
	const UNFREE_NICK = 0;//重置免费修改昵称标志
	const OPEN_LIVE_NOTICE = 1; //开启开播提醒
	const COLSE_LIVE_NOTICE = 0; //关闭开播提醒
	const INTEGRAL = 1000;//经验值常数

	const EMAIL_NOT = 0;//邮箱未填写
	const EMAIL_UNPASS = 1;//邮箱填写未认证;
	const EMAIL_PASS = 2;//邮箱认证通过

	const USER_CLIENT_APP = 1;//用户客户端为app
	const USER_CLIENT_WEB = 2;//用户客户端为web

	const CTIME_DEFAULT = "0000-00-00 00:00:00";

	/**
	 * User constructor. 类初始化
	 *
	 * @param int    $uid 用户uid
	 * @param object $db  数据库对象
	 */
	public function __construct( $uid = '', $db = '', $redis = '' )
	{
		if( $uid )
		{
			$this->uid = (int)$uid;
		}
		if( $db )
		{
			$this->_db = $db;
		}
		else
		{
			$this->_db = new DBHelperi_huanpeng();
		}
		if( $redis )
		{
			$this->_redis = $redis;
		}
		else
		{
			$this->_redis = new RedisHelp();
		}

	}
	/**
	 * 约玩禁言辅助 方法 | 切换uid
	 * ---------------------
	 * @author yalongSun<yalong2017@6.cn>
	 * @param number $uid
	 */
    public function resetUid($uid=0){
        $this->uid = $uid;
    }
	public static function getDB()
	{
		return new DBHelperi_huanpeng();
	}

	public static function getUidByPhoneNumber($mobile)
	{
		$db = self::getDB();
		$sql = $db->field('uid')->where('phone='.$mobile)->select('userstatic', 1);
		$res = $db->query($sql);

		if(!$res)
		{
			//todo log
			return false;
		}

		$row = $res->fetch_assoc();

		return $row['uid'];
	}

	public static function getUserLoginDataByPhone($mobile)
	{
		$db = self::getDB();
		$sql = $db->field('uid,password,encpass')->where('phone='.$mobile)->select('userstatic',1);

		$res = $db->query($sql);
		if(!$res)
		{
			//todo log
			return false;
		}

		$row = $res->fetch_assoc();

		return $row;
	}

	/**
	 * 获取用户信息
	 *
	 * @param int $base 0 获取uid,头像,昵称, 1 获取uid,头像,昵称,等级 经验值 2 获取所有信息
	 *
	 * @return array|bool array(
	 * 'uid'=>,//用户id
	 * 'nick'=>,//昵称
	 * 'pic'=>//头像
	 * )
	 * 注释 以上是base为0的情况，根据$base的不同，返回数组中的参数也会增多，
	 *  base＝1时  uid,nick,pic,phone,mail,sex,mailstatus,level,integral,readsign,isnotice
	 *  base= 2时
	 *  uid,nick,pic,phone,mail,sex,mailstatus,isfree,ltime,level,integral,readsign,isnotice,province,city,address
	 */
	public function getUserInfo( $base = self::USER_INFO_BASE )
	{
		$res = $this->getUsersInfoByUids( array( $this->uid ), $this->_db, $base );
		if( false === $res )
		{
			return false;
		}
		else
		{
			if( $res )
			{
				return $res[$this->uid];
			}
			else
			{
				return $res;
			}
		}
	}

	/**
	 * 获取用户基本信息
	 *
	 * @param array  $uidArray用户uid 查询多个可传入数组 array(90,2780)
	 * @param object $db
	 *
	 * @return array|bool
	 */
	private static function _getUserBaseInfo( $uidArray, $db )
	{
		$list = array();
		if( empty( $uidArray ) || !is_array( $uidArray ) )
		{
			return false;
		}
		$uids = implode( ',', $uidArray );
		$res = $db->field( 'uid,nick,pic' )->where( "uid in ($uids)" )->select( 'userstatic' );
		if( $res )
		{
			foreach ( $res as $v )
			{
				$list[$v['uid']] = $v;
			}
		}
		return $list;
	}

	/**
	 * 获取userstatic表中的详细信息
	 *
	 * @param  array $uidArray 用户uid数组
	 * @param object $db
	 *
	 * @return array|bool
	 */
	private static function _getUserDetailInfo( $uidArray, $db )
	{
		$list = array();
		if( empty( $uidArray ) || !is_array( $uidArray ) )
		{
			return false;
		}
		$uids = implode( ',', $uidArray );
		$res = $db->field( 'uid,phone,mail,sex,mailstatus' )->where( "uid in ($uids)" )->select( 'userstatic' );
		if( $res )
		{
			foreach ( $res as $v )
			{
				$list[$v['uid']] = $v;
			}
		}
		return $list;
	}

	/**
	 * 获取useractive表中的基础信息
	 *
	 * @param array  $uidArray 用户uid数组
	 * @param object $db
	 *
	 * @return array|bool
	 */
	private static function _getUserBaseActiveInfo( $uidArray, $db )
	{
		$list = array();
		if( empty( $uidArray ) || !is_array( $uidArray ) )
		{
			return false;
		}
		$uids = implode( ',', $uidArray );
		$res = $db->field( 'uid,level,integral,readsign,isnotice' )->where( "uid in ($uids) " )->select( 'useractive' );
		if( $res )
		{
			foreach ( $res as $v )
			{
				$list[$v['uid']] = $v;
				unset( $list[$v['uid']]['uid'] );
			}
		}
		return $list;
	}


	/**
	 * 批量获取用户等级
	 *
	 * @param array  $luidsArray用户id 多个如array(2780,1815)
	 * @param object $db
	 *
	 * @return array|bool
	 */
	public static function getUserLevelByUids( $uidsArray, $db )
	{
		if( empty( $uidsArray ) || !is_array( $uidsArray ) )
		{
			return false;
		}
		$list = array();
		$uids = implode( ',', $uidsArray );
		$res = $db->field( 'uid,level' )->where( "uid in($uids)" )->order( 'level desc' )->select( 'useractive' );

		if( $res )
		{
			foreach ( $res as $v )
			{
				$list[$v['uid']] = $v;
			}
		}
		return $list;

	}

	/**
	 *获取useractive详细信息
	 *
	 * @param  array  $uidArray 用户数组
	 * @param  object $db
	 *
	 * @return array|bool
	 */
	private static function _getUserDetailActiveInfo( $uidArray, $db )
	{
		$list = array();
		if( empty( $uidArray ) || !is_array( $uidArray ) )
		{
			return false;
		}
		$uids = implode( ',', $uidArray );
		$res = $db->field( 'uid,ltime,province,city,address' )->where( "uid in ($uids) " )->select( 'useractive' );
		if( $res )
		{
			foreach ( $res as $v )
			{
				$list[$v['uid']] = $v;
			}
		}
		return $list;
	}

	/**
	 * 批量获取用户基本信息
	 *
	 * @param array  $uidsArray 支持批量获取  如批量获取uid需用逗号隔开 如:90,2679
	 * @param object $db
	 * @param int    $base      0基本信息  1详细信息  2 全部信息
	 *
	 * @return array|bool  输出同getUserInfo这个方法
	 */
	public static function getUsersInfoByUids( $uidArray, $db, $base = self::USER_INFO_BASE )
	{
		$list = array();
		if( empty( $uidArray ) || !is_array( $uidArray ) )
		{
			return false;
		}
		$uidArray = array_filter( $uidArray );
		if( !in_array( $base, array( self::USER_INFO_BASE, self::USER_INFO_DETAIL, self::USER_INFO_ALL ) ) )
		{
			return false;
		}
		if( $base == self::USER_INFO_BASE )
		{
			$res = self::_getUserBaseInfo( $uidArray, $db );
		}
		else
		{
			$res = self::_getUserBaseInfo( $uidArray, $db );
			$dinfo = self::_getUserDetailInfo( $uidArray, $db );
			if( $res )
			{
				foreach ( $dinfo as $v )
				{
					$res[$v['uid']]['phone'] = $v['phone'];
					$res[$v['uid']]['mail'] = $v['mail'];
					$res[$v['uid']]['sex'] = $v['sex'];
					$res[$v['uid']]['mailstatus'] = $v['mailstatus'];
				}
			}
		}
		if( $res )
		{
			$activeInfo = self::_getUserActiveInfo( $uidArray, $db, $base );
			foreach ( $res as $v )
			{

				$list[$v['uid']] = $v;
				if( !$v['pic'] )
				{
					$list[$v['uid']]['pic'] = DEFAULT_PIC;
				}
				else
				{
					$list[$v['uid']]['pic'] = DOMAIN_PROTOCOL . $GLOBALS['env-def'][$GLOBALS['env']]['domain-img'] . '/' . $v['pic'];
				}
				if( $base > self::USER_INFO_BASE )
				{
					$list[$v['uid']]['level'] = $activeInfo[$v['uid']]['level'];
					$list[$v['uid']]['integral'] = $activeInfo[$v['uid']]['integral'];
					$list[$v['uid']]['readsign'] = $activeInfo[$v['uid']]['readsign'];
					$list[$v['uid']]['isnotice'] = $activeInfo[$v['uid']]['isnotice'];
					if( $base == self::USER_INFO_ALL )
					{
						$list[$v['uid']]['province'] = $activeInfo[$v['uid']]['province'];
						$list[$v['uid']]['city'] = $activeInfo[$v['uid']]['city'];
						$list[$v['uid']]['address'] = $activeInfo[$v['uid']]['address'];
						$list[$v['uid']]['ltime'] = $activeInfo[$v['uid']]['ltime'];
					}
				}

			}

		}
		return $list;
	}

	/**
	 * 获取useractive 表信息
	 *
	 * @param  array   $uidArray 用户ID 支持批量操作 array(90,2780)
	 * @param   object $db
	 * @param int      $base
	 *
	 * @return array|bool
	 */
	private static function _getUserActiveInfo( $uidArray, $db, $base = self::USER_INFO_DETAIL )
	{
		if( empty( $uidArray ) )
		{
			return false;
		}
		if( !in_array( $base, array( self::USER_INFO_DETAIL, self::USER_INFO_ALL ) ) )
		{
			return false;
		}

		if( $base == self::USER_INFO_DETAIL )
		{
			$res = self::_getUserBaseActiveInfo( $uidArray, $db );
		}
		else
		{
			$res = self::_getUserBaseActiveInfo( $uidArray, $db );
			$ainfo = self::_getUserDetailActiveInfo( $uidArray, $db );
			if( $res )
			{
				foreach ( $ainfo as $v )
				{
					$res[$v['uid']]['ltime'] = $v['ltime'];
					$res[$v['uid']]['province'] = $v['province'];
					$res[$v['uid']]['city'] = $v['city'];
					$res[$v['uid']]['address'] = $v['address'];
				}
			}
		}

		return $res;
	}

	/**
	 * 根据昵称获取uid
	 *
	 * @param string $nick 昵称
	 * @param        $db
	 *
	 * @return int
	 */
	public static function getUserIdByNick( $nick, $db )
	{
		$nick = $db->realEscapeString( $nick );
		$res = $db->field( 'uid' )->where( "nick='$nick'" )->select( 'userstatic' );
		if( $res )
		{
			return (int)$res[0]['uid'];
		}
		else
		{
			return 0;
		}
	}

	/**
	 * 添加用户欢朋豆
	 *
	 * @param $hpBean 要添加的数量
	 *
	 * @return bool
	 */
	public function addUserHpBean( $hpBean )
	{
		if( empty( $hpBean ) )
		{
			return false;
		}
		$sql = "UPDATE useractive set hpbean = hpbean + $hpBean where uid=$this->uid";
		$res = $this->_db->doSql( $sql );
		if( false !== $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	/**
	 * 更新用户欢朋豆
	 *
	 * @param $hpBean  欢豆余额
	 *
	 * @return bool  成功返回true  失败返回false
	 */
	public function updateUserHpBean( $hpBean )
	{
		if( $this->uid == 73163 )
		{
			write_log( '欢豆:' . $hpBean, 'upUserBean_log' );
			$sql = $this->_db->where( 'uid=' . $this->uid )->update( 'useractive', array( 'hpbean' => $hpBean ), 1 );
			write_log( $sql, 'upUserBean_log' );
		}
		$res = $this->_db->where( 'uid=' . $this->uid )->update( 'useractive', array( 'hpbean' => $hpBean ) );
		if( false !== $res )
		{
                        $event = new \service\event\EventManager();
                       //用户财产变动事件
                       $event->trigger(\service\event\EventManager::ACTION_USER_MONEY_UPDATE,['uid' => $this->uid]);
                       $event = null;
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 更新用户欢朋币
	 *
	 * @param $hpCoin 用户欢朋币余额
	 *
	 * @return bool  成功返回true  失败返回false
	 */
	public function updateUserHpCoin( $hpCoin )
	{
		$res = $this->_db->where( 'uid=' . $this->uid )->update( 'useractive', array( 'hpcoin' => $hpCoin ) );
		if( false !== $res )
		{
                    
                    $event = new \service\event\EventManager();
                    //用户财产变动事件
                    $event->trigger(\service\event\EventManager::ACTION_USER_MONEY_UPDATE,['uid' => $this->uid]);
                    $event = null;
                    return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 检查用户Encpass是否有效
	 *
	 * @param  string $encpass
	 *
	 * @return bool|int  true没有错误，非true返回错误代码
	 */
	public function checkStateError( $encpass )
	{
		if( empty( $encpass ) )
		{
			return -1003;
		}
		$res = $this->_db->field( 'encpass' )->where( 'uid=' . $this->uid )->select( 'userstatic' );
		if( !$res[0] )
		{
			return -1014;
		}
		if( $res[0]['encpass'] != $encpass )
		{
			return -1013;
		}
		return true;
	}

	/**
	 * 获取用户财产（欢朋豆|欢朋币）
	 *
	 * @return array  array(
	 * 'bean' => 0,//欢朋豆
	 * 'coin' => 0//欢朋币
	 * )
	 */
	public function getUserProperty()
	{
		$res = $this->_db->field( 'hpbean,hpcoin' )->where( 'uid=' . $this->uid )->limit( 1 )->select( 'useractive' );
		if( $res && false !== $res )
		{
			return array( 'bean' => $res[0]['hpbean'], 'coin' => $res[0]['hpcoin'] );
		}
		else
		{
			return false;
		}
	}

	/**
	 * 添加经验值
	 *
	 * @param $exp 经验值
	 *
	 * @return array 返回 是否升级|和等级领个字段
	 * array(
	 * 'isUp' =>  1,//0未升级 1升级
	 * 'level' => 7//当前等级数
	 * )
	 */
	public function addUserExp( $exp )
	{
		$isUp = 0;
		$levelInfo = $this->_getNowLevelInfo();//未加经验前的等级和经验值
		$addres = $this->_addExp( $exp );//添加经验值
		if( $addres )
		{
			$userLevelList = $this->getUserLevelInfoList();
			$exp = ( $exp * self::INTEGRAL ) + ( $levelInfo['integral'] * self::INTEGRAL );
			$level = $levelInfo['level'];
			$nextLevelExp = $userLevelList[$level] * self::INTEGRAL;
			if( (int)$exp >= (int)$nextLevelExp )
			{
				$maxExp = max( array_values( $userLevelList ) );
				if( (int)$exp < $maxExp )
				{
					$newlevel = $this->_getUserLevelByExp( $exp / self::INTEGRAL );
					$uplevel = $this->_updateLevel( $newlevel );
					if( $uplevel )
					{
						$isUp = 1;
					}
				}
			}
		}
		if( $isUp )
		{
			$lv = $newlevel;
		}
		else
		{
			$lv = $levelInfo['level'];
		}
		return array( 'isUp' => $isUp, 'level' => $lv );
	}


	private function _getUserLevelByExp( $exp )
	{
		if( empty( $exp ) )
		{
			return false;
		}
		$res = $this->_db->where( "integral >$exp" )->limit( 1 )->select( 'userlevel' );
		if( false !== $res )
		{
			return $res[0]['level'];
		}
		else
		{
			return false;
		}
	}

	/**
	 * 加经验值操作
	 *
	 * @param $exp 经验值
	 *
	 * @return bool
	 */
	private function _addExp( $exp )
	{
		$sql = "update useractive set integral= integral + $exp  where uid = $this->uid";
		$res = $this->_db->doSql( $sql );
		if( false !== $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 更新用户等级
	 *
	 * @param $level 等级
	 *
	 * @return bool 成功返回true 失败返回false
	 */
	private function _updateLevel( $level )
	{
		$res = $this->_db->where( 'uid=' . $this->uid )->update( 'useractive', array( 'level' => $level ) );
		if( false !== $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 获取用户等级信息
	 *
	 * @return array array(
	 * 'level' => 0,//等级
	 * 'integral' => 0//经验值
	 * )
	 */
	private function _getNowLevelInfo()
	{
		$res = $this->_db->field( 'level,integral' )->where( 'uid=' . $this->uid )->limit( 1 )->select( 'useractive' );
		if( $res && false !== $res )
		{
			return array( 'level' => $res[0]['level'], 'integral' => $res[0]['integral'] );
		}
		else
		{
			return array( 'level' => 0, 'integral' => 0 );
		}
	}

	/**
	 * 获取用户等级列表信息
	 *
	 * @return array  key为等级 value为经验值
	 */
	public function getUserLevelInfoList()
	{
		$levelList = array();
		$res = $this->_db->select( 'userlevel' );
		if( $res && false !== $res )
		{
			foreach ( $res as $v )
			{
				$levelList[$v['level']] = $v['integral'];
			}
		}
		return $levelList;
	}

	/**
	 * 获取用户手机认证状态
	 *
	 * @return array array(
	 * 'phone'='',//手机号码
	 * 'status'=>''//  1已绑定手机  0 未绑定手机
	 * )
	 */
	private function _getPhoneCertifyInfo()
	{
		$res = $this->_db->field( 'phone' )->where( 'uid=' . $this->uid )->limit( 1 )->select( 'userstatic' );
		if( $res[0]['phone'] )
		{
			$r['phone'] = $res[0]['phone'];
			$r['status'] = 1;
		}
		else
		{
			$r['phone'] = '';
			$r['status'] = 0;
		}
		return $r;
	}

	/**
	 * 获取邮箱认证状态
	 *
	 * @return array array(
	 *    mail=>'',//邮箱
	 *    status=>''//邮箱认证状态
	 * )
	 */
	private function _getEmailCertifyInfo()
	{
		$res = $this->_db->field( 'mail, mailstatus' )->where( 'uid = ' . $this->uid )->limit( 1 )->select( 'userstatic' );
		$ret['mail'] = $res[0]['mail'];
		$ret['status'] = $res[0]['mailstatus'];
		return $ret;
	}

	/**
	 * 获取实名认证状态
	 *
	 * @return array array(
	 *    ident=>'',//证件号
	 *    status=>''//认证状态
	 * )
	 */
	private function _getRealNameCertifyInfo()
	{
		$res = $this->_db->field( 'id, papersid, status' )->where( 'uid=' . $this->uid )->limit( 1 )->select( 'userrealname' );
		if( empty( $res ) )
		{
			$r['ident'] = '';
			$r['status'] = 0;
		}
		else
		{
			$r['ident'] = $res[0]['papersid'];
			$r['status'] = (int)$res[0]['status'];
		}
		return $r;
	}

	/**
	 * 获取银行卡认证状态
	 *
	 * @return array array(
	 *    bank=>'',//卡号
	 *    status=>''//认证状态
	 * )
	 */
	private function _getBankCertifyInfo()
	{
		$res = $this->_db->field( 'id,cardid, status' )->where( 'uid=' . $this->uid )->limit( 1 )->select( 'userbankcard' );
		if( !isset( $res[0]['id'] ) )
		{
			$r['bank'] = '';
			$r['status'] = 0;
		}
		else
		{
			$r['bank'] = $res[0]['cardid'];
			$r['status'] = (int)$res[0]['status'];
		}
		return $r;
	}

	/**
	 * 获取用户认证信息 邮箱｜电话｜实名认证｜银行卡
	 *
	 * @return array  array(){
	 *     'email'=>'',//邮箱
	 *     'emailstatus'=>'',//邮箱认证状态
	 *     'phone'=>'',//手机号码
	 *     'phonestatus'=>'',//手机认证状态
	 *     'ident'=>'',//证件号码
	 *     'identstatus'=>'',//实名认证状态
	 *     'bank'=>'',//银行卡号
	 *     'bankstatus'=>''//银行卡绑定状态
	 * }
	 */
	public function getCertifyInfo()
	{
		$tmp = $this->_getEmailCertifyInfo();
		$r['email'] = $tmp['mail'];
		$r['emailstatus'] = (int)$tmp['status'];

		$tmp = $this->_getPhoneCertifyInfo();
		$r['phone'] = $tmp['phone'];
		$r['phonestatus'] = $tmp['status'];

		$tmp = $this->_getRealNameCertifyInfo();
		$r['ident'] = $tmp['ident'];
		$r['identstatus'] = $tmp['status'];

		$tmp = $this->_getBankCertifyInfo();
		$r['bank'] = $tmp['bank'];
		$r['bankstatus'] = $tmp['status'];

		return $r;
	}

	/**
	 * 是否关注
	 *
	 * @param int $luid 主播id
	 *
	 * @return bool  true:已关注  false:未关注
	 */
	public function isFollow( $luid )
	{
		if( empty( $luid ) )
		{
			return false;
		}
		$res = $this->_db->field( 'uid1' )->where( "uid2 = $luid  and  uid1=" . $this->uid )->select( 'userfollow' );
		if( $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	/**
	 * 关注主播
	 *
	 * @param int $luid 主播id
	 *
	 * @return  bool  成功返回true  失败返回false
	 */
	public function followAnchor( $luid )
	{
		if( empty( $luid ) )
		{
			return false;
		}
		$res = $this->_addFollow( $luid );
		if( $res )
		{
			$this->_addLiveNotice( $luid );//开播提醒
			$isOver = "FOLLOWUSER_OVER_$this->uid";
			if( $this->_redis->isExists( $isOver ) === false )
			{//同步关注5个主播的奖励
				if( $this->getCountForFollow() >= 5 )
				{
					$this->_redis->set( $isOver, 1 );
					Task::synchroTask( $this->uid, 18, $this->_db );
				}
				return true;
			}
			else
			{
				return true;
			}

		}
		else
		{
			return false;
		}
	}

	private function _addFollow( $luid )
	{
		$sql = "INSERT INTO `userfollow` (`uid1`, `uid2`) VALUES ($this->uid,$luid) on duplicate key update uid1 = $this->uid, uid2 = $luid";
		$res = $this->_db->doSql( $sql );
		if( false !== $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 *关注主播以后，添加到开播提醒表
	 *
	 * @param int $luid 主播id
	 *
	 * @return bool
	 */
	private function _addLiveNotice( $luid )
	{
		$res = $this->_db->insert( 'live_notice', array( 'uid' => $this->uid, 'luid' => $luid ) );
		if( false !== $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 取消关注
	 *
	 * @param int $luid 主播id
	 *
	 * @return  成功返回true  失败返回false
	 */
	public function removeFollowedAnchor( $luid )
	{
		if( empty( $luid ) )
		{
			return false;
		}
		$res = $this->_removeFollow( $luid );
		if( $res )
		{
			$this->_removeLiveNotice( $luid );
			return true;
		}
		else
		{
			return false;
		}
	}

	private function _removeFollow( $luid )
	{
		$res = $this->_db->where( "uid2=$luid and uid1=" . $this->uid )->delete( 'userfollow' );
		if( false !== $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	private function _removeLiveNotice( $luid )
	{
		$res = $this->_db->where( "uid=$this->uid  and luid in ($luid)" )->delete( 'live_notice' );
		return $res;
	}

	/**
	 * 已关注主播数量
	 *
	 * @return  int
	 */
	public function getCountForFollow()
	{
		$res = $this->_db->field( "count(*) as total" )->where( "uid1=" . $this->uid )->select( "userfollow" );
		if( $res )
		{
			return $res[0]['total'];
		}
		else
		{
			return 0;
		}
	}

	/**
	 * 已关注主播中正在直播的主播数量
	 *
	 * @return  int
	 */
	public function getAnchorLiveListInFollow()
	{
		$list = array();
		$luids = $this->getFollowList();
		if( $luids )
		{
			$luids = implode( ',', $luids );
			$res = $this->_db->field( "uid" )->where( "status=" . LIVE . " and  uid in ($luids)" )->select( "live" );
			if( $res )
			{
				foreach ( $res as $v )
				{
					array_push( $list, $v['uid'] );
				}

			}
		}
		return array( 'list' => $list, 'total' => count( $list ) );
	}

	/**
	 * 已关注主播列表
	 *
	 * return array  array(90,1815)
	 */
	public function getFollowList()
	{
		$list = array();
		$res = $this->_db->field( 'uid2' )->where( 'uid1=' . $this->uid )->select( "userfollow" );
		if( $res )
		{
			foreach ( $res as $v )
			{
				array_push( $list, $v['uid2'] );
			}
		}
		return $list;
	}

	/**
	 * 是否收藏该录像
	 *
	 * @param int $videoid 录像id
	 *
	 * return  bool  true:已收藏   false:未收藏
	 */
	public function isCollect( $videoid )
	{
		if( empty( $videoid ) )
		{
			return false;
		}
		$res = $this->_db->field( 'videoid' )->where( "videoid=$videoid and uid=$this->uid" )->limit( 1 )->select( 'videofollow' );
		if( isset( $res[0]['videoid'] ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 收藏录像
	 *
	 * @param int $videoid 录像id
	 *
	 * return  bool  true:收藏成功   false:收藏失败
	 */
	public function collectVideo( $videoid )
	{
		if( empty( $videoid ) )
		{
			return false;
		}
		$sql = "insert into videofollow (uid, videoid) value($this->uid, $videoid) on duplicate key update videoid=$videoid";
		$res = $this->_db->doSql( $sql );
		if( false !== $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 取消收藏录像
	 *
	 * @param int $videoids 录像id  多个用逗号隔开 如:8976,234
	 *
	 * return  bool  true:取消收藏成功   false:取消收藏失败
	 */
	public function removeCollectVideo( $videoids )
	{
		if( empty( $videoids ) )
		{
			return false;
		}
		$res = $this->_db->where( "uid=$this->uid and videoid  in($videoids)" )->delete( 'videofollow' );
		if( false !== $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 收藏总数
	 *
	 * return  int
	 */
	public function getCountForCollect()
	{
		$res = $this->_db->field( "count(videoid) as total" )->where( 'uid=' . $this->uid )->select( 'videofollow' );
		if( $res )
		{
			return $res[0]['total'];
		}
		else
		{
			return 0;
		}
	}

	/**
	 * 已收藏录像列表
	 *
	 * return array  array(2908,12815)
	 */
	public function getCollectList()
	{
		$list = array();
		$res = $this->_db->field( 'videoid' )->where( 'uid=' . $this->uid )->select( "videofollow" );
		if( $res )
		{
			foreach ( $res as $v )
			{
				array_push( $list, $v['videoid'] );
			}
		}
		return $list;
	}

	/**
	 * 获取当前等级对应的积分上限
	 *
	 * @param int $level 用户等级
	 *
	 * @return bool|int 积分
	 */
	public function getIntegralByUserLevel( $level )
	{
		if( empty( $level ) )
		{
			return false;
		}
		$res = $this->_db->field( 'integral' )->where( 'level=' . $level )->limit( 1 )->select( 'userlevel' );
		if( $res )
		{
			return (int)$res[0]['integral'];
		}
		else
		{
			return false;
		}
	}

	/**
	 * 是否被禁言
	 *
	 * return  待定
	 */
	public function isSilenced( $luid )
	{
		$time = date( 'Y-m-d H:i:s', time() );
		$res = $this->_db->field( 'uid,etime' )->where( "type=1 and uid =$this->uid and luid=$luid and etime >= '$time'" )->select( 'usersilence' );
		if( $res[0]['uid'] )
		{
			return strtotime( $res[0]['etime'] );
		}
		else
		{
			return 0;
		}
	}

	/**
	 * 获取用户注册时间
	 *
	 * @return bool 注册时间
	 */
	public function getUserRegisterTime()
	{
		$res = $this->_db->field( 'rtime' )->where( 'uid=' . $this->uid )->select( 'userstatic' );
		if( $res )
		{
			return $res[0]['rtime'];
		}
		else
		{
			return false;
		}
	}

	/**
	 * 是否是房管
	 *
	 * @param  int $luid 主播id
	 *
	 * return  bool  true:是房管   false:不是房管
	 */
	public function isRoomManager( $luid )
	{
		if( empty( $luid ) )
		{
			return false;
		}
		$res = $this->_db->field( 'uid' )->where( " uid = $this->uid and luid = $luid" )->select( 'roommanager' );
		if( $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 获取用户encpass
	 *
	 * return string
	 */
	public function getUserEncpass()
	{
		$res = $this->_db->field( 'encpass' )->where( 'uid=' . $this->uid )->limit( 1 )->select( 'userstatic' );
		if( $res )
		{
			return $res[0]['encpass'];
		}
		else
		{
			return 0;
		}
	}

	/**
	 * 修改昵称
	 *
	 * @param string $nick 昵称
	 *
	 * return bool 成功true  失败false
	 */
	public function updateNick( $nick )
	{
		if( empty( $nick ) )
		{
			return false;
		}
		$res = $this->_db->where( "uid=" . $this->uid )->update( "userstatic", array( 'nick' => $nick, 'username' => $nick ) );
		if( $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 修改性别
	 *
	 * @param int $sex 性别  0女  1男
	 *
	 * return bool 成功true  失败false
	 */
	public function updateSex( $sex )
	{
		if( !in_array( $sex, array( 0, 1 ) ) )
		{
			return false;
		}
		$res = $this->_db->where( "uid=" . $this->uid )->update( "userstatic", array( 'sex' => $sex ) );
		if( $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 校验是否有免费的改名机会
	 *
	 * return bool  true有机会 | false 木有机会
	 */
	public function checkIsFreeUpdateNick()
	{
		$res = $this->_db->field( 'isfree' )->where( "uid=" . $this->uid )->limit( 1 )->select( 'userstatic' );
		if( $res[0]['isfree'] == self::FREE_NICK )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**设置免费改名| 重置免费改名
	 *
	 * @param  int $type 1设置免费改名机会标志  0重置免费机会标志
	 *
	 * @return bool 成功true | 失败false
	 */
	public function updateIsFree( $type )
	{
		if( !in_array( $type, array( self::UNFREE_NICK, self::FREE_NICK ) ) )
		{
			return false;
		}
		$res = $this->_db->where( "uid=" . $this->uid )->update( 'userstatic', array( 'isfree' => $type ) );
		if( $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 修改用户地址
	 *
	 * @param int    $pid     省份id
	 * @param int    $cid     城市id
	 * @param string $address 详细信息
	 *
	 * return bool  成功true  失败false
	 */
	public function updateAddress( $pid, $cid, $address )
	{
		if( empty( $pid ) || empty( $cid ) || empty( $address ) )
		{
			return false;
		}
		$data = array(
			'province' => $pid,
			'city' => $cid,
			'address' => $address
		);
		$res = $this->_db->where( "uid=" . $this->uid )->update( 'useractive', $data );
		if( $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 修改密码
	 *
	 * @param string $password 密码
	 *
	 * @return bool  成功 true  失败 false
	 */
	public function updatePassword( $password )
	{
		if( empty( $password ) )
		{
			return false;
		}
		$data = array( 'password' => md5password( $password ), 'encpass'=> md5(md5($password . time())));
		$res = $this->_db->where( "uid=" . $this->uid )->update( 'userstatic', $data );
		if( $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 获取消息列表
	 *
	 * @param $page  页数
	 * @param $size  每页数量
	 *
	 *  return array  array(){
	 *  'id'=>'',//消息id
	 *  'title'=>'',//标题
	 *  'msg'=>'',//消息内容
	 *  'stime'=>'',//发送时间
	 *  'sendid'=>''//发件人id
	 *  }
	 *
	 */
	public function getMessageList( $page, $size )
	{
		$res = $this->_db->field( 'msgid' )->where( "uid=$this->uid and status=0" )->order( 'msgid desc' )->limit( $page, $size )->select( 'usermessage' );
		if( $res )
		{
			foreach ( $res as $v )
			{
				$lists[] = $v['msgid'];
			}
			$msgid = implode( ',', $lists );
		}
		else
		{
			$msgid = '';
		}
		if( $msgid )
		{
			$msglist = $this->_db->field( 'id,title,msg,stime,sendid' )->where( "id in ($msgid)" )->order( 'stime DESC' )->select( 'sysmessage' );
			if( $msglist )
			{
				return array( 'list' => $msglist, 'total' => $this->_getMessageCount() );
			}
			else
			{
				return array( 'list' => array(), 'total' => 0 );
			}
		}
		else
		{
			return array( 'list' => array(), 'total' => 0 );
		}
	}

	/**
	 * 获取消息总数
	 *
	 * return int
	 */
	private function _getMessageCount()
	{
		$res = $this->_db->field( 'count(*) as total' )->where( "uid=$this->uid and status=0" )->select( 'usermessage' );
		if( $res )
		{
			return $res[0]['total'];
		}
		else
		{
			return 0;
		}
	}


	/**
	 * 删除消息
	 *
	 * @param array $msgIds 删除消息    多条用 array(32,123)
	 *
	 * return  bool 成功true 失败false
	 */
	public function removeMessage( $msgIdArray )
	{
		if( empty( $msgIdArray ) || !is_array( $msgIdArray ) )
		{
			return false;
		}
		$delList = implode( ',', $msgIdArray );
		$res = $this->_db->where( "uid=$this->uid and msgid in ($delList)" )->update( 'usermessage', array( 'status' => 1 ) );
		if( $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 点赞｜取消点赞
	 *
	 * @param int $videoid 录像id
	 * @param int $type    1点赞   0取消点赞
	 *
	 * @return bool 成功true  失败false
	 */
	public function upVoteOrUnVote( $videoId, $type )
	{
		if( empty( $videoId ) || !in_array( $type, array( 0, 1 ) ) )
		{
			return false;
		}
		$checkisup = $this->_db->where( "videoid=$videoId and  uid=$this->uid" )->select( 'isupvideo' );
		if( $type )
		{//点赞
			if( $checkisup )
			{
				return true;
			}
			else
			{
				$sql = "update video set upcount=upcount+1 where videoid=$videoId";
				$res = $this->_db->doSql( $sql );
				if( $res )
				{
					$data = array(
						'videoid' => $videoId,
						'uid' => $this->uid
					);
					$this->_db->insert( 'isupvideo', $data );
					return true;
				}
				else
				{
					return false;
				}
			}
		}
		else
		{//取消点赞
			if( $checkisup )
			{
				$res = $this->_db->where( "videoid=$videoId and  uid=$this->uid" )->delete( 'isupvideo' );
				if( $res )
				{
					$sql = "update video set upcount=upcount-1 where videoid=$videoId";
					$result = $this->_db->doSql( $sql );
					if( $result )
					{
						return true;
					}
				}
				else
				{
					return false;
				}
			}
			else
			{
				return true;
			}
		}
	}


	/**
	 * 获取浏览历史
	 *
	 * @param  int $page 页码
	 * @param  int $size 每页数量
	 *
	 * return array array(
	 *  'luid'=>'',//主播id
	 *  'stime'=> ''//时间
	 * )
	 */
	public function getHistoryList( $page, $size )
	{
		$before = date( 'Y-m-d H:i:s', strtotime( '-30 day' ) );
		$number = $this->_db->field( 'count(*) as total' )->where( "status=1 and  uid =$this->uid  and  stime >='$before'" )->select( 'history' );
		if( $number )
		{
			$res = $this->_db->field( 'luid,stime' )->where( "status=1 and  uid =$this->uid  and  stime >='$before'" )->order( 'stime desc' )->limit( $page, $size )->select( 'history' );
			if( $res )
			{
				return array( 'list' => $res, 'total' => $number );
			}
			else
			{
				return array( 'list' => array(), 'total' => $number );
			}
		}
		else
		{
			return array( 'list' => array(), 'total' => 0 );

		}
	}

	/**
	 * 删除浏览历史
	 *
	 * @param array $historyIds 历史纪录id  删除多个可用数组 如：array(23,56)
	 *
	 *return bool  成功true  ｜ 失败false
	 */
	public function removeHistory( $historyIdArray )
	{
		if( empty( $historyIdArray ) || !is_array( $historyIdArray ) )
		{
			return false;
		}
		$delList = implode( ',', $historyIdArray );
		$res = $this->_db->where( 'uid=' . $this->uid . ' and luid in (' . $delList . ')' )->update( 'history', array( 'status' => 0 ) );
		if( $res !== false )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 检测是否对某个主播开启开播提醒
	 *
	 * @param int $luid 主播id
	 *
	 * @return bool  开启true 未开启false
	 */
	public function isLiveNotify( $luid )
	{
		if( empty( $luid ) )
		{
			return false;
		}
		$res = $this->_db->where( "uid=$this->uid and luid=$luid" )->select( "live_notice" );
		if( $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 检测用户是否开启开播提醒总开关
	 *
	 * @return bool|int  0未开启  1开启
	 */
	public function checkLiveNotifyIsOpen()
	{
		$res = $this->_db->field( 'isnotice' )->where( 'uid=' . $this->uid )->select( 'useractive' );
		if( $res )
		{
			return $res[0]['isnotice'];
		}
		else
		{
			return false;
		}
	}


	/**
	 * 设置用户推送总开关
	 *
	 * @param int $type 0关闭 1开启
	 *
	 * @return bool  成功true 失败false
	 */
	public function setNotifyStatus( $type )
	{
		if( !in_array( $type, array( self::OPEN_LIVE_NOTICE, self::COLSE_LIVE_NOTICE ) ) )
		{
			return false;
		}
		if( $type )
		{
			$status = self::OPEN_LIVE_NOTICE;//开启
		}
		else
		{
			$status = self::COLSE_LIVE_NOTICE;//关闭
		}

		$res = $this->_db->where( 'uid=' . $this->uid )->update( 'useractive', array( 'isnotice' => $status ) );
		if( false !== $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 开启｜关闭对某个主播的消息推送
	 *
	 * @param int $luid 主播id
	 * @param int $type 0关闭 1开启
	 *
	 * @return bool  成功true 失败false
	 */
	public function setLiveNotify( $luid, $type )
	{
		if( !in_array( $type, array( self::OPEN_LIVE_NOTICE, self::COLSE_LIVE_NOTICE ) ) )
		{
			return false;
		}
		if( $type == self::COLSE_LIVE_NOTICE )
		{//删除
			$res = $this->_db->where( "uid=$this->uid  and luid=$luid" )->delete( 'live_notice' );
		}
		if( $type == self::OPEN_LIVE_NOTICE )
		{//添加
			$data = array(
				'uid' => $this->uid,
				'luid' => $luid
			);
			$res = $this->_db->where( "uid=$this->uid  and luid=$luid" )->select( 'live_notice' );
			if( $res )
			{
				$res = true;
			}
			else
			{
				$res = $this->_db->insert( 'live_notice', $data );
			}
		}
		if( $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function initTable( $type, $month )
	{

		if( $type == Gift::SEND_TYPE_BEAN )
		{
			return 'giftrecord_' . $month;//初始化
		}
		else
		{
			return 'giftrecordcoin_' . $month;//初始化
		}

	}

	/**
	 * 赠送欢朋豆纪录
	 *
	 * @param string $from  开始时间 如:'2017-03-01 00:00:00'
	 * @param string $to    结束时间 如:'2017-03-31 23:59:59'
	 * @param int    $page  页数
	 * @param int    $size  数量
	 * @param int    $month 月份
	 *
	 * @return array
	 */
	public function getHpBeanSendRecord( $from, $to, $page, $size, $month )
	{
		if( empty( $from ) || empty( $to ) )
		{
			return false;
		}
		if( $month )
		{
			if( (int)$month < 10 )
			{
				$month = date( 'Y' ) . '0' . $month;
			}
			else
			{
				$month = date( 'Y' ) . $month;
			}
		}
		else
		{
			$month = date( 'Ym' );
		}

		$res = $this->_db->where( " uid = $this->uid  and otid !=0  and ctime BETWEEN '$from' and '$to'" )->order( 'ctime DESC' )->limit( $page, $size )->select( $this->initTable( Gift::SEND_TYPE_BEAN, $month ) );
		if( $res )
		{
			return array( 'bean' => $res, 'total' => self::getHpBeanSendNumber( $from, $to, $month ) );
		}
		else
		{
			return array( 'bean' => array(), 'total' => 0 );
		}
	}

	/**
	 * 赠送欢朋币列表
	 *
	 * @param string $from  开始时间 如:'2017-03-01 00:00:00'
	 * @param string $to    结束时间 如:'2017-03-31 23:59:59'
	 * @param int    $page  页数
	 * @param int    $size  数量
	 * @param int    $month 月份
	 *
	 * @return array
	 */
	public function getHpCoinSendRecord( $from, $to, $page, $size, $month )
	{
		if( empty( $from ) || empty( $to ) )
		{
			return false;
		}
		if( $month )
		{
			if( (int)$month < 10 )
			{
				$month = date( 'Y' ) . '0' . $month;
			}
			else
			{
				$month = date( 'Y' ) . $month;
			}
		}
		else
		{
			$month = date( 'Ym' );
		}
		$res = $this->_db->where( "uid = $this->uid and otid !=0 and ctime BETWEEN '$from' and '$to'" )->order( 'ctime DESC' )->limit( $page, $size )->select( $this->initTable( Gift::SEND_TYPE_COIN, $month ) );
		if( $res )
		{
			return array( 'coin' => $res, 'total' => self::getHpCoinSendNumber( $from, $to, $month ) );
		}
		else
		{
			return array( 'coin' => array(), 'total' => 0 );
		}
	}

	/**
	 * 获取欢朋豆消费总记录数量
	 *
	 * @param string $from 开始时间 如:'2017-03-01 00:00:00'
	 * @param string $to   结束时间 如:'2017-03-31 23:59:59'
	 *
	 * @return int
	 */
	private function getHpBeanSendNumber( $from, $to, $month )
	{
		$res = $this->_db->field( 'count(*) as  total' )->where( "uid = $this->uid  and otid !=0  and ctime between '$from' and '$to'" )->select( $this->initTable( Gift::SEND_TYPE_BEAN, $month ) );
		if( $res )
		{
			return $res[0]['total'];
		}
		else
		{
			return 0;
		}
	}

	/**
	 * 获取欢朋币消费总记录数量
	 *
	 * @param string $from 开始时间 如:'2017-03-01 00:00:00'
	 * @param string $to   结束时间 如:'2017-03-31 23:59:59'
	 *
	 * @return int
	 */
	private function getHpCoinSendNumber( $from, $to, $month )
	{
		$res = $this->_db->field( 'count(*) as  total' )->where( "uid = $this->uid and otid !=0  and ctime between '$from' and '$to'" )->select( $this->initTable( Gift::SEND_TYPE_COIN, $month ) );
		if( $res )
		{
			return $res[0]['total'];
		}
		else
		{
			return 0;
		}
	}

	/**
	 * 校验用户是否存在
	 *
	 * @param int    $uid 用户id
	 * @param object $db
	 *
	 * @return bool
	 */
	public static function checkUersIsExistByUid( $uid, $db )
	{
		$res = $db->where( "uid=$uid" )->limit( 1 )->select( 'userstatic' );
		if( false !== $res && !empty( $res ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	/**
	 * 今日赠送欢朋币总数量
	 *
	 * @return int
	 */
	public function getTodayHpCoinSendNumber()
	{
		//TODO
	}


	/**
	 * 今日赠送欢朋豆总数量
	 *
	 * @return int
	 */
	public function getTodayHpBeanSendNumber()
	{
		//TODO
	}

	/**
	 * 更新用户邮箱状态
	 *
	 * @param  string $email  邮箱
	 * @param  int    $status 认证状态
	 *
	 * @return bool
	 */
	public function updateUserMailStatus( $email, $status )
	{
		$res = $this->_db->where( "uid=" . $this->uid )->update( 'userstatic', array( 'mail' => "$email", 'mailstatus' => $status ) );
		if( false !== $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 设置用户第一次充值时间|充值次数
	 *
	 * @param string $time 充值完成时间
	 *
	 * @return bool
	 */
	public function afterRecharge( $time )
	{
		if( empty( $time ) )
		{
			return false;
		}

		$checkIsExist = $this->getFirstRechargeTime();

		if($checkIsExist == self::CTIME_DEFAULT)
		{
			$res = $this->setFirstSendGiftTime($time);
		}
		else
		{
			$res = $this->setRechargeNumber();
		}

		if( false !== $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 获取用户首充时间
	 *
	 * @return bool 没有充值过返回0 有则返回充值时间
	 */
	public function getFirstRechargeTime()
	{
		$res = $this->_db->field( 'first_recharge_time' )->where( "uid=" . $this->uid )->limit( 1 )->select( 'userstatic' );
		if( false !== $res )
		{
			if( $res )
			{
				return $res[0]['first_recharge_time'];
			}
			else
			{
				return 0;
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * 设置用户第一次送礼时间
	 *
	 * @param string $time 首次送礼时间
	 *
	 * @return bool
	 */
	public function setFirstSendGiftTime( $time )
	{
		if( empty( $time ) )
		{
			return false;
		}
//		$res = $this->_db->where( "uid=" . $this->uid )->update( 'userstatic', array( 'first_sendgift_time' => "$time" ) );
		$res = $this->_db->where( "uid=" . $this->uid )->update( 'userstatic', array( 'first_recharge_time' => "$time",'recharge_number'=>1) );
		if( false !== $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 获取用户首次送礼时间
	 *
	 * @return bool 没有送过返回0 有则返回送礼时间
	 */
	public function getFirstSendGiftTime()
	{
		$res = $this->_db->field( 'first_sendgift_time' )->where( "uid=" . $this->uid )->limit( 1 )->select( 'userstatic' );
		if( false !== $res )
		{
			if( $res )
			{
				return $res[0]['first_sendgift_time'];
			}
			else
			{
				return 0;
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * 设置用户第一次开播时间
	 *
	 * @param string $time 首次开播时间
	 *
	 * @return bool
	 */
	public function setFirstLiveTime( $time )
	{
		if( empty( $time ) )
		{
			return false;
		}
		$res = $this->_db->where( "uid=" . $this->uid )->update( 'userstatic', array( 'first_live_time' => "$time" ) );
		if( false !== $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 获取用户首次开播时间
	 *
	 * @return bool 没有开播返回0 有则返回首次开播时间
	 */
	public function getFirstLiveTime()
	{
		$res = $this->_db->field( 'first_live_time' )->where( "uid=" . $this->uid )->limit( 1 )->select( 'userstatic' );
		if( false !== $res )
		{
			if( $res )
			{
				return $res[0]['first_live_time'];
			}
			else
			{
				return 0;
			}
		}
		else
		{
			return false;
		}
	}

	/**获取用户充值次数
	 * @return bool|int
	 */
    public function getRechargeNumber(){
		$res = $this->_db->field( 'recharge_number' )->where( "uid=" . $this->uid )->limit( 1 )->select( 'userstatic' );
		if( false !== $res )
		{
			return $res[0]['recharge_number'];
		}
		else
		{
			return false;
		}
	}

	/**
	 *设置用户充值次数
	 */
	public  function setRechargeNumber(){
		$sql="update userstatic set recharge_number=recharge_number+1 where uid=".$this->uid;
		$res=$this->_db->doSql($sql);
		if( false !== $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

}