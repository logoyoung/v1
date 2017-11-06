<?php
include '../init.php';

$conf = $GLOBALS['env-def'][$GLOBALS['env']];

$db = new DBHelperi_huanpeng();

function checkUserStates($uid, $encpass, $db){
	$sql = "SELECT `encpass` FROM `user` WHERE `uid` = $uid";
	$res = $db->query($sql);

	if( !$row = $res->fetch_assoc() )
		return '-1014';

	if( $row['encpass'] != $encpass )
		return '-1013';

	return true;
}

function getUserInfo($uid,$db){
	$sql = "select nick, pic from userstatic where uid = $uid";
	$res = $db->query($sql);

	$row = $res->fetch_assoc();

	return $row;
}

function getLiveRoomUserCount($luid,$db){
	$sql = "select count(*) from liveroom where luid=$luid";
	$res = $db->query($sql);
	$row = $res->fetch_row();

	return $row[0];
}

function getLiveInfo($luid, $db){
	$sql = "select uid, title, ctime,status from live where uid = $luid ORDER BY liveid DESC LIMIT 1";
	$res = $db->query($sql);
	$row = $res->fetch_assoc();

	return $row;
}

$uid = isset( $_POST['uid'] ) ? (int)$_POST['uid'] : 0;
$encpass= isset( $_POST['encpass'] ) ? trim($_POST['encpass']) : '';
$size = isset($_POST['size']) ? (int)$_POST['size'] : 3;

if(!$uid || !$encpass )
	error('-1014');

if($size == 0);
$size = 10;

$s = checkUserStates($uid, $encpass, $db);
if( true !== $s )
	error($s);

$sql = "select * from history where uid=$uid order by stime desc limit $size";
$res = $db->query($sql);

$history = array();

while($row = $res->fetch_assoc()){
	if($r = getLiveInfo($row['luid'], $db)){
		$userinfo = getUserinfo($r['uid'], $db);
		$r['pic'] = "http://" . $conf['domain-img'] . '/' . $userinfo['pic'];
		$r['nick'] = $userinfo['nick'];
		$r['stime'] = strtotime($row['stime']);
		$r['viewer'] = getLiveRoomUserCount($row['luid'],$db);
		array_push($history, $r);
	}
}

$json = array('historyList' => $history);
$json = json_encode($json);

exit($json);