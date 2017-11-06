<?php
include '../init.php';
require(INCLUDE_DIR . 'Cache.class.php');
/**
 * 检测手机号是否已存在
 * author yandong@6rooms.com
 * date 2016-06-07 12:07
 * copyright@6.cn version 0.0
 */
$db = new DBHelperi_huanpeng();
/**
 * @param type $mobile 手机号码
 * @param type $db
 * @return boolean
 */
function checkUserMobile($mobile, $db) {
    if (empty($mobile)) {
        return false;
    }
    $res = $db->where(" phone=$mobile")->limit(1)->select('userstatic');
    if ($res !== false) {
        return true;
    } else {
        return false;
    }
}

/**
 * start
 */
$mobile = isset($_POST['mobile']) ? (int) trim($_POST['mobile']) : '';
if (empty($mobile)) {
    error(-4056);
}
$mobile = checkMobile($mobile);
if(true !==$mobile){
    error(-4058);
}
$res = checkUserMobile($mobile, $db);
if ($res) {
    exit(jsone(array('isSuccess' => '1')));
} else {
    exit(jsone(array('isSuccess' => '0')));
}


//$demo=new Cache();
//$luid = isset($_POST['luid']) ? (int)($_POST['luid']) : '';
//$gameid = isset($_POST['gameid']) ? (int)($_POST['gameid']) : '';
//$page = isset($_POST['page']) ? (int)($_POST['page']) : 1;
//$size = isset($_POST['size']) ? (int)($_POST['size']) : 3;
//
//// $res=$demo->getHotLive($luid, $gameid, $page,$size);
//// var_dump($res);
//// $res=$demo->getMostFollowLive($luid, $gameid, $page, $size);
//// var_dump($res);
//  $res1=$demo->getNewLive($luid, $gameid,$page,$size);
// var_dump($res1);
////$demo->flushAll();
////$res1=$demo->startLive($luid, $gameid);
////var_dump($res1);
////$res=$demo->enterLiveRoom($luid);
////var_dump($res);
//// $res3=$demo->endtLive($luid, $gameid);
//// var_dump($res3);
//////$demo->quitLiveRoom($luid);
////$demo->userFollow($luid);
//$res2=$demo->getKey($gameid);
//var_dump($res2);
