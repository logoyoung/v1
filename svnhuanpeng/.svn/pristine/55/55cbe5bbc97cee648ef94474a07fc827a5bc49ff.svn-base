<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/11/16
 * Time: 上午11:26
 */

include '../../include/init.php';
include INCLUDE_DIR.'User.class.php';



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
if($code =$userHelp->checkStateError($encpass))
	error2($code);

$applicationKey = 'application:'.$uid;
$redis = new RedisHelp();


if($redis->get($applicationKey) != $mobileCode)
	error2(-4031, 2);

succ();