<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/4/24
 * Time: 下午11:09
 */

namespace lib;


class TreasureBox
{
	const USER_DAY_MAX_PICK_TIME = 10;

	const REDIS_RED_ENVELOPE_TIME_OUT = 24 * 3600;

	const REDIS_KEY_BOX_STATUS_PRE = 'OPEN_TREASURE';

	const REDIS_KEY_ENVELOPE_STATUS_PRE = "ENVELOPE_STATUS";

	const REDIS_KEY_ENVELOPE_LIST_PRE = "ENVELOPE_LIST";

	const REDIS_KEY_ENVELOPE_GET_MAP = "ENVELOPE_MAP";

	const REDIS_KEY_ROOM_BOX_DETAIL = "ROOM_BOX";

	const REDIS_KEY_TREASURE_INFO = "TREASURE_INFO";

	const TREASURE_TIME_OUT = 90;

	const TREASURE_BOX_STATUS_CLOSED = 1;

	const TREASURE_BOX_STATUS_OPEN = 0;

	const ERROR_BOX_NOT_EXIST = -4049;

	const ERROR_BOX_NOT_GET_TIME = -4048;

	const ERROR_BOX_CLOSED = -4055;

	const ERROR_GET_BOX_FAILED = -1111;

	private $_luid;

	private $_db;

	private $_redis;

	private $_tableMap;

	private $_redisKeyMap;

	private $_errorno = 0;

	public function __construct( int $luid, \DBHelperi_huanpeng $db, \RedisHelp $redisHelp )
	{
		$this->_db                   = $db;
		$this->_tableMap['treasure'] = 'treasurebox';
		$this->_tableMap['pick']     = "pickTreasure";

		$this->_redis = $redisHelp;

		$this->_roomBoxRedisKey = self::REDIS_KEY_ROOM_BOX_DETAIL . "_" . $luid . "_" . date( "Ymd" );

		$this->_luid = $luid;

	}

	/**
	 * 创建宝箱
	 *
	 * @param $uid
	 *
	 * @return int
	 */
	public function createTreasure( $uid )
	{
		$insertData = [
			'uid'   => $uid,
			'luid'  => $this->_luid
		];

		$this->_log( $this->_luid );

		$sql = $this->_db->insert( $this->_tableMap['treasure'], $insertData, true );

		if ( $this->_db->query( $sql ) )
		{
			$treasureID = $this->_db->insertID;

			$this->_createTreasureIntoRedis( $treasureID, $uid );

			return $treasureID;
		}
		else
		{
			return 0;
		}
	}

	public function getTreasureOwner( int $trid )
	{
		$sql = "select uid from {$this->_tableMap['treasure']} where id=$trid";
		$res = $this->_db->query( $sql );
		if ( !$res )
		{
			$t = "QueryError @ " . __CLASS__ . "::" . __FUNCTION__ . "()[{$this->_db->errno()}][{$this->_db->errstr()}][$sql]";
			$this->_log( $t );

			return false;
		}
		$row = $res->fetch_assoc();

		return (int)$row['uid'];
	}

	public function openTreasure( $uid, $treasureID, &$result )
	{
		$this->_log( "open treaure ===>> $treasureID" );

		$this->_clearError();
		$this->_initTreasureRedisKey( $treasureID );

		if ( !$this->_checkTreasureIDIsValid( $treasureID ) )
		{
			$result = $this->_errorno;
			$this->_log( "$treasureID==>> is not valid" );
			$this->_log( $result );

			return false;
		}
		elseif ( $this->_getUsrTodayOpenCount( $uid ) > self::USER_DAY_MAX_PICK_TIME )
		{
			$result = 0;
			$this->_redis->hset( $this->_redisKeyMap['envelopeMap'], $uid, -2 );

			$this->_addOpenTreasureBoxUSerIDMapByRedis( $treasureID, $uid );
			$this->_log( "$treasureID==>> count MAx" );

			return true;
		}
		else
		{
			//加入 redis 领取记录
			//注意，在业务层，一般是先进行获取，然后在领取，
			if ( $this->_redis->isExists( $this->_roomBoxRedisKey ) )
			{
				$this->_addOpenTreasureBoxUSerIDMapByRedis( $treasureID, $uid );
			}

			$envelopeListKey = $this->_redisKeyMap['envelopeList'];

			if ( !$this->_redis->isExists( $envelopeListKey ) )
			{
				$redEnvelope     = new RedEnvelope();
				$redEnvelopeList = $redEnvelope->getReadEnvelopeList();
				$this->_log( "redenvelopelist" . json_encode( $redEnvelopeList ) );
				foreach ( $redEnvelopeList as $value )
				{
					$this->_redis->rpush( $envelopeListKey, $value );
				}
			}

			$envelopeList = $this->_redis->lranges( $envelopeListKey, 0, -1 );
			$this->_log( __FUNCTION__ . json_encode( $envelopeList ) );

			if ( count( $envelopeList ) <= 0 )
			{
				$this->_errorno = self::ERROR_BOX_CLOSED;
				$this->_closeTreasure( $treasureID );
				$result = $this->_errorno;

				return false;
			}
			$ret = $this->_openTreasure( $uid, $treasureID );
			if ( $ret !== false )
			{
				$result = $ret;

				if ( count( $envelopeList ) - 1 <= 0 )
				{
					$this->_closeTreasure( $treasureID );
				}


				return true;
			}
			else
			{
				$result = $this->_errorno;

				return false;
			}
		}
	}

	private function _openTreasure( $uid, $treasureID )
	{
		$this->_log( __FUNCTION__ );
		$count = $this->_openTreasureByRedis( $uid );
		$this->_log( "$treasureID==>>count:$count" );

		if ( false === $count )
		{
			$this->_errorno = self::ERROR_GET_BOX_FAILED;

			return false;
		}

		if ( $count < 0 )
		{
			$this->_errorno = self::ERROR_BOX_CLOSED;

			return false;
		}

		$tid = $this->_addOpenTreasureRecord( $uid, $treasureID, $count );
		$this->_log( "tid:" . $tid );
		if ( !$tid )
		{
			$this->_errorno = self::ERROR_GET_BOX_FAILED;

			return false;
		}

		$finance = new Finance( $this->_db, $this->_redis );
		$desc    = json_encode( [ 'treasureid' => $treasureID ] );

		$result = $finance->addUserBean( $uid, $count, Finance::GET_BEAN_CHANNEL_TREASURE, $desc, $tid );
		if ( Finance::checkBizResult( $result ) )
		{
			$user = new User( $uid, $this->_db, $this->_redis );

			$user->updateUserHpBean( $result['hd'] );
			//todo failed log

			$this->_finishOpenTreasureRecord( $uid, $treasureID, $result['tid'] );

			//todo failed log

			return $count;

		}
		else
		{
			$this->_errorno = self::ERROR_GET_BOX_FAILED;

			return false;
		}
	}

	private function _initTreasureRedisKey( $treasureID )
	{
//		$this->_redisKeyMMap['boxStatus']     = self::REDIS_KEY_BOX_STATUS_PRE . "_" . $treasureID . "_" . date( "Ymd" );
//		$this->_redisKeyMap['envelopeStatus'] = self::REDIS_KEY_ENVELOPE_STATUS_PRE . "_" . $treasureID . "_" . date( "Ymd" );

		$this->_redisKeyMap['treasureInfo'] = self::REDIS_KEY_TREASURE_INFO;

		$this->_redisKeyMap['envelopeList'] = self::REDIS_KEY_ENVELOPE_LIST_PRE . "_" . $treasureID;
		$this->_redisKeyMap['envelopeMap']  = self::REDIS_KEY_ENVELOPE_GET_MAP . "_" . $treasureID;

		if ( !$this->_redis->isExists( $this->_redisKeyMap['envelopeMap'] ) )
		{
			$this->_redis->hset( $this->_redisKeyMap['envelopeMap'], 0, -1 );
		}

		if ( !$this->_redis->isExists( $this->_redisKeyMap['treasureInfo'] ) )
		{
			$this->_redis->hset( $this->_redisKeyMap['treasureInfo'], 0, json_encode( [] ) );
		}
	}

	private function _closeTreasure( $treasureID )
	{
		$updateData = [ 'status', self::TREASURE_BOX_STATUS_CLOSED ];
		$where      = [ 'id' => $treasureID ];
		$sql        = $this->_db->where( $where )->update( $this->_tableMap['treasure'], $updateData, true );

		$this->_db->query( $sql );
		if ( $this->_db->affectedRows )
		{
			$this->_redis->expire( $this->_redisKeyMap['envelopeMap'], self::REDIS_RED_ENVELOPE_TIME_OUT );
			$this->_redis->expire( $this->_redisKeyMap['envelopeList'], self::REDIS_RED_ENVELOPE_TIME_OUT );
		}

		$this->_redis->getMyRedis()->hDel( $this->_roomBoxRedisKey, $treasureID );
	}

	private function _clearRedisKey()
	{
		$this->_redis->dels( $this->_redisKeyMap );
	}

	private function _checkTreasureIDIsValid( $treasureID )
	{
		$where = [
			'id'   => $treasureID,
			'luid' => $this->_luid
		];
		$field = [ 'luid', 'ctime', 'status' ];

		$sql = $this->_db->field( $field )->where( $where )->select( $this->_tableMap['treasure'], true );
		$res = $this->_db->query( $sql );

		$row = $res->fetch_assoc();

		$this->_log( json_encode( $row ) );
		if ( !intval( $row['luid'] ) )
		{
			$this->_errorno = self::ERROR_BOX_NOT_EXIST;

			return false;
		}
		else
		{
			if ( intval( $row['status'] ) != self::TREASURE_BOX_STATUS_OPEN )
			{
				$this->_errorno = self::ERROR_BOX_CLOSED;

				return false;
			}
			else
			{
				if ( ( time() - strtotime( $row['ctime'] ) ) < self::TREASURE_TIME_OUT )
				{
					$this->_errorno = self::ERROR_BOX_NOT_GET_TIME;

					return false;
				}
				else
				{
					return true;
				}
			}
		}
	}

	private function _openTreasureByRedis( $uid )
	{
		$tryGetRedEnvelope =
			"if redis.call('hexists',KEYS[2],KEYS[3]) ~= 0 then\n"
			. "return '-1'\n"
			. "else\n"
			. "local hongbao = redis.call('lpop', KEYS[1])\n"
			. "if hongbao then\n"
			. "redis.call('hset', KEYS[2], KEYS[3], hongbao)\n"
			. "return hongbao\n"
			. "else\n" . "redis.call('hset', KEYS[2], KEYS[3], '-1')\n" . "return nil\n"
			. "end\n"
			. "end\n" .
			"return nil";

		$ret = $this->_redis->evals( $tryGetRedEnvelope, [ $this->_redisKeyMap['envelopeList'], $this->_redisKeyMap['envelopeMap'], $uid ], 3 );

		return $ret;
	}

	/**
	 *
	 *
	 * @param $uid
	 *
	 * @return int
	 */
	private function _getUsrTodayOpenCount( $uid )
	{
		$stime = date( "Y-m-d" ) . " 00:00:00";
		$etime = date( "Y-m-d" ) . " 23:59:59";
		$sql   = "select count(*) as count from pickTreasure where uid=$uid and ctime BETWEEN '$stime' AND '$etime'";
		if ( !$res = $this->_db->query( $sql ) )
		{
			// todo handle sql error
			return DAY_MAX_PICK_TIME;
		}
		$row = $res->fetch_assoc();

		return (int)$row['count'];
	}

	/**
	 * @param $uid
	 * @param $treasureID
	 * @param $num
	 *
	 * @return bool|string
	 */
	private function _addOpenTreasureRecord( $uid, $treasureID, $num )
	{
		$tid        = time() . rand( 1000, 9999 );
		$insertData = [
			'uid'        => $uid,
			'luid'       => $this->_luid,
			'treasureID' => $treasureID,
			'getNum'     => $num,
			'tid'        => $tid
		];

		$sql = $this->_db->insert( $this->_tableMap['pick'], $insertData, true );
		if ( $this->_db->query( $sql ) )
		{
			return $tid;
		}
		else
		{
			$this->_log( "sql Error " . $sql );

			return false;
		}
	}

	/**
	 * @param $uid
	 * @param $treasureID
	 * @param $otid
	 *
	 * @return bool
	 */
	private function _finishOpenTreasureRecord( $uid, $treasureID, $otid )
	{
		$updateData = [ 'otid' => $otid ];
		$where      = [ 'uid' => $uid, 'treasureid' => $treasureID ];

		$sql = $this->_db->where( $where )->update( $this->_tableMap['pick'], $updateData, true );

		$res = $this->_db->query( $sql );

		if ( $res )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 *
	 *
	 * @param $treasureID
	 * @param $uid
	 */

	private function _createTreasureIntoRedis( $treasureID, $uid )
	{
		//TODO
		$this->_initTreasureRedisKey( $treasureID );

		$this->_setOpenTreasureBoxUserIDMapByRedis( $treasureID, [] );

	}

	private function _getRedisField( array $list )
	{
		return implode( ":", $list );
	}

	private function _getRedisKey( array $pre, $between )
	{

	}

	private function _clearError()
	{
		$this->_errorno = 0;
	}


	public function getUnPickedTreasureInfo( $uid )
	{
		$treasureIDList = $this->getUnPickedTreasureID( $uid );

		if ( !$treasureIDList )
		{
			return [
				'list'  => [],
				'total' => 0
			];
		}

		$idlist = implode( ',', $treasureIDList );

		$this->_log( $idlist );

		$sql = "select id, uid as suid, ctime from " . $this->_tableMap['treasure'] . " where id in ($idlist)";
		$res = $this->_db->query( $sql );
		if ( !$res )
		{
			$this->_log( "sql error " );

			//todo log
			return [
				'list'  => [],
				'total' => 0
			];
		}

		$list = [];

		while ( $row = $res->fetch_assoc() )
		{
			$tmp = [
				'uid'   => $row['suid'],
				'trid'  => $row['id'],
				'ctime' => strtotime( $row['ctime'] )
			];

			array_push( $list, $tmp );
		}

		$this->_log( "return list" . json_encode( $list ) );

		return [
			'list'  => $list,
			'total' => count( $list )
		];
	}

	/**
	 * @param $uid
	 *
	 * @return array
	 */
	public function getUnPickedTreasureID( $uid )
	{
		if ( !$this->_redis->isExists( $this->_roomBoxRedisKey ) )
		{
			$list = $this->_getOpenTreasureBoxUserIDMapByDb();
			$this->_redis->expire( $this->_roomBoxRedisKey, self::REDIS_RED_ENVELOPE_TIME_OUT );
		}
		else
		{
			$list = $this->_getOpenTreasureBoxUserIDMapByRedis();
		}

		$this->_log( "getList" . json_encode( $list ) );

		$treasureIDList = array();

		foreach ( $list as $key => $value )
		{
			$this->_log( $uid );
			$this->_log( !in_array( $uid, $value ) );
			if ( !$uid || !in_array( $uid, $value ) )
			{
				array_push( $treasureIDList, $key );
			}
		}
		$this->_log( "TREASUREIDLIST" . json_encode( $treasureIDList ) );

		return $treasureIDList;
	}

	/**
	 * @return array
	 */
	private function _getOpenTreasureBoxUserIDMapByRedis()
	{
		$result = $this->_redis->getMyRedis()->hGetAll( $this->_roomBoxRedisKey );
		foreach ( $result as $key => $value )
		{
			$result[$key] = json_decode( $value, true );
		}

		return $result;
	}

	/**
	 * @param $treasureID
	 * @param $list
	 */
	private function _setOpenTreasureBoxUserIDMapByRedis( int $treasureID, array $list )
	{
		$result = $this->_redis->hset( $this->_roomBoxRedisKey, $treasureID, json_encode( $list ) );
		$this->_log( __FUNCTION__ . json_encode( $result ) );
	}

	/**
	 * @param $treasureID
	 * @param $uid
	 */
	private function _addOpenTreasureBoxUSerIDMapByRedis( int $treasureID, int $uid )
	{
		if ( !$this->_redis->isExists( $this->_roomBoxRedisKey ) )
		{
			$this->_getOpenTreasureBoxUserIDMapByDb();
		}

		$result = $this->_redis->hget( $this->_roomBoxRedisKey, $treasureID );
		$result = json_decode( $result, true );

		if ( !$result )
		{
			$result = [ $uid ];
			$this->_setOpenTreasureBoxUserIDMapByRedis( $treasureID, $result );
		}
		elseif ( !in_array( $uid, $result ) )
		{
			array_push( $result, $uid );

			$this->_setOpenTreasureBoxUserIDMapByRedis( $treasureID, $result );
		}
	}

	/**
	 * @return array
	 */
	private function _getOpenTreasureBoxUserIDMapByDb()
	{
		$idList = $this->_getUnClosedTreasureIDByDb();
		if ( !$idList )
		{
			return [];
		}

		$list = implode( ",", $idList );

		$result = array();

		$sql = "select uid, treasureid from " . $this->_tableMap['pick'] . " where treasureid in ($list)";
		$res = $this->_db->query( $sql );

		while ( $row = $res->fetch_assoc() )
		{
			$tmpUid = intval( $row['uid'] );
			$tmpTID = intval( $row['treasureid'] );
			if ( !$result[$tmpTID] )
			{
				$result[$tmpTID] = [];
			}

			array_push( $result[$tmpTID], $tmpUid );

//			$this->_addOpenTreasureBoxUSerIDMapByRedis($tmpTID,$tmpUid);
		}
//
		foreach ( $idList as $tid )
		{
			if ( $result[$tid] )
			{
				$this->_log( "===>" . json_encode( $result[$tid] ) );
				$this->_setOpenTreasureBoxUserIDMapByRedis( $tid, $result[$tid] );
			}
			else
			{
				$result[$tid] = [];
				$this->_setOpenTreasureBoxUserIDMapByRedis( $tid, [] );
			}
		}

		return $result;
	}

	/**
	 * @return array|bool
	 */
	public function _getUnClosedTreasureIDByDb()
	{
		$treasureIDList = array();

		$field = [ 'id' ];
		$where = [
			'status' => self::TREASURE_BOX_STATUS_OPEN,
			'luid'   => $this->_luid
		];

		$sql = $this->_db->field( $field )->where( $where )->select( $this->_tableMap['treasure'], true );
		$res = $this->_db->query( $sql );
		if ( !$res )
		{
			//todo log
			return false;
		}

		while ( $row = $res->fetch_assoc() )
		{
			array_push( $treasureIDList, $row['id'] );
		}

		return $treasureIDList;
	}


	private function _log( $msg )
	{
		$dir = LOG_DIR . __CLASS__ . ".log";
		mylog( $msg, $dir );
	}
}