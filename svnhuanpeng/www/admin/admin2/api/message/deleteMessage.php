<?php

require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
/**
 * 站类信列表
 * @author yandong@6room.com
 * date 2016-07-12  11:14
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_admin();
function  deleteMessage($mid,$db){
    if(empty($mid)){
        return false;
    }
    $res=$db->where("id=$mid")->delete("sysmessage");
    if($res !==false){
      return '1';
    }else{
      return '0'; 
    }
}
$uid = isset($_POST['uid']) ? (int)($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim(($_POST['encpass'])) : '';
$mid = isset($_POST['mid']) ? (int)($_POST['mid']) : '';
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
if(empty($mid)){
    error(-1007);
}
$res=deleteMessage($mid,$db);
succ($res);





