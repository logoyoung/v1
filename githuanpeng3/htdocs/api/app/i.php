<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/11/22
 * Time: 下午3:45
 */

//================================
//定义基础变量
//================================

$basicAppVersion;

$appDescription;
$appTitle;
$isForceUpdate;

//10:非常保守 空和0-9:超星巨星去掉舞区 100：全部
$openType;

//开放黑名单
$openVersionBlackList;

//升级指向的AppID
$updateAPPID;

//评论指向的AppID
$reviewAppID;

//发布直播所需位置信息等级 0:关闭 1:提示但自愿（不授权也可以发直播） 2:提示并强制（不授权不可以发直播）
$publishNeedLocationLevel;

//获取渠道列表数据
$scriptPath = preg_replace( "/\/[a-z]+\.php/i", "/getChannelInfo.php", $_SERVER['PHP_SELF'] );
$getChannelInfoUrl = "http://".$_SERVER['HTTP_HOST'] . "/$scriptPath";

$data = array();

$channelInfo = http_get( $getChannelInfoUrl, $data );
$channelInfo = json_decode( $channelInfo, true );

$channelNameList = $channelInfo['channelNameList'];

$allChannelDic = json_encode($channelNameList);

//================================
//根据BundleID对变量进行赋值
//================================
$appVersionID  = isset( $_POST["av"] ) ? $_POST["av"] : ( isset( $_GET["av"] ) ? $_GET["av"] : 0 );
$bundleID = isset( $_POST["bid"] ) ? $_POST["bid"] : ( isset( $_GET["bid"] ) ? $_GET["bid"] : "" );

if($bundleID == "" || $bundleID == "com.huanpeng.live")
{
	$basicAppVersion  = "1.0.4";
	
	$appDescription = "很遗憾地通知您，当前APP已经停止服务。体验更新更全的产品服务，请下载欢朋最新APP“欢朋手游直播”，下载完成后请务必手动删除当前APP！";
	$appTitle = "官方升级提示";
	$isForceUpdate = "0";
	
	$openType = 1;
	
	$openVersionBlackList = array("1.0.3");
	
	$updateAPPID = "1240076397";
	
	$reviewAppID = "1191399310";
	
	$publishNeedLocationLevel = 0;
}
else if($bundleID == "com.huanpeng.show")
{
	$basicAppVersion  = "1.0.6";
	
	$appDescription = "陪玩功能上线了！立刻升级，体验全新玩法吧！";
	$appTitle = "升级提示";
	$isForceUpdate = "1";
	
	$openType = 1;
	
	$openVersionBlackList = array("1.0.8");
	
	$updateAPPID = "1240076397";
	
	$reviewAppID = "1240076397";
	
	$publishNeedLocationLevel = 0;
}
else if($bundleID == "com.huanpeng.liveassistant")
{
	$basicAppVersion  = "1.0.1";
	
	$appDescription = "-优化体验，修复已知bug";
	$appTitle = "更新提示";
	$isForceUpdate = "0";
	
	$openType = 1;
	
	$openVersionBlackList = array("1.0.3");
	
	$updateAPPID = "1240076397";
	
	$reviewAppID = "1240076397";
	
	$publishNeedLocationLevel = 0;
}

//================================
//处理变量，并输出
//================================

$infoJSON = "";
$appJSON = "";

if( getVersionFloatValue( $appVersionID ) < getVersionFloatValue( $basicAppVersion ))
{
	$appJSON = "{\"version\":\"" . $basicAppVersion . "\", \"title\":\"" . $appTitle . "\", \"description\":\"" . $appDescription . "\", \"isForce\":" . $isForceUpdate . "}";
}

$infoJSON = "{";

//如果客户端版本在黑名单中，则只展示保守列表
if( in_array( $appVersionID, $openVersionBlackList )) $openType = 10;

$infoJSON .= "\"subVersion\":" . $openType;

if( $appJSON != "" ) $infoJSON .= ", ";

if( $appJSON != "" ) $infoJSON .= "\"app\":" . $appJSON;

$infoJSON .= ", \"uaid\":\"" . $updateAPPID . "\"";

$infoJSON .= ", \"rvaid\":\"" . $reviewAppID . "\"";

$infoJSON .= ", \"pnll\":\"" . $publishNeedLocationLevel . "\"";

$infoJSON .= ", \"acd\":" . $allChannelDic;

$infoJSON .= "}";

exit($infoJSON);

//将版本号数字化，以比较大小
function getVersionFloatValue($str)
{
	$versionList = explode(".", $str);

	$versionFloatValue = 0;

	if (isset($versionList))
	{
		$maxVersionDigit = 4;
		$listCount = min(count($versionList), $maxVersionDigit);
		for ($i = 0; $i < $listCount; $i++)
		{
			$versionFloatValue += ($versionList[$i] * pow(10, ($maxVersionDigit - $i - 1) * 2));
		}
	}

	return $versionFloatValue;
}

function http_get($url, $data = false)
{
	if($data)
		$url = $url."?".http_build_query($data);
	return file_get_contents($url);
}