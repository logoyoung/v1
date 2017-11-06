<?php

include '../../../include/init.php';
use service\user\UserAuthService;

/**
 * 取消收藏录像
 * date 2015-12-31 10:47 am
 * author yandong@6rooms.com
 * version 0.0
 */
$db = new DBHelperi_huanpeng();

/**
 * 取消收藏录像
 * @param int $uid
 * @param int $videoID
 * @param object $db
 * @return success 0 and fail false
 */
function removeCollectVideo($uid, $videoIDList, $db) {
    $res = $db->where('uid=' . $uid . ' and videoid in (' . $videoIDList . ')')->delete('videofollow');
    if (false !== $res) {
        $res = array('failedList' => '');
    } else {
        $res = array('failedList' => $videoIDList);
    }
    return $res;
}

/**
 * 校验videoIDList
 * @param type $videoIDList
 * @return type
 */
function checkVideoId($videoIDList) {
    $arr = array_filter(explode(',', $videoIDList));
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
$videoIDList = isset($_POST['videoIDList']) ? $_POST['videoIDList'] : '';
if (empty($videoIDList)) {
    error2(-991);
}
if (empty($uid) || empty($encpass)) {
    error2(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$videoIDList = checkVideoId($videoIDList);

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

$result = removeCollectVideo($uid, $videoIDList, $db);
succ($result);
