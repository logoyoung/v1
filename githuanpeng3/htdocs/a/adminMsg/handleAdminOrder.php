<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/7/11
 * Time: 下午2:43
 */

include '../../init.php';
include_once INCLUDE_DIR."LiveRoom.class.php";
include_once INCLUDE_DIR."Live.class.php";

$db = new DBHelperi_huanpeng();
$request = array('luid' => 'int', 'liveid'=>'int','order' => 'int', "tm" => 'int', 'reason' => "str", 'sign' => 'str');

foreach ($request as $key => $val) {
    if ($val == 'int') {
        $$key = isset($_GET[$key]) ? (int)$_GET[$key] : 0;
    } else {
        $$key = isset($_GET[$key]) ? trim(urldecode($_GET[$key])) : '';
    }

    if (!$$key) {
        mylog('**handleAdminOrder: '.$key.' is empty', LOGFN_SEND_MSG_ERR);
        error(-4013);
    }

}

//if(!verifySign($_GET, MSG_ADMIN)){
//    error(-4024);
//}

$lroom = new LiveRoom($luid, $db);
$live = new LiveHelp($liveid, $db);
$array=array('luid'=>$luid,'order'=>$order,'reason'=>$reason,'sign'=>$sign);
if($order == 1){
    $content = array(
        't' => '540',
        'reason' => $reason
    );
    $res=$lroom->sendUserMsg($luid, json_encode($content));
}else if($order == 2){
    $content = array(
        't' => '541',
        'reason' => $reason,
        'luid' => $luid
    );
    $lroom->sendRoomMsg(json_encode(toString($content)));
    $live->stopLive(1);
    $live->addLive2VideoRecord(VIDEO_SAVETYPE_CALL);
    $lroom->stop($liveid);

}else if($order == 3){

    $content = array(
        't' => '542',
        'reason' => $reason,
        'luid' => $luid
    );
    $lroom->sendRoomMsg(json_encode(toString($content)));
    $live->stopLive(2);
    $live->addLive2VideoRecord(VIDEO_SAVETYPE_CALL);
    $lroom->stop($liveid);
}

exit(json_encode(array('isSuccess'=>1)));