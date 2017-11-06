<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/1/24
 * Time: 下午4:58
 */
/**
 * 编辑公告
 */

include '../../../../include/init.php';
$db = new DBHelperi_huanpeng();

function  addLivebulletin($uid,$db){
    $sql = "insert into admin_livebulletin (luid,adminid,status) value($uid,0,0) on duplicate key update adminid=0,status=0";
    $res=$db->query($sql);
    if(false !==$res){
        return true;
    }else{
        return false;
    }
}
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$bulletin = isset($_POST['bulletin']) ? trim($_POST['bulletin']) : '';
if(!$uid || !$enc){
    error2(-4013);
}

if(!$bulletin){
  error2(-4053,2);
}
$code = checkUserState($uid, $enc, $db);
if($code !== true){
    error2(-4067,2);
}

$bulletin = $db->realEscapeString($bulletin);
$bulletin = xss_clean($bulletin);
$checkNoticeMode = checkMode(CHECK_NOTICE, $db);
if($checkNoticeMode){//检测公告审核模式
    $status=1;
}else{
    $status=0;
}
$sql = "insert into livebulletin (luid, bulletin,status) value($uid, '$bulletin',$status) on duplicate key update bulletin='$bulletin',status=$status";
$db->query($sql);
addLivebulletin($uid,$db);//添加到审核表
succ();
