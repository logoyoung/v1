<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/3/3
 * Time: 下午1:32
 */


include __DIR__.'/../../include/init.php';
include __DIR__."/robot.inc.php";

$env = $GLOBALS['env'];

$robotAddIntervalHashList = $env."_robotAddInterval"; //记录当前主播增加机器人的执行周期 [luid=>interval]
$robotCurCountList = $env."_robotCurCountList";//记录当前主播房间有多少机器人

$robotHeadList = $env."_robotHeadList";//纪录主播房间机器人头像 PRO_robotHeadList$luid //机器人个数为5-20个

$redisObj = new redishelp();
$dbObj = new DBHelperi_huanpeng();

$isAuthorLiving = function ( $luid ) use ( $dbObj )
{
	$sql = "select uid from live where uid=$luid and status=".LIVE;
	$res = $dbObj->query($sql);
	$row = $res->fetch_assoc();

	return (int)$row['uid'];
};

$threadRun = function( $luid , $number ) use ( $env ){
	$cmd = "(php /usr/local/huanpeng/bin/robot/addRobot.php $env $luid $number &)";
	`$cmd`;
};

$runTimeList = array();
$runInterval = array();

while( true ){
	$intervalList = $redisObj->hgetAll( $robotAddIntervalHashList );

	foreach ( $intervalList as $luid => $interval ){
//		mylog("reduce un living robot", LOGFN_ROBOT_ACTION );
//		echo "reduce un living robot\n";

		if( $isAuthorLiving( $luid ) )
		{
			continue;
		}

		//real robot leave room
		$headListRedisKey = $robotHeadList."-$luid";
		if($redisObj->scard( $headListRedisKey ) > 0)
		{
			$list = $redisObj->smembers( $headListRedisKey );
			foreach ($list as $robotUid)
			{
				$cmd = "(php addRobot.php $env $luid -1 $robotUid &)";
				`$cmd`;
			}
		}


//		echo "reduce author:$luid start...\n";
		$runTimeList[$luid] = empty($runTimeList[$luid]) ? 1 : $runTimeList[$luid];


		if( !$runInterval[$luid])
		{
			$runInterval[$luid] = rand(2, 5);
		}

//		echo "reduce author:$luid runtime={$runTimeList[$luid]}\n";
//		echo "reduce author:$luid interval={$runInterval[$luid]}\n";

		if( (int)$runTimeList[$luid] <= $runInterval[$luid] ){
			$runTimeList[$luid] ++;
		}else{

			$subnumber = rand( 3, 20 );
			$currentRobotCount = $redisObj->hget( $robotCurCountList,$luid );
//			echo "reduce author:$luid sunnumber={$subnumber}\n";
//			echo "reduce author:$luid currentRobotNumber={$currentRobotCount}\n";

			if($currentRobotCount <= 0)
			{

				$redisObj->hdel( $robotAddIntervalHashList, $luid );
				unset( $runInterval[$luid] );
				unset( $runTimeList[$luid] );
			}

			if( $subnumber > $currentRobotCount )
			{
				$subnumber = $currentRobotCount;
			}

//			echo "reduce author:$luid reduce number={$subnumber}\n";

			$subnumber = -abs($subnumber);

			$threadRun( $luid, $subnumber );

			$runTimeList[$luid ] = 1;
			$runInterval[$luid] = rand(2, 5);
		}
	}
	sleep(2);
}

