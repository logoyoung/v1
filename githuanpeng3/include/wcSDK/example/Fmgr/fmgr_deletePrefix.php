<?php

// 请先填写相关字段,$fops字段格式详见wcs api 文档
require '../../vendor/autoload.php';
use \Wcs\Fmgr\Fmgr;
use \Wcs\Config;
use \Wcs\MgrAuth;

//可选参数
$notifyURL = '';
$force = 0;
$separate  = 0;

//fops参数
$bucket = \Wcs\url_safe_base64_encode('<input key>');
$prefix = \Wcs\url_safe_base64_encode('<input key>');
$output = \Wcs\url_safe_base64_encode('<input key>');


$fops = 'fops=bucket/'.$bucket.'/prefix/'.$prefix.'&notifyURL='.\Wcs\url_safe_base64_encode($notifyURL).'&force='.$force.'&separate='.$separate;

$ak = Config::WCS_ACCESS_KEY;
$sk = Config::WCS_SECRET_KEY;

$auth = new MgrAuth($ak, $sk);  

$client = new Fmgr($auth, $notifyURL, $force, $separate);
print_r($client->deletePrefix($fops));
print_r("\n");
