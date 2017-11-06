<?php

include '../../init.php';
require_once INCLUDE_DIR . 'redis.class.php';
/**
 * 检测登录时是否需要验证
 * date 2016-10-20 16:41
 * author yandong@6rooms.com
 */
$db = new DBHelperi_huanpeng();
$userName = isset($_POST['userName']) ? trim($_POST['userName']) : '';
if (empty($userName)) {
    error(-4013);
}
$userNameRes = checkMobile($userName);
if (true !== $userNameRes) {
    error(-4058);
}
$redisObj = new RedisHelp();
$mkey = "LogInNumber:$userName";
if ($redisObj->get($mkey) >= 3) {
    exit(jsone(array('status' => '1')));
} else {
    exit(jsone(array('status' => '0')));
}
