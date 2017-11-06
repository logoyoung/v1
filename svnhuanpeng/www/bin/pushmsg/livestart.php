<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/3/13
 * Time: 上午9:25
 */

/**
 * 直播开启推送消息守护脚本
 */


include_once __DIR__."/../../include/init.php";

use lib\LivePush;

$dbObj = new \DBHelperi_huanpeng();

$livePush = new LivePush($dbObj);

for (;;)
{
	$livePush->push();
	sleep(1);
}

exit();

include_once INCLUDE_DIR."Anchor.class.php";
include_once INCLUDE_DIR."WABPush.class.php";



$db = new DBHelperi_huanpeng();
$pushObj = new WABPush($db);


$setTask = function($liveids) use ( $db )
{
	if(!$liveids)
		return false;
	$utime = date("Y-m-d H:i:s");
	$liveids = "(".implode(",", $liveids).")";
	$sql = "update live_pushmsg_list set utime='$utime', status = " . LIVE_PUSH_RUNING . " where status = " . LIVE_PUSH_CREATE ." and liveid in $liveids";
	return $db->query($sql);
};

$getTask = function () use ( $db )
{

	$list = array();
	$sql = "select * from live_pushmsg_list where  status=".LIVE_PUSH_CREATE." group by liveid";//如果列表钟有多次次相同的LiveID 那么只发送一次
	$res = $db->query($sql);
	while( $row = $res->fetch_assoc() )
	{
		$list[$row['liveid']] = $row;
	}
	return $list;
};

$finishTask = function ( $liveid ) use ( $db )
{
	$stime = date("Y-m-d H:i:s");
	$sql = "update live_pushmsg_list set stime='$stime',utime='$stime',status=" . LIVE_PUSH_FINISH . " where liveid=$liveid and status = ".LIVE_PUSH_RUNING;
	var_dump($sql);
	return $db->query( $sql );
};

//需要增加一个错误状态，当发送失败的时候，记录

while(true)
{

//	echo "task start =====.\n";
	$info = $getTask();
	$liveIdList = array_keys($info);
//	echo "tast id list".json_encode($liveIdList)."\n";
	if( $setTask( $liveIdList ) ){
		foreach ( $liveIdList as $liveid )
		{
//			echo "handle $liveid\n";
			$luid = $info[$liveid]['luid'];
			$pushObj->sliveStart( $luid );
			$finishTask( $liveid );
		}
	}

	sleep(1);
}