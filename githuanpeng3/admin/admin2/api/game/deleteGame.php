<?php
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
/**
 * 删除游戏
 * @author yandong@6room.com
 * date 2016-06-24  11:11
 */

/**
 * 删除游戏 [逻辑删除]
 * @param type $gameid 游戏id
 * @param type $db
 * @return boolean
 */
function deleteGame($gameid, $db) {
    if (empty($gameid)) {
        return false;
    }
    $res = $db->where("gameid=$gameid")->update('game', array('status'=>1));
    if ($res !== false) {
        $zres = $db->where("gameid=$gameid")->update('game_zone', array('status'=>1));
        if ($zres !== false) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/**
 * start
 */
$gameid = isset($_POST['gameid']) ? (int) $_POST['gameid'] : '';
if (empty($gameid)) {
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

$gameid = checkInt($gameid);
$res = deleteGame($gameid, $db);
if ($res) {
    exit(json_encode(array('data' => '1')));
} else {
    exit(json_encode(array('data' => '0')));
}






