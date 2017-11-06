<?php

/**
 * 发送站内信
 * date 2016-2-28 14:00
 * author yandong@6rooms.com
 * copyright @www.6.cn
 */
include '../init.php';
$db = new DBHelperi_huanpeng();
/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) ($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? ($_POST['encpass']) : '';
$sendId = isset($_POST['sendId']) ? ($_POST['sendId']) : '';//收件人
$title = isset($_POST['title']) ? ($_POST['title']) : '';
$message = isset($_POST['message']) ? ($_POST['message']) : '';
$type = isset($_POST['type']) ? ($_POST['type']) : '';

$uid = checkInt($uid);
$encpass = checkStr($encpass);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error($s);
}
$res = sendMessages($sendId, $title, $message, $type, $db);
    if ($res) {
        exit(jsone(array('errorCode' => 0, 'msg' => '发送成功')));
    } else {
        exit(jsone(array('errorCode' => -1, 'msg' => '系统繁忙,请稍候再试')));
    }