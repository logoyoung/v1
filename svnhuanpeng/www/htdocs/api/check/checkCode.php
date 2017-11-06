<?php
/**
 *申请主播验证码校验
 *
 */

include '../../../include/init.php';
include INCLUDE_DIR.'User.class.php';
require_once(INCLUDE_DIR . 'bussiness_flow.fun.php');

$params = ['uid'=>'int','encpass'=>'string','mobileCode'=>'string'];
$check = ['uid','encpass','mobileCode'];

foreach ($params as $k=>$v){
	$$k = isset($_POST[$k]) ? $_POST[$k] : '';
	$$k = $v == 'int' ? (int) $$k : trim($$k);
	$param = $$k;
	if(in_array($k, $check) && !$param)
		error2(-4013);
}
$db = new DBHelperi_huanpeng();
$userHelp = new UserHelp($uid, $db);
if($code =$userHelp->checkStateError($encpass)){
    error2(-4067,2);
}

$applicationKey = 'application:'.$uid;
$redis = new RedisHelp();

if($redis->get($applicationKey) != $mobileCode){
    error2(-4031, 2);
}
//同步主播信息
if(false === RN_MODEL){
    hpBizFun\applyToAuthor($uid,$db,RN_MODEL);
}
succ();