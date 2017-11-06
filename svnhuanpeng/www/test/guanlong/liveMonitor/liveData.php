<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/4/28
 * Time: 15:16
 */

/****************************直播、录像监控****************************/

include (__DIR__.'/../../../../include/init.php');
//获取所有直播
function getAllLive($db){
	$r = $db->where("status=".LIVE." order by liveid desc")->select('live');
	return $r;
}
//获取对应直播流
function getLiveStreams($liveID,$db){
	$r = $db->where("liveid={$liveID}")->select('liveStreamRecord');
	return $r;
}
//
function getStreamLog($liveID,$db){
	$r = $db->where("liveid={$liveID}")->select('liveStreamLog');
	return $r;
}
//
function getRtmpPub(){}
//
function getRtmpWatch(){}
//
function getUserByLive($uid,$db){
	$r = $db->where("uid={$uid}")->select('userstatic');
	return $r;
}

$db = new DBHelperi_huanpeng();
$lives = getAllLive($db);
$users = [];
$streams = [];
$streamLog = [];
foreach ($lives as $k=>$live)
{
	$users[$k] = getUserByLive($live['uid'],$db);
	$streams[$k] = getLiveStreams($live['liveid'],$db);
	$streamLog[$k] = getStreamLog($live['liveid'],$db);
}



