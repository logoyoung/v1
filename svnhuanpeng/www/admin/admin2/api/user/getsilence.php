<?php
/**
 * 禁言设置   供后台使用，同时为前台提供接口（内网访问）
 * jiantao@6.cn
 * date 2016-10-12 13:55
 * 
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
require '../../includeAdmin/Redis.class.php';
$db = new DBHelperi_admin();

$redis = new RedisHelp();
$res = $redis->hgetAll('silence_100312', 15075);


var_dump($res);

var_dump(json_decode($res, true));


