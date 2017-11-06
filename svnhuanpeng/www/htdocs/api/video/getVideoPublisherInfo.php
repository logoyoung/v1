<?php

/**
 * 获取个人信息
 */
include '../../../include/init.php';
$db = new DBHelperi_huanpeng();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
//
//function checkUserStates($uid, $encpass, $db){
//	$sql = "SELECT `encpass` FROM `user` WHERE `uid` = $uid";
//	$res = $db->query($sql);
//
//	if( !$row = $res->fetch_assoc() )
//		return '-1014';
//
//	if( $row['encpass'] != $encpass )
//		return '-1013';
//
//	return true;
//}
//function getUserBaseInfo($uid, $db){
//	$conf = $GLOBALS['env-def'][$GLOBALS['env']];
//
//	$sql = "select nick, pic from user where uid = $uid";
//	$res = $db->query($sql);
//	$row = $res->fetch_assoc();
//
//	$userinfo = array();
//
//	if(!$row)
//		return false;
//
//	foreach($row as $k => $v)
//		$userinfo[$k] = $v;
//
//	$sql = "select level, integral, readsign,hpbean,hpcoin from useractive where uid = $uid";
//	$res = $db->query($sql);
//	$row = $res->fetch_assoc();
//
//	if(!$row)
//		return false;
//
//	foreach($row as  $k => $v)
//		$userinfo[$k] = $v;
//
//	$userinfo['pic'] = "http://".$conf['domain-img'].'/' .$userinfo['pic'];
//
//	return $userinfo;
//}



function selUserFansCount($uid, $db) {
    $sql = "select count(*)  from userfollow where uid2 = $uid";
    $res = $db->query($sql);
    $row = $res->fetch_row();

    return (int) $row[0];
}

function selUserFollowCount($uid, $db) {
    $sql = "select count(*) as count from userfollow where uid1 = $uid";
    $res = $db->query($sql);
    $row = $res->fetch_row();

    return (int) $row[0];
}

function selUserVideoCount($uid, $db) {
    $sql = "select count(*) from video where uid = $uid";
    $res = $db->query($sql);
    $row = $res->fetch_row();

    return (int) $row;
}

function selUserLiveInfo($luid, $db) {
    $sql = "select * from live where uid = $luid order by liveid desc limit 1";
    $res = $db->query($sql);
    $row = $res->fetch_assoc();

    if (!$row)
        return false;

    return $row;
}

function getPublisherInfo($tuid, $db, $uid = 0, $enc = '',$conf) {
    $flag = false;
    if (!$tuid)
        return -4013;

    if ($uid && $enc) {
        $err = checkUserState($uid, $enc, $db);
        $flag = $err === true ? true : false;
    }

    $userinfo = array();
    $u = getUserBaseInfo($tuid, $db);
    if (!$u)
        return -998; //用户不存在

    $userinfo['nick'] = $u['nick'];
    $userinfo['head'] = $u['pic'] ? DOMAIN_PROTOCOL . $conf['domain-img'] . "/" .$u['pic'] : DEFAULT_PIC;
    $userinfo['level'] = $u['level'];
    $userinfo['integral'] = $u['integral'];
    $userinfo['unreadMsg'] = $u['readsign'];
    $userinfo['hpbean'] = $u['hpbean'];
    $userinfo['hpcoin'] = $u['hpcoin'];

    $userinfo['userID'] = $tuid;

    if ($flag) {
        $isFollow = isFollow($uid, $tuid, $db);
        $userinfo['isFollow'] = (int) $isFollow;
    }
    $roomid=getRoomIdByUid($tuid, $db);
    $userinfo['roomID'] =$roomid[$tuid] ? $roomid[$tuid] : 0;
    $userinfo['fansCount'] = selUserFansCount($tuid, $db);
    $userinfo['followCount'] = selUserFollowCount($tuid, $db);
    $userinfo['videoCount'] = selUserVideoCount($tuid, $db);

    if (!$r = selUserLiveInfo($tuid, $db))
        $userinfo['isLiving'] = 0;
    else {
        $userinfo['isLiving'] = 1;
        $userinfo['liveStatus'] = $r['status'];
        $userinfo['liveStime'] = strtotime($r['ctime']);
        $userinfo['liveEtime'] = strtotime($r['etime']);
    }

    return $userinfo;
}

$targetUserID = isset($_POST['luid']) ? (int) $_POST['luid'] : 0;
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : 0;
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';


$r = getPublisherInfo($targetUserID, $db, $uid, $encpass,$conf);
if ($r && is_array($r))
    succ($r);
else
    error2($r,2);