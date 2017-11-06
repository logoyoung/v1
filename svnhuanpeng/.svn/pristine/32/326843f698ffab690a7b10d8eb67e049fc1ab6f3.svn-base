<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/4/19
 * Time: 13:41
 */

include '../../../include/init.php';
use lib\Live;
/*$db = new DBHelperi_huanpeng(true);
Live::sendVideoComplete(array('uid'=>1870,'title'=>'123','content'=>'hahahha'),$db,null);

exit;*/
/************************直播追踪*****************************/
$uid    = isset( $_GET['uid'] ) ? $_GET['uid'] : 0;
$liveID = isset( $_GET['liveid'] ) ? $_GET['liveid'] : 0;
function getLiveByUid( $uid, $db )
{
	$live = $db->where( "uid={$uid} order by ctime desc limit 1" )->select( 'live' );

	return $live[0];
}

function getLiveByLiveID( $liveID, $db )
{
	$live = $db->where( "liveid={$liveID}" )->select( 'live' );

	return $live[0];
}

function getStreamRecordByLiveID( $liveID, $db )
{
	$streamRecord = $db->where( "liveid={$liveID}" )->select( 'liveStreamRecord' );

	return $streamRecord;
}

function getStreamLogByLiveID( $liveID, $db )
{
	$streamLog = $db->where( "liveid={$liveID}" )->select( 'liveStreamLog' );

	return $streamLog;
}

$db = new DBHelperi_huanpeng();
if( $uid )
{
	$live = getLiveByUid( $uid, $db );
}
else
{
	$live = getLiveByLiveID( $liveID, $db );
}
$streamRecord = getStreamRecordByLiveID( $live['liveid'], $db );
$streamLog    = getStreamLogByLiveID( $live['liveid'], $db );
$timeLength   = Live::getLiveTimeLength( $live['liveid'], $db );
/*
var_dump($live);
var_dump($streamRecord);
var_dump($streamLog);*/

echo "<html>
<head><title>直播追踪</title></head>
<body>
<style>
table{
	border: 1px solid #000;
	width: 1000px;
	height: 150px;
	margin: auto;
}
tr,td{
	border: 1px solid  #000;
	background: #DDDDDD;
}
</style>
<table>";

echo "<tr><td>直播ID</td><td>{$live['liveid']}</td></tr>";
echo "<tr><td>直播状态</td><td>{$live['status']}</td></tr>";
foreach ( $streamRecord as $k => $stream )
{
	echo "<tr><td>直播流{$stream['stream']}--{$k}</td><td>{$stream['status']}</td><td>{$stream['stime']}</td><td>{$stream['etime']}</td></tr>";
}
echo "<tr><td>直播时长</td><td>$timeLength</td></tr>";
echo "</table>
</body>
</html>";

exit;


$filed = isset( $_GET['passwd'] ) ? $_GET['passwd'] : '';
$db    = new DBHelperi_huanpeng();
$filed = $db->realEscapeString( $filed );
//var_dump($filed);
$filed = $db->realEscapeString( $filed );
//var_dump($filed);
$r = $db->where( 'uid=' . 1570 . " and encpass='{$filed}'" )->select( 'userstatic' );
var_dump( $r );
//$res = $db->prepare("select uid,encpass from userstatic where uid=1570 and encpass='1 or 1=1'");
//var_dump($res);