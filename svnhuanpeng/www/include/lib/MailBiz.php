<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/4/19
 * Time: 17:20
 */

namespace lib;


/**
 * Class MailBiz
 *
 * @package hp\lib
 */
class MailBiz
{
	/**
	 *
	 */
	const SEND_RECORD_TABLE = 'send_email_record';
	/**
	 *
	 */
	const STATUS_SEND = 0;
	/**
	 *
	 */
	const STATUS_BACK = 1;
	/**
	 *
	 */
	const TIMEOUT_LIMIT = 86400;
	/**
	 *
	 */
	const SEND_TIMER_LIMIT = 3;
	/**
	 *
	 */
	const URL_GET_FAILED_ERRORNO = -4070;
	/**
	 *
	 */
	const SEND_SUCCESS_STATUS = 1;

	/**
	 *
	 */
	const SEND_MAIL_TIMER_REDIS_KEY = 'sendMailTimer:';

	/**
	 *
	 */
	const SEND_MAIL_REDIS_KEY = 'sendMailRedis:';

	/**
	 * @var \DBHelperi_huanpeng|null
	 */
	private $_db = null;
	/**
	 * @var null|\RedisHelp
	 */
	private $_redis = null;
	/**
	 * @var MailSend|null
	 */
	private $_mail = null;

	/**
	 * MailBiz constructor.
	 *
	 * @param \DBHelperi_huanpeng|null $db
	 * @param \RedisHelp|null          $redis
	 */
	public function __construct( \DBHelperi_huanpeng $db = null, \RedisHelp $redis = null )
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
			$this->_redis = new \RedisHelp();
		}

		$this->_mail = new MailSend();
	}

	/**
	 * @param string $type
	 * @param string $email
	 * @param int    $limit
	 *
	 * @return bool
	 */
	public function canSendMsg( string $type, string $email, $limit = MailBiz::SEND_TIMER_LIMIT )
	{
		$timer = $this->_getSendTimer( $type, $email );

		if( $timer > $limit )
		{
			return false;
		}

		return true;
	}

	/**
	 * @param array $content
	 * @param int   $timeOut
	 *
	 * @return bool
	 */
	public function sendMsg($uid, array $content, $timeOut = MailBiz::TIMEOUT_LIMIT )
	{
		$type  = $content['type'];
		$email = $content['email'];


		$sendTimer = $this->_getSendTimer( $type, $email );

		$sendID = $this->_createSendRecord( $type, $email, $uid );
		if( !$sendID )
		{
			return false;
		}

		$ret = $this->_mail->sendMsg( $content );

		$this->_log($ret);

		if( $ret && $ret = json_decode($ret,true))
		{
//			$ret = json_decode( $ret, true );

			$this->_setRecordError( $sendID, $ret['resuNo'], self::STATUS_SEND );

			if( $ret['resuNo'] == 1 )
			{
				$sendTimer++;

				$this->_setSendTimer( $type, $email, $sendTimer );
				$this->_setSendIDCache( $type, $email, $sendID, (int)$timeOut );

				return true;
			}
			else
			{
				return false;
			}
		}

		$this->_setRecordError( $sendID, self::URL_GET_FAILED_ERRORNO, self::STATUS_SEND );

		return false;
	}

	/**
	 * @param string $type
	 * @param string $email
	 */
	public function sendMsgCallBack( string $type, string $email )
	{
		$sendID = $this->_getSendIDByCache( $type, $email );
		if( $sendID )
		{
			$this->_setRecordError( $sendID, self::SEND_SUCCESS_STATUS, self::STATUS_BACK );
			$info  = $this->_getRecordInfo( $sendID );
			$email = $info['email'];
			$type  = $info['type'];

			$this->_deleteSendIDCache( $type, $email );
		}

	}

	/**
	 *
	 *
	 * @param $type
	 * @param $email
	 *
	 * @return bool|int|string
	 */
	private function _getSendIDByCache( string $type, string $email )
	{
		$redisKey = $this->_getSendMsgRedisKey( $type, $email );

		return $this->_redis->get( $redisKey );
	}

	/**
	 * @param string $type
	 * @param string $email
	 * @param string $sendID
	 * @param int    $time
	 */
	private function _setSendIDCache( string $type, string $email, string $sendID, int $time = MailBiz::TIMEOUT_LIMIT )
	{
		$redisKey = $this->_getSendMsgRedisKey( $type, $email );

		$this->_redis->set( $redisKey, $sendID, $time );
	}

	/**
	 * @param string $type
	 * @param string $email
	 */
	private function _deleteSendIDCache( string $type, string $email )
	{
		$redisKey = $this->_getSendMsgRedisKey( $type, $email );

		$this->_redis->del( $redisKey );
	}

	/**
	 * @param string $type
	 * @param string $email
	 *
	 * @return string
	 */
	private function _getSendMsgRedisKey( string $type, string $email )
	{
		return self::SEND_MAIL_REDIS_KEY . $type . ":" . $email;
	}

	/**
	 * @param int $sendID
	 *
	 * @return bool
	 */
	private function _getRecordInfo( int $sendID )
	{
		$field = [ 'id', 'eamil', 'type', 'ctime' ];
		$where = [ 'id' => $sendID ];
		$sql   = $this->_db->field( $field )->where( $where )->select( self::SEND_RECORD_TABLE, true );

		$res = $this->_db->query( $sql );
		if( !$res )
		{
			//TODO LOG
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
	 * @param string $type
	 * @param string $email
	 * @param string $businessID
	 *
	 * @return bool|int
	 */
	private function _createSendRecord( string $type, string $email, string $businessID )
	{
		$port = '';
		$ip   = ip2long( fetch_real_ip( $port ) );

		$data = [
			'type'       => $type,
			'email'      => $email,
			'businessid' => $businessID,
			'ip'         => $ip,
			'port'       => $port
		];

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

	/**
	 * @param int $sendID
	 * @param int $retNo
	 * @param int $status
	 */
	private function _setRecordError( int $sendID, int $retNo, int $status )
	{
		$data  = [
			'status'   => $status,
			'errorNo' => $retNo,
			'etime'    => date( 'Y-m-d H:i:s' )
		];
		$where = [ 'id' => $sendID ];
		$this->_db->where( $where )->update( self::SEND_RECORD_TABLE, $data );
	}

	/**
	 * @param string $type
	 * @param string $email
	 *
	 * @return bool|int|string
	 */
	private function _getSendTimer( string $type, string $email )
	{
		$redisKey = $this->_getSendTimerRedisKey( $type, $email );
		$timer    = $this->_redis->get( $redisKey );

		if( !$timer )
		{
			$stime = date( "Y-m-d" ) . " 00:00:00";
			$etime = date( "Y-m-d" ) . " 23:59:59";
			$sql   = "select count(*)  as count from " . self::SEND_RECORD_TABLE . " where type='$type' and email='$email' and ctime between '$stime' and '$etime' and errorNo=" . self::SEND_SUCCESS_STATUS;
			$res   = $this->_db->query($sql);
			if( !$res )
			{
				//logs
				return false;
			}

			$row   = $res->fetch_assoc();
			$timer = (int)$row['count'];

			$this->_setSendTimer( $type, $email, $timer );
		}

		return $timer;
	}

	/**
	 * @param string $type
	 * @param string $email
	 * @param int    $timer
	 */
	private function _setSendTimer( string $type, string $email, int $timer )
	{
		$redisKey = $this->_getSendTimerRedisKey( $type, $email );
		$expire   = $this->_getSendTimerExpire();

		$this->_redis->set( $redisKey, $timer, $expire );
	}

	/**
	 * @return false|int
	 */
	private function _getSendTimerExpire()
	{
		return strtotime( date( "Y-m-d" . " 23:59:59" ) ) - time();
	}

	/**
	 * @param string $type
	 * @param string $email
	 *
	 * @return string
	 */
	private function _getSendTimerRedisKey( string $type, string $email )
	{
		return self::SEND_MAIL_TIMER_REDIS_KEY . $type . ":" . $email;
	}

	private function _log($msg)
	{
		$dir = LOG_DIR . __CLASS__ . ".log";
		mylog( $msg, $dir );
	}
}