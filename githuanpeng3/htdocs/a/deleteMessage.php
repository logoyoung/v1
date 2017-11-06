<?php

include '../init.php';
$db = new DBHelperi_huanpeng();

/**
 * 删除站内信
 * @param int $uid
 * @param string $encpass
 * @param json $delMsgList
 * @param object $db
 * @return type
 */
function delMessages($uid, $delMsgList, $db) {
    $delres = $db->where("uid=$uid and msgid in ($delMsgList)")->update('usermessage', array('status' => 1));
    return $delres;
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) ($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? ($_POST['encpass']) : '';
$delMsgList = isset($_POST['delMsgList']) ? ($_POST['delMsgList']) : '';
if (empty($uid) || empty($encpass)) {
    error(-4013);
}
if (empty($delMsgList)) {
    error(-986);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error($s);
}
$res = delMessages($uid, $delMsgList, $db);
if ($res) {
    exit(jsone(array('errorCode' => 0, 'msg' => '删除成功!')));
} else {
    exit(jsone(array('errorCode' => -1, 'msg' => '系统繁忙,请稍后重试!')));
}