<?php

require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
/**
 * 获取资讯类型
 * @author yandong@6room.com
 * date 2016-11-23  14:23
 */
function getInforMationType($base, $db)
{
    if ($base) {
        $status = '1';
        $field = 'id,name';
    } else {
        $status = '0,1';
        $field = 'id,name,ctime,status';
    }
    $res = $db->field("$field")->where("status in ($status)")->order('ctime DESC')->select('admin_information_type');
    if ($res !== false) {
        return $res;
    } else {
        return false;
    }
}

$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '1';
$base = isset($_POST['base']) ? trim($_POST['base']) : '0';
if (empty($uid) || empty($encpass)) {
    error(-1007);
}
if (!in_array($base, array(0, 1))) {
    error(-1023);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$res = getInforMationType($base, $db);
if ($res) {
    $list = array();
    foreach ($res as $v) {
        if ($base) {
            $temp['id'] = $v['id'];
            $temp['name'] = $v['name'];
        } else {
            $temp['id'] = $v['id'];
            $temp['name'] = $v['name'];
            $temp['ctime'] = $v['ctime'];
            $temp['status'] = $v['status'];
        }
        array_push($list, $temp);
    }
    succ(array('list' => $list));
} else {
    error(-1014);
}



