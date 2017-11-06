<?php

/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/4/7
 * Time: 11:45
 */
include '../../../include/init.php';
use lib\Anchor;
use lib\CDNHelper;
use LiveRoom;
use lib\Live;
use lib\live\LiveLog;

/*********************************main***************************/
$ip = isset( $_GET['ip'] ) ? trim( $_GET['ip'] ) : '';
$stream = isset( $_GET['stream'] ) ? trim( $_GET['stream'] ) : '';
$node = isset( $_GET['node'] ) ? trim( $_GET['node'] ) : '';
$domain = isset( $_GET['domain'] ) ? trim( $_GET['domain'] ) : '';
$path = isset( $_GET['path'] ) ? trim( $_GET['path'] ) : '';
$tm = isset( $_GET['tm'] ) ? trim( $_GET['tm'] ) : '';
$signWscStr = isset( $_GET['sign'] ) ? trim( $_GET['sign'] ) : '';

if( empty( $ip ) || empty( $stream ) || empty( $node ) || empty( $domain ) || empty( $path ) || empty( $tm ) || empty( $signWscStr ) )
{
	//Live::liveErrorLog( array( 70014, '网速回调参数不全' ) );
	LiveLog::wslog('error:网宿回调参数不全' . json_encode($_GET));
	echo 0;
	exit;
}
//加密校验
$signHPStr = CDNHelper::getStreamCallBackSecret(array('stream'=>$stream,'ip'=>$ip,'tm'=>$tm));
if($signWscStr != $signHPStr)
{
	//mylog('推流或断流回调加密串不一致',LOG_DIR.'Live.error.log');
	LiveLog::wslog('error:推流或断流回调加密串不一致');
	echo 0;
	exit;
}
//回调类型
$callBackType = array( 'cdn/ws/livestart', 'cdn/ws/liveend' );
//回调uri
$uri = $_SERVER['REQUEST_URI'];
$db = new DBHelperi_huanpeng();

$uid = Live::getUidByLiveStream( $stream, $db );

if( !$uid )
{
	//mylog('回调直播流名称异常',LOG_DIR.'Live.error.log');
	LiveLog::wslog('error:回调直播流名称异常');
	echo 0;
	exit;
}
//主播检测
//todo

$Live = new Live( $uid, $db );
if( strstr( $uri, $callBackType[0] ) )
{
	//mylog("主播{$uid}推流回调",LOG_DIR.'Live.error.log');
	LiveLog::wslog("record:主播{$uid}推流{$stream}回调");
	$r = $Live->startLive($tm);
}
elseif( strstr( $uri, $callBackType[1] ) )
{
	//mylog("主播{$uid}断流回调",LOG_DIR.'Live.error.log');
	livelog::wslog("record:主播{$uid}断流{$stream}回调");
	$r = $Live->liveDisconnect($tm);
}
echo 1;
exit;


