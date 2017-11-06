<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/3/2
 * Time: 下午12:58
 */

include __DIR__.'/../../include/init.php';
include __DIR__."/robot.inc.php";

$env = $GLOBALS['env'];

$robotCountLimitList = $env."_robotRoomLimitCount";//记录当前主播房间需要设置多少机器人
$robotCurCountList = $env."_robotCurCountList";//记录当前主播房间有多少机器人
$robotAddIntervalHashList = $env."_robotAddInterval"; //记录当前主播增加机器人的执行周期 [luid=>interval]
$robotCountNumLineToSubUser = $env."_robotNumberToSub";//记录主播机器人数递减的分割线
$robotRunTimeList = $env.'_robotRunTimeList';//记录主播当前等待时间


$redis = new redishelp();
$db = new DBHelperi_huanpeng();

$timeIntervalRange = [30, 60];

$userMaxRange = [20, 120];
$addRange = [1,90];
$lineAddRange = [0,100];

$lineToSub = 2;

$secondAddUserNum = 1/6;


//$redis->del( $robotAddIntervalHashList );
//$redis->set( "liveCountFictitious370",0 );
//$redis->set( "liveCount370",0 );

$getLiveList = function() use ( $db )
{
    $liveList = array();
    $sql = "select * from live where status=".LIVE;
    $res = $db->query($sql);
    while($row = $res->fetch_assoc())
    {
        array_push($liveList, $row['uid']);
    }
    return $liveList;
};

$getMaxViewCount = function() use ( $userMaxRange )
{
    return call_user_func_array( 'rand', $userMaxRange );
};

$threadRun = function( $luid , $number ) use ( $env ){
	$cmd = "(php /usr/local/huanpeng/bin/robot/addRobot.php $env $luid $number &)";
	`$cmd`;
};


$getInterval = function() use ( $timeIntervalRange )
{
	return call_user_func_array('rand', $timeIntervalRange);
};

$getLineToSub = function( $count ) use ( $lineToSub )
{
	return (int)( $count / $lineToSub );
};

$isInRange = function( $range )
{
	$num = call_user_func_array( 'rand', [0,100]);
	if($num >= $range[0] && $num <= $range[1] )
	{
		return true;
	}
	else
	{
		return false;
	}
};

$checkAnchorIsLiving = function ( $luid ) use ( $db )
{
	$sql = "select uid from live where uid=$luid and status=".LIVE;
	$res = $db->query( $sql );
	if(!$res)
	{
		return false;
	}
	$row = $res->fetch_assoc();
	return (int)$row['uid'];
};


while(true){
	mylog( "logs=============tart run ============", LOGFN_ROBOT_ACTION );
	//init data
    $liveList = $getLiveList();
    foreach($liveList as $luid)
    {

		if($luid == 26080)
		{
			$userMaxRange = [600, 800];
		}else
		{
			$userMaxRange = [20, 120];
		}

		//查询主播的执行周期，如果没有 则说明是新用户，则创建规则
		$interval = $redis->hget($robotAddIntervalHashList, $luid);
		if($interval === false){

			mylog( "add new list $luid", LOGFN_ROBOT_ACTION );
			$interval = $getInterval();
			$redis->hset( $robotAddIntervalHashList, $luid, $interval );

			$roomRobotCountLimit = $getMaxViewCount();
			$redis->hset( $robotCountLimitList, $luid, $roomRobotCountLimit );

			//set cur room robot count
			$redis->hset( $robotCurCountList, $luid, 0 );

			$redis->hset( $robotCountNumLineToSubUser, $luid, $getLineToSub( $roomRobotCountLimit ) );

			$redis->hset( $robotRunTimeList, $luid, 0);
		}
    }

    $runlist = $redis->hgetAll( $robotAddIntervalHashList );
	foreach ( $runlist as $luid => $interval ){

		$runTime = (int)$redis->hget( $robotRunTimeList, $luid );
		mylog('current run time is '. $runTime, LOGFN_ROBOT_ACTION );
		mylog('current interval time is '. $interval, LOGFN_ROBOT_ACTION );
		if($runTime < $interval )
		{
			$runTime ++;
			$redis->hset( $robotRunTimeList, $luid, $runTime );
		}else {
			mylog( "handle author: $luid", LOGFN_ROBOT_ACTION );
			//检测当前人数 增加 或者 减少的人数
			$robotCount = $redis->hget( $robotCurCountList, $luid );
			mylog( "handle author: $luid--current robot count is $robotCount", LOGFN_ROBOT_ACTION );

			$robotLimitCount = $redis->hget($robotCountLimitList, $luid);
			mylog( "handle author: $luid--robot limit count is $robotLimitCount", LOGFN_ROBOT_ACTION );

			$lineToSubNumber = $redis->hget($robotCountNumLineToSubUser, $luid);

			$addNumber = $interval * $secondAddUserNum;


			$step = 0;
			if( $robotCount > $robotLimitCount )
			{
				$addNumber = -abs( $addNumber );
				$step = 1;
			}
			elseif( ( $robotCount > $lineToSubNumber ) &&  ( $robotCount < $robotLimitCount ) )
			{
				$step = 2;
				if( $isInRange( $addRange ) ){
					$addNumber = abs( $addNumber );

				}else
				{
					$addNumber = -abs( $addNumber );
				}
			}else
			{

				$addNumber = abs( $addNumber );
			}

			$threadRun( $luid, $addNumber );

			if($step == 1){
				$interval = rand(90,120);
			}else if($step == 2){
				$interval = rand(60,90);
			}else{
				$interval = $getInterval();
			}

			$redis->hset( $robotAddIntervalHashList, $luid, $interval );
			$redis->hset( $robotRunTimeList, $luid, 0);
		}

	}
	sleep(1);
}

