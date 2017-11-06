<?php

require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
/**
 * 删除游戏类型
 * @author yandong@6room.com
 * date 2016-06-28  10:45
 */

/**
 * 删除游戏类型
 * @param type $gametid  游戏类型id
 * @param type $db
 * @return boolean
 */
function deleteGameType($gametid, $db) {
    if (empty($gametid)) {
        return false;
    }
    $res = $db->where("gametid=$gametid")->delete('gametype');
    if ($res !== false) {
        return true;
    } else {
        return false;
    }
}

/**
 * start
 */
$gametid = isset($_POST['gametid']) ? (int) $_POST['gametid'] : '';
if (empty($gametid)) {
    error(-1007);
}
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int)$_POST['type'] : 1;
if (empty($uid) || empty($encpass) || empty($type)) {
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

$gametid = checkInt($gametid);
$res = deleteGameType($gametid, $db);
if ($res) {
    exit(json_encode(array('data' => '1')));
} else {
    exit(json_encode(array('data' => '0')));
}






