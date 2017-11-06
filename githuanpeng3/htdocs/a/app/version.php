<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/5/31
 * Time: 上午10:11
 */

include '../../init.php';
include_once INCLUDE_DIR.'redis.class.php';
$redis = new redishelp();

$version = getApkVersion($redis);



$url = "http://dev.huanpeng.com/main/a/app/download.php";
$array = array(
    'url' => $url,
    'version' => $version['version'],
    "version_name" => $version['name'],
    "version_desc" => $version['desc']
);


exit(json_encode($array));
