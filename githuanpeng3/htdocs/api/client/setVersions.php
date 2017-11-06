<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/11/18
 * Time: 下午5:34
 */

include '../../../include/init.php';

function setClientVersionsBuildKeys($data,$secretKey = 'client_set_version_key@KSZ14%003'){
	if (!isset($data['sign']) || !$data['sign']) {
		return false;
	}
	//验证请求有无时间戳
	if (!isset($data['tm']) || !$data['tm']) {
		return false;
	}
	//验证请求，3分钟实效
//	if (time() - $data['tm'] > 180 || time() - $data['tm'] < 0) {
//		return false;
//	}
	$sign = $data['sign'];
	unset($data['sign']);
	ksort($data);
	foreach ($data as $key => $val) {
		$data[$key] = urldecode($val);
	}
//    $tmpdata = json_encode($data,JSON_UNESCAPED_UNICODE);
	$tmpdata = json_encode($data);
	$sign2 = md5(sha1($tmpdata . $secretKey));
	return array('sign' => $sign2, 'data' => $data, 'sha1' => sha1($tmpdata.$secretKey));
}

$params = ['versions'=>'string','tm'=>'string', 'sign'=>'string'];
$check = ['versions', 'tm', 'sign'];
foreach ($params as $k=>$v){
	$$k = isset($_GET[$k]) ? $_GET[$k] : '';
	$$k = $v == 'int' ? (int) $$k : trim($$k);

	$param = $$k;
	if(in_array($k, $check) && !$param){
		error2(-4013);
	}
}

$request = $_GET;
$ret = setClientVersionsBuildKeys($request);
if($ret){
	if($ret['sign'] == $request['sign']){
		$CLIENT_VERSION = 'clientversions:setversions';
		$redis = new RedisHelp();
		$redis->set($CLIENT_VERSION,$versions);
		echo jsone('+OK');
	}else{
		error2(-4031);
	}
}else{
	error2(-4031);
}





