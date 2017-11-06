<?php

require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';

/**
 * 添加站类信
 * @author yandong@6room.com
 * date 2016-07-13  12:14
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_admin();
function  addMessage($title,$msg,$type,$db){
    if(empty($title) || empty($msg)){
        return false;
    }
    $data=array(
        'title'=>$title,
        'msg'=>$msg,
        'type'=>$type
    );
    $res=$db->insert("sysmessage",$data);
    if($res !==false){
      return '1';
    }else{
      return '0'; 
    }
}
$uid = isset($_POST['uid']) ? (int)($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim(($_POST['encpass'])) : '';
$title = isset($_POST['title']) ? trim(($_POST['title'])) : '';
$msg = isset($_POST['msg']) ? trim(($_POST['msg'])) : '';
$type = isset($_POST['type']) ? (int)$_POST['type'] : 1;
if (empty($uid) || empty($encpass) || empty($type)) {
    error(-1007);
}
if(!is_numeric($type) || !is_numeric($uid)){
    error(-1023);
}
if(empty($title) || empty($msg)){
    error(-1007);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$res=addMessage($title,$msg,2,$db);
succ($res);