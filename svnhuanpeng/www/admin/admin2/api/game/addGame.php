<?php

require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
/**
 * 添加游戏
 * @author yandong@6room.com
 * date 2016-06-29  14:51
 */
function addGame($gname, $gtype, $poster, $gamepic, $bg, $desc, $status, $db) {
    $data = array('name' => $gname, 'gametid' => $gtype);
    $res = $db->insert('game', $data);
    if ($res !== false) {
        $zone_data = array(
            'gameid' => $res,
            'bgpic' => $bg,
            'gamepic' => $gamepic,
            'poster' => $poster,
            'description' => $desc,
            'status' => $status
        );
        $res = $db->insert('game_zone', $zone_data);
        if ($res !== false) {
            return '1';
        } else {
            return '0';
        }
    } else {
        return '0';
    }
}

$gname = isset($_POST['gname']) ? trim($_POST['gname']) : '';
$gtype = isset($_POST['gtype']) ? (int) ($_POST['gtype']) : '';
$poster = isset($_POST['poster']) ? trim($_POST['poster']) : '';
$gamepic = isset($_POST['gamepic']) ? trim($_POST['gamepic']) : '';
$bg = isset($_POST['bg']) ? trim($_POST['bg']) : '';
$desc = isset($_POST['desc']) ? trim($_POST['desc']) : '';
$status = isset($_POST['status']) ? trim($_POST['status']) : 0;
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int)$_POST['type'] : 1;
if (empty($uid) || empty($encpass) || empty($type)) {
    error(-1007);
}
if(!is_numeric($type) || !is_numeric($uid)){
    error(-1023);
}
if (empty($gname)) {
    error(-4013);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
if (!empty($gamepic)) {
    $gamepic = json_encode(explode(',', substr($gamepic, 0, -1)));
}
$res = addGame($gname, $gtype, $poster, $gamepic, $bg, $desc, $status, $db);
exit(json_encode(array('data' => $res)));
