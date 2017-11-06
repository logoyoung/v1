<?php

include '../init.php';
/*
 * 发布录像
 * date 2016-04-18 18:30
 * author yandong@6rooms.com
 * version 0.0
 */
$db = new DBHelperi_huanpeng();

/**
 * 发布录像
 * @param int $uid
 * @param int $videoId
 * @param object $db
 * @return type
 */
function publishOrCancel($uid, $videoId, $type, $db) {
    if ($type == 1) {//发布
        $datas = array(
            'status' => VIDEO_UNPUBLISH
        );
    }
//    if ($type == 2) {//撤销发布
//        $datas = array(
//            'status' => VIDEO_WAIT
//        );
//    }
    $res = $db->where("uid=$uid and videoid=$videoId")->update('video', $datas);
    return $res;
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) ($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$videoId = isset($_POST['videoID']) ? (int) ($_POST['videoID']) : '';
$type = isset($_POST['type']) ? (int) ($_POST['type']) : '';

if (empty($uid) || empty($encpass) || empty($videoId) || empty($type) || !in_array($type, array(1))) {
    error(-4013);
}
$uid = checkInt($uid);
$type = checkInt($type);
$encpass = checkStr($encpass);
$videoId = checkInt($videoId);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error($s);
}
$isAnchor=checkUserIsAnchor($uid, $db);
if(empty($isAnchor)){
    error(-4057);
}
$getLimit=getAuchorVideoLimit($uid, $db);//获取发布录数
$getpublish=getAnchorAlreadyPublishVideo($uid, $db);//获取已发布的录像数
if((int)$getpublish >= (int)$getLimit){
    error(-5024);exit;
}
$result = publishOrCancel($uid, $videoId, $type, $db);
if ($result) {
    synchroAdminWiatPassVideo($videoId,$db);//同步到admin_wait_pass_video
    exit(jsone(array('isSuccess' => 1)));
} else {
    exit(jsone(array('isSuccess' => 0)));
}

