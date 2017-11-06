<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/5/31
 * Time: 上午10:16
 */

include '../../init.php';
include_once INCLUDE_DIR.'redis.class.php';
$redis = new redishelp();

$version = getApkVersion($redis);


$file = $version['file'];
header("Content-type: application/octet-stream");
header('Content-Disposition: attachment; filename="' . basename($file) . '"');
header("Content-Length: ". filesize($file));
readfile($file);

?>
