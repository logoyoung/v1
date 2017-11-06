<?php

include '../../init.php';
/**
 * 检测手机号是否被用过
 * date 2016-07-18 11:21
 * author yandong@6rooms.com
 */
$db = new DBHelperi_huanpeng();
$mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
if (empty($mobile)) {
    error(-4056);
}
$checkMobile = checkMobile($mobile);
if (true !== $checkMobile) {
    error(-4058);
}
$res = checkMobileIsUsed($mobile, $db);
$redisObj = new RedisHelp();
$mkey = "LogInNumber:$mobile";

$conf = $GLOBALS['env-def'][$GLOBALS['env']];
if ($redisObj->get($mkey) >= 3) {//连续登录三次失败,开启验证码校验
    setcookie('_login_identCode_open', 1, 0, '/main', $conf['domain']);
}else{
    setcookie('_login_identCode_open', 0, 0, '/main', $conf['domain']);
}
if ($res) {
    exit(jsone(array('isSuccess' => '1')));
} else {
    exit(jsone(array('isSuccess' => '0')));
}
