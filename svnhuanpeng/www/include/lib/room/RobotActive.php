<?php

namespace lib\room;

//include_once __DIR__ . "/../../init.php";
use lib\LiveRoom;
use lib\MsgPackage;
use lib\SocketSend;
use lib\User;


/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/5/10
 * Time: 上午11:45
 */
class RobotActive
{

	protected $_db;
	protected $_redis;

	private $_liveRoomObjList = [];

	public function __construct( \DBHelperi_huanpeng $db, \RedisHelp $redisHelp )
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
	}

//	public function init( int $luid )
//	{
//		if ( !isset( $this->_liveRoomObjList[$luid] ) )
//		{
//			$this->_liveRoomObjList[$luid] = new LiveRoom($luid,$this->_db,$this->_redis);
//		}
//	}

	/**
	 * @param int $luid
	 *
	 * @return LiveRoom
	 */
	public function getLiveRoomObj( int $luid )
	{
		return $this->_getLiveRoomObj( $luid );
	}

	private function _getLiveRoomObj( int $luid ) : LiveRoom
	{
		if ( !isset( $this->_liveRoomObjList[$luid] ) )
		{
			$this->_liveRoomObjList[$luid] = new LiveRoom( $luid, $this->_db, $this->_redis );
		}

		return $this->_liveRoomObjList[$luid];
	}

	public function enterRoom( $luid, $info )
	{

		$liveRoomObj = $this->_getLiveRoomObj( $luid );

		$liveRoomObj->addFictitiousViewCount();

		$uid       = $info['uid'];
		$nick      = $info['nick'];
		$level     = $info['level'];
		$pic       = $info['pic'];
		$group     = $liveRoomObj->getUserGroup( $uid );
		$viewCount = $liveRoomObj->getLiveRoomUserCountFictitious();
		$showHead  = 1;
		$showWel   = $liveRoomObj->isShowWel( $level );
		$isGust    = 0;

		$msg = MsgPackage::getUserEnterMsgSocketPackage( $luid, $uid, $nick, $group, $level, $pic, $viewCount, $showHead, $showWel, $isGust );

		SocketSend::sendMsg( $msg, $this->_db );
	}

	public function msg( $luid, $robotInfo, $msg )
	{
		$roomObj = $this->_getLiveRoomObj( $luid );

		$uid     = $robotInfo['uid'];
		$nick    = $robotInfo['nick'];
		$level   = $robotInfo['level'];
		$group   = $roomObj->getUserGroup( $uid );
		$isphone = 0;
		$msgid   = time();

		$socketMsg = MsgPackage::getUserMsgSocketPackage( $luid, $uid, $nick, $msg, $group, $level, $isphone, $msgid );

		SocketSend::sendMsg( $socketMsg, $this->_db );
	}

	public function exitRoom( $luid, $info )
	{
		$liveRoomObj = $this->_getLiveRoomObj( $luid );
		$liveRoomObj->subFictitiousViewCount();

		$uid       = $info['uid'];
		$level     = $info['level'];
		$viewCount = $liveRoomObj->getLiveRoomUserCountFictitious();
		$showHead  = 1;
		$showWel   = $liveRoomObj->isShowWel( $level );
		$isGust    = 0;

		$msg = MsgPackage::getUSerExitMsgSocketPackage( $luid, $uid, $viewCount, $showHead, $showWel, $isGust );

		SocketSend::sendMsg( $msg, $this->_db );
	}

	public function openTreasure( $luid, $uid, $treasureID )
	{
		$result  = '';
		$roomObj = $this->_getLiveRoomObj( $luid );

		$roomObj->openTreasureBox( $uid, $treasureID, $result );
		//todo Robot get bean log;
	}

	public function addGustRobot( $luid, $count )
	{
		$liveRoomObj = $this->_getLiveRoomObj( $luid );

		$liveRoomObj->addFictitiousViewCount( $count );

		$uid       = LIVEROOM_ANONYMOUS;
		$viewCount = $liveRoomObj->getLiveRoomUserCountFictitious();
		$nick      = '';
		$group     = 1;
		$level     = 1;
		$pic       = '';
		$showWel   = 0;
		$showHead  = 0;
		$isGust    = 1;

		$msg = MsgPackage::getUserEnterMsgSocketPackage( $luid, $uid, $nick, $group, $level, $pic, $viewCount, $showHead, $showWel, $isGust );

		SocketSend::sendMsg( $msg, $this->_db );
	}

	public function subGustRobot( $luid, $count )
	{
		$liveRoomObj = $this->_getLiveRoomObj( $luid );

		for ( $i = 0; $i < $count; $i++ )
		{
			$liveRoomObj->subFictitiousViewCount( $count );
		}

		$uid       = LIVEROOM_ANONYMOUS;
		$viewCount = $liveRoomObj->getLiveRoomUserCountFictitious();
		$showWel   = 0;
		$showHead  = 0;
		$isGust    = 1;

		$msg = MsgPackage::getUSerExitMsgSocketPackage( $luid, $uid, $viewCount, $showHead, $showWel, $isGust );
		SocketSend::sendMsg( $msg, $this->_db );
	}

	public function upDateRoomViewerMsg( $luid )
	{
		$liveRoomObj = $this->_getLiveRoomObj( $luid );

		$uid       = LIVEROOM_ANONYMOUS;
		$viewCount = $liveRoomObj->getLiveRoomUserCountFictitious();
		$showWel   = 0;
		$showHead  = 0;
		$isGust    = 1;

		$msg = MsgPackage::getUSerExitMsgSocketPackage( $luid, $uid, $viewCount, $showHead, $showWel, $isGust );
		SocketSend::sendMsg( $msg, $this->_db );
	}
}
