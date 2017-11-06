<?php

require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
function getAdvertisementNumber($adtype, $location, $db)
{
    $where = 1;
    if (in_array($adtype, array(0, 1))) {
        $where .= " and type=$adtype ";
    }
    if (in_array($location, array(0, 1))) {
        $where .= " and location =$location";
    }

    $res = $db->field('status,count(*) as num')->where("$where  group by status")->select('admin_advertisement');
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
$adtype = isset($_POST['adtype']) ? (int)($_POST['adtype']) : 0;
$location = isset($_POST['location']) ? (int)($_POST['location']) : 0;
if (empty($uid) || empty($encpass)) {
    error(-1007);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$res = getAdvertisementNumber($adtype, $location, $db);
if (false !== $res) {
    if (!empty($res)) {
        $wait = $public = $delete = 0;
        foreach ($res as $v) {
            if ($v['status'] == 0) {
                $wait = $v['num'];
            }
            if ($v['status'] == 1) {
                $public = $v['num'];
            }
            if ($v['status'] == 2) {
                $delete = $v['num'];
            }
        }
        succ(array('wait' => $wait, 'public' => $public, 'delete' => $delete));
    } else {
        succ(array('wait' => 0, 'public' => 0, 'delete' => 0));
    }

} else {
    error(-1014);
}



