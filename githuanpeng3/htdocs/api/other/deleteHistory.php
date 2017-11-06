<?php

include '../../../include/init.php';
use service\user\UserDataService;
use service\user\UserAuthService;

/**
 * 删除浏览历史纪录
 * date 2016-05-09 11:43 am
 * author yandong@6rooms.com
 * version 0.0
 */
$db = new DBHelperi_huanpeng();

/**
 * 删除浏览历史
 * @param int $uid
 * @param int $videoID
 * @param object $db
 * @return success 0 and fail false
 */
function removeHistory($uid, $history, $db) {
    $res = $db->where('uid=' . $uid . ' and luid in (' . $history . ')')->update('history',array('status'=>0));
    return $res ? $res : array();
}

/**
 * 校验videoIDList
 * @param type $videoIDList
 * @return type
 */
function checkVideoId($uidList) {
    $arr = array_filter(explode(',', $uidList));
    $newarr = $new = array();
    foreach ($arr as $v) {
        if (is_numeric($v)) {
            $new = checkInt((int) $v);
            array_push($newarr, $new);
        }
    }
    $lids = implode(',', $newarr);
    return $lids;
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$history = isset($_POST['history']) ? trim($_POST['history']) : '';
if (empty($history) || empty($uid) || empty($encpass)) {
    error2(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$history = checkVideoId($history);

$auth = new UserAuthService();
$auth->setUid($uid);
$auth->setEnc($encpass);
//校验encpass、用户 登陆状态
if($auth->checkLoginStatus() !== true)
{
    //获取校验结果
    $result    = $auth->getResult();
    //错误码
    $errorCode = $result['error_code'];
    //错误消息
    $errorMsg  = $result['error_msg'];
    //假如是封禁的，可以获取禁时间
    $etime     = isset($result['login_disable_etime']) ? $result['login_disable_etime'] : 0;
    write_log("notice|uid:{$uid};error_code:{$errorCode};error_msg:{$errorMsg};解禁时间:{$etime}|api:".__FILE__,'auth_access');
    error2(-4067,2);
}

$history=trim($history,',');
$result = removeHistory($uid, $history, $db);
if($result){
    succ(array('failedList'=>''));
}else{
    succ(array('failedList'=>$history));
}

