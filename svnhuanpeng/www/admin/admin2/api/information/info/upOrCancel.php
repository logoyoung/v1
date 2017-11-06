<?php

require '../../../includeAdmin/init.php';
require '../../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();


function changeRecommendInfor($itype, $list,$db)
{
    if (empty($itype) || empty($list)) {
        return false;
    }
    $res = $db->where("id=$itype")->update('recommend_information',array('list'=>$list));
    if (false !== $res) {
            return true;
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
$itype = isset($_POST['itype']) ? (int)($_POST['itype']) : '';
$list = isset($_POST['list']) ? trim($_POST['list']) : '';
if (empty($uid) || empty($encpass)) {
    error(-1007);
}
if (empty($itype)) {
    error(-1007);
}
if (!in_array($itype, array(1, 2))) {
    error(-1023);
}
if(empty($list)){
    error(-1007);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$res = changeRecommendInfor($itype, $list,$db);
if ($res) {
    succ();
} else {
    error(-1014);
}



