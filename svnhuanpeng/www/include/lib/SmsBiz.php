<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/4/19
 * Time: 10:42
 */

namespace lib;


use \DBHelperi_huanpeng;
use \RedisHelp;

class SmsBiz
{
	const SEND_RECORD_TABLE        = 'send_mobileMsg_record';
	const STATUS_SEND              = 0;
	const STATUS_BACK              = 1;
	const TIMEOUT_LIMIT            = 900;
	const SEND_TIMER_LIMIT         = 3;
	const MSG_BALANCE_REDIS_KEY    = 'mobileMsgBalance';
	const URL_GET_FAILED           = -4070;
	const SEND_MSG_REDIS_KEY       = 'sendMSgRedis:';
	const SEND_MSG_TIMER_REDIS_KEY = 'sendMsgTimer:';

	private $_db = null;
	private $_redis = null;
	private $_sms = null;

	public function __construct( \DBHelperi_huanpeng $db=null, \RedisHelp $redis=null )
	{
		if( $db )
		{
			$this->_db = $db;
		}
		else
		{
			$this->_db = new \DBHelperi_huanpeng();
		}

		if( $redis )
		{
			$this->_redis = $redis;
		}
		else
		{
			$this->_redis = new RedisHelp();
		}

		$this->_sms = new Sms();
	}

	public function canSendMsg( $type, $mobile, $limit = SmsBiz::SEND_TIMER_LIMIT )
	{
		$timer = $this->_getSendTimer( $type, $mobile );
		if( $timer > $limit )
		{
			return false;
		}

		return true;
	}

	/**
	 * 发送短信
	 *
	 * @param array $content
	 * @param int   $timeOut
	 *
	 * @return bool
	 */
	public function sendMsg( array $content, $timeOut = SmsBiz::TIMEOUT_LIMIT )
	{
		$type   = $content['type'];
		$mobile = $content['mobile'];
		$code   = $content['code'];

		$sendTimer = $this->_getSendTimer( $type, $mobile );

		$sendID = $this->_createSendID( $type, $mobile, $mobile, $code );
		if( !$sendID )
		{
			return false;
		}

		$content['codeid'] = intval( $sendID );

		$ret = $this->_sms->sendMsg( $content );

		if( $ret )
		{
			$ret = json_decode( $ret );
			$this->_setRecordError( $sendID, $ret['resuNo'], self::STATUS_SEND );

			if( $ret['resuNo'] == 1 )
			{
				$this->_setSendIDCache( $type, $mobile, $sendID, $timeOut );
				$sendTimer++;
				$this->_setSendTimer( $sendTimer );

				return true;
			}
			else
			{
				return false;
			}
		}

		$this->_setRecordError( $sendID, self::URL_GET_FAILED, self::STATUS_SEND );

		return false;
	}

	/**
	 * 短信发送回调
	 *
	 * @param int $type
	 * @param int $mobile
	 */
	public function sendMsgCallBack( int $type, int $mobile )
	{
		$codeid = $this->_getSendIDByCache( $type, $mobile );
		$ret    = $this->_sms->sendMsgCallBack( $codeid );
		if( $ret )
		{
			$ret = json_decode( $ret, true );
			$this->_setRecordError( $codeid, $ret['resuNo'], self::STATUS_BACK );
		}
		else
		{
			$this->_setRecordError( $codeid, self::URL_GET_FAILED, self::STATUS_BACK );
		}
	}

	/**
	 *
	 *
	 * @param      $type
	 * @param      $mobile
	 * @param      $code
	 * @param bool $timeOut
	 * @param bool $autoClear
	 *
	 * @return bool
	 */
	public function checkCodeValid( $type, $mobile, $code, $timeOut = false, $autoClear = true )
	{
		$sendID = $this->_getSendIDByCache( $type, $mobile );
		if( !$sendID )
		{
			return false;
		}

		if( $timeOut === false )
		{
			$timeOut = self::TIMEOUT_LIMIT;
		}

		$info = $this->_getRecordInfo( $sendID );
		if( false === $info || !is_array( $info ) )
		{
			return false;
		}
		$time = time() - strtotime( $info['ctime'] );
		if( $info['mobile'] != $mobile || $info['code'] != $code || $time > $timeOut )
		{
			return false;
		}

		if( $autoClear )
		{
			$this->_deleteSendIDCache( $type, $mobile );
		}

		return true;
	}

	/**
	 * 从缓存中获取发送ID
	 *
	 * @param int $type
	 * @param int $mobile
	 *
	 * @return bool|string
	 */
	private function _getSendIDByCache( int $type, int $mobile )
	{
		$redisKey = $this->_getSendMsgRedisKey( $type, $mobile );

		return $this->_redis->get( $redisKey );
	}

	/**
	 * 设置发送ID缓存
	 *
	 * @param int $type
	 * @param int $mobile
	 * @param int $sendID
	 * @param int $time
	 */
	private function _setSendIDCache( int $type, int $mobile, int $sendID, $time = SmsBiz::TIMEOUT_LIMIT )
	{
		$redisKey = $this->_getSendMsgRedisKey( $type, $mobile );
		$this->_redis->set( $redisKey, $sendID, $time );
	}

	/**
	 * 清除发送ID缓存
	 *
	 * @param int $type
	 * @param int $mobile
	 */
	private function _deleteSendIDCache( int $type, int $mobile )
	{
		$redisKey = $this->_getSendMsgRedisKey( $type, $mobile );
		$this->_redis->del( $redisKey );
	}

	/**
	 * 获取发送记录redis key
	 *
	 * @param int $type
	 * @param int $mobile
	 *
	 * @return string
	 */
	private function _getSendMsgRedisKey( int $type, int $mobile )
	{
		return self::SEND_MSG_REDIS_KEY . $type . ":$mobile";
	}

	/**
	 * 设置记录结果
	 *
	 * @param int $codeid
	 * @param int $retNo
	 * @param int $status
	 */
	private function _setRecordError( int $codeid, int $retNo, int $status )
	{
		$data = array(
			'status'  => $status,
			'errorNo' => $retNo,
			'etime'   => date( 'Y-m-d H:i:s' )
		);

		$this->_db->where( "id=$codeid" )->update( self::SEND_RECORD_TABLE, $data );
	}

	private function _getRecordInfo( $sendID )
	{
		$field = [ 'id', 'code', 'mobile', 'type', 'ctime' ];
		$where = [ 'id' => $sendID ];

		$sql = $this->_db->field( $field )->where( $where )->select( self::SEND_RECORD_TABLE, true );
		$res = $this->_db->query( $sql );
		if( !$res )
		{
			//TODO : log
			return false;
		}

		$row = $res->fetch_assoc();
		if( $row['id'] )
		{
			return $row;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 构建记录ID
	 *
	 * @param int $type
	 * @param int $mobile
	 * @param int $businessID
	 * @param int $code
	 *
	 * @return bool|int
	 */
	private function _createSendID( int $type, int $mobile, int $businessID, int $code = 0 )
	{
		$port = '';
		$ip   = fetch_real_ip( $port );

		$data = array(
			'type'       => $type,
			'mobile'     => $mobile,
			'businessid' => $businessID,
			'code'       => $code,
			'ip'         => $ip,
			'port'       => $port
		);

		$codeid = $this->_db->insert( self::SEND_RECORD_TABLE, $data );
		if( $codeid )
		{
			return $codeid;
		}
		else
		{
			return false;
		}
	}

	private function _getSendTimer( $type, $mobile )
	{
		$redisKey = $this->_getSendTimerRedisKey( $type, $mobile );
		$timer    = $this->_redis->get( $redisKey );
		if( !$timer )
		{
			$stime = date( "Y-m-d" ) . " 00:00:00";
			$etime = date( "Y-m-d" ) . " 23:59:59";
			$sql   = "select count(*)  as count from " . self::tableName . " where type=$type and mobile='$mobile' and ctime between '$stime' and '$etime' and errorNo=" . self::sendSuccStatus;
			$res   = static::$db->query( $sql );
			if( !$res )
			{
				//TODO logs
				return false;
			}

			$row   = $res->fetch_assoc();
			$timer = (int)$row['count'];
			$this->_setSendTimer( $type, $mobile, $timer );
		}

		return $timer;
	}

	private function _setSendTimer( $type, $mobile, $timer )
	{
		$redisKey = $this->_getSendTimerRedisKey( $type, $mobile );
		$expire   = $this->_getSendTimerExpire();
		$this->_redis->set( $redisKey, $timer, $expire );
	}

	private function _getSendTimerExpire()
	{
		return strtotime( date( 'Y-m-d' ) . " 23:59:59" ) - time();
	}

	private function _getSendTimerRedisKey( $type, $mobile )
	{
		return self::SEND_MSG_TIMER_REDIS_KEY . $type . ":$mobile";
	}
}