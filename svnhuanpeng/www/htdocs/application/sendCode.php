<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/11/16
 * Time: 上午10:51
 */

include '../../include/init.php';
include INCLUDE_DIR.'User.class.php';



$params = ['uid'=>'int','encpass'=>'string'];
$check = ['uid','encpass'];

foreach ($params as $k=>$v){
	$$k = isset($_POST[$k]) ? $_POST[$k] : '';
	$$k = $v == 'int' ? (int) $$k : trim($$k);

//	if(in_array($k, $check))
//		if(!)
	$param = $$k;
	if(in_array($k, $check) && !$param){
//		print_r(in_array($k, $check));
//		var_dump(!($$K));
//		var_dump(!135);
//		var_dump(!'135');
//		echo $k .'values is '.$$k;
		error2(-4013);
	}

}

$db = new DBHelperi_huanpeng();

$userHelp = new UserHelp($uid, $db);
if($code =$userHelp->checkStateError($encpass))
	error2($code);


$phone = $userHelp->getPhoneCertifyInfo();
if(!$phone['status'])
	error2(-5026);

$applicationKey = 'application:'.$uid;
$redis = new RedisHelp();

//print_r($phone['phone']);

//不要重复发送
if($redis->get($applicationKey))
	error2(-5037);

$mobile = $phone['phone'];

$mobile_code = random(6, 1);
$codeid = '6' . date('mdH') . random(2, 1);

$codetype = 123;

$key = "ekxklhuangTSDpengfkjekldc";

$data = array(
	'appid' => 102,
	'code' => (int) $mobile_code,
	'codeid' => (int) $codeid,
	'mobile' => $mobile,
	'type' => $codetype
);

$str = json_encode($data);
$sign = md5($str . $key);
$sendurl = "http://dev.liveuser.6.cn/api/pubSendSmsCodeApi.php?appid=102&type=$codetype&code=$mobile_code&mobile=$mobile&codeid=$codeid&sign=$sign";
//exit(json_encode(array('code'=>1)));
$res = file_get_contents($sendurl);

if($res){
	$r = jsond($res);
	if($r->resuNo == 1){
		$redis->set($applicationKey, $mobile_code, 1800);
		succ();
	}else{
		error2(-5038);
	}
}else{
	error2(-5038);
}