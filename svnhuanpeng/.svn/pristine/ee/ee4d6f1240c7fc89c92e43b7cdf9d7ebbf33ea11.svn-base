<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/1/14
 * Time: 上午10:34
 */

include '../../../../include/init.php';

session_start();

$mobile = trim($_POST['mobile']);

$code = checkMobile($mobile);
if(!$code)
	exit(json_encode(array('err'=>-1)));

$send_code = strtolower($_POST['send_code']);

if(empty($_SESSION['send_code']) or $send_code != $_SESSION['send_code'])
{
	//防止用户恶意请求
	exit(json_encode(array('err'=>-114)));//请求超时，请刷新页面后重试
}
unset($_SESSION['send_code']);

$mobile_code = random(6, 1);
$codeid = '6'.date('mdH').random(2,1);

$codetype = 123;

$key = "ekxklhuangTSDpengfkjekldc";

$data = array(
	'appid' => 102,
	'code' => (int)$mobile_code,
	'codeid' => (int)$codeid,
	'mobile' => $mobile,
	'type' => $codetype
);

$str = json_encode($data);

$sign = md5($str . $key);

$sendurl = "http://dev.liveuser.6.cn/api/pubSendSmsCodeApi.php?appid=102&type=$codetype&code=$mobile_code&mobile=$mobile&codeid=$codeid&sign=$sign";

//exit(json_encode(array('code'=>1)));
$res = file_get_contents($sendurl);

if($res){
	$r = json_decode($res, true);
	if($r['resuNo'] == 1){
		$_SESSION['mobile'] = $mobile;
		$_SESSION['mobile_code'] = $mobile_code;
		$_SESSION['codeid'] = $data['codeid'];
		exit(json_encode(array('code'=>1)));
	}else{
		exit(json_encode(array('code'=>0,'desc'=>$r['resuMsg'],'url'=>$sendurl,'str'=>$str)));
	}
}else{
	exit(json_encode(array('code'=>-1, 'desc'=>'url error')));
}