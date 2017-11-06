<?php
include '../../../include/init.php';
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
function addReport($uid, $luid, $liveid, $pic, $reason, $content, $db)
{
    $data = array(
        'uid' => $uid,
        'luid' => $luid,
        'liveid' => $liveid,
        'pic' => $pic,
        'reason' => $reason,
        'contact' => $content
    );
    $res = $db->insert('report', $data);
    return $res;
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$luid = isset($_POST['luid']) ? (int)($_POST['luid']) : '';
$liveid = isset($_POST['liveID']) ? trim($_POST['liveID']) : '';
$pic = isset($_POST['pic']) ? trim($_POST['pic']) : '';
$reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
$content = isset($_POST['content']) ? trim($_POST['content']) : '';
//if(empty($_SESSION['check_code']) || (strtolower($code)  != $_SESSION['check_code']))
//{
//	error(-4031);
//}
if (empty($uid) || empty($encpass) || empty($luid)  || empty($reason)) {
    error2(-4013);
}
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error2(-4067,2);
}
$isBind = checkUserIsBindMobile($uid, $db);
if (false === $isBind) {
    error2(-5026, 2);
}
if($pic){
    $redis=new RedisHelp();
    $picturekey=$uid.':REPORT_PICTURE';
    $catchPic=$redis->get($picturekey);
    if($pic !=$catchPic){
        error2(-4070,2);
    }
    $redis->del($picturekey);
}
//$liveid = checkInt($liveid);
$pic = filterData($pic);
$reason = filterData($reason);
if(!empty($reason)){
    $content = filterData($content);
}
addReport($uid, $luid, $liveid, $pic, $reason, $content, $db);
succ();
