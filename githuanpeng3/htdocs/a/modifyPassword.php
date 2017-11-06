<?php

include '../init.php';
/**
 * 修改用户密码
 * date 2016-1-10 14:35
 * author yandong@6rooms.com
 * version 0.0
 */
$db = new DBHelperi_huanpeng();
use service\event\EventManager;
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
    error(-993);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$password = filterData(checkStr($password));
if ( mb_strlen($newPassword) < 6 || mb_strlen($newPassword) > 12 ) {
    error(-1003);
}
$newPassword = filterData(checkStr($newPassword));
if (empty($newPassword)) {
    error(-989);
}
//检测密码中是否含有中文
if (preg_match("/[\x7f-\xff]/", $newPassword)) {
    error("-1006");
}
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error($s);
}
$cpass = checkOldPassword($uid, $db);
if ($cpass === md5password($password)) {
    $result = setUpPassword($uid, md5password($newPassword), $db);
    if ($result) {
        $isSuccess = 1;
        $event = new EventManager();
        $event->trigger(EventManager::ACTION_USER_INFO_UPDATE,['uid' => $uid]);
        $event = null;
    } else {
        $isSuccess = 0;
    }
} else {
    error(-4032);
}
exit(jsone(array('isSuccess' => $isSuccess)));
