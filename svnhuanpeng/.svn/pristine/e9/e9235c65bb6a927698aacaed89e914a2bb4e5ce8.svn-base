<?php

/**
 * 检测手机短信验证码
 * yandong@6rooms.com
 * Date 2016-07-05 14:48
 */
session_start();
include '../init.php';

/**
 * 校验手机验证码
 * @param type $mobile_code 手机验证码
 * @return type
 */
function checkMCode($mobile_code) {
    if ($mobile_code && $_SESSION['forget_mobile_code'] && $mobile_code == $_SESSION['forget_mobile_code'] && !empty($_SESSION['forget_mobile_code'])) {
        $data = array(
            'appid' => 102,
            'codeid' => (int) $_SESSION['forget_codeid'],
            'tm' => (int) time()
        );
        $str = json_encode($data);
        $key = "ekxklhuangTSDpengfkjekldc";
        $sign = md5($str . $key);

        $send_url = "http://dev.liveuser.6.cn/api/callBackSendSmsCode.php?appid=102&codeid={$data['codeid']}&tm={$data['tm']}&sign=$sign";
        $result = file_get_contents($send_url);
        if ($result) {
            $r = json_decode($result, true);
            $sms['code'] = $r['resuNo'];
            $sms['desc'] = $r['resuMsg'];
        } else {
            $sms['code'] = -1;
            $sms['desc'] = 'URL Error';
        }
    } else {
        $sms['code'] = -1;
    }
    return $sms;
}

$mobile_code = isset($_REQUEST['mcode']) ? trim($_REQUEST['mcode']) : '';
$mobile = isset($_REQUEST['mobile']) ? trim($_REQUEST['mobile']) : '';
if (empty($mobile_code) || empty($mobile)) {
    error(-4013);
}
$mobileRes = checkMobile($mobile);
if($mobileRes !==true){
    error(-4031);
}
if (!$mobile || $mobile != $_SESSION['forget_mobile'] || empty($_SESSION['forget_mobile']) || !$_SESSION['forget_mobile']) {
    exit(-4058);
}
$checkmcode = checkMCode($mobile_code);
if ($checkmcode['code'] == 1) {
    exit(jsone(array('isSuccess' => "1")));
} else {
    exit(jsone(array('isSuccess' => "0")));
}




