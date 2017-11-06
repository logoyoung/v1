<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/3/9
 * Time: 上午10:57
 */


include __DIR__.'/../../include/init.php';
include INCLUDE_DIR."LiveRoom.class.php";
include __DIR__."/robot.inc.php";


$env = $GLOBALS['env'];

$robotCountLimitList = $env."_robotRoomLimitCount";//记录当前主播房间需要设置多少机器人
$robotCurCountList = $env."_robotCurCountList";//记录当前主播房间有多少机器人
$robotAddIntervalHashList = $env."_robotAddInterval"; //记录当前主播增加机器人的执行周期 [luid=>interval]
$robotCountNumLineToSubUser = $env."_robotNumberToSub";//记录主播机器人数递减的分割线
$robotRunTimeList = $env.'_robotRunTimeList';//记录主播当前等待时间


$robotHeadList = $env."_robotHeadList";//纪录主播房间机器人头像 PRO_robotHeadList$luid //机器人个数为5-20个

$redisObj = new redishelp();
$dbObj = new DBHelperi_huanpeng();

$getRobotList = function () use ( $dbObj )
{

	$result = array();

	$sql = "select nick,pic,userstatic.uid as uid,`level` from userstatic,useractive where username='hpRobot' and userstatic.uid=useractive.uid";
	$res = $dbObj->query( $sql );
	while($row = $res->fetch_assoc())
	{
		$result[$row['uid']]['nick'] = $row['nick'];
		$result[$row['uid']]['pic'] = $row['pic'];
		$result[$row['uid']]['level'] = $row['level'];
	}

	return $result;
};


$msgList = array("666666666","666","233333333","233","FFFFFFFFFF","主播，好有爱！","求露脸～","不要逗！","主播，求BGM","约吗？","吓死宝宝了！","我的内心几乎是崩溃的","笑尿了","演的不错","漂亮","啊啊啊","哈哈哈啊啊","哈哈","呵呵","完美","套路","٩( 'ω' )و","0.0","？？？","。。。","太简单了吧","←_←","→_→","厉害了!","那你很棒哦！","尴尬","还能这么玩","困=_=","看不懂啊","好冷","欢迎","我服了","服了","没办法","别闹了","醉了","来晚了。。","给主播来波豆","可以的","关注了","你好意思吗？");
for($i = 1; $i<=22; $i++)
{
	array_push($msgList, str_repeat("[em_$i]", rand(2, 5)));
}

$robotList = $getRobotList();

$spareRobot = array_keys($robotList);//空闲列表

$realRobotRange = array(20, 40);
$realRobotInterval = $env == "DEV" ? array(20,30) : array(20,80);//40 80

$realRobotIndexRange = array( 0, count($robotList) - 1 );

$addTimeRange = $env == 'DEV' ?  array(4, 5) : array(5,50);//5-50
$addInterval = array();
$addRunTime = array();
$addLimit = array();


$msgIntervalRange = $env == 'DEV' ?  array(15, 70) : array(60, 150);
$msgSendNumRange = $env == "DEV" ? array(5,10) : array(2,5);

$msgSendNum = array();
$msgAlreadySendNum = array();
$msgInterval = array();
$msgRunTime = array();

//print_r($robotList);
//print_r($spareRobot);
//exit;

while(true)
{
	$intervalList = $redisObj->hgetAll( $robotAddIntervalHashList );

	foreach( $intervalList as $luid => $val )
	{
		$addRunTime[$luid] = empty( $addRunTime[$luid] ) ? 1 : $addRunTime[$luid];

		$addInterval[$luid] = empty( $addInterval[$luid] ) ? call_user_func_array('rand', $addTimeRange ) : $addInterval[$luid];

		if( empty( $addLimitInterval[$luid] ) ){
			$addLimitInterval[$luid] = call_user_func_array( 'rand', $realRobotInterval );
		}

		if( !isset( $addLimit[$luid] ) )
		{
			$addLimit[$luid]['count'] = call_user_func_array( 'rand', $realRobotRange );
			$addLimit[$luid]['runtime'] = 1;
		}

		if( $addLimit[$luid]['runtime'] < $addLimitInterval[$luid] )
		{
			$addLimit[$luid]['runtime'] ++;
		}else
		{
			$addLimit[$luid]['count'] = call_user_func_array( 'rand', $realRobotRange );
			$addLimit[$luid]['runtime'] = 1;
			$addLimitInterval[$luid] = call_user_func_array('rand', $realRobotInterval);
		}

		$addCount = 1;//进入房间的人数

		$headListRedisKey = $robotHeadList."-$luid";

		if( $addRunTime[$luid] < $addInterval[$luid] )
		{
			$addRunTime[$luid] ++;
		}else
		{

			$currentCount = $redisObj->scard($headListRedisKey);

			if($currentCount < $addLimit[$luid]['count'])
			{
				$list = array();
				for($i=0; $i<$addCount; $i++)
				{
					$enterUidIndex = call_user_func_array( 'rand', $realRobotIndexRange );
					$enterUid = $spareRobot[$enterUidIndex];
//					echo "robot active enter=======\n";
//					echo "robot active enter uid is $enterUid\n";
//					echo "robot info is ".json_encode($robotList[$enterUid])."\n";
					$nick = $robotList[$enterUid]['nick'];
					$pic = $robotList[$enterUid]['pic'];
					$level = $robotList[$enterUid]['level'];
					array_push($list, array($enterUid, $nick, $pic, $level));
				}
				$info = json_encode($list);//一次一个人
				$cmd = "(php addRobot.php $env $luid 1 '$info' &)";
			}else
			{
				$list = array();
				for($i = 0; $i< $addCount; $i++)
				{
//					echo "robot active leave=======\n";
					$enterUid = $redisObj->sRandMember($headListRedisKey);
//					echo "robot active leave uid is $enterUid\n";
//					echo "robot info is ".json_encode($robotList[$enterUid])."\n";
					$nick = $robotList[$enterUid]['nick'];
					$pic = $robotList[$enterUid]['pic'];
					$level = $robotList[$enterUid]['level'];
					array_push($list, array($enterUid, $nick, $pic, $level));
				}
				$info = json_encode($list);
				$cmd = "(php addRobot.php $env $luid -1 '$info' &)";
			}
//			echo "robot active cmd is $cmd\n";
			`$cmd`;
			$addInterval[$luid] = call_user_func_array('rand', $addTimeRange );
			$addRunTime[$luid] = 1;
		}

		//send msg
		if( empty( $msgSendNum[$luid] ) )
		{
//			$msgSendNum[$luid] = rand( 5, 10 );
			$msgSendNum[$luid] = call_user_func_array('rand', $msgSendNumRange);
		}
		if( empty($msgInterval[$luid]) )
		{
//			$msgInterval[$luid] = rand( 15, 70 );
			$msgInterval[$luid] = call_user_func_array('rand', $msgIntervalRange);
		}
		if( empty( $msgRunTime[$luid] ) )
		{
			$msgRunTime[$luid] = 1;
		}

		if( empty( $msgAlreadySendNum[$luid] ) )
		{
			$msgAlreadySendNum[$luid] = 0;
		}

//		echo "robot active already send num {$msgAlreadySendNum[$luid]}\n";
//		echo "robot active limit send num {$msgSendNum[$luid]}\n";
//		echo "robot run time is {$msgRunTime[$luid]}\n";
//		echo "robot run interval is {$msgInterval[$luid]}\n";
		if( $msgAlreadySendNum[$luid] > $msgSendNum[$luid] )
		{
//			echo "send msg finish reset\n";
//			$msgSendNum[$luid] = rand( 5, 10 );
			$msgSendNum[$luid] = call_user_func_array('rand', $msgSendNumRange);
			$msgAlreadySendNum[$luid] = 0;
		}else
		{
//			echo "send msg  start\n";
			if( $msgRunTime[$luid] < $msgInterval[$luid])
			{
//				echo " msg send run time ++ \n";
				$msgRunTime[$luid] ++;
			}else
			{
//				echo " todo send msg \n";
				if( $redisObj->scard( $headListRedisKey ) > 0 )
				{
//					echo "there room has robot";
					$liveroomObj = new LiveRoom($luid, $dbObj);
					$uid = $redisObj->sRandMember( $headListRedisKey );
					$msgIndex = rand(0, count($msgList)-1 );
					$msg = $msgList[$msgIndex];
					$nick = $robotList[$uid]['nick'];
					$pic = $robotList[$uid]['pic'];
					$level = $robotList[$uid]['level'];

					$msg = array(
						't' => 502,
						'tm' => time(),
						'cuid' => $uid,
						'cunn' => $nick,
						'msg' => $msg,
						'group' => $liveroomObj->getUsergroup($uid),
						'msgid' => time(),
						'level' => $level,
						'phone' => 0
					);
//					echo "robot active pre to send ".json_encode($msg)."\n";
					$r = $liveroomObj->sendRoomMsg( json_encode( toString( $msg ) ) );
					if($r)
					{
						$msgAlreadySendNum[$luid] ++;
					}
					$msgRunTime[$luid] = 1;
					$msgInterval[$luid] = call_user_func_array('rand', $msgIntervalRange);
					unset($liveroomObj);
				}
			}
		}
	}
	sleep(2);
}
