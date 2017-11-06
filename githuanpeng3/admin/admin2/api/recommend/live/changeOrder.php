<?php

/**
 * 改变推荐列表排序
 * yandong@6rooms.com
 * date 2016-11-21 17:39
 *
 */
require '../../../includeAdmin/init.php';
require INCLUDE_DIR . "Admin.class.php";
$db = new DBHelperi_admin();


/**更改推荐排序
 * @param $client  1app 端  2 web端
 * @param $list   有序推荐列表
 * @param $db
 * @return bool
 */
function changeOrder($client,$list, $db)
{
    if (empty($list)) {
        return false;
    }
    $res = $db->where("client=$client")->update('recommend_live', array('list' => $list));
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
$list = isset($_POST['list']) ? trim($_POST['list']) : '';
$client = isset($_POST['client']) ? (int)$_POST['client'] : 2;


if (empty($uid) || empty($encpass) || empty($list)) {
    error(-1007);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}

$res = changeOrder($client,$list, $db);
if ($res) {
    succ();
} else {
    error(-1014);
}
