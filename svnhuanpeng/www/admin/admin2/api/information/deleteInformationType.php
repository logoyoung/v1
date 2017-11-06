<?php

require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
/**
 * 删除资讯类型
 * @author yandong@6room.com
 * date 2016-11-23  16:23
 */
$db = new DBHelperi_admin();
/**
 * @param $id  资讯类型id
 * @param $db
 * @return bool
 */
function deleteInforMationType($id,$db)
{
    $res = $db->where("id in ($id)")->update('admin_information_type',array('status'=>2));
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
if (empty($uid) || empty($encpass)  || empty($id)) {
    error(-1007);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$res = deleteInforMationType(trim($id,','),$db);
if ($res) {
    succ();
} else {
    error(-1014);
}



