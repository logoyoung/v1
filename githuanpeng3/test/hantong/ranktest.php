<?php
error_reporting(E_ALL ^ E_NOTICE);

include __DIR__."/../../include/init.php";


use \lib\LiveRoom;



$t = 103;

$liveid = 656469;

$luid = 96767;


$uid = $argv[1];

$gid = $argv[2];

$userObj = new \lib\User($uid);

$encpass = $userObj->getUserEncpass();

var_dump($encpass);

$content = [
	't' => $t,
	'liveid' => $liveid,
	'gid'=> $gid,
	'enc' => $encpass,
	'sendType' => 1,
	'packid' => 123123
];


var_dump($content);

$liveRoom  = new LiveRoom($luid);

$r = $liveRoom->sendGift($uid,$content);

var_dump($r);