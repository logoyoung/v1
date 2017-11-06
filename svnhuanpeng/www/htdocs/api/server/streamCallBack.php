<?php
/**
 * 推流鉴权
 * auchor: Dylan
 * date: 2017-01-05 17:00
 */
include( __DIR__ . '/../../../include/init.php' );
use lib\Live;
use lib\CDNHelper;
use lib\live\LiveLog;

//mylog( '鉴权回调', LOG_DIR . 'Live.error.log' );
$uid    = isset( $_GET['uid'] ) ? (int)( $_GET['uid'] ) : '';
$liveID = isset( $_GET['liveid'] ) ? (int)( $_GET['liveid'] ) : '';
$tm     = isset( $_GET['tm'] ) ? trim( $_GET['tm'] ) : '';
$wsSign = isset( $_GET['sign'] ) ? trim( $_GET['sign'] ) : '';
//测试白名单，测完立即关闭
if($uid == 888888)
{
	echo 1;
	exit;
}
if( empty( $uid ) || empty( $liveID ) || empty( $tm ) || empty( $wsSign ) )
{	//mylog( "$uid-$liveID-$tm-$wsSign", LOG_DIR . 'Live.error.log' );
	//mylog( '推流鉴权参数不全', LOG_DIR . 'Live.error.log' );
	LiveLog::wslog("error:推流鉴权参数不全 $uid-$liveID-$tm-$wsSign");
	echo 0;
	exit;
}
$signData = array( 'uid' => $uid, 'liveid' => $liveID, 'tm' => $tm );
$hpSign   = CDNHelper::getPublishLiveSecret( $signData );

$db = new DBHelperi_huanpeng();
if( $hpSign != $wsSign || !Live::checkPubStream( $liveID, $db ) )
{
	//mylog( '鉴权不通过', LOG_DIR . 'Live.error.log' );
	LiveLog::wslog("error:{$liveID}鉴权不通过");
	echo 0;
	exit;
}
if( !Live::checkLiveExistByUid( $uid, $liveID, $db ) )
{
	//mylog( '鉴权不存在的直播', LOG_DIR . 'Live.error.log' );
	LiveLog::wslog("error:{$liveID} 鉴权不存在的直播");
	echo 0;
	exit;
}
echo 1;
exit;


