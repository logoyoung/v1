<?php

/**
 * 注册
 * author yandong@6rooms.com
 * date 2015-12-15 10:30
 * version 0.1
 */
session_start();
include '../init.php';
require INCLUDE_DIR . 'register.class.php';
//header("Content-type:text/html;charset=utf-8");
$reg = new Register();
$db = new DBHelperi_huanpeng();

/**
 * 校验手机验证码
 * @param type $mobile_code 手机验证码
 * @return type
 */
function checkMobileCode($mobile_code) {
    if ($mobile_code && $_SESSION['register_mobile_code'] && $mobile_code == $_SESSION['register_mobile_code'] && !empty($_SESSION['register_mobile_code'])) {
        $data = array(
            'appid' => 102,
            'codeid' => (int) $_SESSION['register_codeid'],
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

/**
 * start
 */
$mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
$nick = isset($_POST['nick']) ? trim($_POST['nick']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$mobileCode = isset($_POST['mobileCode']) ? trim($_POST['mobileCode']) : '';
if (empty($mobile)) {
    error(-4056);
}
$mobileRes = checkMobile($mobile);
if (true !== $mobileRes) {
    error(-4058);
}
$checkMobileRes = checkMobileIsUsed($mobile, $db);
if ($checkMobileRes) {
    error(-4060);
}
//校验手机
if ($mobile != $_SESSION['register_mobile'] || empty($_SESSION['register_mobile']) || !$_SESSION['register_mobile']) {
    $err = array('code' => "-4065", 'desc' => "mobile is $mobile and register_mobile is {$_SESSION['register_mobile']}");
    exit(json_encode($err));
    error(-4065);
}
//if ($identCode != $_SESSION['register_code']) {
//    error(-4013);
//}
$checkmcode = checkMobileCode($mobileCode);
if ($checkmcode['code'] != 1) {
    error(-4031);
}
if (empty($nick) || empty($password)) {
    error(-4013);
}
$passLeng = checkPasswordLeng($password);
if (true !== $passLeng) {
    error(-1003);
}
$nickLeng = $reg->userName($nick);
if (true !== $nickLeng) {
    error(-1004);
}
$nick = filterData($nick);
$checknick = checkNickIsUsed($nick, $db);
if ($checknick) {
    error(-4035);
}
$password = filterData($password);
$checkNickMode=checkMode(CHECK_NICK,$db);//检测昵称审核模式
if(!$checkNickMode){
    $defaultNick="用户$mobile";//先审后发状态下默认昵称
}else {
    $defaultNick=$nick;
$result = $reg->reg($mobile, $defaultNick, $password);
if (false === $result) {
    error(-5017);
} else {
    $_SESSION['register_mobile'] = $_SESSION['register_mobile_code'] = $_SESSION['register_codeid'] = '';
    $loginInfo = json_decode($result);
    setUserLoginCookie($loginInfo->uid, $loginInfo->encpass);
    if($checkNickMode){
        //先发后审
        $status=USER_NICK_AUTO_PASS;
    }else{
        //先审后发
        $status=USER_NICK_WAIT;
    }
    setNickToAdmin($result['uid'], $nick, $db, $status);//同步到admin_user_nick表中
    exit(json_encode($result));
}





