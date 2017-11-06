<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/10/12
 * Time: 上午10:18
 */

include '../../init.php';
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


if($uid && $enc){
    $userHelp = new UserHelp($uid,$db);
    if(!$userHelp->checkStateError($enc)) {

        $sql = "select pic from admin_user_pic where uid = $uid";
        $res = $db->query($sql);
        $row = $res->fetch_assoc();
        echo file_get_contents( "http://" . $conf['domain-img'] . '/' . $row['pic']);
        exit;
    }
}



