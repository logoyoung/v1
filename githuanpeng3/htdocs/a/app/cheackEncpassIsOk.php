<?php
include '../../init.php';
/**
 * 检测encpass是否过期
 * date 2016-05-11 16:21
 * author yandong@6rooms.com
 */
$db = new DBHelperi_huanpeng();

$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$uid = checkInt($uid);
$encpass = checkStr($encpass);
if(empty($uid) || empty($encpass)){
    error(-4013);
}
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    exit(jsone(array('status'=>0)));
}else{
    exit(jsone(array('status'=>1)));
}
