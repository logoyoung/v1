<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/4/23
 * Time: 13:38
 */

namespace lib;

use \RedisHelp;
use lib\MsgPackage;
use lib\SocketSend;

class RoomRank
{
	const RANK_TYPE_DAY  = 1;
	const RANK_TYPE_WEEK = 5;
	const RANK_TYPE_ALL  = 10;

	const TABLE_RANK_DAY  = 'rank_day';
	const TABLE_RANK_WEEK = 'rank_week';
	const TABLE_RANK_ALL  = 'rank_all';

	const REDIS_KEY_RANK_ALL  = "ROOM_RANK_ALL";
	const REDIS_KEY_RANK_WEEK = "ROOM_RANK_WEEK";
	const REDIS_KEY_RANK_DAY  = "ROOM_RANK_DAY";

	const RANK_UP_TYPE_INTO = 1;
	const RANK_UP_TYPE_UP   = 10;
	const RANK_UP_TYPE_HEAD = 20;

	const RANK_COUNT_LIMIT = 100;

	private $_luid;
	private $_tableMap      = [];
	private $_msgChangeType = [];
	private $_redisKeyMap   = [];

	protected $_redis = null;
	protected $_db    = null;

	private $_debug;

	private $_curChangeType;
	private $_curSendUid;
	private $_curRank;
	private $_preRank;

	public function __construct( int $luid, \DBHelperi_huanpeng $db, \RedisHelp $redis, $debug = false )
	{
		if ( $luid )
		{
			$this->_luid = $luid;
		}
		else
		{
			return false;
		}

		if ( $db )
		{
			$this->_db = $db;
		}
		else
		{
			$this->_db = new \DBHelperi_huanpeng();
		}

		if ( $redis )
		{
			$this->_redis = $redis;
		}
		else
		{
			$this->_redis = new \RedisHelp();
		}

		$this->_debug = $debug;

		if ( $GLOBALS['env'] != "PRO" )
		{
			$this->_debug = true;
		}

		$this->_tableMap[self::RANK_TYPE_ALL]  = self::TABLE_RANK_ALL;
		$this->_tableMap[self::RANK_TYPE_WEEK] = self::TABLE_RANK_WEEK;
		$this->_tableMap[self::RANK_TYPE_DAY]  = self::TABLE_RANK_DAY;

		$this->_msgChangeType[self::RANK_TYPE_DAY]  = MsgPackage::SOCKET_ROOM_RANK_CHANGE_DAY;
		$this->_msgChangeType[self::RANK_TYPE_WEEK] = MsgPackage::SOCKET_ROOM_RANK_CHANGE_WEEK;
		$this->_msgChangeType[self::RANK_TYPE_ALL]  = MsgPackage::SOCKET_ROOM_RANK_CHANGE_ALL;

		$this->_redisKeyMap[self::RANK_TYPE_DAY]  = self::REDIS_KEY_RANK_DAY;
		$this->_redisKeyMap[self::RANK_TYPE_WEEK] = self::REDIS_KEY_RANK_WEEK;
		$this->_redisKeyMap[self::RANK_TYPE_ALL]  = self::REDIS_KEY_RANK_ALL;

	}

	private function _setRankNotice( $type, $uid, $pre, $cur )
	{
		if ( $pre != -1 && $cur >= $pre )
		{
			return false;
		}

		if ( $cur > 10 )
		{
			return false;
		}

		if ( $type < intval( $this->_curChangeType ) )
		{
			return false;
		}

		$this->_curChangeType = $type;
		$this->_curSendUid    = $uid;
		$this->_preRank       = $pre;
		$this->_curRank       = $cur;
	}

	private function _getRankUpType()
	{

		if ( $this->_curRank == 1 )
		{
			return self::RANK_UP_TYPE_HEAD;
		}
		else
		{
			if ( $this->_preRank == -1 )
			{
				return self::RANK_UP_TYPE_INTO;
			}
			else
			{
				return self::RANK_UP_TYPE_UP;
			}
		}
	}

	private function _sendRankUpNotice( $nick, $level )
	{

		$this->_debugLog( "type:{$this->_curChangeType}" );
		$this->_debugLog( "uid:{$this->_curSendUid}" );
		$this->_debugLog( "pre:{$this->_preRank}" );
		$this->_debugLog( "cur:{$this->_curRank}" );

		if ( intval( $this->_curChangeType ) )
		{
			$upType = $this->_getRankUpType();
			$msg    = MsgPackage::getRoomRankUpMsgSocketPackage( $this->_luid, $this->_curSendUid, $this->_curChangeType, $upType, $nick, $level, $this->_preRank, $this->_curRank );

			$this->_sendSocketMsg( $msg );
		}
	}

	public function intoRankList( $uid, $cost, $nick, $level )
	{
		if ( $this->_intoRankList( self::RANK_TYPE_DAY, $uid, $cost )
			&& $this->_intoRankList( self::RANK_TYPE_WEEK, $uid, $cost )
			&& $this->_intoRankList( self::RANK_TYPE_ALL, $uid, $cost )
		)
		{
			$this->_sendRankUpNotice( $nick, $level );

			return true;
		}

		return false;
	}

	private function _intoRankList( $type, $uid, $cost )
	{
		$table = $this->_tableMap[$type];
		if ( !$table )
		{
			return false;
		}

		$insertData = [
			'uid'  => $uid,
			'luid' => $this->_luid,
			'cost' => $cost
		];

		if ( $type != self::RANK_TYPE_ALL )
		{
			$insertData['date'] = date( "Y-m-d", $this->_getTimeStamp( $type ) );
		}

		$sql = $this->_db->insert( $table, $insertData, true ) . " on duplicate key update cost=cost+$cost";
		if ( !$this->_db->query( $sql ) )
		{
			return false;
		}

		$this->_rankDataChangedHandleFlow( $type, $uid, $cost );

		return true;
	}

	private function _rankDataChangedHandleFlow( $type, $uid, $cost )
	{

		$redisKey = $this->_getRankListRedisKey( $type );
		if ( !$this->_redis->isExists( $redisKey ) )
		{
			$result = [];
			$this->_syncData( $type, $redisKey, $result );
			$this->_setRedisExpireTime( $type, $redisKey );

			$newRank = $this->_redis->zRevRank( $redisKey, $uid );
			$newCost = $this->_getRankCost( $type, $uid );

			$oldCost = $newCost - $cost;

			//get old rank
			$rank = $this->_redis->getMyRedis()->zCard( $redisKey ) - $this->_redis->getMyRedis()->zCount( $redisKey, 0, $oldCost ) - 1;
			$this->_log( __FUNCTION__ . " $uid with no redis old rank $rank" );
		}
		else
		{
			$rank = $this->_redis->zRevRank( $redisKey, $uid );

			if ( $rank === false )
			{
				$cost = $this->_getRankCost( $type, $uid );
			}
			else
			{
				$cost = $this->_getRankCost( $type, $uid ) + $cost;
			}

			$this->_redis->zadd( $redisKey, $cost, $uid );

			$newRank = $this->_redis->zRevRank( $redisKey, $uid );
		}

		$isChange = false;
		$isUp     = false;

		if ( $newRank <= 9 )
		{
			if ( $rank === false )
			{
				//一次送礼，进入前十
				$isChange = true;
				$isUp     = true;
			}
			else
			{
				if ( $newRank < $rank )
				{
					//名次提升
					$isChange = true;
					$isUp     = true;
				}
				else
				{
					$isChange = true;
				}
			}
		}

		$this->_debugLog( "$type $uid start ======" );
		$this->_debugLog( "oldrank:$rank, newRank:$newRank" );

		//只要排名变化，则认为是提高了，真正是否需要发送通知，由函数setRankNotice决定
		$isUp = $isChange;
		if ( $isUp )
		{
			$this->_debugLog( "$type $uid is up" );

			//第一次入榜
			if ( isset( $oldCost ) && $oldCost == 0 )
			{
				$pre = -1;
			}
			else
			{
				$pre = $rank === false ? -1 : $rank > 9 ? -1 : $rank + 1;
			}

			$cur = $newRank + 1;
			$this->_debugLog( "type $uid pre:$pre cur:$cur" );

			$this->_setRankNotice( $type, $uid, $pre, $cur );
		}

		if ( $isChange )
		{
			$rankChangeMsgPackage = MsgPackage::getRoomRankChangeMsgSocketPackage( $this->_luid, $this->_msgChangeType[$type] );
			$this->_sendSocketMsg( $rankChangeMsgPackage );
		}

		$this->_clearRankList( $redisKey );
	}


	private function _getRankCost( $type, $uid )
	{
		$cost = $this->_getRankCostByRedis( $type, $uid );
		if ( !$cost )
		{
			$cost = $this->_getRankCostByDb( $type, $uid );
		}

		return $cost;
	}

	private function _getRankCostByRedis( $type, $uid )
	{

		$redisKey = $this->_getRankListRedisKey( $type );
		$tmpScore = (int)$this->_redis->getMyRedis()->zScore( $redisKey, $uid );

		return $tmpScore;
	}

	private function _getRankCostByDb( $type, $uid )
	{
		$table = $this->_tableMap[$type];
		if ( !$table )
		{
			return false;
		}

		$where = [
			'uid'  => $uid,
			'luid' => $this->_luid
		];

		if ( $type != self::RANK_TYPE_ALL )
		{
			$where['date'] = date( "Y-m-d", $this->_getTimeStamp( $type ) );
		}

		$sql = $this->_db->field( 'cost' )->where( $where )->select( $table, true );
		$res = $this->_db->query( $sql );
		if ( !$res )
		{
			//log
			return false;
		}

		$row = $res->fetch_assoc();

		return intval( $row['cost'] );
	}


	private function _getRankListRedisKey( $type )
	{
		return $this->_redisKeyMap[$type] . "_" . $this->_luid . "_" . date( "YmdH" );
	}


	private function _syncData( $type, $redisKey, &$result )
	{
		$result = $this->_getRankListByDb( $type );
		foreach ( $result as $value )
		{
			$cost = $value['cost'];
			$uid  = $value['uid'];
			$this->_redis->zadd( $redisKey, $cost, $uid );
		}
	}

	private function _setRedisExpireTime( $type, $key )
	{
//		if( $type == self::RANK_TYPE_DAY )
//		{
//			$expire = ( 24 * 3600 ) - ( time() - $this->_getTimeStamp( $type ) );
//
//			$this->_redis->expire( $key, $expire );
//		}
//		elseif( $type = self::RANK_TYPE_WEEK )
//		{
//			$expire = ( 7 * 24 * 3600 ) - ( time() - $this->_getTimeStamp( $type ) );
//
//			$this->_redis->expire( $key, $expire );
//		}
		$expire = 3600;
		$this->_redis->expire( $key, $expire );
	}

	private function _getTimeStamp( $type )
	{
		switch ( $type )
		{
			case self::RANK_TYPE_DAY:
				$timestamp = strtotime( 'today' );
				break;
			case self::RANK_TYPE_WEEK:
				$timestamp = strtotime( "last Monday" );
				break;
			default:
				$timestamp = strtotime( "today" );
		}

		return $timestamp;
	}

	private function _sendSocketMsg( $msg )
	{
		return SocketSend::sendMsg( $msg, $this->_db );
	}


	/**
	 * @param $type
	 * @param $size
	 */
	public function getRankList( $type, $size )
	{
		$key = $this->_getRankListRedisKey( $type );

		$this->_log( __FUNCTION__ . "($type) redis key" . json_encode( $key ) );

		if ( !$this->_redis->isExists( $key ) )
		{
			$result = array();
			$this->_syncData( $type, $key, $result );
			$this->_log( __FUNCTION__ . "($type)" . "redis key not found" . json_encode( $result ) );
		}
		else
		{
			$result = $this->_getRankListByRedis( $type );
		}
		$this->_log( __FUNCTION__ . "($type)" . json_encode( $result ) );

		return $result;
	}

	private function _getRankListByDb( $type )
	{
		$filed = [ "uid", "cost" ];
		$where = [
			'luid' => $this->_luid
		];

		if ( $type != self::RANK_TYPE_ALL )
		{
			$where['date'] = date( "Y-m-d", $this->_getTimeStamp( $type ) );
		}

		$sql = $this->_db->field( $filed )->where( $where )->order( "cost desc" )->limit( "10" )->select( $this->_tableMap[$type], true );

		$res = $this->_db->query( $sql );
		if ( !$res )
		{
			$this->_log( "sql error : $sql" );

			//todo false;
			return false;
		}

		$result = [];

		while ( $row = $res->fetch_assoc() )
		{
			array_push( $result, $row );
		}

		return $result;
	}

	private function _getRankListByRedis( $type )
	{
		$key    = $this->_getRankListRedisKey( $type );
		$result = $this->_redis->getMyRedis()->zRevRange( $key, 0, 9, true );

		$result = $result ? $result : [];

		$rank = [];
		$this->_log( __FUNCTION__ . "($type)" . json_encode( $result ) );
		foreach ( $result as $uid => $cost )
		{
			$tmp['uid']  = $uid;
			$tmp['cost'] = $cost;

			array_push( $rank, $tmp );
		}

		return $rank;
	}

	private function _clearRankList( $redisKey )
	{
		$count = $this->_redis->zcard( $redisKey );
		if ( $count > self::RANK_COUNT_LIMIT )
		{
			$end = $count - self::RANK_COUNT_LIMIT - 1;
			$this->_redis->zRemRangeByRank( $redisKey, 0, $end );
		}
	}

	private function _debugLog( $msg )
	{
		if ( $this->_debug )
		{
			$this->_log( $msg );
		}
	}

	private function _log( $msg )
	{
		$dir = LOG_DIR . __CLASS__ . ".log";
		mylog( $msg, $dir );
	}

}