<?php
include '../../../include/init.php';
use lib\Anchor;
use lib\Live;
use lib\LiveRoom;
use service\user\UserAuthService;
use lib\live\LiveLog;


function timeFormat( $seconds )
{
	$time['hour']    = floor( $seconds / ( 60 * 60 ) );
	$time['minute']  = floor( ( $seconds - ( $time['hour'] * 60 * 60 ) ) / 60 );
	$time['seconds'] = $seconds - ( $time['hour'] * 60 * 60 + $time['minute'] * 60 );
	$time            = array_map( function ( $v )
	{
		if ( $v < 10 )
		{
			return '0' . $v;
		}
		else
		{
			return "$v";
		}
	}, $time );

	return implode( ':', $time );
}

$uid     = isset( $_POST['uid'] ) ? (int)( $_POST['uid'] ) : '';
$encpass = isset( $_POST['encpass'] ) ? ( $_POST['encpass'] ) : '';
$liveID  = isset( $_POST['liveID'] ) ? (int)( $_POST['liveID'] ) : '';
if ( empty( $uid ) || empty( $encpass ) || empty( $liveID ) )
{
	error2( -993 );
}
$db = new DBHelperi_huanpeng();

//权限校验

$auth = new UserAuthService();
$auth->setUid( $uid );
$auth->setEnc( $encpass );

if ( $auth->checkLoginStatus() !== true )
{
	$errorCode = $result['error_code'];
	error2( '-1013', 2 );
	exit;
}
if ( !Anchor::isAnchor( $uid, $db ) )
{
	error2( -4057, 2 );
}
$Anchor = new Anchor( $uid );
//检测该直播是否属于主播
if ( !Live::checkLiveExistByUid( $uid, $liveID, $db ) )
{
	//error2( -4067, 2 );
	//不存在该直播
}
$LiveRoom       = new LiveRoom( $uid, $db );
$AnchorInfo     = $Anchor->infoForEndLive();
$liveIncome     = $LiveRoom->getLiveStatisticInfo( $liveID );
$liveTimeLength = Live::getLiveTimeLength( $liveID, $db );
if ( !$AnchorInfo || !$liveIncome || $liveTimeLength )
{
	//error todo
}
//mylog( json_encode( array( $AnchorInfo, $liveIncome, $liveTimeLength ) ), LOG_DIR . 'Live.error.log' );
//重组数据

$liveEndInfo              = array();
$liveEndInfo['nick']      = $AnchorInfo['nick'];
$liveEndInfo['head']      = $AnchorInfo['head'];
$liveEndInfo['fansCount'] = $AnchorInfo['fansCount'];
$liveEndInfo['level']     = $AnchorInfo['level'];
$liveEndInfo['isCertify'] = $AnchorInfo['isCertify'];
$liveEndInfo['hpcoin']    = round( $liveIncome['coin'], 2 );
$liveEndInfo['hpbean']    = round( $liveIncome['bean'], 3 );
$liveEndInfo['userCount'] = $liveIncome['peak'];
$liveEndInfo['liveLong']  = timeFormat( $liveTimeLength );

$getLimit                 = getAuchorVideoLimit( $uid, $db ); //获取发布录数
$getpublish               = getAnchorAlreadyPublishVideo( $uid, $db ); //获取已发布的录像数

if ( (int)$getpublish >= (int)$getLimit )
{
	$liveEndInfo['autoFull'] = '0';
}
else
{
	$liveEndInfo['autoFull'] = '1';
}

//直播时长为0测试日志
if ( !$liveTimeLength )
{
	//mylog( "异常：用户{$uid}直播编号为{$liveID}的直播时长为0", LOG_DIR . "Live.error.log" );
	LiveLog::applog( "error:用户{$uid}直播编号为{$liveID}的直播时长为0" );
}
succ( $liveEndInfo );
//todo
exit;


