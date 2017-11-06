<?php

/**
 * 重新审核评论
 * yandong@6rooms.com
 * date 2016-10-19 11:07
 *
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();


/**审核完成改变admin_wait_video_comment表的状态
 * @param $commentids  评论id
 * @param $adminid  审核者id
 * @param $db
 * @return bool
 */
function upWaitVideoComment($commentids, $adminid, $status, $db)
{
    if (empty($commentids) || empty($adminid)) {
        return false;
    }
    $vres = $db->where("id in ($commentids) ")->update('videocomment', array('status' => $status));
    if ($vres !== false) {
        return true;
    } else {
        return false;
    }
}


/**
 * 修改审核状态
 * @param string $succluid 评论id 多个的话用逗号隔开
 * @param type $db
 * @return boolean
 */
function UpdateCoomentStatus($uid, $succluid, $db)
{
    if (empty($succluid)) {
        return false;
    }
    $res = upWaitVideoComment($succluid, $uid, $status = 0, $db);//重新审核
    if ($res) {
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
$succluid = isset($_POST['succList']) ? trim($_POST['succList']) : ''; //主播id列表批量可用逗号隔开(可通过的)
if (empty($uid) || empty($encpass) || empty($type) || empty($succluid)) {
    error(-1007);
}
if(!is_numeric($type) || !is_numeric($uid)){
    error(-1023);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}

$res = UpdateCoomentStatus($uid, $succluid, $db);
if ($res) {
    succ();
} else {
    error(-1014);
}
