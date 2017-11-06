<?php

include '../../../include/init.php';

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
$uid = isset($_POST['uid']) ? (int) ($_POST['uid']) : "";
$encpass = isset($_POST['encpass']) ? $_POST['encpass'] : '';
if(empty($uid)||empty($encpass)){
    error2(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error2(-4067,2);
}
$videoLimit=getAuchorVideoLimit($uid, $db);
$publish=getAnchorPublishVideo($uid, $db);
$noPublish=getAnchorCheckVideo($uid, $db);
succ(array('limit'=>$videoLimit,'publish'=>$publish,'unpublish'=>$noPublish));

