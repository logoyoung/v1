<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/4/27
 * Time: 13:08
 */

include( __DIR__ . '/../../include/init.php' );
define('BAT_LOG',LOG_DIR.'bat.log');
define('SLEEP_INTERVAL',1);
$stopLiveFile = LOG_DIR.'stopLive.txt';
$api = array(
	'DEV'=>'http://dev.huanpeng.com/api/live/stopLive.php',
	'PRE'=>'http://pre.huanpeng.com/api/live/stopLive.php',
	'PRO'=>'http://www.huanpeng.com/api/live/stopLive.php'
	//todo
);

function batHttpPost( $url, $headers = null, $fields = null )
{
	$ch      = curl_init();
	$options = array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_SSL_VERIFYHOST => false,
		CURLOPT_HEADER         => true,
		CURLOPT_NOBODY         => false,
		CURLOPT_CUSTOMREQUEST  => 'POST',
		CURLOPT_URL            => $url,
		CURLOPT_TIMEOUT        => 0,
	);
	if( !empty( $headers ) )
	{
		$options[CURLOPT_HTTPHEADER] = $headers;
	}
	if( !empty( $fields ) )
	{
		$options[CURLOPT_POSTFIELDS] = $fields;
	}
	curl_setopt_array( $ch, $options );
	$result = curl_exec( $ch );

	$ret   = array();
	$errno = curl_errno( $ch );

	//错误状态码
	if( $errno !== 0 )
	{
		$ret['code']    = $errno;
		$ret['message'] = curl_error( $ch );
		curl_close( $ch );

		return $ret;
	}

	$code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
	curl_close( $ch );

	//分割响应头部和内容
	$responseArray     = explode( "\r\n\r\n", $result );
	$responseArraySize = sizeof( $responseArray );
	$respHeader        = $responseArray[$responseArraySize - 2];
	$respBody          = $responseArray[$responseArraySize - 1];

	$ret['code']       = $code;
	$ret['respHeader'] = $respHeader;
	$ret['respBody']   = $respBody;

	//超时判断
	if( $ret['code'] == 28 )
	{
		$ret['respBody'] = "请求超时！";
	}

	return $ret['respBody'];
}

function batGetLive( $db )
{
	$r = $db->where("status=".LIVE)->select('live');
	return $r;
}

function batGetAnchor($uid,$db)
{
	$r = $db->where("uid={$uid}")->select('userstatic');
	return array(
		'uid'=>$uid,
		'encpass'=>$r[0]['encpass']
	);
}

function batStopLive($requestData,$url)
{
	$requestData = http_build_query($requestData);
	$r = batHttpPost($url,null,$requestData);
	return $r;
}


$db = new DBHelperi_huanpeng();
//获取直播
$lives = batGetLive($db);
foreach ($lives as $k=>$live)
{

	mylog("获得直播ID{$live['liveid']}",BAT_LOG);
	//获取主播信息
	$user = batGetAnchor($live['uid'],$db);
	mylog("获得主播ID{$user['uid']}",BAT_LOG);
	$data = array(
		'uid'=>$user['uid'],
		'encpass'=>$user['encpass'],
		'liveID'=>$live['liveid']
	);
	$url = $api[$GLOBALS['env']];var_dump($url);
	$ret = batHttpPost($url,null,$data);
	var_dump($ret);
	mylog("停止直播：{$live['liveid']}",BAT_LOG);
	mylog("{$live['liveid']}",$stopLiveFile);
	sleep(SLEEP_INTERVAL);
}

