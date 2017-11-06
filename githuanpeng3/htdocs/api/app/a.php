<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/3/13
 * Time: 上午10:06
 */

define("LIVE_CHANNEL_ALL_HP", 1);
define("LVIE_CHANNEL_ALL_TENCENT", 2);
define("LIVE_CHANNEL_BOTH", 3);

function succ($content=array()){
	if (empty($content)) {
		$succ = array(
			'status' => "1",
			'content' => (object) $content
		);
	} else {
		$succ = toString(array(
			'status' => "1",
			'content' => $content
		));
	}
	exit(json_encode($succ));
}

function toString($mix)
{
	if (is_string($mix)) {
		return $mix;
	}
	if (is_int($mix) || is_bool($mix) || is_float($mix) || is_double($mix)) {
		return "$mix";
	}
//	if(is_object($mix)){
//		foreach ($mix as $key => $v){
//			$mix[$key] = $v;
//		}
//		return (object)$mix;
//	}
	if (is_array($mix)) {
		foreach ($mix as $key => $v) {
			$mix[$key] = toString($v);
		}
		return $mix;
	}


	return "";
}


function http_get($url, $data = false)
{
	if($data)
		$url = $url."?".http_build_query($data);
	return file_get_contents($url);
}

$build = $_POST['build'];
$channel = $_POST['channel'];

$scriptPath = preg_replace( "/\/[a-z]+\.php/i", "/getChannelInfo.php", $_SERVER['PHP_SELF'] );
$getChannelInfoUrl = "http://".$_SERVER['HTTP_HOST'] . "/$scriptPath";

//var_dump( $getChannelInfoUrl );

$data = array();

$channelInfo = http_get( $getChannelInfoUrl, $data );
//var_dump($channelInfo);
$channelInfo = json_decode( $channelInfo, true );

//var_dump("adfasdfasdf");
//var_dump($channelInfo);

$channelBuildList = $channelInfo['channelBuildList'];
$channelNameList = $channelInfo['channelNameList'];

$tmp = array();
$nameList = array();
foreach ($channelNameList as $key => $value)
{
	$tmp['channel'] = $key;
	$tmp['name'] = $value;
	array_push($nameList, $tmp);
}
$channelNameList = $nameList;
unset($nameList);

$channelBuildList = array(
	8001=>'109',//网站
	8002=>'108',//oppo商店
	8003=>'108',//vivo商店
	8004=>'108',//小米商店
	8005=>'108',//华为
	8006=>'108',//三星
	8007=>'108',//百度手机助手
	8008=>'108',//360助手
	8009=>'108',//应用宝
	8010=>'108',//豌豆荚
	8011=>'108',//魅族
	8012=>'108',//乐视商店
	8013=>'108',//联想开放平台
	8014=>'108',//2345手机助手
	8015=>'108',//搜狗手机助手
	8016=>'108',//安智市场
	8017=>'108',//木蚂蚁
	8018=>'108',//阿里应用
	8019=>'108',//pp助手
	8020=>'108',//锤子应用市场
	8021=>'108',//海马玩
	8022=>'108',//一点资讯
	8023=>'108',//wifi万能钥匙
	8024=>'108',//应用宝推广1
);

//设置发直播

if( $build >= 110)
{
	$sendLiveType = LIVE_CHANNEL_ALL_HP;
}
else
{
	$sendLiveType = LVIE_CHANNEL_ALL_TENCENT;
}


$whiteList = array();
if( $sendLiveType == LIVE_CHANNEL_BOTH )
{
	$whiteList = array(3700,1895,1915,2240,2305,2250,2235,1910,1930,13795,14570,11625,11645,11655,11835,11955,11970,12080,12140,12170,12375,16090,16305,16315,16475,17230,17425,17530,
17625,17745,17760,17900,17915,17930,18110,18115,18210,18555,18750,19115,19495,25805,48625,48825,48180,53945,48150,48695,40705,48670,48605,48025,51340,53685,56510,3635,56760,49070,68112,58525,7975,48680,72751,68888);
}

$responseData = array(
	'c'=>0,
	'channelNameList' => $channelNameList,
	'live' => array(
		'type' => $sendLiveType,
		'wlist' => $whiteList
	)
);


//$responseData['c'] = 0;
if( $build && $channel && $channelBuildList[$channel] < $build )
{
	$responseData['c'] = 1;
}

$responseData['c'] = 0;

succ($responseData);
