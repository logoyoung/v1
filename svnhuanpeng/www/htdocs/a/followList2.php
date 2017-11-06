<?php

//可能做成分业接口，以及不同的排序顺序
include '../init.php';

$conf = $GLOBALS['env-def'][$GLOBALS['env']];

$db = new DBHelperi_huanpeng();

function getUserInfo($uid,$db){
	$sql = "select nick, pic from userstatic where uid = $uid";
	$res = $db->query($sql);

	$row = $res->fetch_assoc();

	return $row;
}


function checkUserStates($uid, $encpass, $db){
	$sql = "SELECT `encpass` FROM `user` WHERE `uid` = $uid";
	$res = $db->query($sql);

	if( !$row = $res->fetch_assoc() )
		return '-1014';

	if( $row['encpass'] != $encpass )
		return '-1013';

	return true;
}

function getLiveInfo($luid, $db){
	$sql = "select uid, title, ctime,status from live where uid = $luid ORDER BY liveid DESC LIMIT 1";
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

$uid = isset( $_POST['uid'] ) ? (int)$_POST['uid'] : 0;
$encpass= isset( $_POST['encpass'] ) ? trim($_POST['encpass']) : '';
$size = isset($_POST['size']) ? (int)$_POST['size'] : 0;

if(!$uid || !$encpass )
	error('-1014');

$s = checkUserStates($uid, $encpass, $db);
if( true !== $s )
	error($s);

$sql = "select uid2 from userfollow where uid1 = $uid order by tm desc";
$res = $db->query($sql);

$living = array();
$unliving = array();



while($row = $res->fetch_assoc()){
	if($r = getLiveInfo($row['uid2'], $db)) {

		$userinfo = getUserinfo($r['uid'], $db);
		$r['pic'] = "http://".$conf['domain-img'].'/' . $userinfo['pic'];
		$r['nick'] = $userinfo['nick'];
		$r['viewer'] = getLiveRoomUserCount($row['uid2'],$db);
		if ($r['status'] == LIVE)
			array_push($living, $r);
		else
			array_push($unliving, $r);
	}

}
//print_r($living);

$liveCount = count($living);

$followlist = $living ? $living : array();

foreach($unliving as $k => $v)
	array_push($followlist, $v);


$followCount = count($followlist);
//print_r(count($followlist));

$json = array();

$json['followList'] = array();

if($followCount <= $size || $size == 0){
	$json['followList'] = $followlist;

}else{
	for($i=0; $i < $size; $i++)
		array_push($json['followList'], $followlist[$i]);
}

$json['livingCount'] = $liveCount;
$json = json_encode($json);
exit($json);

