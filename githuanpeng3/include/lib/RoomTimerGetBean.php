<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/4/23
 * Time: 下午8:55
 */

namespace lib;
/**
 * 到时领豆
 *
 * Class RoomTimerGetBean
 * @author hantong <[hantong@6.cn]>
 * @version 1.0.1
 * @package hp\lib
 */
class RoomTimerGetBean
{
	const TABLE_GET_BEAN_BY_TIME = 'pickupHpbean';

	const TABLE_GET_BEAN_RULE = "pickupRule";

	const GET_BEAN_STATUS_SUCCESS = 1;

	const GET_BEAN_STATUS_CREATE = 0;

	const NO_ROOM_LUID = 0;

	const ERROR_NOT_TIME = -4048;

	const ERROR_ALREADY_GET = -1112;

	const ERROR_ENTER_ROOM_RECORD = -1111;

	private $_uid;
	private $_db;
	private $_redis;

	private $_date = '';

	private $_tableMap = [];

	private $_rule = [];
	private $_pickInfo = [];

	private $_errorno = 0;

	public function __construct( int $uid, \DBHelperi_huanpeng $db, \RedisHelp $redisHelp )
	{
		$this->_date = date( "Y-m-d" );
		$this->_db   = new \DBHelperi_huanpeng();

		$this->_tableMap['pick'] = self::TABLE_GET_BEAN_BY_TIME;
		$this->_tableMap['rule'] = self::TABLE_GET_BEAN_RULE;

		$this->_uid = $uid;

		$this->_rule     = $this->_getPickRule();
		$this->_pickInfo = $this->_getCurPickInfo();
	}

	/**
	 * 到时领取欢朋豆
	 *
	 * @param int  $luid
	 * @param int  $pickID
	 * @param User $user
	 * @param int  $errno
	 *
	 * @return bool|int
	 */
	public function getBean( int $luid, int $pickID, User $user, &$errno = 0 )
	{
		$this->_clearError();
		if( !$this->_isCanGetBean( $pickID ) )
		{
			$errno = $this->_errorno;
			if( $errno == self::ERROR_ALREADY_GET )
			{
				$errno = 0;
				if( $pickID < $this->_getLastPickID() )
				{
					$index = array_search( $pickID, $this->_rule['index'] );
					$index++;
					$newPickID = $this->_rule['index'][$index];
					$data      = [
						'time' => $this->getPickRule()['map'][$newPickID]['time'],
						'lvl'  => $newPickID
					];
				}
				else
				{
					$data = [
						'time' => 0,
						'lvl'  => $pickID + 1
					];
				}
				$property         = $user->getUserProperty();
				$data['revCount'] = 0;
				$data['hpbean']   = $property['bean'];
				$data['hpcoin']   = $property['coin'];

				return $data;
			}

			return false;
		}

		$beanRange = $this->_rule['map'][$pickID]['range'];
		$beanRange = explode( ',', $beanRange );

		$bean = rand( $beanRange[0], $beanRange[1] );

		//pick id range  0-9999
		$id   = $this->_getTid( $pickID );
		$desc = json_encode( array( 'pickid' => $pickID, 'bean' => $bean, 'luid' => $luid ) );

		$result = $this->_getBean( $bean, $desc, $id );

		if( is_array( $result ) )
		{
			$this->_log( __FUNCTION__ . "update user hp bean number : " . $result['hd'] );
			$user->updateUserHpBean( $result['hd'] );
			$this->_finishGetBean( $pickID, $bean, $result['tid'] );
			//todo log the finish getBean result

			$index = array_search( $pickID, $this->_rule['index'] );
			if( $pickID < $this->_getLastPickID() )
			{
				$index++;
				$newPickID = $this->_rule['index'][$index];

				$this->_addEnterRoomRecord( $luid, $newPickID );
				//todo log the add record result
			}

			if( !isset( $newPickID ) )
			{
				$data = [
					'time' => 0,
					'lvl'  => $pickID + 1
				];
			}
			else
			{
				$data = [
					'time' => $this->getPickRule()['map'][$newPickID]['time'],
					'lvl'  => $newPickID
				];
			}

			$data['revCount'] = $bean;
			$data['hpcoin']   = $result['hb'];
			$data['hpbean']   = $result['hd'];

			return $data;
		}
		else
		{
			$errno = $this->_errorno;

			return false;
		}
	}

	private function _getBean( $bean, $desc, $otid )
	{
		$finance = new Finance( $this->_db, $this->_redis );
		$result  = $finance->addUserBean( $this->_uid, $bean, Finance::GET_BEAN_CHANNEL_TIME, $desc, $otid );
		if( Finance::checkBizResult( $result ) )
		{
			return $result;
		}
		else
		{
			$this->_errorno = $result['errno'];

			return false;
		}
	}

	/**
	 * 获取领取欢朋豆财务外键
	 *
	 * @param int $pickID
	 *
	 * @return string
	 */
	public function _getTid( int $pickID )
	{
		$len = strlen( "$pickID" );

		for ( $i = 0; $i < 4 - $len; $i++ )
		{
			$pickID = "0" . $pickID;
		}

		return str_replace( "-", "", $this->_date ) . $pickID . $this->_uid;
	}

	/**
	 * 进入房间
	 *
	 * @param int $luid
	 *
	 * @return array|bool
	 */
	public function enterRoom( int $luid, &$errno )
	{
		$this->_clearError();

		if( $this->_isFirstEnterRoom() )
		{
			if( !$this->_addEnterRoomRecord( $luid, $this->_getFirstPickID() ) )
			{
				//
				$this->_errorno = self::ERROR_ENTER_ROOM_RECORD;
				$errno          = $this->_errorno;

				return false;
			}
		}
		elseif( $r = $this->_isTodayGetBeanFinished() )
		{
			$result = [
				'time'  => 0,
				'lvl'   => $r,
				'isVip' => 0
			];

			return $result;
		}
		else
		{

			$this->_log(__FUNCTION__."enter room again ");
			if( !$this->_getCurRoomID() )
			{
				$this->_log(__FUNCTION__."enter room again and not in room ");

				$this->_changeRoom( $luid );
				$ret = $this->_updateUTime();
				$this->_log(__FUNCTION__."enter room again updateUtime resutle is  ".json_encode($ret));
			}
		}

		$this->_pickInfo = $this->_getCurPickInfo();

		$pickID = $this->_pickInfo['pickid'];
		$stime  = time() - $this->_pickInfo['utime'] + $this->_pickInfo['time'];

		return [
			'time'  => $this->_rule['map'][$pickID]['time'] - $stime,
			'lvl'   => "$pickID",
			'isVip' => 0
		];
	}

	/**
	 * 退出房间
	 *
	 * @param  int $luid
	 *
	 * @return bool
	 */
	public function exitRoom( int $luid )
	{
		if( $this->_isTodayGetBeanFinished() )
		{
			return true;
		}

		$isInRoom = $this->_getUserViewRoomID();
		if( !$isInRoom )
		{
			$this->_log(__FUNCTION__."($luid)======>real exit room");

			$this->_changeRoom( self::NO_ROOM_LUID );
			$ret = $this->_updateStayTime( true );

			$this->_log("update stay time result is ".json_encode($ret));
			return true;
		}

		if( $luid == $this->_getCurRoomID() )
		{
			$this->_updateStayTime( true );
			if( $isInRoom )
			{
				$this->_changeRoom( $isInRoom );
			}
			else
			{
				$this->_changeRoom( self::NO_ROOM_LUID );
			}
		}
	}

	/**
	 * 是否第一次进入房间
	 *
	 * @return bool
	 */
	private function _isFirstEnterRoom()
	{
		$where = [
			'uid'  => $this->_uid,
			'date' => $this->_date,
		];

		$sql = $this->_db->field( "count(pickid) as total" )->where( $where )->select( $this->_tableMap['pick'], true );
		$res = $this->_db->query( $sql );
		if( !$res )
		{
			//TODO log
			return false;
		}
		$row = $res->fetch_assoc();

		return (int)$row['total'] ? false : true;
	}

	/**
	 * 增加进入房间记录
	 *
	 * @param int $luid
	 * @param int $pickID
	 *
	 * @return \对于更新语句
	 */
	private function _addEnterRoomRecord( int $luid, int $pickID )
	{
		$utime = date( "Y-m-d H:i:s" );

		$insertData = [
			'date'   => $this->_date,
			'uid'    => $this->_uid,
			'pickid' => $pickID,
			'luid'   => $luid,
			'utime'  => $utime
		];

		$sql = $this->_db->insert( $this->_tableMap['pick'], $insertData, true );
		$res = $this->_db->query( $sql );
		$this->_log($sql.$this->_db->errstr());
		return $res;
	}

	private function _changeRoom( $luid )
	{
		$updateData = [
			'luid' => $luid
		];

		$where = [
			'uid'    => $this->_uid,
			'date'   => $this->_date,
			'status' => self::GET_BEAN_STATUS_CREATE
		];

		$sql = $this->_db->where( $where )->update( $this->_tableMap['pick'], $updateData, true );

		return $this->_db->query( $sql );
	}

	/**
	 * 完成领取当前任务
	 *
	 * @param int $pickID
	 * @param int $hpbean
	 *
	 * @return bool|int
	 */
	private function _finishGetBean( int $pickID, int $hpbean, int $otid )
	{
		$updateData = [
			'getNum' => $hpbean,
			'status' => self::GET_BEAN_STATUS_SUCCESS,
			'otid'   => $otid
		];

		$where = [
			'date'   => $this->_date,
			'uid'    => $this->_uid,
			'pickid' => $pickID,
			'status' => self::GET_BEAN_STATUS_CREATE
		];

		$sql = $this->_db->where( $where )->update( $this->_tableMap['pick'], $updateData, true );
		$res = $this->_db->query( $sql );
		if( !$res )
		{
			//todo log
			return false;
		}

		return $this->_db->affectedRows;
	}

	private function _isCanGetBean( $pickID )
	{

		$curPickID = $this->_getCurPickID();
		if( $pickID < $curPickID )
		{
			$this->_errorno = self::ERROR_ALREADY_GET;
			$this->_log( __FUNCTION__ . "$pickID" . " pickid < curpickid" );

			return false;
		}
		elseif( $pickID > $curPickID )
		{
			$this->_errorno = self::ERROR_NOT_TIME;
			$this->_log( __FUNCTION__ . "$pickID" . " pickid > curpickid" );

			return false;
		}
		else
		{
			$ruleTime = $this->_rule['map'][$pickID]['time'];
			$stime    = time() - $this->_pickInfo['utime'] + $this->_pickInfo['time'];


			$this->_log(__FUNCTION__.":".__LINE__.":"."pick time is ".$this->_pickInfo['utime']);
			$this->_log(__FUNCTION__.":".__LINE__.":"."pick time is ".$stime);

			//&& $this->_pickInfo['utime'] > 0
			if( $stime >= $ruleTime  )
			{
				return true;
			}
			else
			{
				$this->_log( __FUNCTION__ . "$pickID" . " not a time  " );

				$this->_errorno = self::ERROR_NOT_TIME;

				return false;
			}
		}
	}

	private function _updateUTime()
	{
		$utime = date( "Y-m-d H:i:s" );
		$sql   = "update " . $this->_tableMap['pick'] . " set utime='$utime' where uid={$this->_uid} and `date`='{$this->_date}' and status=" . self::GET_BEAN_STATUS_CREATE;

		return $this->_db->query( $sql );
	}

	private function _updateStayTime( $flag = false )
	{
		$this->_log(__FUNCTION__."($flag)::".json_encode($this->_pickInfo));

		$utime = $this->_pickInfo['utime'] ;

		if( !$utime )
		{
			return false;
		}
		$time = time() - $utime;

		$utime = date( "Y-m-d H:i:s" );

		if( !$flag )
		{
			if( $time < 30 )
			{
				return true;
			}
		}

		$sql = "update " . $this->_tableMap['pick'] . " set `time`=`time`+$time,utime='$utime' where uid={$this->_uid} and `date`='{$this->_date}' and status=" . self::GET_BEAN_STATUS_CREATE;
		if( $this->_db->query( $sql ) )
		{
			return true;
		}
		else
		{
			$t = "QueryError @ " . __CLASS__ . "::" . __FUNCTION__ . "()[{$this->_db->errno()}][{$this->_db->errstr()}][$sql]";
			$this->_log( $t );
			return false;
		}
	}

	/**
	 * 检测当前用户今日领取是否已经完成
	 *
	 * @return bool|int
	 */
	private function _isTodayGetBeanFinished()
	{
		$pickID = $this->_getLastPickID();

		$field = [ 'status' ];
		$where = [
			'uid'    => $this->_uid,
			'date'   => $this->_date,
			'pickid' => $pickID,
		];

		$sql = $this->_db->field( $field )->where( $where )->select( $this->_tableMap['pick'], true );
		$res = $this->_db->query( $sql );
		if( !$res )
		{
			//TODO log
			return false;
		}
		$row = $res->fetch_assoc();

		return (int)$row['status'] ? (int)$pickID + 1 : false;
	}

	/**
	 * 获取领取规则
	 *
	 * @return array
	 */
	private function _getPickRule()
	{
		$field = [ 'id', 'range', 'time' ];
		$sql   = $this->_db->field( $field )->order( "id" )->select( $this->_tableMap['rule'], true );
		$res   = $this->_db->query( $sql );

		$result = [];
		$index  = [];
		while ( $row = $res->fetch_assoc() )
		{
			array_push( $index, $row['id'] );

			$result[$row['id']]['range'] = $row['range'];
			$result[$row['id']]['time']  = $row['time'];
		}

		return [
			'index' => $index,
			'map'   => $result,
			'count' => count( $index )
		];
	}


	/**
	 * 获取领取规则
	 *
	 * @return array
	 */
	public function getPickRule()
	{
		if( !$this->_rule )
		{
			$this->_rule = $this->_getPickRule();
		}

		return $this->_rule;
	}

	/**
	 * 获取首次领取的领取ID
	 *
	 * @return mixed
	 */
	private function _getFirstPickID()
	{
		return $this->_rule['index'][0];
	}

	/**
	 * 获取最后领取的领取ID
	 *
	 * @return mixed
	 */
	private function _getLastPickID()
	{
		return $this->_rule['index'][$this->_rule['count'] - 1];
	}

	/**
	 * 获取当前领取ID
	 *
	 * @return int
	 */
	private function _getCurPickID()
	{
		$where = [
			'uid'    => $this->_uid,
			'date'   => $this->_date,
			'status' => self::GET_BEAN_STATUS_CREATE,
			'luid'   => [ self::NO_ROOM_LUID, '!=', 'and' ]
		];

		$field = [ 'pickid' ];

		$sql = $this->_db->field( $field )->where( $where )->select( $this->_tableMap['pick'], true );
		$res = $this->_db->query( $sql );

		$row = $res->fetch_assoc();

		return (int)$row['pickid'];
	}

	/**
	 * 获取当前领取记录信息
	 *
	 * @return array
	 */
	private function _getCurPickInfo()
	{
		$where = [
			'uid'    => $this->_uid,
			'date'   => $this->_date,
			'status' => self::GET_BEAN_STATUS_CREATE
		];

		$sql = $this->_db->field( 'utime,time,luid,pickid' )->where( $where )->select( $this->_tableMap['pick'], true );
		$res = $this->_db->query( $sql );
		$row = $res->fetch_assoc();
		$this->_log(__FUNCTION__.":::".$sql);
		$this->_log($row['utime']);
		return [
			'utime'  => strtotime( $row['utime'] ),
			'time'   => $row['time'],
			'luid'   => $row['luid'],
			'pickid' => $row['pickid']
		];
	}

	/**
	 * 获取当前所在房间信息
	 *
	 * @return mixed
	 */
	private function _getCurRoomID()
	{
		return $this->_pickInfo['luid'];
	}

	/**
	 * 获取用户其他房间号
	 *
	 * @return int
	 */
	private function _getUserViewRoomID()
	{
		$luid = MsgPackage::SOCKET_ROOM_LUID;
		$sql = "select luid from liveroom where luid !=$luid and uid={$this->_uid} group by luid limit 1";
		$res = $this->_db->query( $sql );

		if( !$res )
		{
			//
			$t = "QueryError @ " . __CLASS__ . "::" . __FUNCTION__ . "()[{$this->_db->errno()}][{$this->_db->errstr()}][$sql]";
			$this->_log( $t );

			return 0;
		}

		$row = $res->fetch_assoc();

		return (int)$row['luid'];
	}

//	private function _
	/**
	 * 清除错误代码
	 *
	 */
	private function _clearError()
	{
		$this->_errorno = 0;
	}

	private function _log( $msg )
	{
		$dir = LOG_DIR . __CLASS__ . ".log";
		mylog( $msg, $dir );
	}
}