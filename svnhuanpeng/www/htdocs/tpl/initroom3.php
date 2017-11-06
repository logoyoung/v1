<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/2/21
 * Time: 下午8:49
 */

require_once 'init.php';
include_once INCLUDE_DIR.'Anchor.class.php';
include_once INCLUDE_DIR.'User.class.php';
include_once INCLUDE_DIR .'LiveRoom.class.php';


header( "Content-type:text/html;charset=utf-8" );

$conf = $GLOBALS['env-def'][$GLOBALS['env']];

$db = new DBHelperi_huanpeng();

$luid = (int)$_GET['luid'];

if( !$luid )
{
	exit;
}

$anchorHelp = new AnchorHelp( $_GET['luid'], $db );

if( !$anchorHelp->isAnchor() || $anchorHelp->isBlack() )
{
	include WEBSITE_TPL.'error-404.php';
}

$userHelp = null;
$userInfo['isLogin'] = false;
$userInfo['isAnchor'] = false;

$uid = (int)$_COOKIE['_uid'];
$encpass = trim( $_COOKIE['_enc'] );

if( $uid && $encpass )
{
	$userHelp = new UserHelp( $uid, $db );
	if( !$userHelp->checkStateError($encpass) )
	{
		$userInfo['isLogin'] = true;
		$userInfo['isAnchor'] = false;
		if( $luid == $uid)
		{
			$userInfo['isAnchor'] = true;
		}
	}
}

//init some anymouse function
$lroom = new LiveRoom( $luid, $db );
$redis = new RedisHelp();

$userBaseInfo = array();
$roomInfo = array();


$getUserBaseInfo = function( $uid, $luid ) use ( &$userBaseInfo, $userHelp, $db)
{
	$info = $userHelp->getUsers();
	$level = $userHelp->getLevelInfo();
	$property = $userHelp->getProperty();
	$phoneStatus = $userHelp->getPhoneCertifyInfo()['status'];
	$silenceTime = $userHelp->isSilenced($luid);

	$getUserLevelIntegral = function( $lvl ) use ( $db )
	{
		$sql = "select integral from userlevel where `level` = $lvl";
		$res = $db->query($sql);
		$row = $res->fetch_assoc();

		return (int)$row['integral'];
	};

	$getUserReadSign = function( $uid ) use ( $db )
	{
		$sql = "select readsign from useractive where uid = $uid";
		$res = $db->query($sql);
		$row = $res->fetch_assoc();

		 return (int)$row['readsign'];
	};

	$getUserGroup = function ( $uid, $luid ) use ( &$userBaseInfo, $userHelp )
	{
		$groupid = 1;

		if( $uid == $luid )
		{
			$groupid = 5;
		}
		else if( $userHelp->isRoomAdmin( $luid ) )
		{
			$groupid = 4;
		}

		return $groupid;
	};

	$userBaseInfo['userID'] = $userHelp->uid;
	$userBaseInfo['nickName'] = $info['nick'];
	$userBaseInfo['pic'] = $info['pic'];
	$userBaseInfo['level'] = $level['level'];
	$userBaseInfo['integral'] = $level['integral'];
	$userBaseInfo['hpbean'] = $property['hpbean'];
	$userBaseInfo['hpcoin'] = $property['hpcoin'];
	$userBaseInfo['phonestatus'] = $phoneStatus;
	$userBaseInfo['silenceTime'] = $silenceTime;
	$userBaseInfo['isSilence'] = $silenceTime > 0 ? 1 : 0;
	$userBaseInfo['levelIntegral'] = $getUserLevelIntegral( $userBaseInfo['level'] );
	$userBaseInfo['readsign'] = $getUserReadSign( $userBaseInfo['userID'] );
	$userBaseInfo['groupid'] = $getUserGroup( $userBaseInfo['userID'], $luid );
};

if( $userInfo['isLogin'] )
{
	$getUserBaseInfo( $uid, $luid );
}
else
{
	$userHelp = new UserHelp(LIVEROOM_ANONYMOUS);
}

$getRoomInfo = function( $uid, $luid ) use ( &$roomInfo, $anchorHelp, $lroom, $userHelp, $db, $redis )
{
	$income = (int)$anchorHelp->getProperty()['bean'];
	$level = $anchorHelp->getLevelInfo();
	$info = $anchorHelp->getUsers();
	$liveInfo = $anchorHelp->getMyLivingInfo();

	$getAnchorLevelList = function () use ( $anchorHelp )
	{
		$levelList = array();
		$anchorLevelList = $anchorHelp->getLevelInfo();
		foreach ($anchorLevelList as $key => $val)
		{
			$levelList[$val['level']] = $val['integral'];
		}
		return $levelList;
	};

	$getUserLevelList = function () use ( $userHelp )
	{
		$list = $userHelp->getLevelInfoList();
		$ulist = array();
		foreach ($list as $key => $val)
		{
			$ulist[$val['level']] = $val['integral'];
		}

		return $ulist;
	};

	$getRoomGiftExp = function () use ( $anchorHelp )
	{
		$list = $anchorHelp->getGiftInfo();
		$ret = array();
		foreach ($list as $key => $val)
		{
			$ret[$val['id']]['exp'] = $val['exp'];
			$ret[$val['id']]['money'] = $val['money'];
		}

		return $ret;
	};

	$getRoomChatServer = function()
	{
		$conf = $GLOBALS['env-def'][$GLOBALS['env']];
		$serverList = $conf['socket'];
		shuffle($serverList);

		return $serverList;
	};

	$getRoomTreasureInfo = function( $uid, $luid ) use ( $userHelp, $redis, $db )
	{
		$treasure = array(
			'total' => 0,
			'list'  => array(),
			'timeOut' => TREASURE_TIME_OUT
		);

		$treasureList = $userHelp->getUnPickTreasureBoxInfoList( $luid );
		if( $treasureList && is_array( $treasureList ) )
		{
			foreach ($treasureList as $key => $val )
			{
				if($uid)
				{
					$egmap = "envelope_map_".$val['id'];
					if( $redis->hget( $egmap, $uid ) != 0 ){
						mylog("box".$val['id']." is received", LOGFN_SENDGIFT_LOG);
						continue;
					}
				}
				$tmp['uid'] = $val['suid'];
				$tmp['trid'] = $val['id'];
				$tmp['ctime'] = strtotime( $val['ctime'] );
				$tmp['nick'] = getUserInfo( $val['suid'], $db )[0]['nick'];

				array_push($treasure['list']);
			}
			$treasure['total'] = count( $treasure['list'] );
		}

		return $treasure;
	};

	$gameNameList = function () use ( $db )
	{
		$sql = "select `name` from game";
		$res = $db->query($sql);
		$gamelist = array();
		while($row = $res->fetch_assoc()){
			array_push($gamelist, $row['name']);
		}

		return $gamelist;
	};

	$roomInfo['treasure'] = $getRoomTreasureInfo( $uid, $luid);
	$roomInfo["chatServer"] = $getRoomChatServer();
	$roomInfo['giftExp'] = $getRoomGiftExp();
	$roomInfo['userLevelList'] = $getUserLevelList();
	$roomInfo['anchorLevelList'] = $getAnchorLevelList();
	$roomInfo['anchorLevel'] = $level['level'];
	$roomInfo['anchorIntegral']    = $level['integral'];
	$roomInfo['fansCount'] = $anchorHelp->fansCount();
	$roomInfo['anchorUserID'] = $anchorHelp->uid;
	$roomInfo['anchorNickName']   = $info['nick'];
	$roomInfo['anchorUserPicURL'] = $info['pic'];
	$roomInfo['anchorIncome'] = $anchorHelp->exchangeToBean($income);
	$roomInfo['liveID']      = $liveInfo['liveid'];
	$roomInfo['gameID']      = $liveInfo['gameid'];
	$roomInfo['gammeTypeID'] = $liveInfo['gametid'];
	$roomInfo['gameName']    = $liveInfo['gamename'] ? $liveInfo['gamename'] : '其他游戏';
	$roomInfo['status']      = $liveInfo['status'];
	$roomInfo['liveTitle']   = $liveInfo['title'] ? $liveInfo['title']:$roomInfo['anchorNickName']."的直播间";
	$roomInfo['isLiving']    = $liveInfo['status'] == 100 ? 1 : 0;
	$roomInfo['viewerCount'] = $lroom->getRoomUserCount();
	$roomInfo['manageList'] = $anchorHelp->myRoomManagerIdList();
	$roomInfo['isFollow'] = 0;
	$roomInfo['gameHistory'] = gameNameHistory($roomInfo['anchorUserID'], $db);
	$roomInfo['gameList'] = $gameNameList();
	$roomInfo['isFollow'] = (int)$userHelp->isFollow($roomInfo['anchorUserID']);
};

$getRoomInfo( $uid, $luid );

$room = '<script>var $ROOM = '.json_encode($roomInfo).';</script>';
$pageUser = '<script>var pageUser='.json_encode($userInfo).';</script>';