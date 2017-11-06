<?php

/**
 * 审核昵称
 * yandong@6rooms.com
 * date 2016-10-19 11:07
 * 
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();


function updateLivebulletin($uids,$status,$db){
    if(empty($uids) ){
        return false;
    }
    $res=$db->where("luid in ($uids)")->update('livebulletin',array('status'=>$status));

    if(false !==$res){
        return true;
    }else{
        return false;
    }
}
function updateAdminLivebulletin($uids,$adminid,$status,$db){
    if(empty($uids) ||empty($adminid)){
        return false;
    }
    $res=$db->where("luid in ($uids)  and adminid=$adminid")->update('admin_livebulletin',array('status'=>$status));
    if(false !==$res){
        return true;
    }else{
        return false;
    }
}


/**
 * 修改审核状态
 * @param string $succluid  主播id 多个的话用逗号隔开
 * @param type $db
 * @return boolean
 */
function  UpdateNoticeStatus($uid, $succluid, $failluid, $db) {
    if (empty($succluid) && empty($failluid)) {
        return false;
    }
    if ($succluid) {
        $sres=updateAdminLivebulletin($succluid,$uid,1,$db);
        $slres=updateLivebulletin($succluid,1,$db);
        if($sres && $slres){
            if($failluid){
                $fres=updateAdminLivebulletin($failluid,$uid,2,$db);
                $flres=updateLivebulletin($failluid,2,$db);
                if($fres && $flres){
                    return true;
                }else{
                    return false;
                }
            }else{
                return true;
            }
        }else{
            return false;
        }
    } else {
        if ($failluid) {
            $fres=updateAdminLivebulletin($failluid,$uid,2,$db);
            $flres=updateLivebulletin($failluid,2,$db);
            if($fres && $flres){
                return true;
            }else{
                return false;
            }
        }
    }
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int) $_POST['type'] : 1;
$succluid = isset($_POST['succList']) ? trim($_POST['succList']) : ''; //主播id列表批量可用逗号隔开(可通过的)
$failluid = isset($_POST['failedList']) ? trim($_POST['failedList']) : ''; //主播id列表批量可用逗号隔开(不合格的)
if (empty($uid) || empty($encpass) || empty($type)) {
    error(-1007);
}
if (empty($succluid) && empty($failluid)) {
    error(-1007);
}

$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}

$res = UpdateNoticeStatus($uid, $succluid, $failluid, $db);
if ($res) {
    succ();
} else {
    error(-1014);
}
