<?php

session_start();
/**
 * 登出接口
 * anchor by dong
 * 2016-11-19 11:00
 */
include '../../../include/init.php';
$db = new DBHelperi_huanpeng();
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$client = isset($_POST['client']) ? (int)($_POST['client']) : '';//1,安卓 2,IOS 3,WEB
if(empty($uid)||empty($encpass) || empty($client) || !in_array($client,array(1,2,3))){
    error2(-4013);
}
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error2($s,2);
}
delUserLoginCookie($uid, $encpass);
succ();