<?php

require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
/**
 * 修改游戏
 * @author yandong@6room.com
 * date 2016-09-08  15:15
 */

$gameid = isset($_POST['gid']) ? trim($_POST['gid']) : '';
$gtype = isset($_POST['gtype']) ? (int) ($_POST['gtype']) : '';
$poster = isset($_POST['poster']) ? trim($_POST['poster']) : '';
$icon = isset($_POST['icon']) ? trim($_POST['icon']) : '';
$bg = isset($_POST['bg']) ? trim($_POST['bg']) : '';
$desc = isset($_POST['desc']) ? trim($_POST['desc']) : '';
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int)$_POST['type'] : 1;
if (empty($uid) || empty($encpass) || empty($type)) {
    error(-1007);
}
if(!is_numeric($type) || !is_numeric($uid)){
    error(-1023);
}
if (empty($gtype)) {
    error(-1007);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
if (empty($gameid)) {
    return false;
}
if (!empty($gtype)) {
    $data['gametid'] = $gtype;
}
if (!empty($poster)) {
    $data['poster'] = $poster;
}
if (!empty($icon)) {
    $data['icon'] = $icon;
}
if (!empty($bg)) {
    $data['bgpic'] = $bg;
}
if (!empty($desc)) {
    $data['description'] = $desc;
}

/**
 * 修改game 表 主要修改游戏类型
 * @param type $gameid  游戏id
 * @param type $data  
 * @param type $db
 * @return boolean
 */
function updateGame($gameid, $data, $db) {
    if (empty($data)) {
        return false;
    }
    $res = $db->where('gameid=' . $gameid)->update('game', $data);
    if ($res !== false) {
        return true;
    } else {
        return false;
    }
}

$res = updateGame($gameid, $data, $db);
if ($res) {
    exit(json_encode(array('data' => '1')));
} else {
    exit(json_encode(array('data' => '0')));
}
 
 
