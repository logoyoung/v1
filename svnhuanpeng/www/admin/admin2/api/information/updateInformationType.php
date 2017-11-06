<?php

require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
/**
 * 修改资讯类型
 * @author yandong@6room.com
 * date 2016-11-23  16:23
 */
$db = new DBHelperi_admin();
/**
 * @param $id  资讯类型id
 * @param $name 类型名称
 * @param $status  状态
 * @param $db
 * @return bool
 */
function updateInforMationType($id,$status, $db)
{
    $data = array(
        'status' => $status
    );
    $res = $db->where("id=$id")->update('admin_information_type', $data);
    if ($res !== false) {
        return true;
    } else {
        return false;
    }
}

$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '1';
$id = isset($_POST['id']) ? trim($_POST['id']) : '';
$status = isset($_POST['status']) ? trim($_POST['status']) : '';
if (empty($uid) || empty($encpass)  || empty($id)) {
    error(-1007);
}
if($status === ''){
    error(-1023);
}
if(!in_array($status,array(0,1))){
    error(-1023);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$res = updateInforMationType($id,$status, $db);
if ($res) {
    succ();
} else {
    error(-1014);
}



