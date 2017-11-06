<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/10/12
 * Time: 上午10:18
 * #2017-05-18 10:29:59  修改显示规则:审核不同过显示原图片,其它状态显示当前图片  
 */

include '../../../../include/init.php';
include INCLUDE_DIR.'User.class.php';

$db = new DBHelperi_huanpeng();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

$uid = (int)$_COOKIE['_uid'] ? (int)$_COOKIE['_uid'] : 0;
$enc = trim($_COOKIE['_enc']) ? trim($_COOKIE['_enc']) : '';

if($_GET['uid']){
    $uid = (int)$_GET['uid'] ? (int)$_GET['uid'] : 0;
}else{
    $uid = (int)$_COOKIE['_uid'] ? (int)$_COOKIE['_uid'] : 0;
}

if($_GET['enc']){
    $enc = trim($_GET['enc']) ? trim($_GET['enc']) : 0;
}else{
    $enc = trim($_COOKIE['_enc']) ? trim($_COOKIE['_enc']) : '';
}

header("Content-type: image/png");
if($uid && $enc){
    $userHelp = new UserHelp($uid,$db);
    if(!$userHelp->checkStateError($enc)) {

        $sql = "select pic,status from admin_user_pic where uid = $uid";
        $res = $db->query($sql);
        $row = $res->fetch_assoc();
        if(in_array($row['status'],[USER_PIC_UNPASS,USER_PIC_AUTO_UNPASS])){
            $res = $db->field('pic')->where("uid={$uid}")->select('userstatic');
            $row = $res[0];
        }
        echo file_get_contents( DOMAIN_PROTOCOL . $conf['domain-img'] . '/' . $row['pic']);
        exit;
    }
}



