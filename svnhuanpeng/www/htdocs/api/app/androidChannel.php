<?php
/**
 * 安卓版本渠道
 */

include '../../../include/init.php';
$redis = new redishelp();

$channel = isset($_POST['channel']) ? (int)$_POST['channel'] : 0;
$version = isset($_POST['version']) ? (int)$_POST['version'] : 0;
if(empty($channel) || empty($version)){
   error2(-4013,2);
}
$key='Android:'.$channel;
$redis->set($key,$version);
echo $redis->get($key);
succ();
