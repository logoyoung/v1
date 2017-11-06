<?php

/**
 * 取消待推荐推荐
 * yandong@6rooms.com
 * date 2016-11-21 19:02
 *
 */
require '../../../includeAdmin/init.php';
require INCLUDE_DIR . "Admin.class.php";
$db = new DBHelperi_admin();


/**删除待推荐列表
 * @param $uid  要删除的主播ID
 * @param $db
 * @return bool
 */
function deleteWaitList($uid, $db)
{
    if (empty($uid)) {
        return false;
    }
    $res = $db->where("uid in ($uid)  and  status=0")->delete('admin_recommend_live');
    if (false !== $res) {
        return true;
    } else {
        return false;
    }
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int)$_POST['type'] : 1;
$luid = isset($_POST['luid']) ? trim($_POST['luid']) : '';
$client = isset($_POST['client']) ? (int)$_POST['client'] : 2;


if (empty($uid) || empty($encpass) || empty($luid)) {
    error(-1007);
}
if ($luid) {
    if (preg_match("/[^\d-., ]/", $luid)) {
        error(-1019);
    }
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$res = deleteWaitList($luid, $db);
if ($res) {
    succ();
} else {
    error(-1014);
}
