<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/6/12
 * Time: 17:37
 */

include '../../../include/init.php';
use lib\WcsHelper;
use lib\LiveCheck;
use lib\Live;

function getS($stream){
	$now      = time()-30;
	$cTime    = dechex( $now );
	$wsSecret = md5( WS_SECURITY_CHAIN . '/' . 'liverecord' . '/' . $stream . $cTime );
	$data     = array(
		'wsSecret' => $wsSecret,
		'eTime'    => $cTime
	);

	return http_build_query( $data );
}

$stream = isset($_GET['stream'])?$stream:'test';
$play = 'rtmp://'.$GLOBALS['env-def'][$GLOBALS['env']]['stream-watch'].'/'.$stream.'?'.getS($stream);
$pub = 'rtmp://'.$GLOBALS['env-def'][$GLOBALS['env']]['stream-pub'].'/'.$stream."?uid=888888";

echo "推流地址：$pub<br>";
echo "播流地址：$play<br>";

/*********************************************/

/*$streams = WcsHelper::getWsStreamInfoByApi('dev-urtmp.huanpeng.com');
var_dump($streams);*/

$LiveCheck = new LiveCheck();
$results = $LiveCheck->getPullStreams();
//$results = $LiveCheck->getPushStreams();
//var_dump($results1);
var_dump($results);