<?php


include __DIR__."/../../include/init.php";

use lib\MsgPackage;
use lib\SocketSend;

$db = new DBHelperi_huanpeng();


$uid = 8560;

$chatRoom = new \service\chatRoom\ChatRoomService();
$chatRoom->setUid($uid);

$luids = $chatRoom->getChatRoomIdByUid();


var_dump($luids);

$type = $argv[1];
$level = $argv[2];
$exp = $level;
$msg = MsgPackage::getLiveLengthExpRewardMsgSocketPackage($luids,$uid,$type, $level, $exp);
SocketSend::sendMsg($msg);

exit();

$luid = '96767';
$uid = '8560';
$num = 10;

//$msg = MsgPackage::getNewDueOrderNumMsgSocketPackage($luid,$uid,$num);



//SocketSend::sendMsg($msg,$db);

$type = $argv[1];
$level = $argv[2];

$exp = $level;

$msg  = MsgPackage::getLiveLengthExpRewardMsgSocketPackage($luid, $uid, $type, $level, $exp);
var_dump($msg);
$db = new DBHelperi_huanpeng(true);
SocketSend::sendMsg($msg);