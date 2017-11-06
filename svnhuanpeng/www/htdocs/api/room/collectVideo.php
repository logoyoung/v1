<?php

include '../../../include/init.php';
use service\user\UserAuthService;
/**
 * 收藏录像
 * date 2015-12-31 10:47 am
 * author yandong@6rooms.com
 * version 0.0
 */
$db = new DBHelperi_huanpeng();

/**
 * 收藏录像
 * @param int $uid
 * @param int $videoID
 * @param object $db
 * @return success 0 and fail false
 */
function addCollectVideo($uid, $videoID, $db) {
    $sql = "INSERT INTO `videofollow` (`uid`,`videoid`) VALUES ($uid, $videoID) on duplicate key update uid = $uid, videoid = $videoID";
    $res = $db->doSql($sql);
    if (false !== $res) {
        $res = array('videoID' => $videoID);
    } else {
        error2(-5017,2);
    }
    return $res;
}

$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$videoID = isset($_POST['videoID']) ? (int) $_POST['videoID'] : '';
if (empty($uid) || empty($encpass) || empty($videoID)) {
    error2(-993);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$videoID = checkInt($videoID);

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

$result = addCollectVideo($uid, $videoID, $db);
succ($result);


