<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/5/9
 * Time: 下午5:51
 */

include '../init.php';
include_once INCLUDE_DIR.'Live.class.php';
include_once INCLUDE_DIR.'Anchor.class.php';

$db = new DBHelperi_huanpeng();

$type = isset($_GET['type']) ? trim($_GET['type']) : '';
$liveid = isset($_GET['liveid']) ? (int)$_GET['liveid'] : 0;
$luid = isset($_GET['luid']) ? (int)$_GET['luid'] : 0;

if(!$type || $type !='timeout' || !$liveid || !$luid)
    error(-4013);


$anchor = new AnchorHelp($luid, $db);

$currLiveID = $anchor->isLiving();
if(!$currLiveID || $liveid != $currLiveID){
//    error(-4045);//主播与直播ID 不匹配
}

if(!verifySign($_GET, TIMEOUT_STOPLIVE_KEY)){
    error(-4024);
}


$liveHelp = new LiveHelp($liveid, $db);

if($liveHelp->stopLive()){
    $liveHelp->addLive2VideoRecord(VIDEO_SAVETYPE_TIMEOUT);
    $lroom = new LiveRoom($luid);
    $lroom->stop($liveid);
    exit(jsone(toString(array('isSuccess' => 1))));
}