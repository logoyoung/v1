<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/5/31
 * Time: 上午10:16
 */

include '../../../include/init.php';
//$redis = new redishelp();


use service\app\AppUpdateService;


$channel = isset( $_GET['channel'] ) ? intval( $_GET['channel'] ) : 0;
$version = isset( $_GET['version'] ) ? intval( $_GET['version'] ) : 0;

$app = new AppUpdateService( $channel, $version );

$app->download();

//$file = WEBSITE_MAIN."liveTools/android/huanpeng_default_channel.apk";
//
//header("Content-type: application/octet-stream");
//header('Content-Disposition: attachment; filename="' . basename($file) . '"');
//header("Content-Length: ". filesize($file));
//readfile($file);


//if($GLOBALS['env'] == 'DEV'){
//	$version = getApkVersion($redis);
//	$file = $version['file'];
//	header("Content-type: application/octet-stream");
//	header('Content-Disposition: attachment; filename="' . basename($file) . '"');
//	header("Content-Length: ". filesize($file));
//	readfile($file);
//}else{
//
//	$file = 'huanpeng.apk';
//	header("Content-type: application/octet-stream");
//	header('Content-Disposition: attachment; filename="' . basename($file) . '"');
//	$res = `curl  http://dev.huanpeng.com/main/api/app/download.php`;
//	print_r($res);
//}


?>
