<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/4/25
 * Time: 19:49
 */

namespace lib;

use lib\ApplePush;
use lib\MsgPackage;
use lib\SocketSend;

class LivePush
{

	const ADD_TO_QUEUE_TIME_LIMIT = 300;

	const TABLE_PUSH_MSG_LIST = "live_pushmsg_list";

	const TABLE_LIVE_NOTICE_LIST = "live_notice";

	const TABLE_USER_ACTIVE = 'useractive';

	const TABLE_IOS_PUSH_NOTIFY = 'push_notify_set';

	const PUSH_MSG_STATUS_FINISHED = 2;

	const PUSH_MSG_STATUS_RUNING = 1;

	const PUSH_MSG_STATUS_CREATE = 0;

	const IOS_PUSH_STATUS_OPEN = 1;

	const IOS_PUSH_STATUS_CLOSED = 0;

	private $_db;
	private $_redis;
	private $_tableMap;

	public function __construct( \DBHelperi_huanpeng $db = null, \RedisHelp $redisHelp = null )
	{
		if ( $db )
		{
			$this->_db = $db;
		}
		else
		{
			$this->_db = new \DBHelperi_huanpeng();
		}

		if ( $redisHelp )
		{
			$this->_redis = $redisHelp;
		}
		else
		{
			$this->_redis = new \RedisHelp();
		}

		$this->_tableMap['list']   = self::TABLE_PUSH_MSG_LIST;
		$this->_tableMap['notice'] = self::TABLE_LIVE_NOTICE_LIST;
		$this->_tableMap['user']   = self::TABLE_USER_ACTIVE;

		$this->_tableMap['ios_push'] = self::TABLE_IOS_PUSH_NOTIFY;
		$this->_tableMap['liveroom'] = LiveRoom::TABLE_LIVE_ROOM;

		$this->_applePushObj = new ApplePush();

	}

	public function add( int $liveid, int $luid, array $info )
	{
		$this->_log( "what's the fuck error give me " );
//		$this->_log(__FUNCTION__."$liveid,$luid,".json_encode($info));

		if ( $this->_added( $liveid ) )
		{
			$insertData = [
				'liveid' => $liveid,
				'luid'   => $luid,
				'info'   => json_encode( $info )
			];

			$sql = $this->_db->insert( $this->_tableMap['list'], $insertData, true );

			if ( !$this->_db->query( $sql ) )
			{
				$t = "QueryError @ " . __CLASS__ . "::" . __FUNCTION__ . "()[{$this->_db->errno()}][{$this->_db->errstr()}][$sql]";
				$this->_log( $t );

				return false;
			}

			return true;
		}

		return true;
	}

	private function _added( $liveid )
	{
		$sql = "select id,stime,status from {$this->_tableMap['list']} where liveid=$liveid ORDER  BY id desc limit 1";
		$res = $this->_db->query( $sql );
		if ( !$res )
		{
			$t = "QueryError @ " . __CLASS__ . "::" . __FUNCTION__ . "()[{$this->_db->errno()}][{$this->_db->errstr()}][$sql]";
			$this->_log( $t );

			return false;
		}

		$row    = $res->fetch_assoc();
		$id     = $row['id'];
		$status = $row['status'];
		$stime  = $row['stime'];

		if ( !$id )
		{
			return true;
		}

		if ( $status != self::PUSH_MSG_STATUS_FINISHED )
		{
			return false;
		}

		$timeLimit = $GLOBALS['env'] == "PRO" ? self::ADD_TO_QUEUE_TIME_LIMIT : 0;

		if ( $stime != '0000-00-00 00:00:00' && time() - strtotime( $stime ) > $timeLimit)
		{
			return true;
		}

		return false;
	}

	public function push()
	{
		$info = $this->_getTask();
		if ( $info )
		{
			$liveIDList = array_keys( $info );
			$this->_log( "push live id list" . json_encode( $liveIDList ) );

			if ( $this->_lockTask( $liveIDList ) )
			{
				foreach ( $liveIDList as $liveid )
				{
					$luid     = $info[$liveid]['luid'];
					$pushList = $this->_getPushUidList( $luid );

					$applePushList  = $this->_getApplePushList( $pushList );
					$socketPushList = $this->_getSocketPushLst( $pushList );
					$this->_log( "apple push list" . json_encode( $applePushList ) );

					foreach ( $socketPushList as $uid )
					{
						$detail = json_decode( $info[$liveid]['info'], true );
//						$this->_log(jsone($detail));
						$msg = MsgPackage::getLiveStartNoticeMsgSocketPackage( $luid, $uid, $detail['nick'], $detail['pic'] );
						$this->_log( json_encode( $msg ) );
						SocketSend::sendMsg( $msg, $this->_db );
					}

					foreach ( $applePushList as $value )
					{
						$detail  = json_decode( $info[$liveid]['info'], true );
						$mid     = $luid . "-" . $value['uid'] . "-" . time();
						$custome = [
							'type' => 1,
							'data' => [
								'luid' => $luid,
								'nick' => $detail['nick'],
								'pic'  => $detail['pic'],
								'title' =>"开播提醒"
							]
						];

						$msg = MsgPackage::getLiveStartApplePushMsgPackage( $value['deviceToken'], $detail['nick'], $mid, $custome );
						$ret = $this->_applePushObj->send( $msg );

						if($ret != "+OK")
						{
							$this->_log( json_encode( $msg ) );
							$this->_log("apple push anchor:$luid send to {$value['uid']} failed $ret");
						}

					}

					$this->_finishTask( $liveid );
				}
			}
		}
	}


	private function _getTask()
	{
		$list = array();
		$sql  = "select * from " . $this->_tableMap['list'] . " where status=" . self::PUSH_MSG_STATUS_CREATE . " group by liveid";
		$res  = $this->_db->query( $sql );
		if ( !$res )
		{
			//todo log
			return false;
		}

		while ( $row = $res->fetch_assoc() )
		{
			$list[$row['liveid']] = $row;
		}

		return $list;
	}

	private function _lockTask( $liveIDs )
	{
		if ( !$liveIDs )
		{
			return false;
		}

		$utime   = date( "Y-m-d H:i:s" );
		$liveIDs = implode( ',', $liveIDs );

		$sql = "update {$this->_tableMap['list']} set utime='$utime', status=" . self::PUSH_MSG_STATUS_RUNING . " where status=" . self::PUSH_MSG_STATUS_CREATE . " and liveid in ($liveIDs)";
		$res = $this->_db->query( $sql );

		if ( !$res )
		{
			//todo log
			return false;
		}

		return true;
	}

	private function _finishTask( $liveID )
	{
		$stime = date( 'Y-m-d H:i:s' );
		$sql   = "update {$this->_tableMap['list']} set stime='$stime' , utime='$stime',status=" . self::PUSH_MSG_STATUS_FINISHED . " where liveid=$liveID and status=" . self::PUSH_MSG_STATUS_RUNING;

		return $this->_db->query( $sql );
	}

	private function _getPushUidList( $luid )
	{
		//TODO  LIVE_START_NOTICE_RECEIVE  should user class const
		$sql = "select notice.uid as uid from {$this->_tableMap['notice']} as notice, {$this->_tableMap['user']} as `userT` where notice.uid=userT.uid and luid=$luid and userT.isnotice=" . LIVE_START_NOTICE_RECEIVE;

		$res = $this->_db->query( $sql );
		if ( !$res )
		{
			$t = "QueryError @ " . __CLASS__ . "::" . __FUNCTION__ . "()[{$this->_db->errno()}][{$this->_db->errstr()}][$sql]";

			$this->_log( $t );

			return false;
		}

		$uidList = [];

		while ( $row = $res->fetch_assoc() )
		{
			array_push( $uidList, $row['uid'] );
		}

		return $uidList;
	}

	public function getApplePushList($uidList)
	{
		return $this->_getApplePushList($uidList);
	}

	private function _getApplePushList( $uidList )
	{
		if ( !$uidList )
		{
			return [];
		}

		$uidList = implode( ',', $uidList );

		$sql = "select deviceToken,uid from " . $this->_tableMap['ios_push'] . " where uid in ($uidList) and isopen=" . self::IOS_PUSH_STATUS_OPEN;
		$res = $this->_db->query( $sql );
		if ( !$res )
		{
			$t = "QueryError @ " . __CLASS__ . "::" . __FUNCTION__ . "()[{$this->_db->errno()}][{$this->_db->errstr()}][$sql]";

			$this->_log( $t );

			return false;
		}

		$list = array();

		while ( $row = $res->fetch_assoc() )
		{
			array_push( $list, $row );
		}

		return $list;
	}

	private function _getSocketPushLst( $uidList )
	{
		if ( !$uidList )
		{
			return array();
		}

		$uidList = implode( ',', $uidList );
		$luid    = MsgPackage::SOCKET_ROOM_LUID;

		$sql = "select uid from {$this->_tableMap['liveroom']} where luid=$luid and uid in ($uidList) GROUP BY uid";
		$res = $this->_db->query( $sql );
		if ( !$res )
		{
			$t = "QueryError @ " . __CLASS__ . "::" . __FUNCTION__ . "()[{$this->_db->errno()}][{$this->_db->errstr()}][$sql]";

			$this->_log( $t );

			return false;
		}

		$list = array();

		while ( $row = $res->fetch_assoc() )
		{
			array_push( $list, $row['uid'] );
		}

		return $list;
	}

	private function _log( $msg )
	{
		$dir = LOG_DIR . __CLASS__ . ".log";
		mylog( $msg, $dir );
	}
}