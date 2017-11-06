<?php

include '../../../include/init.php';

use lib\Anchor;
use lib\Live;
use lib\LiveRoom;
use service\user\UserAuthService;
use lib\live\LiveLog;
/***************************main*************************/
//用户ID
$uid = 						 isset( $_POST['uid'] ) ? trim( $_POST['uid'] ) : '';
//校验码
$encpass = 					 isset( $_POST['encpass'] ) ? trim( $_POST['encpass'] ) : '';
//直播标题
$liveParams['title'] = 		 isset( $_POST['title'] ) ? trim( $_POST['title'] ) : '';
//游戏名称
$liveParams['gamename'] = 	 isset( $_POST['gameName'] ) ? trim( $_POST['gameName'] ) : '';
//直播画质
$liveParams['quality'] = 	 isset( $_POST['quality'] ) ? trim( $_POST['quality'] ) : 0;
//直播角度
$liveParams['orientation'] = isset( $_POST['orientation'] ) ? trim( $_POST['orientation'] ) : 0;
//设备标识
$liveParams['deviceid'] = 	 isset( $_POST['deviceID'] ) ? trim( $_POST['deviceID'] ) : '';
//直播类型
$liveParams['livetype'] = 	 isset( $_POST['liveType'] ) ? trim( $_POST['liveType'] ) : 0;
//主播所在地经度
$liveParams['longitude'] =   isset( $_POST['longitude'] ) ? trim( $_POST['longitude'] ) : 0;
//主播所在地纬度
$liveParams['latitude'] =    isset( $_POST['latitude'] ) ? trim( $_POST['latitude'] ) : 0;

//必填参数不能为空
if( empty( $uid ) || empty( $encpass ) || empty( $liveParams['deviceid'] ) )
{
	error2( -4013, 2 );
}

$db = new DBHelperi_huanpeng();

//权限校验

$auth = new UserAuthService();
$auth->setUid($uid);
$auth->setEnc($encpass);

if($auth->checkLoginStatus() !== true)
{
	$errorCode = $result['error_code'];
	error2( '-1013', 2 );
	exit;
}
if( !Anchor::isAnchor( $uid, $db ) )
{
	error2( -4057, 2 );
}

$Live = new Live($uid,$db);

$liveCreateBack = $Live->createLive($liveParams);
if(!isset($liveCreateBack['liveID']))
	error2($liveCreateBack,2);

$conf = $GLOBALS['env-def'][$GLOBALS['env']];
//mylog("用户{$uid}继续了直播：{$liveCreateBack['liveID']}",LOG_DIR.'Live.error.log');
LiveLog::applog("record:用户{$uid}继续了直播：{$liveCreateBack['liveID']}");
succ(array(
	'server'=>array($liveCreateBack['rtmpServer']),
	'stream' => $liveCreateBack['stream'],
	'hpbean' => 0,
	'fansCount' => 0,
	'userCount'=>0
));
exit;