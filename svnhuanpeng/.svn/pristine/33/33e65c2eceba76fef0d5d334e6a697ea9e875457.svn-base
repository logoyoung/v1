<?php

/**
 * 忘记密码 找回密码
 * yandong@6rooms.com
 * Date 2016-07-05 14:48
 */
session_start();
include '../init.php';
$db = new DBHelperi_huanpeng();

/**
 * 检测新密码长度以及是否包含中文
 * @param type $password  密码
 * @return boolean
 */
function checkPwd($password) {
    if (mb_strlen($password) < 6 || mb_strlen($password) > 12) {
        return false;
    }
    //密码中是否包含中文
    preg_match('/[\x{4e00}-\x{9fa5}]+/u', $password, $matches_c);
    if ($matches_c) {
        return false;
    } else {
        return true;
    }
}

$mobile = isset($_REQUEST['mobile']) ? trim($_REQUEST['mobile']) : '';
$password = isset($_REQUEST['password']) ? trim($_REQUEST['password']) : '';
$password2 = isset($_REQUEST['password2']) ? trim($_REQUEST['password2']) : '';
if (empty($mobile) || empty($password) || empty($password2)) {
    error(-4013);
}
if ($password !== $password2) {
    error(-4014);
}
$password = filterData($password);
$Pwdres = checkPwd($password);
if ($Pwdres === false) {
    error(-4003);
}
$checkmobile = checkMobile($mobile);
if(!$checkmobile){
     error(-4058);
}
if ( $mobile != $_SESSION['forget_mobile'] || empty($_SESSION['forget_mobile']) || !$_SESSION['forget_mobile']) {
    exit(-4058);
}
$_SESSION['forget_mobile'] = $_SESSION['forget_mobile_code'] = $_SESSION['forget_codeid'] = '';
$password = md5password($password);
$sql = "update userstatic set password='$password' where phone= $mobile";
$res = $db->query($sql);
if ($res) {
    exit(jsone(array('isSuccess' => "1")));
} else {
    exit(jsone(array('isSuccess' => "0")));
}




