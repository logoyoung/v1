<?php

include '../init.php';
require_once INCLUDE_DIR . 'redis.class.php';
session_start();
/**
 * 举报主播
 * author yandong@6rooms.com
 * date 2016-04-13 10:33
 * copyright@6.cn version 0.0
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();
$redobj = new RedisHelp();

/**
 * 举报
 * @param int $uid
 * @param int $luid
 * @param string $liveid
 * @param string $pic
 * @param string $reason
 * @param string $contact
 * @return bool
 */
function addReport($uid, $luid, $liveid, $pic, $reason, $contact, $db) {
    $data = array(
        'uid' => $uid,
        'luid' => $luid,
        'liveid' => $liveid,
        'pic' => $pic,
        'reason' => $reason,
        'contact' => $contact
    );
    $res = $db->insert('report', $data);
    return $res;
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) ($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$luid = isset($_POST['luid']) ? (int) ($_POST['luid']) : '';
$liveid = isset($_POST['liveID']) ? trim($_POST['liveID']) : '';
$pic = isset($_POST['pic']) ? trim($_POST['pic']) : '';
$reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
$contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';
$code = isset($_POST['code']) ? trim($_POST['code']) : '';
//if(empty($_SESSION['check_code']) || (strtolower($code)  != $_SESSION['check_code']))
//{
//	error(-4031);
//}
if (empty($uid) || empty($encpass) || empty($luid)  || empty($pic) || empty($reason)) {
    error(-993);
}
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error($s);
}
//$liveid = checkInt($liveid);
$pic = filterData($pic);
$reason = filterData($reason);
$contact = filterData($contact);
$res = addReport($uid, $luid, $liveid, $pic, $reason, $contact, $db);
if ($res != FALSE) {
    exit(jsone(array('isSuccess' => '1')));
} else {
    exit(jsone(array('isSuccess' => '0')));
}
