<?php
/**
 * 领取奖励
 * auchor  Dylan
 * date 2016-12-08 14:38
 */
include '../../../../include/init.php';
$db = new DBHelperi_huanpeng();




function  addToInviteTest($icode,$uid,$db){
    if(empty($icode)||empty($uid)){
        return false;
    }
    $sql = "INSERT INTO `inside_test_inviteRecoed` (`code`,`ruid`) VALUES ($icode, $uid) on duplicate key update code = $icode, ruid = $uid";
    $res = $db->doSql($sql);
    if(false !==$res){
        return true;
    }else{
        return false;
    }
}

function  checkExistUserByIcode($icode,$db){
    if(empty($icode)){
        return false;
    }
    $res=$db->where("uid=$icode")->select('anchor');
    if($res !==false && !empty($res)){
        return true;
    }else{
        return false;
    }
}



/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$icode = isset($_POST['icode']) ? (int)($_POST['icode']) : '';
if (empty($uid) || empty($encpass) ||empty($icode) ) {
    error2(-4013);
}
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error2(-4067,2);
}

if($icode){
    if(!is_numeric($icode)){
        error2(-4013,2);
    }
}
$ires=checkExistUserByIcode($icode,$db);
if(!$ires){
    error2(-4090,2);
}
$res = addToInviteTest($icode,$uid,$db);
if (false !== $res) {
    succ();
} else {
    error2(-5017,2);
}