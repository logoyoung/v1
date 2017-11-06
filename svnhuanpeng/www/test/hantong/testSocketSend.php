<?php


include __DIR__."/../../include/init.php";

use lib\MsgPackage;
use lib\SocketSend;

$db = new DBHelperi_huanpeng();

$luid = '1895';
$uid = '8560';
$num = 10;

$msg = MsgPackage::getNewDueOrderNumMsgSocketPackage($luid,$uid,$num);



SocketSend::sendMsg($msg,$db);