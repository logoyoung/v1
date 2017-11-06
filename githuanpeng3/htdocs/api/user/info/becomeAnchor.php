<?php

include '../../../../include/init.php';
require(INCLUDE_DIR . 'User.class.php');
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
/**
 * 添加主播
 * date 2016-6-16 11:08
 * author yandong@6rooms.com
 * version 0.0
 */
$db = new DBHelperi_huanpeng();

/**
 * 检测是手机,邮箱,实名是否完成,完成则添加到主播表,没有则返回未完成项
 * @param type $uid  主播id
 * @param type $db
 * @return boolean
 */
function become($uid, $db) {
    if (empty($uid)) {
        return false;
    }
    $user = new UserHelp($uid);
    $phone = $user->getPhoneCertifyInfo();
    if ($phone['status'] == 0) {
        error2(-5026);
    }
    $email = $user->getEmailCertifyInfo();
    if ($email['status'] != EMAIL_PASS) {
        error2(-5027); //邮箱未认证
    }
    $realName = $user->getRealNameCertifyInfo();
    if ($realName['status'] != RN_PASS) {
        error2(-5028); //实名未认证
    }
    $isexit = checkUserIsAnchor($uid, $db);
    if (empty($isexit)) {
        $res = $db->insert('anchor', array('uid' => $uid));
        if ($res !== false) {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';

if (empty($uid) || empty($encpass)) {
    error2(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error2(-4067,2);
}
become($uid, $db);
succ();

