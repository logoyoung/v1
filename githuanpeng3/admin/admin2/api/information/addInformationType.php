<?php

require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
/**
 * 添加资讯类型
 * @author yandong@6room.com
 * date 2016-11-23  14:23
 */
function  addInforMationType($uid,$name, $status, $db){
    $data = array(
        'adminid'=>$uid,
        'name' => $name,
        'status' => $status
    );
    $res = $db->insert('admin_information_type', $data);
    if ($res !== false) {
       return true;
    }else{
       return false;  
    }
}

$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '1';
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$status=isset($_POST['status']) ? trim($_POST['status']) : '0';
if(empty($uid) || empty($encpass)||empty($name)){
    error(-1007);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$name=filterWords($name);
$res = addInforMationType($uid,$name, $status, $db);
if($res){
    succ();
}else{
    error(-1014);
}



