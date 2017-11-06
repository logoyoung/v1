<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/27
 * Time: 下午1:19
 */



include '../init.php';
include_once INCLUDE_DIR.'Anchor.class.php';
include_once INCLUDE_DIR.'Live.class.php';
include_once INCLUDE_DIR.'LiveRoom.class.php';

$db = new DBHelperi_huanpeng();

$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$deviceid = isset($_POST['deviceid']) ? trim($_POST['deviceid']) : '';
$liveid = isset($_POST['liveid']) ? (int)$_POST['liveid'] : 0;


$title = isset($_POST['liveTitle']) ? trim($_POST['liveTitle']) : '';
$gameid = isset($_POST['gameID']) ? trim($_POST['gameID']) : '';
$gametid = isset($_POST['gameTypeID']) ? trim($_POST['gameTypeID']) : '';
$gamename = isset($_POST['gameName']) ? trim($_POST['gameName']) : '';
$quality = isset($_POST['videoQuality']) ? trim($_POST['videoQuality']) : '';
$orientation = isset($_POST['orientation']) ? trim($_POST['orientation']) :'';


if(!$uid || !$enc || !$deviceid || !$liveid ){
	error(-4013);
}

$anchor = new AnchorHelp($uid, $db);
if($loginError = $anchor->checkStateError($enc)){
	error($loginError);
}

$curLiveid = $anchor->isLiving();

if(!$curLiveid || $curLiveid != $liveid){
	error(-5015);
}


$liveHelp = new LiveHelp($liveid, $db);
if($liveHelp->isCurrentLive($deviceid) && $liveHelp->isCurrentConfig($title, $gameid, $gametid, $gamename, $quality, $orientation)){
	//继续直播
	if($liveHelp->continueLive($server, $stream)){
        $lroom = new LiveRoom($uid, $db);
        $followCount = $anchor->followCount();
        $userCount = $lroom->getRoomUserCount();
        if(!(int)$_POST['isClient'])
            $r = $lroom->start($liveid);

        exit(json_encode(toString(array('isSuccess' => 1,'server' => array($server),'stream' => $stream,'follow' => $followCount, 'userCount' => $userCount))));
    }
	error(-5007);
}else{
//	error(-5016);
    exit(json_encode(array(
        'code' => -5016,
        'isCurrentlive' => $liveHelp->isCurrentLive($deviceid),
        'isCurrentConfig' => $liveHelp->isCurrentConfig($title, $gameid, $gametid, $gamename, $quality, $orientation)
    )));
}