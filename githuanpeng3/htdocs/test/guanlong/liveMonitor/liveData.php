<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/4/28
 * Time: 15:16
 */

/****************************直播、录像监控****************************/

include (__DIR__.'/../../../../include/init.php');
use lib\WcsHelper;
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
function getStreamInfoByApi($domain = ''){
	if(!$domain)
		return false;
	return WcsHelper::getWsStreamInfoByApi($domain);
}

$db = new DBHelperi_huanpeng();
$lives = getAllLive($db);
$users = [];
$streams = [];
$streamLog = [];
list($rtmpDomain,$node) = explode('/', $GLOBALS['env-def'][$GLOBALS['env']]['stream-pub']);

//$streamPush = WcsHelper::getWsStreamInfoByApi($rtmpDomain,'dev-urtmp.huanpeng.com/liverecord/Y-657412-20170915095723');
$streamPush = WcsHelper::getWsStreamInfoByApi($rtmpDomain);
$streamPush = $streamPush['content'];
$streamPush = json_decode($streamPush,true);
$s  = [];
foreach ($streamPush['dataValue'] as $wss){
	$s[] = $wss['streamname'];
}
var_dump($s);
var_dump(\lib\live\LiveHelper::getallstreamstatus());
foreach ($lives as $k=>$live)
{
	$users[$k] = getUserByLive($live['uid'],$db);
	$streams[$k] = getLiveStreams($live['liveid'],$db);
	$streamLog[$k] = getStreamLog($live['liveid'],$db);
}



