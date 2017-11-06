<?php

require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
/**
 * 修改游戏类型
 * @author yandong@6room.com
 * date 2016-06-29  11:51
 */

function updateGameType($gametid, $icon, $db) {
    $data = array(
        'icon' => $icon
    );
    $res = $db->where("gametid=$gametid")->update('gametype', $data);
    if ($res !== false) {
        return true;
    } else {
        return false;
    }
   
}

$gametid = isset($_POST['gametid']) ? trim($_POST['gametid']) : '';
$img = isset($_POST['img']) ? trim($_POST['img']) : '';
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int)$_POST['type'] : 1;
if (empty($uid) || empty($encpass) || empty($type)) {
    error(-1007);
}
if(!is_numeric($type) || !is_numeric($uid)){
    error(-1023);
}
if (empty($gametid)) {
    return (-1007);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$res = updateGameType($gametid, $img, $db);
if ($res) {
    exit(json_encode(array('data' => '1')));
} else {
    exit(json_encode(array('data' => '0')));
}

