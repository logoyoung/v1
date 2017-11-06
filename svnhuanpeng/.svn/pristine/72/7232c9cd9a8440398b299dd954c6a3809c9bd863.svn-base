<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/11/10
 * Time: 下午5:38
 */
include '../../../include/init.php';
include INCLUDE_DIR.'User.class.php';
$db = new DBHelperi_huanpeng();

$params = ['uid'=>'int','encpass'=>'string','deviceToken'=>'string','opt'=>'int'];


foreach ($params as $key => $val){
	$$key = isset($_POST[$key]) ? $_POST[$key] : '';
	$$key = $val == 'int' ? (int)$$key : trim($$key);
}

if($opt){
	$checkParams = ['uid', 'encpass', 'deviceToken'];
}else{
	$checkParams = ['uid','encpass'];
}

foreach ($checkParams as $val){
	if(!$$val) error2(-2004);
}


$userHelp = new UserHelp($uid, $db);

if($code = $userHelp->checkStateError($encpass)){
	error2($code);
}

$opt = $opt ? 1 : 0;


if($userHelp->setIphonePushNotify($deviceToken,$opt) && $userHelp->setNotifyStatus($opt))
	succ();
else
	error2(-5031,1);