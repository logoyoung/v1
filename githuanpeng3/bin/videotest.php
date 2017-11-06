<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/1/10
 * Time: 11:34
 */

include '/usr/local/huanpeng/include/init.php';$GLOBALS['env'] = 'PRO';
include (INCLUDE_DIR.'wcSDK/vendor/autoload.php');
include (INCLUDE_DIR.'videoHelper.class.php');
use \Wcs\PersistentFops\Fops;
use \Wcs\Config;
use \Wcs\MgrAuth;

$bucket = '6huanpeng-test001';
$key = '60.mp4';
$key2 = 'liverecord-Y-270-6243971--20170113104104.flv';
$dst = \Wcs\url_safe_base64_encode($key).'/'.\Wcs\url_safe_base64_encode($key2);
//参数设置
$notifyURL = 'http://www.huanpeng.com/';
$force = 0;
$separate = 0;

$ak = Config::WCS_ACCESS_KEY;
$sk = Config::WCS_SECRET_KEY;
$auth = new MgrAuth($ak, $sk);

//$fops = 'vframe/jpg/offset/10/w/1900/h/1069|saveas/'.\Wcs\url_safe_base64_encode($bucket.':test6.jpg');
//$fops = 'avconcat/mp4/mode/1/'.$dst.'|saveas/'.\Wcs\url_safe_base64_encode($bucket.':testmerge.mp4');
$fops = 'avthumb/mp4/moovToFront/1'.'|saveas/'.\Wcs\url_safe_base64_encode($bucket.':dev/v/transcode2.mp4');;
$client = new Fops($auth, $bucket);
print_r($client->exec($fops, \Wcs\url_safe_base64_encode($key2), $notifyURL));
print("\n");

