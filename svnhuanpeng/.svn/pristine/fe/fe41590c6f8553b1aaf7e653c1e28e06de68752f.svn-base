<?php

include '../init.php';

/*
 * 我的空间发布&&未发布录像详情
 * date 2016-5-19 16:30
 * author yandong@6rooms.com
 * version 0.0
 */
$db = new DBHelperi_huanpeng();

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) ($_POST['uid']) : 2158;
$encpass = isset($_POST['encpass']) ? $_POST['encpass'] : '9db06bcff9248837f86d1a6bcf41c9e7';
if(empty($uid)||empty($encpass)){
    error(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error($s);
}
$videoLimit=getAuchorVideoLimit($uid, $db);
$publish=getAnchorPublishVideo($uid, $db);
$noPublish=getAnchorCheckVideo($uid, $db);
exit(json_encode(array('limit'=>$videoLimit,'publish'=>$publish,'unpublish'=>$noPublish))); 

