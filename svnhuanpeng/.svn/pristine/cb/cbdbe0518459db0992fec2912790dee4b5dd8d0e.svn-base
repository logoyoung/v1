<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/27
 * Time: 上午11:57
 */

include '../../../include/init.php';

use lib\Anchor;
use lib\Live;
use service\user\UserAuthService;
use lib\live\LiveLog;

$uid      = isset( $_POST['uid'] ) ? (int)$_POST['uid'] : 0;
$enc      = isset( $_POST['encpass'] ) ? trim( $_POST['encpass'] ) : '';
$deviceid = isset( $_POST['deviceID'] ) ? trim( $_POST['deviceID'] ) : '';

if( !$uid || !$enc || !$deviceid )
{
	error2( -4013 );
}
$db           = new DBHelperi_huanpeng();

//权限校验

$auth = new UserAuthService();
$auth->setUid($uid);
$auth->setEnc($enc);


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

//重组数据
$live = Live::getLastLive( $uid, $db );
$liveInfo = [ 'gameID'=>'',
			  'gameTypeID'=>'',
			  'gameName'=>'',
			  'liveType'=>'',
			  'title'=>"",
			  'orientation'=>1,
			  'quality'=>'' ];
$appQuality = array(
	array('quality'=>'2','width'=>'1280','height'=>'720','rate'=>'1500','rec'=>'wifi','desc'=>'高清（720p，1.5M）'),
	array('quality'=>'1','width'=>'960','height'=>'540','rate'=>'800','rec'=>'4g','desc'=>'普清（540p，800k）'),
	//array('quality'=>'0','width'=>'640','height'=>'360','rate'=>'600','rec'=>'','desc'=>'流畅（360p，600k）')
);
$pcQuality = array(
	array('quality'=>'2','width'=>'1280','height'=>'720','rate'=>'1500','rec'=>'wifi','desc'=>'高清（720p，1500k）'),
	array('quality'=>'1','width'=>'960','height'=>'540','rate'=>'800','rec'=>'4g','desc'=>'普清（540p，800k）'),
	//array('quality'=>'0','width'=>'640','height'=>'360','rate'=>'600','rec'=>'','desc'=>'流畅（360p，600k）')
);
//返回数据
$res = array(
	'status' => 1,
	'liveID' => 0,
	'list'   => $liveInfo,
	'appQualityConf' => $appQuality,
	'pcQualityConf' =>  $pcQuality
);
//第一次直播
if( !isset( $live['liveid'] ) )
{
	//mylog( '用户'.$uid.'准备第一次直播', LOG_DIR . 'Live.error.log' );
	LiveLog::applog('record:用户'.$uid.'准备第一次直播');
	succ( $res );
}
//发过直播,获取上次直播数据
$liveInfo['gameID']      = $live['gameid'];
$liveInfo['gameTypeID']  = $live['gametid'];
$liveInfo['gameName']    = $live['gamename'];
$liveInfo['liveType']    = $live['livetype'];
$liveInfo['title']       = $live['title'] ? $live['title'] : '';
$liveInfo['orientation'] = $live['orientation'] ? $live['orientation'] : '0';
$liveInfo['quality']     = $live['quality'] ? $live['quality'] : '0';

//发过直播，开新直播
if( isset( $live['status'] ) && $live['status'] != LIVE && $live['status'] != LIVE_CREATE )
{
	$res['list'] = $liveInfo;
	//mylog( '用户['.$uid."]准备发起一场直播", LOG_DIR . 'Live.error.log' );
	LiveLog::applog('record:用户['.$uid."]准备发起一场直播");
	succ( $res );
}
//继续直播
else
{
	//mylog( '用户'.$uid.'准备继续一场直播', LOG_DIR . 'Live.error.log' );
	LiveLog::applog('record:用户'.$uid.'准备继续一场直播');
	$res['liveID'] = $live['liveid'];
	//继续直播
	if( $deviceid == $live['deviceid'] )
	{
		$res['status'] = 0;
	}
	//异地直播
	else
	{
		$res['status'] = 2;
	}
	$res['list'] = $liveInfo;
	succ( $res );
}
//mylog( '准备直播直播检测出错:' . json_encode( $live ), LOG_DIR . 'Live.error.log' );
LiveLog::applog('record:准备直播直播检测出错:' . json_encode( $live ));





