<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/11/23
 * Time: 下午8:48
 */

include '../../../include/init.php';

define( 'WEIXIN_LOG', LOG_DIR . "weixin.log" );

$order   = $_GET['order'];
$channel = $_GET['channel'];

$getData = array(
	'three_notify_order' => $order
);

if ( isset( $_GET["ref_url"] ) && $_GET['ref_url'] )
{
//	$getData['ref_url'] = $_GET['ref_url'];
	hpsetCookie( "t_login_ref_url", $_GET['ref_url'], 600 );
}

if ( $channel == 'wechat' )
{
	if ( $_GET['isWxClient'] )
	{
		$wechatData = array(
			'suid'               => (int)$_GET['refUser'],
			'luid'               => (int)$_GET['luid'],
			'isClient'           => '1',
			'three_notify_order' => 'share-login'
		);

		hpsetCookie( 'wx-share-user', (int)$_GET['refUser'] );
		hpsetCookie( 'wx-anchor-uid', (int)$_GET['luid'] );

//		$_SESSION['wx-share-user'] = (int)$_GET['refUser'];
//		$_SESSION['wx-anchor-uid'] = (int)$_GET['luid'];
	}
	else
	{
		$wechatData = array(
			'three_notify_order' => $order
		);
	}

	$getData = array_merge( $getData, $wechatData );
}


$url         = WEB_PERSONAL_URL . 'oauth/signin/';
$channelList = [
	'wechat' => $url . 'weixin/index.php?' . http_build_query( $getData ),
	'qq'     => $url . 'qq/index.php?' . http_build_query( $getData ),
	'weibo'  => $url . 'weibo/index.php?' . http_build_query( $getData ),
];
$orderList   = [ 'login', 'bind' ];


mylog( "header location url is " . $channelList[$channel], WEIXIN_LOG );

if ( in_array( $order, $orderList ) && $channelList[$channel] )
{
	hpsetCookie( 'three_startPage_order', $getData['three_notify_order'] );
	if ( !isset( $_GET['ref'] ) )
	{
		hpsetCookie( '_loginway', $channel );
	}
//	$_SESSION['startPage_order'] = $order;
	header( 'Location:' . $channelList[$channel] );
}
else
{
	header( 'Location:' . WEB_ROOT_URL );
}