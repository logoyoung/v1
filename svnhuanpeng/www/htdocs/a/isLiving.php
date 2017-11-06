<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/27
 * Time: 上午11:57
 */


include '../init.php';
include_once INCLUDE_DIR.'Anchor.class.php';
include_once INCLUDE_DIR.'Live.class.php';

$db = new DBHelperi_huanpeng();

$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$deviceid = isset($_POST['deviceid']) ? trim($_POST['deviceid']) : '';

if(!$uid || !$enc || !$deviceid){
	error(-4013);
}

$anchor = new AnchorHelp($uid, $db);
if($loginError = $anchor->checkStateError($enc)){
	error($loginError);
}

$liveid = $anchor->isLiving();


$res = array(
    'isSuccess' => 1,
    'stat' => 1,
    'liveid' => 0,
    'info' => array()
);
if(!$liveid){
    if($liveid = $anchor->getLastLiveid()){
        $liveHelp = new LiveHelp($liveid, $db);
        $liveInfo = $liveHelp->getLiveInfo();
    }else{
        $liveInfo = array();
    }
}else{
    $res['liveid'] = $liveid;

    $liveHelp = new LiveHelp($liveid, $db);
    $liveInfo = $liveHelp->getLiveInfo();

    if($liveHelp->isCurrentLive($deviceid))
        $res['stat'] = 0;//继续直播
    else
        $res['stat'] = 2;//异地登录
}

$res['info'] = $liveInfo;

exit(json_encode(toString($res)));

?>


