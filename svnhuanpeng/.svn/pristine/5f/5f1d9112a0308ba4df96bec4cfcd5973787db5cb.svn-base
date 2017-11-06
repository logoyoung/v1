<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/3/2
 * Time: 下午5:00
 */

include __DIR__.'/../../include/init.php';
include INCLUDE_DIR.'LiveRoom.class.php';
include __DIR__."/robot.inc.php";


$env = $GLOBALS['env'];

$robotAddIntervalHashList = $env."_robotAddInterval"; //记录当前主播增加机器人的执行周期 [luid=>interval]
$robotRunTimeList = $env.'_robotRunTimeList';//记录主播当前等待时间
$robotCurCountList = $env."_robotCurCountList";//记录当前主播房间有多少机器人

$robotHeadList = $env."_robotHeadList";//纪录主播房间机器人头像 PRO_robotHeadList$luid //机器人个数为5-20个

$timeIntervalRange = [1, 5];
$userMaxRange = [300, 500];
$addRange = [1,90];
$lineAddRange = [0,100];


$luid = (int)$argv[2];
$number = (int)$argv[3];
$robotList = $argv[4];

mylog("test enter params is ".json_encode($argv), LOGFN_ROBOT_ACTION);

if( !$luid || !$number )
{
	mylog("error params \n\n", LOGFN_ROBOT_ACTION );
	exit;
}

$headListRedisKey = $robotHeadList . "-$luid";

mylog( "enter params is ". json_encode( [$luid, $number] ), LOGFN_ROBOT_ACTION );

$dbObj = new DBHelperi_huanpeng();
$liveRoomObj = new LiveRoom($luid, $dbObj);
$redisObj = new redishelp();

$leaveRoom = function ( $count ) use( $liveRoomObj )
{
	if($liveRoomObj->getLiveUserCountFictitious() <= 10)
		return;

	$count = abs( $count );
	mylog("send user leave msg", LOGFN_ROBOT_ACTION );
	for($i = 0; $i < $count; $i++)
	{
		$liveRoomObj->liveUserCountSub();
	}
	$msg = array(
		't' => 506,
		'tm' => time(),
		'uid' => LIVEROOM_ANONYMOUS,
		'viewCount' => $liveRoomObj->getLiveUserCountFictitious(),
		'showHead' => 0,
		'showWel'=>0,
		'isGust' => 1
	);
	$liveRoomObj->sendRoomMsg( json_encode( toString( $msg ) ) );
};

$enterRoom = function ( $count ) use ( $liveRoomObj )
{
	mylog("send user enter msg", LOGFN_ROBOT_ACTION );
	for($i = 0; $i < $count; $i++)
	{
		$liveRoomObj->liveUserCountAdd();
		$liveRoomObj->setLiveCountPeakValueFictitious();
	}
	$msg = array(
		't' => 501,
		'tm' => time(),
		'uid' => LIVEROOM_ANONYMOUS,
		'viewCount' => $liveRoomObj->getLiveUserCountFictitious(),
		'showHead' => 0,
		'showWel'=>0,
		'isGust' => 1,
		'level' => 1,
		'pic' => '',
		'group' => 1,
	);
	$liveRoomObj->sendRoomMsg( json_encode( toString( $msg ) ) );
};


$checkAnchorIsLiving = function ( $luid ) use ( $dbObj )
{
	$sql = "select uid from live where uid=$luid and status=".LIVE;
	$res = $dbObj->query( $sql );
	if(!$res)
	{
		return false;
	}
	$row = $res->fetch_assoc();
	return (int)$row['uid'];
};

//realrobot enter or level
if((int)$robotList)
{
	$uid = (int)$robotList;
	$msg = array(
		't' => 506,
		'tm' => time(),
		'uid' => $uid,
		'viewCount' => $liveRoomObj->getLiveUserCountFictitious(),
		'showHead' => 1,
		'showWel' => 0,
		'isGust' => 0
	);
	$r = $liveRoomObj->sendRoomMsg( json_encode( toString( $msg ) ) );
	if($r)
	{
		$liveRoomObj->liveUserCountSub();
		$redisObj->sRem( $headListRedisKey, $uid );
	}
}

if($robotList && is_array( $robotList = json_decode( $robotList, true ) ) )
{
	if( $number > 0 )
	{
		foreach( $robotList as $value )
		{
			$uid = $value[0];
			$nick = $value[1];
			$pic = $value[2];
			$pic = $pic ? DOMAIN_PROTOCOL . $GLOBALS['env-def'][$GLOBALS['env']]['domain-img'] . "/" . $pic : DEFAULT_PIC;
			$level = $value[3];
			$msg = array(
				't' => 501,
				'tm' => time(),
				'nn' => $nick,
				'uid' => $uid,
				'level' => (int)$level,
				'pic' => $pic,
				'group' => $liveRoomObj->getUsergroup($uid),
				'viewCount' => $liveRoomObj->getLiveUserCountFictitious(),
				'showHead' => 1,
				'showWel'=>$liveRoomObj->isShowWel( $level ),
				'isGust' => 0
			);
			$r = $liveRoomObj->sendRoomMsg( json_encode( toString( $msg ) ) );
			if($r)
			{
				$liveRoomObj->liveUserCountAdd();
				$liveRoomObj->setLiveCountPeakValueFictitious();
				$redisObj->sadd( $headListRedisKey, $uid );
			}
		}
	}else
	{
		foreach( $robotList as $value )
		{

			$uid = $value[0];
			$nick = $value[1];
			$pic = $value[2];
			$level = $value[3];
			$msg = array(
				't' => 506,
				'tm' => time(),
				'uid' => $uid,
				'viewCount' => $liveRoomObj->getLiveUserCountFictitious(),
				'showHead' => 1,
				'showWel' => $liveRoomObj->isShowWel($level),
				'isGust' => 0
			);
			$r = $liveRoomObj->sendRoomMsg( json_encode( toString( $msg ) ) );
			if($r)
			{
				$liveRoomObj->liveUserCountSub();
				$redisObj->sRem( $headListRedisKey, $uid );

			}
		}
	}
	exit();
}


$robotCount = (int)$redisObj->hget( $robotCurCountList, $luid );

if( ($robotCount + $number ) < 0)
{
	$leaveRoom( $robotCount );
}else if($number < 0)
{
	$leaveRoom( $number );
}else
{
	$enterRoom( $number );
}

$tmpCount = $robotCount + $number;
$robotCount = $tmpCount > 0 ? $tmpCount : 0;

$getInterval = function() use ( $timeIntervalRange )
{
	return call_user_func_array('rand', $timeIntervalRange);
};

$redisObj->hset( $robotCurCountList, $luid, $robotCount );
mylog(" handle author: $luid end....\n\n\n", LOGFN_ROBOT_ACTION );