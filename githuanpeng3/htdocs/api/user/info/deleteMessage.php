<?php

include '../../../../include/init.php';
$db = new DBHelperi_huanpeng();

/**
 * 删除站内信
 * @param int $uid
 * @param string $encpass
 * @param json $delMsgList
 * @param object $db
 * @return type
 */
function delMessages($uid, $delMsgList, $db)
{
    $delres = $db->where("uid=$uid and msgid in ($delMsgList)")->update('usermessage', array('status' => 1));
    return $delres;
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? ($_POST['encpass']) : '';
$delMsgList = isset($_POST['delMsgList']) ? trim($_POST['delMsgList']) : '';
if (empty($uid) || empty($encpass)) {
    error2(-4013);
}
if (empty($delMsgList)) {
    error2(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error2(-4067,2);
}
$delMsgList = trim($delMsgList, ',');
$res = delMessages($uid, $delMsgList, $db);
if ($res) {
    succ();
} else {
    error2(-5017);
}