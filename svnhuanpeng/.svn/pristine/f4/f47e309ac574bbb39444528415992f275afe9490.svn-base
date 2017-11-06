<?php
require '../../vendor/autoload.php';
use \Wcs\PersistentFops\Fops;
use \Wcs\Config;
use \Wcs\MgrAuth;

$bucket = 'lumj-test';
$key = 'video_sync.mp4';

//参数设置
$notifyURL = 'http://callback-test.wcs.biz.matocloud.com:8088/notifyUrl';
$force = 0;
$separate = 0;

$ak = Config::WCS_ACCESS_KEY;
$sk = Config::WCS_SECRET_KEY;
$auth = new MgrAuth($ak, $sk);

$fops = 'vframe/jpg/offset/10/w/1000/h/1000|saveas/bHVtai10ZXN0OnZmcmFtZS10ZXN0LTI3LmpwZw==';
$client = new Fops($auth, $bucket);

print_r($client->exec($fops, $key, $notifyURL));
print("\n");
