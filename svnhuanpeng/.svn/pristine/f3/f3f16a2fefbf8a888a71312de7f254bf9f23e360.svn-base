<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/6/23
 * Time: 下午3:42
 */

include '../../init.php';
include INCLUDE_DIR . 'LiveRoom.class.php';
include_once INCLUDE_DIR . 'Anchor.class.php';
include_once INCLUDE_DIR . 'Live.class.php';

$db = new DBHelperi_huanpeng();

$requestParam = array('uid'=>'int', 'encpass' =>'str', 'liveid' =>'int');
foreach($requestParam as $param => $type){
    $$param = isset($_POST[$param]) ? trim($_POST[$param]) : '';
    $$param = $type == 'int' ? (int)$$param : $$param;
    if(!$$param) error(-4013);
}


$anchor = new AnchorHelp($uid);
if($loginError = $anchor->checkStateError($encpass)){
    error($loginError);
}

if(!$anchor->isAnchor()){
    error(-4057);
}

$lastLiveId = $anchor->getLastLiveid();
if($lastLiveId != $liveid){
    error(-4013);
}

$live = new LiveHelp($liveid, $db);



if(!$live->isClientCreateStatus()){
    error(-5007);
}



if($live->clientStart()){
    $liveroom = new LiveRoom($uid, $db);
    $liveroom->start($liveid);

    exit(json_encode(array('isSuccess' => 1)));
}else{
    error(-5007);
}