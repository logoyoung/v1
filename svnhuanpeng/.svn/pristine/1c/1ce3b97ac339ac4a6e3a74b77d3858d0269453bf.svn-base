<?php

require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
function getAdvertisementInfo($id, $db)
{
    if (empty($id)) {
        return false;
    }
    $res = $db->field('id,type,location,url,poster,ctime,luid,click,status')->where("id=$id")->select('admin_advertisement');
    if ($res !== false) {
        if (empty($res)) {
            return array();
        } else {
            return $res;
        }
    } else {
        return false;
    }
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '1';
$id = isset($_POST['id']) ? (int)($_POST['id']) : 0;
if (empty($uid) || empty($encpass) || empty($id)) {
    error(-1007);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}

$res = getAdvertisementInfo($id, $db);
if (false !== $res) {
    if (!empty($res)) {
        foreach ($res as $v) {
            $temp['id'] = $v['id'];
            $temp['adtype'] = $v['type'];
            $temp['location'] = $v['location'];
            $temp['url'] = $v['url'];
            $temp['poster'] = $v['poster'] ? "http://" . $conf['domain-img'] . '/' . $v['poster'] : '';
            $temp['ctime'] = $v['id'];
            $temp['luid'] = $v['luid'];
            $temp['click'] = $v['click'];
            $temp['status'] = $v['status'];
        }
        succ($temp);
    } else {
        succ(array());
    }
} else {
    error(-1014);
}



