<?php

require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
/**
 * 添加游戏类型
 * @author yandong@6room.com
 * date 2016-06-27  17:51
 */
function addGameType($typename, $img, $db) {
    $data = array(
        'name' => $typename,
        'icon' => $img
    );
    $res = $db->insert('gametype', $data);
    if ($res !== false) {
       return true;
    }else{
       return false;  
    }
}

$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '';
$typename = isset($_POST['typename']) ? trim($_POST['typename']) : '';
$img=isset($_POST['img']) ? trim($_POST['img']) : '';
if (empty($uid) || empty($encpass) || empty($type) || empty($typename)) {
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
$res = addGameType($typename, $img, $db);
exit(json_encode(array('data' => $res)));



