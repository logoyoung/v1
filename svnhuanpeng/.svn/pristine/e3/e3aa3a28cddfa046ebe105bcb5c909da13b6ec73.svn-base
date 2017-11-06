<?php
namespace lib;

use \DBHelperi_huanpeng;
use lib\GiftTable;
use \RedisHelp;
use \Task;
use Think\Cache\Driver\Db;

/**
 * 礼物类
 * User: dong
 * Date: 17/3/30
 * Time: 上午9:50
 */
class Gift
{
	const SEND_TYPE_BEAN_GIDS = [ 31 ];
	const SEND_TYPE_COIN_GIDS = [ 32, 33, 34, 35, 36];
	const SEND_TYPE_GLOBAL_NOTIFY_GIDS = [ 35 ];
	const SEND_TYPE_BEAN = 1;
	const SEND_TYPE_COIN = 2;

	const ALL_SITE_NOTIFY_OPEN = 1;

	const TABLE_COIN_GIFT_RECORD = 'giftrecordcoin';
	const TABLE_BEAN_GIFT_RECORD = "giftrecord";
	const TABLE_GIFT_INFO = "gift";


	const ANCHOR_SALARY_RANK = 'anchorSalary'; //主播收入榜前缀
	const ANCHOR_POPULARITY_RANK = 'anchorPopularity';//主播人气榜前缀
	const USER_DEVOTE_RANK = 'userDevote';//观众贡献榜前缀

	static $db;

	public static function getDB()
	{
		if(!static::$db )
		{
			static::$db = new \DBHelperi_huanpeng();
		}

//		return new DBHelperi_huanpeng();

		return static::$db;
	}

	public function __construct( $redis = '' )
	{

		if( $redis )
		{
			$this->redis = $redis;
		}
		else
		{
			$this->redis = new RedisHelp();
		}
	}

	private static function getRedis()
	{
		return new RedisHelp();
	}

	/**
	 *
	 *
	 * @return string
	 */
	public function initRecordTable( $type )
	{
		$gTable = new GiftTable();
		return $gTable->checkTable( $type );//初始化
	}

	/**
	 * 获取礼物详情
	 *
	 * @param int $giftId 礼物id
	 * @param     $db
	 *
	 * @return array|bool
	 */
	public static function getGiftInfo( $giftId, DBHelperi_huanpeng $db )
	{
		if( empty( $giftId ) )
		{
			return false;
		}
		$res = $db->where( "id=$giftId" )->limit( 1 )->select( self::TABLE_GIFT_INFO );
		if( false !== $res && $res )
		{
//			$list['id'] = $res[0]['id'];
//			$list['money'] = $res[0]['money'];
//			$list['giftname'] = $res[0]['giftname'];
//			$list['type'] = $res[0]['type'];
//			$list['exp'] = $res[0]['exp'];

			return $res[0];
		}
		else
		{
			return array();
		}
	}

	public static function getGiftsInfo($giftIds, DBHelperi_huanpeng $db)
	{
		if(empty($giftIds))
		{
			return [];
		}

		$giftIds = implode(",", $giftIds);

		$res = $db->where("id in ($giftIds)")->select(self::TABLE_GIFT_INFO);

		$result = [];

		if(is_array($res))
		{
			foreach ($res as $giftInfo)
			{
				$result[$giftInfo['id']] = $giftInfo;
			}
		}

		return $result;
	}

	/**
	 * 添加送礼纪录
	 *
	 * @param int $uid     用户id
	 * @param int $luid    主播id
	 * @param int $liveid  直播id
	 * @param int $giftid  礼物id
	 * @param int $giftnum 礼物数量
	 * @param int $type    类型 1免费礼物 2 收费礼物
	 * @param     $db
	 *
	 * @return bool
	 */
	public static function addGiftRecord( &$id, $uid, $luid, $liveid = 0, $giftid, $giftnum, $type, \DBHelperi_huanpeng $db )
	{
		if( empty( $uid ) || empty( $luid ) || empty( $giftid ) || empty( $giftnum ) || !in_array( $type, array( self::SEND_TYPE_BEAN, self::SEND_TYPE_COIN ) ) )
		{
			return false;
		}
		$data = array(
			'uid' => $uid,
			'luid' => $luid,
			'liveid' => $liveid,
			'giftid' => $giftid,
			'giftnum' => $giftnum
		);

		if( $type == self::SEND_TYPE_BEAN )
		{
			$data['id'] = date( 'YmdHis' ) . rand( 1000, 9999 );//生成单号
			$sql = $db->insert( self::initRecordTable( $type ), $data, true );
			write_log('Bean:'.$sql,'gift_log');
			$res = $db->query( $sql );
			$id = $data['id'] ;

		}
		else
		{
			$data['id'] = date( 'YmdHis' ) . rand( 1000, 9999 );//生成单号
			$sql = $db->insert( self::initRecordTable( $type ), $data, true );
			write_log('coin:'.$sql,'gift_log');
			$res = $db->query( $sql );
			$id = $data['id'] ;
		}
		write_log('res:'.$res,'gift_log');
		if( false != $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * @param        int         $id      记录id
	 * @param        string      $type    类型
	 * @param        int         $otid    交易id
	 * @param        string      $ctime   时间
	 * @param        float       $cost  消费金额
	 * @param        float       $income  收益金额
	 * @param        int         $uid  送礼人ID
	 * @param DBHelperi_huanpeng $db
	 *
	 * @return bool|int
	 */
	public static function sendGiftSuccessCallBack( $id, $type, $otid, $ctime, $cost=0,$income=0,$uid, \DBHelperi_huanpeng $db )
	{
		$data = [
			'otid' => $otid,
			'ctime' => $ctime,
			'cost' => $cost,
			'income' => $income
		];
		$where = [
			'id' => $id
		];
		if( $type == self::SEND_TYPE_BEAN )
		{
			$sql = $db->where( $where )->update( self::initRecordTable( $type ), $data, true );
		    $taskObj=new \lib\Task($uid); //同步任务
			$checkIsFinish=$taskObj->_isTaskFinish( \lib\Task::TASK_SEND_HPBEAN); //这个已完成的标志，以后优化放redis里面
			if(!$checkIsFinish){
				\lib\Task::synchroTask($uid,\lib\Task::TASK_SEND_HPBEAN,$db);
			}
		}
		elseif( $type == self::SEND_TYPE_COIN )
		{
			$sql = $db->where( $where )->update( self::initRecordTable( $type ), $data, true );
		}

		self::_log( __FUNCTION__ . "  " . $sql );

		if( $db->query( $sql ) )
		{
			return $db->affectedRows;
		}
		else
		{
			return false;
		}


	}

	/**
	 * @param int $uid       送礼人id
	 * @param int $luid      收益人id
	 * @param int $type      礼物类型
	 * @param int $sendmoney 送出
	 * @param int $getmoney  收益
	 *
	 * @return bool
	 */
	public static function askRedisIfSendGiftSuccess( $uid, $luid, $type, $sendmoney, $getmoney )
	{
		if( empty( $uid ) || empty( $luid ) )
		{
			return false;
		}
		self::_getGiftSalaryToRedis( $uid, $luid, $type, $sendmoney, $getmoney );
	}


	/**礼物＝>经验值
	 *
	 * @param int $sendType 礼物类型
	 * @param int $sendNum  礼物数量
	 * @param     $money    价格
	 * @param     $exp      经验
	 *
	 * @return float|int
	 */
	public static function getSendExp( $sendType, $sendNum, $money, $exp )
	{
		if( $sendType == self::SEND_TYPE_BEAN )
		{
			return $sendNum / $money * $exp;
		}
		elseif( $sendType == self::SEND_TYPE_COIN )
		{
			return $sendNum * $exp;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * 获取礼物列表
	 *
	 * @param object $db 数据库对象
	 *
	 * @return array|bool
	 */
	public static function getGiftList( $db )
	{
		$res = $db->select( "gift" );
		if( false !== $res )
		{
			$list = array();
			foreach ( $res as $v )
			{
				$list[$v['id']] = $v;
			}

			return $list;
		}
		else
		{
			return false;
		}
	}


	/**送礼回调回来写redis里面,用于主播收入排行榜
	 *
	 * @param int $luid   主播id
	 * @param int $salary 收入
	 */
	private function _setAnchorSalaryToRedis( $luid, $salary )
	{
		$redis = self::getRedis();
		$strKey = self::ANCHOR_SALARY_RANK . date( 'Y-m-d' );
		$redis->zincrby( $strKey, $salary, $luid );
	}

	/**送礼回调回来写redis里面,用于观众贡献排行榜
	 *
	 * @param int $uid  用户id
	 * @param int $cost 消费
	 */
	private function _setUserDevoteToRedis( $uid, $cost )
	{
		$redis = self::getRedis();
		$strKey = self::USER_DEVOTE_RANK . date( 'Y-m-d' );
		$redis->zincrby( $strKey, $cost, $uid );
	}

	/**送礼回调回来写redis里面,用于主播人气排行榜
	 *
	 * @param int $luid 主播id
	 * @param int $cost 收入
	 */
	private function _setAnchorPopularityToRedis( $luid, $salary )
	{
		$redis = self::getRedis();
		$strKey = self::ANCHOR_POPULARITY_RANK . date( 'Y-m-d' );
		$redis->zincrby( $strKey, $salary, $luid );
	}

	/**
	 * 送完礼物将记录写到redis里面
	 *
	 * @param int    $uid   用户id
	 * @param int    $luid  主播id
	 * @param int    $money 收益｜送出总金额
	 * @param string $type  类型 币｜豆
	 */
	private function _getGiftSalaryToRedis( $uid, $luid, $type, $sendmoney, $getmoney )
	{
		if( $type == self::SEND_TYPE_BEAN )
		{
			self::_setAnchorPopularityToRedis( $luid, $getmoney );//主播人气
		}
		if( $type == self::SEND_TYPE_COIN )
		{
			self::_setAnchorSalaryToRedis( $luid, $getmoney );//主播收入
			self::_setUserDevoteToRedis( $uid, $sendmoney );//用户贡献
		}

	}

	/**
	 * 计算礼物收益
	 *
	 * @param int    $giftid  礼物id
	 * @param int    $giftnmu 礼物数量
	 * @param string $type    类型
	 */
	public static function _getGiftTotal( $giftid, $giftnum, $type )
	{
		$salary = 0;
		if( $type == self::SEND_TYPE_BEAN )
		{
			$salary = $giftnum;
		}
		if( $type == self::SEND_TYPE_COIN )
		{
			$giftInfo = self::getGiftInfo( $giftid, self::getDB() );
			if( $giftInfo )
			{
				$salary = $giftnum * $giftInfo['money'];
			}
		}
		return $salary;
	}


	/**
	 * 根据id获取giftrecord或者giftrecordcoin表中的一条数据
	 *
	 * @param int    $id   送礼记录id
	 * @param string $type 类型
	 *
	 * @return array|bool
	 */
	private function _getGiftRecordById( $id, $type )
	{
		$detail = array();
		if( empty( $id ) )
		{
			return false;
		}
		if( $type == self::SEND_TYPE_BEAN )
		{
			$res = self::getDB()->field( 'luid,uid,giftid,giftnum' )->where( "id=" . $id )->limit( 1 )->select( self::TABLE_BEAN_GIFT_RECORD );
		}
		elseif( $type == self::SEND_TYPE_COIN )
		{
			$res = self::getDB()->field( 'luid,uid,giftid,giftnum' )->where( "id=" . $id )->limit( 1 )->select( self::TABLE_COIN_GIFT_RECORD );
		}
		else
		{
			$res = array();
		}
		if( $res )
		{
			foreach ( $res as $v )
			{
				$detail['luid'] = $v['luid'];
				$detail['uid'] = $v['uid'];
				$detail['giftid'] = $v['giftid'];
				$detail['giftnum'] = $v['giftnum'];
			}
		}
		return $detail;
	}

	private function _log( $msg )
	{
		$dir = LOG_DIR . __CLASS__ . ".log";
		mylog( $msg, $dir );
	}

}