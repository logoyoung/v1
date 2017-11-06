<?php
namespace lib;


use \DBHelperi_huanpeng;
use lib\User;

class Anchor extends User
{
	public $uid = '';//用户uid
	private $_db = '';//数据库对象

	/**
	 * Anchor constructor 初始化
	 *
	 * @param string $uid 用户id
	 * @param string $db  数据库对象
	 */
	public function __construct( $uid = '', $db = '' )
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
		parent::__construct( $uid, $this->_db );
	}

	/**
	 * 添加主播金豆
	 *
	 * @param $bean 要添加的金豆数量
	 *
	 * @return bool  添加成功 返回true|失败false
	 */
	public function addAnchorBean( $bean )
	{
		if( empty( $bean ) )
		{
			return false;
		}
		$sql = "UPDATE anchor SET bean = bean + $bean where uid=$this->uid";
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
	 * 更新主播金豆
	 *
	 * @param $bean    消费金豆数额
	 *
	 * @return bool 成功返回true ｜ 失败false
	 */
	public function updateAnchorBean( $bean )
	{
		$res = $this->_db->where( 'uid=' . $this->uid )->update( 'anchor', array( 'bean' => $bean ) );
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
	 * 更新主播金币
	 *
	 * @param $coin 用户金币余额
	 *
	 * @return bool 成功返回true  失败返回false
	 */
	public function updateAnchorCoin( $coin )
	{
		$res = $this->_db->where( 'uid=' . $this->uid )->update( 'anchor', array( 'coin' => $coin ) );
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
	 * 获取主播财产（金豆｜金币）
	 *
	 * @return array
	 * array(
	 *  'bean' => 0,//金豆
	 *  'coin' => 0//金币
	 * );
	 */
	public function getAnchorProperty()
	{
		$res = $this->_db->field( 'bean,coin' )->where( 'uid=' . $this->uid )->limit( 1 )->select( 'anchor' );
		if( $res && false !== $res )
		{
			return array( 'bean' => $res[0]['bean'], 'coin' => $res[0]['coin'] );
		}
		else
		{
			return array( 'bean' => 0, 'coin' => 0 );
		}
	}

    /**
     * 获取主播认证信息 （cid经纪公司、cert_status认证状态）
     * @return array
     */
    public  function getAnchorCertInfo()
    {
        $res = $this->_db->field( 'cid,cert_status' )->where( 'uid=' . $this->uid )->limit( 1 )->select( 'anchor' );
        if( $res && false !== $res )
        {
            return array( 'cid' => $res[0]['cid'], 'cert_status' => $res[0]['cert_status'] );
        }
        else
        {
            return array( 'cid' => 0, 'cert_status' => 0 );
        }

    }
	/**
	 * 批量获取主播等级
	 *
	 * @param array  $luidsArray 主播id  多个如array(2780,1815)
	 * @param object $db
	 *
	 * @return array|bool
	 */
	public static function getAnchorsLevelByUids( $luidsArray, $db )
	{
		if( empty( $luidsArray ) || !is_array( $luidsArray ) )
		{
			return false;
		}
		$list = array();
		$uids = implode( ',', $luidsArray );
		$res = $db->field( 'uid,level' )->where( "uid in($uids)" )->order( 'level desc' )->select( 'anchor' );

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
	 * 获取等级最高的前若干个主播
	 *
	 * @param int    $size 数量
	 * @param object $db
	 *
	 * @return array
	 */
	public static function getAnchorlevelRank( $size, $db )
	{
		$list = array();
		$res = $db->field( 'uid,level,integral' )->order( 'level desc' )->where( "uid not in (" . WHITE_LIST . ")" )->limit( $size )->select( 'anchor' );
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
	 * 添加经验值
	 *
	 * @param $exp 经验值
	 *
	 * @return array 返回 是否升级|和等级领个字段  isUp：0未升级 1升级 level 当前等级数
	 * array( 'isUp' => 0, 'level' => 7 );
	 */
	public function addAnchorExp( $exp )
	{
		$isUp = 0;
		$levelInfo = $this->_getNowAnchorLevelInfo();//未加经验前的等级和经验值
		$addres = $this->_addExp( $exp );//添加经验值
		if( $addres )
		{
			$userLevelList = $this->getAnchorLevelInfoList();
			$exp = ( $exp * self::INTEGRAL ) + ( $levelInfo['integral'] * self::INTEGRAL );
			$level = $levelInfo['level'];
			$nextLevelExp = $userLevelList[$level] * self::INTEGRAL;
			if( (int)$exp >= (int)$nextLevelExp )
			{
				$maxExp = max( array_values( $userLevelList ) );
				if( (int)$exp < (int)$maxExp )
				{
					$newlevel = $this->_getAnchorLevelByExp( $exp / self::INTEGRAL );
					$uplevel = $this->_updateAnchorLevel( $newlevel );
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

	private function _getAnchorLevelByExp( $exp )
	{
		if( empty( $exp ) )
		{
			return false;
		}
		$res = $this->_db->where( "integral >$exp" )->limit( 1 )->select( 'anchorlevel' );
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
	 * @param $exp 经验值
	 *
	 * @return bool 成功返回true  失败返回false
	 */
	private function _addExp( $exp )
	{
		$sql = "update anchor set integral= integral + $exp  where uid = $this->uid";
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
	 * 更新主播等级
	 *
	 * @param int $level 等级
	 *
	 * @return bool  更新成功返回true  失败返回false
	 */
	private function _updateAnchorLevel( $level )
	{
		if( empty( $level ) )
		{
			return false;
		}
		$res = $this->_db->where( 'uid=' . $this->uid )->update( 'anchor', array( 'level' => $level ) );
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
	 * 获取当前等级对应的积分上限
	 *
	 * @param int $level 主播等级
	 *
	 * @return bool|int 积分
	 */
	public function getIntegralByAnchorLevel( $level )
	{
		if( empty( $level ) )
		{
			return false;
		}
		$res = $this->_db->field( 'integral' )->where( 'level=' . $level )->limit( 1 )->select( 'anchorlevel' );
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
	 * 获取主播等级信息
	 *
	 * @return array array(
	 * 'level' => 0, //等级
	 * 'integral' => 0 //经验值
	 * );
	 */
	private function _getNowAnchorLevelInfo()
	{
		$res = $this->_db->field( 'level,integral' )->where( 'uid=' . $this->uid )->limit( 1 )->select( 'anchor' );
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
	 * 获取主播等级列表信息
	 *
	 * @return array array(1=>10)  key为等级 值为经验值
	 */
	public function getAnchorLevelInfoList()
	{
		$levelList = array();
		$res = $this->_db->select( 'anchorlevel' );
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
	 * 获取主播等级｜经验值
	 *
	 * @return array  array(
	 * 'level' => '',//等级
	 * 'integral' => ''//经验值
	 * )
	 */
	public function getAnchorLevel()
	{
		return $this->_getNowAnchorLevelInfo();
	}

	/**
	 * 是否可以发直播
	 *
	 * @param int    $uid 主播id
	 * @param object $db
	 *
	 * @return bool
	 */
	public static function isSendLive( $uid, $db )
	{
		return self::isAnchor( $uid, $db );
	}

	/**
	 * 是否是主播
	 *
	 * @param int    $uid 用户uid
	 * @param object $db
	 *
	 * @return bool  true:是主播 false:不是主播
	 */
	public static function isAnchor( $uid, $db )

	{
		$isBlack = self::isBlack( $uid, $db );
		if( $isBlack )
		{
			return -5030;//黑名单主播
		}
		else
		{
			if( !RN_MODEL )
			{
				$res = $db->field( 'uid' )->where( 'uid=' . $uid )->limit( 1 )->select( 'anchor' );
				if( $res )
				{
					return true;
				}
				else
				{
					return -5017;
				}
			}
			else
			{
				$isAnchor = $db->field( 'uid' )->where( 'uid=' . $uid )->limit( 1 )->select( 'anchor' );
				$isRealAnchor = self::isRealAnchor( $uid, $db );
				if( $isAnchor && ( $isRealAnchor == true ) )
				{
					return true;
				}
				else
				{
					return -5028;
				}
			}
		}

	}

	/**
	 * 是否已通过实名审核
	 *
	 * @param int    $uid 用户uid
	 * @param object $db
	 *
	 * @return bool true:已实名审核  false:未实名审核
	 */
	public static function isRealAnchor( $uid, $db )
	{
		$res = $db->field( 'status' )->where( 'uid=' . $uid )->limit( 1 )->select( 'userrealname' );
		if( isset( $res[0]['status'] ) && ( $res[0]['status'] == RN_PASS ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 是否是黑名单主播
	 *
	 * @param int    $uid 主播uid
	 * @param object $db
	 *
	 * @return bool true:是黑名单主播  false:不是黑名单主播
	 */
	public static function isBlack( $uid, $db )
	{
		$res = $db->field( 'luid' )->where( 'luid=' . $uid )->limit( 1 )->select( 'anchor_blackList' );
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
	 * 批量检测主播是否在直播
	 *
	 * @param array  $luids 主播id  多个如 array(34,33);
	 * @param object $db
	 */
	public static function checkAuthoerIsLive( $uids, $db )
	{
		$list = array();
		if( empty( $uids ) || !is_array( $uids ) )
		{
			return false;
		}
		$s = implode( ',', $uids );
		$res = $db->field( 'uid,status' )->where( 'status=' . LIVE . ' ' . '  and uid in (' . $s . ')' )->select( 'live' );
		if( $res )
		{
			foreach ( $res as $v )
			{
				array_push( $list, $v['uid'] );
			}
		}
		return $list;
	}

	/**
	 * 获取房间号
	 *
	 * @return bool|mixed  返回房间号
	 */
	public function getRoomID()
	{
		$res = $this->getRoomIDs( array( $this->uid ), $this->_db );
		if( false !== $res )
		{
			if($res){
				return $res[$this->uid];
			}else{
				return 0;
			}

		}
		else
		{
			return false;
		}
	}

	/**
	 * 批量获取房间id
	 *
	 * @param array  $luidsArray 主播id列表 多个的话如;array(1890,2389)
	 * @param object $db
	 *
	 * @return array|bool
	 */
	public static function getRoomIDs( $luidsArray, $db )
	{
		$list = array();
		if( empty( $luidsArray ) || !is_array( $luidsArray ) )
		{
			return false;
		}
		$luids = implode( ',', $luidsArray );
		$res = $db->field( 'uid,roomid' )->where( "uid  in ($luids)" )->select( 'roomid' );
		if( $res )
		{
			foreach ( $res as $v )
			{
				$list[$v['uid']] = $v['roomid'];
			}
		}
		return $list;
	}

	/**
	 * 获取关注主播的用户数
	 *
	 * @return int  返回粉丝数
	 */
	public function getFollowNumber()
	{
		$res = $this->_db->field( 'count(*) as  total' )->where( "uid2=$this->uid" )->select( 'userfollow' );
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
	 *批量获取关注主播的用户数
	 *
	 * @param array  $luidsArray 主播id  多个用数组 如:array(2789,3541)
	 * @param object $db
	 *
	 * @return array array('3'=>'15')  key主播id,value粉丝数
	 */
	public static function getMornAnchorFans( $luidsArray, $db )
	{
		$list = array();
		if( empty( $luidsArray ) || !is_array( $luidsArray ) )
		{
			return false;
		}
		$uids = implode( ',', $luidsArray );
		$res = $db->field( 'uid2,count(uid1) as total' )->where( "uid2 in ($uids)   group  by uid2" )->select( 'userfollow' );
		if( $res )
		{
			foreach ( $res as $v )
			{
				$list[$v['uid2']] = $v['total'];
			}
		}
		return $list;
	}

	/**
	 * 判断用户是否是房管
	 *
	 * @param int $uid 用户id
	 *
	 * @return bool  是 返回 true   不是 返回false
	 */
	public function isRoomManager( $uid )

	{
		if( empty( $uid ) )
		{
			return false;
		}
		$res = $this->_db->field( 'uid' )->where( " luid = $this->uid and uid = $uid" )->select( 'roommanager' );
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
	 * 获取房管列表
	 *
	 * @param $page
	 * @param $size
	 *
	 * @return array array(3,90,16,7887) 返回房管uid数组
	 */
	public function roomManagerList( $page, $size )
	{
		$list = array();
		$res = $this->_db->field( 'uid' )->where( "luid=$this->uid" )->order( 'ctime desc' )->limit( $page, $size )->select( 'roommanager' );
		if( $res )
		{
			foreach ( $res as $v )
			{
				array_push( $list, $v['uid'] );
			}
		}
		return $list;
	}

	/**
	 * 添加房管
	 *
	 * @param int $adminId 待添加用户id
	 *
	 * @return bool  添加成功返回 true  失败返回false
	 */
	public function addRoomManager( $adminId )
	{
		if( empty( $adminId ) )
		{
			return false;
		}
		$checkIsExist = $this->_db->where( "luid=$this->uid and uid = $adminId" )->select( 'roommanager' );
		if( !empty( $checkIsExist ) )
		{
			return true;
		}
		else
		{
			$data = array(
				'luid' => $this->uid,
				'uid' => $adminId,
				'ctime' => date( 'Y-m-d H:i:s' )
			);
			$res = $this->_db->insert( 'roommanager', $data );
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

	/**
	 * 取消房管
	 *
	 * @param array $uid 房管id 支持批处理 批量的话传参数如 array(2398,459);
	 *
	 * @return bool 取消成功返回 true  失败返回false
	 */
	public function removeRoomManager( $uidArray )
	{
		if( empty( $uidArray ) || !is_array( $uidArray ) )
		{
			return false;
		}
		$adminids = implode( ',', $uidArray );
		$res = $this->_db->where( "luid=$this->uid and uid in ($adminids)" )->delete( 'roommanager' );
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
	 * 主播欢朋币收益纪录(礼单)
	 *
	 * @param string $from  开始时间 如 '2017-03-01 00:00:00'
	 * @param string $to    截至时间 如 '2017-03-31 23:59:59'
	 * @param int    $size  每页数量
	 * @param int    $page  页码
	 * @param int    $month ; 月份
	 *
	 * @return array 二维数组 array(
	 * array(
	 *     'luid'=>2780,//收礼人id
	 *     'liveid'=>9527,//直播id
	 *     'uid'=>90,//送礼人id
	 *     'giftid'=>31,//礼物id
	 *     'giftnum'=>'',//欢朋特饮
	 *     'ctime'=>''//赠送时间
	 * ));
	 */
	public function getReceiveCoinRecord( $from, $to, $page, $size, $month )
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
		$res = $this->_db->where( "luid = $this->uid and otid !=0 and ctime BETWEEN '$from' and '$to'" )->order( 'ctime DESC' )->limit( $page, $size )->select( $this->initTable( Gift::SEND_TYPE_COIN, $month ) );
		if( $res )
		{
			return array( 'coin' => $res, 'total' => self::_getCoinSendNumber( $from, $to, $month ) );
		}
		else
		{
			return array( 'coin' => array(), 'total' => 0 );
		}
	}


	/**
	 * 主播欢朋豆收益纪录(礼单)
	 *
	 * @param string $from  开始时间 如 '2017-03-01 00:00:00'
	 * @param string $to    截至时间 如 '2017-03-31 23:59:59'
	 * @param int    $size  每页数量
	 * @param int    $page  页码
	 * @param int    $month ; 月份
	 *
	 * @return array 二维数组 array(
	 * array(
	 *     'luid'=>2780,//收礼人id
	 *     'liveid'=>9527,//直播id
	 *     'uid'=>90,//送礼人id
	 *     'giftid'=>31,//礼物id
	 *     'giftnum'=>'',//欢朋豆
	 *     'ctime'=>''//赠送时间
	 * ));
	 */
	public function getReceiveBeanRecord( $from, $to, $page, $size, $month )
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
		$res = $this->_db->where( "luid = $this->uid and otid !=0 and ctime BETWEEN '$from' and '$to'" )->order( 'ctime DESC' )->limit( $page, $size )->select( $this->initTable( Gift::SEND_TYPE_BEAN, $month ) );
		if( $res )
		{
			return array( 'bean' => $res, 'total' => self::_getBeanSendNumber( $from, $to, $month ) );
		}
		else
		{
			return array( 'bean' => array(), 'total' => 0 );
		}
	}

	/**
	 * 获取主播获得欢朋豆消费总记录数量
	 *
	 * @param string $from  开始时间 如:'2017-03-01 00:00:00'
	 * @param string $to    结束时间 如:'2017-03-31 23:59:59'
	 * @param int    $month ; 月份
	 *
	 * @return int
	 */
	private function _getBeanSendNumber( $from, $to, $month )
	{
		$res = $this->_db->field( 'count(*) as  total' )->where( "luid = $this->uid and otid !=0 and ctime between '$from' and '$to'" )->select( $this->initTable( Gift::SEND_TYPE_BEAN, $month ) );
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
	 * 获取主播获得欢朋币消费总记录数量
	 *
	 * @param string $from  开始时间 如:'2017-03-01 00:00:00'
	 * @param string $to    结束时间 如:'2017-03-31 23:59:59'
	 * @param int    $month ; 月份
	 *
	 * @return int
	 */
	private function _getCoinSendNumber( $from, $to, $month )
	{
		$res = $this->_db->field( 'count(*) as  total' )->where( "luid = $this->uid and otid !=0 and ctime between '$from' and '$to'" )->select( $this->initTable( Gift::SEND_TYPE_COIN, $month ) );
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
	 * 通过房间号获取主播ID
	 *
	 * @param $rid
	 * @param $db
	 *
	 * @return array | false
	 */
	function getLuidByRid( $rid )
	{
		if( !$rid )
		{
			return false;
		}

		$res = $this->_db->where( "roomid = $rid" )->select( 'roomid' );
		if( $res )
		{
			return (int)$res[0]['uid'];
		}
		else
		{
			return false;
		}
	}

	/**
	 * 获取最近直播的五场游戏名称
	 *
	 * @param type $db
	 *
	 * @return type
	 */
	public function getHistoryGameName()
	{
		$list = array();
		$res = $this->_db->field( 'gamename' )->where( "uid=$this->uid  group by gamename" )->order( 'ctime DESC' )->limit( 5 )->select( 'live' );
		if( $res !== false )
		{
			foreach ( $res as $v )
			{
				array_push( $list, $v['gamename'] );
			}
		}
		return $list;
	}


	/**
	 * 获取某场直播的金豆收益
	 *
	 * @param int $liveid 直播id
	 *
	 * @return int 金豆数
	 */
	public function getBeanIncomeForLive( $liveid )
	{
		//TODO
	}


	/**
	 * 今日获取金豆数
	 *
	 * @return float  返回金豆数
	 */
	public function getTodayBeanIncome()
	{
		//TODO
	}

	/**
	 * 本月获取金币数  这个接口需要调用财务类的相关接口
	 *
	 * @return float 金币数
	 */
	public function getMonthCoinIncome()
	{
		//TODO
	}

	/**
	 * 本月获取金豆数
	 *
	 * @return float  返回金豆数
	 */
	public function getMonthBeanIncome()
	{
		//TODO
	}

	/**
	 * 结束直播显示数据
	 * @return mixed
	 */
	public function infoForEndLive()
	{
		$userInfo = $this->getUserInfo();
		$list['nick'] = $userInfo['nick'];
		$list['head'] = $userInfo['pic'];
		$list['fansCount'] = $this->getFollowNumber();
		$levelInfo = $this->_getNowAnchorLevelInfo();
		$list['level'] = $levelInfo['level'];
		// $getLimit = getAuchorVideoLimit($uid, $db); //获取发布录数
		//$getpublish = getAnchorAlreadyPublishVideo($uid, $db); //获取已发布的录像数
		//$list['autoFull']=$userInfo['head'];
		$isCertify = $this->getCertifyInfo();
		if( $isCertify['identstatus'] == RN_PASS && $isCertify['phonestatus'] == 1 )
		{
			$list['isCertify'] = 1;
		}
		else
		{
			$list['isCertify'] = 0;
		}
		return $list;
	}

	/**
	 * 获取用户
	 *
	 * @return array|bool
	 */
	public function getAnchorNotice()
	{
		$res = $this->_db->field( 'bulletin,status' )->where( 'luid=' . $this->uid )->limit( 1 )->select( 'livebulletin' );
		if( false !== $res )
		{
			if( $res )
			{
				return array( 'status' => $res[0]['status'], 'message' => $res[0]['bulletin'] );
			}
			else
			{
				return array( 'status' => '-1', 'message' => '' );
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 *  获取最后一场直播id
	 *
	 * @return bool|int
	 */
	public function getLastLiveId()
	{
		$res = $this->_db->field( 'liveid' )->where( 'uid=' . $this->uid )->order( 'ctime DESC' )->limit( 1 )->select( 'live' );
		if( false !== $res )
		{
			if( $res )
			{
				return $res[0]['liveid'];
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

}