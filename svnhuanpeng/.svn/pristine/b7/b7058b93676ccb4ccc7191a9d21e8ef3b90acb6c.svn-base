<?php

/**
 * 注册时获取手机验证码
 * yandong@6rooms.com
 * Data 2016-07-19 15:44
 */
include '../../init.php';
require_once INCLUDE_DIR . 'class.geetestlib.php';
session_start();
$mobile = isset($_REQUEST['mobile']) ? trim($_REQUEST['mobile']) : '';
$code = isset($_REQUEST['code']) ? trim($_REQUEST['code']) : '';
if (empty($mobile)) {
    error(-4056);
}
$mres = checkMobile($mobile);
if ($mres !== true) {
    error(-4013);
}

//geetest 模式
if($_POST['type'] == 'gt'){
	$GtSdk = $_POST['client'] =='1' ? new GeetestLib(CAPTCHA_APP_ID, PRIVATE_APP_KEY) : new GeetestLib(CAPTCHA_ID, PRIVATE_KEY);
    $user_id = $_SESSION['user_id'];
    if ($_SESSION['gtserver'] == 1) {
        $result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $user_id);
        if (!$result) {
            error(-4031);
        }
    }else{
        if (!$GtSdk->fail_validate($_POST['geetest_challenge'],$_POST['geetest_validate'],$_POST['geetest_seccode'])) {
            error(-4031);
        }
    }
}else{
    //验证码模式
    if (empty($code) || $code != $_SESSION['register_code']) {
        error(-4031);
    }
}



$db = new DBHelperi_huanpeng();
$checkMobileRes = checkMobileIsUsed($mobile, $db);
if ($checkMobileRes) {
    error(-4060);
}
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
if ($res) {
    $r = json_decode($res, true);
    if ($r['resuNo'] == 1) {
        $_SESSION['register_mobile'] = $mobile;
        $_SESSION['register_mobile_code'] = $mobile_code;
        $_SESSION['register_codeid'] = $data['codeid'];
        $_SESSION['register_code']='';
        exit(json_encode(array('isSuccess' => 1, 'desc' => '发送手机验证码成功!')));
    } else {
        exit(json_encode(array('isSuccess' => 0, 'desc' => $r['resuMsg'], 'url' => $sendurl, 'str' => $str)));
    }
} else {
    exit(json_encode(array('isSuccess' => -1, 'desc' => 'url error')));
}