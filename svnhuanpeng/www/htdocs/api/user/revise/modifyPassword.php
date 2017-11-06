<?php

include '../../../../include/init.php';
use lib\User;
use service\event\EventManager;
use service\user\UserAuthService;

/**
 * 修改用户密码
 * date 2016-1-10 14:35
 * author yandong@6rooms.com
 * version 0.0
 */
$db = new DBHelperi_huanpeng();

/**
 * 检测用户密码是否正确
 * @param int $uid
 * @param object $db
 * @return string
 */
function checkOldPassword($uid, $db) {
    $result = $db->field('password')->where("uid=$uid")->limit(1)->select('userstatic');
    return $result[0]['password'];
}

/**
 * 更改用户密码
 * @param int $uid
 * @param string $newPassword
 * @param object $db
 * @return int
 */
function setUpPassword($uid, $newPassword, $db) {
    $data = array('password' => $newPassword);
    $ires = $db->where('uid=' . $uid . '')->update('userstatic', $data);
    return $ires;
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$newPassword = isset($_POST['newPassword']) ? trim($_POST['newPassword']) : '';
if (empty($uid) || empty($encpass) || empty($password)) {
    error2(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$password = filterData(checkStr($password));
if ( mb_strlen($newPassword) < 6 || mb_strlen($newPassword) > 12 ) {
    error2(-1003,2);
}
$newPassword = filterData(checkStr($newPassword));
if (empty($newPassword)) {
    error2(-989,2);
}
//检测密码中是否含有中文
if (preg_match("/[\x7f-\xff]/", $newPassword)) {
    error2("-1006",2);
}

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

$cpass = checkOldPassword($uid, $db);
if ($cpass === md5password($password)) {

	$userObj = new User($uid,$db);

	$result = $userObj->updatePassword($newPassword);
	$event = new EventManager();
	$event->trigger(EventManager::ACTION_USER_INFO_UPDATE,['uid' => $uid]);
	$event = null;
//    $result = setUpPassword($uid, md5password($newPassword), $db);
    if ($result) {
       succ();
    } else {
        error2(-5017);
    }
} else {
    error2(-4032,2);
}
