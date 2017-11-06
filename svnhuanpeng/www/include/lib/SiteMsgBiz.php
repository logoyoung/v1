<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/4/20
 * Time: 11:47
 */

namespace lib;

use service\event\EventManager;

class SiteMsgBiz
{

	const SYSTEM_MSG_TABLE  = 'sysmessage';
	const USER_MSG_TABLE    = 'usermessage';
	const USER_ACTIVE_TABLE = 'useractive';

	/**
	 * @var \DBHelperi_huanpeng|null
	 */
	private $_db = null;

	/**
	 * @var null|\RedisHelp
	 */
	private $_redis = null;

	private $_msgEvent = null;

	/**
	 * SiteMsgBiz constructor.
	 *
	 * @param \DBHelperi_huanpeng $db
	 * @param \RedisHelp          $redisHelp
	 */
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
//		$this->_db = new \DBHelperi_huanpeng(true);

		if ( $redisHelp )
		{
			$this->_redis = $redisHelp;
		}
		else
		{
			$this->_redis = new \RedisHelp();
		}

		$this->_msgEvent = new EventManager();
		$this->_log( '__construct' );
	}

	/**
	 * 发送消息
	 *
	 * @param array $package
	 *
	 * @return bool|int
	 */
	public function sendMsg( array $package )
	{
//		print_r($package);
		if ( !$this->_isPackageValid( $package ) )
		{
			return false;
		}
//		print_r("package is valid");
		if ( $package['type'] == MsgPackage::SITEMSG_TYPE_TO_USER )
		{
			if(is_array($package['uid']))
			{
//				var_dump($package['uid']);
				return $this->_sendToUserList($package);
			}
			else
			{
				return $this->_sendToUser( $package );
			}
		}
		elseif ( $package['type'] == MsgPackage::SITEMSG_GROUP_ALL )
		{
			return $this->_sendToAll( $package );
		}
		else
		{
			$this->_log( "send message undefined send type" );

			return false;
		}

		return true;
	}

	/**
	 * 拉取消息
	 *
	 * @param      $uid
	 * @param User $user 用户类
	 *
	 * @return bool
	 */
	public function pullMsg( $uid, User $user )
	{
		$msgID = $this->_getLastMsgIDByUID( $uid );
		if ( !$msgID )
		{
			$regsiterTime = $user->getRegisterTime();
			$msgIDList    = $this->_getNewMsgByTime( $regsiterTime );
		}
		else
		{
			$msgIDList = $this->_getNewMsgByLastMsgID( $msgID );
		}

		foreach ( $msgIDList as $newMsgID )
		{
			$this->_sendToUser( [ 'uid' => $uid ], $newMsgID );
		}

		return true;
	}

	/**
	 * 发送全部信息
	 *
	 * @param array $package
	 *
	 * @return int
	 */
	private function _sendToAll( array $package )
	{
		return $this->_addMsgText( $package );
	}

	/**
	 * 给用户发送站内信
	 *
	 * @param array $package
	 * @param int   $msgid 如果为0 ，则创建一条信息并添加，否则，添加此消息
	 *
	 * @return int
	 */
	private function _sendToUser( array $package, int $msgid = 0 )
	{
		if ( !$msgid )
		{
			$msgid = $this->_addMsgText( $package );
		}

		if ( !$msgid )
		{
			return false;
		}

		$uid = $package['uid'];

		$data = [
			'uid'   => $uid,
			'msgid' => $msgid
		];
		$this->_addUserMsgCount( $uid );
		$sql = $this->_db->insert( self::USER_MSG_TABLE, $data, true );

		if ( $this->_db->query( $sql ) )
		{
			$this->_msgEvent->trigger( EventManager::ACTION_USER_MSG_UPDATE, [ 'uid' => $uid ] );

			return true;
		}
		else
		{
			$msg = "QueryError @ SiteMsgBiz::_sendToUser($package)[{$this->_db->errno()}][{$this->_db->errstr()}][]";
			$this->_log( $msg );

			return false;
		}
	}

	private function _sendToUserList(array $package, int $msgid=0)
	{
		if(!$msgid)
		{
			$msgid = $this->_addMsgText($package);
		}

		if(!$msgid)
		{
			return false;
		}

		$uidList = $package['uid'];

		if(!is_array($uidList) || empty($uidList))
		{
			return false;
		}

		$this->_addUserListMsgCount($uidList);

		if($this->_addUserMsgByUidList($uidList, $msgid))
		{
			foreach ($uidList as $uid)
			{
				$this->_msgEvent->trigger( EventManager::ACTION_USER_MSG_UPDATE, [ 'uid' => $uid ] );
			}

			return true;
		}
		else
		{
			return false;
		}
	}


	private function _addUserMsgByUidList($uidList, $msgId)
	{
		if(!is_array($uidList) || empty($uidList))
		{
			return false;
		}

		$data = [];

		foreach ($uidList as $uid)
		{
			$tmp = [];
			$tmp[0] = $uid;
			$tmp[1] = $msgId;
			$tmp = "(".implode(",",$tmp).")";
			array_push($data, $tmp);
		}

		$values = implode(",",$data);

		$sql = "insert into " . self::USER_MSG_TABLE . "(uid,msgid) values $values";
		$res = $this->_db->query($sql);

		if(!$res)
		{
			$msg = "QueryError @ SiteMsgBiz::_addUserMsgByUidList[{$this->_db->errno()}][{$this->_db->errstr()}][]";
			$this->_log( $msg );

			return false;
		}

		return true;
	}

	/**
	 * 消息表增加信息
	 *
	 * @param $package
	 *
	 * @return int
	 */
	private function _addMsgText( $package )
	{
		unset( $package['uid'] );

		$result = $this->_db->insert( self::SYSTEM_MSG_TABLE, $package );
//		var_dump( $result );
		if ( !$result )
		{
			$msg = "QueryError @ " . __CLASS__ . "::" . __FUNCTION__ . "($package)[{$this->_db->errno()}][{$this->_db->errstr()}][]";
			$this->_log( $msg );
		}

		return $result;
	}

	/**
	 * 为用户添加未读信息
	 *
	 * @param int $uid
	 *
	 * @return \对于更新语句
	 */
	private function _addUserMsgCount( int $uid )
	{
		$sql = "update " . self::USER_ACTIVE_TABLE . " set readsign=readsign+1 where uid=$uid";
		$res = $this->_db->query( $sql );

		return $res;
	}

	private function _addUserListMsgCount(array $uid)
	{
		$uid = implode(",", $uid);
		$sql = "update ". self::USER_ACTIVE_TABLE . " set readsign=readsign +1 where uid in ($uid)";

		$res = $this->_db->query($sql);

		return $res;
	}



	/**
	 * 获取用户最后一条消息ID
	 *
	 * @param int $uid
	 *
	 * @return int
	 */
	private function _getLastMsgIDByUID( int $uid )
	{
		$where = [ 'uid' => $uid ];
		$sql   = $this->_db->field( "MAX(msgid) as lastmid" )->where( $where )->select( self::USER_MSG_TABLE, true );
		$res   = $this->_db->query( $sql );
		$row   = $res->fetch_assoc();

		return (int)$row['lastmid'];
	}

	/**
	 * 获取最新消息列表
	 *
	 * @param string $time
	 *
	 * @return array
	 */
	private function _getNewMsgByTime( string $time )
	{
		$field = [ 'id' ];
		$where = [
			'stime' => [ $time, '>', 'AND' ],
			'type'  => [ MsgPackage::SITEMSG_TYPE_TO_ALL, '=', 'AND' ]
		];

		$sql = $this->_db->field( $field )->where( $where )->select( self::SYSTEM_MSG_TABLE, true );
		$res = $this->_db->query( $sql );

		$msgIDList = array();

		while ( $row = $res->fetch_assoc() )
		{
			array_push( $msgIDList, $row['id'] );
		}

		return $msgIDList;
	}

	/**
	 * 获取最新消息列表
	 *
	 * @param int $msgID
	 *
	 * @return array
	 */
	private function _getNewMsgByLastMsgID( int $msgID )
	{
		$field = [ 'id' ];
		$where = [
			'id'   => [ $msgID, '>', 'and' ],
			'type' => [ MsgPackage::SITEMSG_TYPE_TO_ALL, '=', 'AND' ]
		];

		$sql = $this->_db->field( $field )->where( $where )->select( self::SYSTEM_MSG_TABLE, true );
		$res = $this->_db->query( $sql );

		$msgIDList = array();
		while ( $row = $res->fetch_assoc() )
		{
			array_push( $msgIDList, $row['id'] );
		}

		return $msgIDList;
	}

	/**
	 * 检测package 是否有效
	 *
	 * @param array $package
	 *
	 * @return bool
	 */
	private function _isPackageValid( array $package )
	{
		$field = [ 'uid', 'title', 'msg', 'type', 'group', 'sendid' ];

		foreach ( $field as $key )
		{
			if ( !isset( $package[$key] ) )
			{
				return false;
			}
		}

		return true;
	}

	private function _log( $msg )
	{
		$dir = LOG_DIR . __CLASS__ . ".log";
		mylog( $msg, $dir );
	}
}

