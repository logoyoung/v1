<?php

include '../init.php';
/**
 * 取消房间管理预员
 * date 2016-1-11 10:14
 * author yandong@6rooms.com
 * version 0.0
 */
$db = new DBHelperi_huanpeng();

/**
 * 取消房管理员
 * @param int $uid
 * @param int $adminID
 * @param object $db
 * @return bool
 */
function cancelAdmin($uid, $adminID, $db) {
    $res = $db->where("luid=$uid and uid=$adminID")->delete('roommanager');
    return $res;
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$adminID = isset($_POST['adminID']) ? trim($_POST['adminID']) : '';
if (empty($uid) || empty($encpass) || empty($adminID)) {
    error(-993);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$adminID = checkInt($adminID);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error($s);
}

$rest = cancelAdmin($uid, $adminID, $db);
if ($rest) {
    $isSuccess = 1;
} else {
    $isSuccess = 0;
}

exit(jsone(array('isSuccess' => $isSuccess)));
