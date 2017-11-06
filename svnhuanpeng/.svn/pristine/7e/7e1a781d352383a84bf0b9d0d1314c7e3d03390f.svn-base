<?php

require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
/**
 * 站类信详情
 * @author yandong@6room.com
 * date 2016-07-12  16:14
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_admin();
function  getMessageInfo($mid,$db){
    $list=array();
    $res=$db->where("id=$mid")->select("sysmessage");
    if($res !==false){
        foreach($res as $v){
            $temp['id']=$v['id'];
            $temp['title']=$v['title'];
            $temp['msg']=$v['msg'];
            $temp['stime']=$v['stime'];
            array_push($list,$temp);
        }
    }
    return $list;
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
$res=getMessageInfo($mid,$db);
succ($res);





