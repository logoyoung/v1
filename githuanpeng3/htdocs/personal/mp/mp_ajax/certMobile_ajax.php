<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/1/14
 * Time: 下午2:55
 */
session_start();
include '../../../../include/init.php';

$db = new DBHelperi_huanpeng();

$mobile_code = trim($_POST['mcode']);

if($mobile_code && $_SESSION['mobile_code'] && $mobile_code == $_SESSION['mobile_code'] && !empty($_SESSION['mobile_code'])){

	$data = array(
		'appid' => 102,
		'codeid' => (int)$_SESSION['codeid'],
		'tm' => (int)time()
	);
	$str = json_encode($data);
	$key = "ekxklhuangTSDpengfkjekldc";
	$sign = md5($str . $key);

	$send_url = "http://dev.liveuser.6.cn/api/callBackSendSmsCode.php?appid=102&codeid={$data['codeid']}&tm={$data['tm']}&sign=$sign";
	$result = file_get_contents($send_url);
	if($result){
		$r = json_decode($result, true);
		$sms['code'] = $r['resuNo'];
		$sms['desc'] = $r['resuMsg'];
	}else{
		$sms['code'] = -1;
		$sms['desc'] = 'URL Error';
	}
}else{
	exit(jsone(array('code'=> -116)));
}

$uid = $_POST['uid'] ? (int)$_POST['uid'] : 0;
$enc = $_POST['encpass'] ? trim($_POST['encpass']) : '';

if(!$uid || !$enc){
	error(-1013);
}

$code = checkUserState($uid, $enc, $db);
$code === true || error($code);

$mobile = trim($_POST['mobile']);

if(!$mobile || $mobile != $_SESSION['mobile'] || empty($_SESSION['mobile']) || !$_SESSION['mobile']){
	exit(jsone(array('code'=> -115)));
}



$_SESSION['mobile'] = $_SESSION['mobile_code'] = $_SESSION['codeid'] ='';

$sql = "update userstatic set phone='$mobile' where uid=$uid";
$res = $db->query($sql);

if($res){
         synchroTask($uid, 6, 0, 100, $db);//任务同步
	exit(jsone(array('isSuccess' => 1, 'msgcode' => $sms['code'],'msgdesc' => $sms['desc'])));
}else{
	exit(jsone(array('isSuccess' => 0, 'msgcode' => $sms['code'],'msgdesc' => $sms['desc'] )));
}




