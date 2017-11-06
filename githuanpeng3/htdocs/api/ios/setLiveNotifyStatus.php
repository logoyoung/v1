<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/11/10
 * Time: 下午6:07
 */

include '../../../include/init.php';
include INCLUDE_DIR.'User.class.php';
$db = new DBHelperi_huanpeng();

$params = ['uid'=>'int','encpass'=>'string','deviceToken'=>'string','luid'=>'int', 'opt'=>'int'];

$checkParams = ['uid', 'encpass', 'deviceToken','luid'];

foreach ($params as $key => $val){
	$$key = isset($_POST[$key]) ? $_POST[$key] : '';
	$$key = $val == 'int' ? (int)$$key : trim($$key);
}

foreach ($checkParams as $val){
	if(!$$val) error2(-2004);
}

$userHelp = new UserHelp($uid, $db);
if($code = $userHelp->checkStateError($encpass)){
	error2($code);
}

$opt = $opt ? 1 : 0;

if($userHelp->setLiveNotify($luid, $opt) && $userHelp->setIphonePushNotify($deviceToken,$opt)){
	succ();
}else{
	error2(-5031,1);
}

