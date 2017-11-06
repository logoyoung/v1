<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/1
 * Time: 上午11:19
 */
require_once '../../../../../include/init.php';
include_once INCLUDE_DIR . "User.class.php";

define( 'WEIXIN_LOG', LOG_DIR . "weixin.log" );

session_start();

$express = 1200;


//appsecret 77db2db8fe919c11aa27fba0a0c5f3de
//appid wx8025f0afdf08a83f

//设置页面指令
if ( $_GET['three_notify_order'] )
{
	hpsetCookie( 'three_notify_order', $_GET['three_notify_order'] );
}
//	$_SESSION['notify_order'] = $_GET['notify_order'];

if ( $_GET['three_notify_order'] == 'share-login' )
{
	if ( $_GET['suid'] )
	{
		hpsetCookie( 'wx-share-user', (int)$_GET['suid'] );
	}
	if ( $_GET['luid'] )
	{
		hpsetCookie( 'wx-anchor-uid', (int)$_GET['luid'] );
//		$_SESSION['wx-anchor-uid'] = (int)$_GET['luid'];
	}
}

$orderList = [ 'login', 'bind', 'share-login' ];
$channel   = 'wechat';//qq,weibo,wechat


//==============================/
//验证模块
//==============================/


mylog( "================begin login================", WEIXIN_LOG );
if ( $_REQUEST['isClient'] )
{
	$appid        = "wx79c0b818ca367bc6";
	$appsecret    = '01c6d59a3f9024db6336662ac95c8e74';
}
else
{
	$appid        = "wx8025f0afdf08a83f";
	$appsecret    = '77db2db8fe919c11aa27fba0a0c5f3de';
}


$redirect_uri = urlencode( WEB_PERSONAL_URL . 'oauth/signin/weixin/index.php' );
$response_type = 'code';
$scope         = 'snsapi_login';

$order = $_COOKIE['three_startPage_order'];


$redis            = new RedisHelp();
$redis_cookie_key = '_three_' . $order . '_key';
$wx_state_key     = $_COOKIE[$redis_cookie_key];
if ( !empty( $wx_state_key ) )
{
	mylog( 'cookie key is set', WEIXIN_LOG );

	$redis_wx_state = $redis->get( $wx_state_key );
	if ( !$redis_wx_state )
	{
		mylog( 'redis value is not set', WEIXIN_LOG );
		$redis_wx_state = md5( uniqid( rand(), true ) );
		$redis->set( $wx_state_key, $redis_wx_state, $express );
	}
	else
	{
		mylog( 'redis value is set', WEIXIN_LOG );
	}
}
else
{
	mylog( 'cookie key is not set', WEIXIN_LOG );
	$wx_state_key = md5( time() . rand( 10, 1000000 ) );
	hpsetCookie( $redis_cookie_key, $wx_state_key, $express );

	$redis_wx_state = md5( uniqid( rand(), true ) );
	$redis->set( $wx_state_key, $redis_wx_state, $express );
}


mylog( "================end login================\n", WEIXIN_LOG );


$state = $redis_wx_state;

//$redis = new RedisHelp();
//$redis_cookie_key = '_three_'.$order."_key";
//$wx_state_key = $_COOKIE[$redis_cookie_key];
//mylog('current redis key is [cookie]'.$wx_state_key, WEIXIN_LOG);
//$redis_wx_state = $redis->get($wx_state_key);
//if(!$redis_wx_state){
//	$state = md5(uniqid(rand(), true));
//	$redis->set($wx_state_key, $state, $express);
//
//	mylog('test set redis the value is '. $redis->get($wx_state_key),WEIXIN_LOG);
//
//	$redis_cookie_val = md5(json_encode($_COOKIE).time().rand(10,1999999));
//	hpsetCookie($redis_cookie_key, $redis_cookie_val, $express);
//}else{
//	$state = $redis_wx_state;
//}


if ( $_REQUEST['code'] && $_REQUEST['state'] && $_REQUEST['state'] == $redis_wx_state )
{

	mylog( "certify success", WEIXIN_LOG );

	$code      = $_REQUEST['code'];
	$token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $appsecret . '&code=' . $code . '&grant_type=authorization_code';
	$token     = json_decode( file_get_contents( $token_url ), true );

	if ( !$token || isset( $token['errorcode'] ) )
	{
		exit( json_encode( $token ) );
	}

	$access_token_url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=' . $appid . '&grant_type=refresh_token&refresh_token=' . $token['refresh_token'];
	$access_token     = json_decode( file_get_contents( $access_token_url ) );


	if ( isset( $access_token->errcode ) )
	{
		echo '<h1>错误：</h1>' . $access_token->errcode;
		echo '<br/><h2>错误信息：</h2>' . $access_token->errmsg;
		exit;
	}

	$user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token->access_token . '&openid=' . $access_token->openid . '&lang=zh_CN';
	$user_info     = json_decode( file_get_contents( $user_info_url ) );

	if ( isset( $user_info->errorcode ) )
	{
		echo '<h1>错误：</h1>' . $user_info->errcode;
		echo '<br/><h2>错误信息：</h2>' . $user_info->errmsg;
		exit;
	}

	//==============================
	/*执行模块*/
	//==============================
	$db = new DBHelperi_huanpeng( false );

	$data   = [ 'nickname' => $user_info->nickname, 'pic' => $user_info->headimgurl[0], 'unionid' => $user_info->unionid ];
	$openid = $user_info->openid;
	if ( in_array( $order, $orderList ) && $order == $_COOKIE['three_notify_order'] )
	{

//		dong_log('邀请','session wx-share-user'.$_SESSION['wx-share-user'],$db);
		//微信share流程处理
		if ( $order == 'share-login' )
		{
			mylog( "share_login start", WEIXIN_LOG );
//			print_r('share_login start');
			UserHelp::$db2 = $db;
			$isOpenidUsed  = UserHelp::isOpenidUsed( $openid, $channel );

			$loginRef = threeSideLogin( $openid, $channel, $data, $db );
			$suid     = $_COOKIE['wx-share-user'];
			$ruid     = $loginRef['uid'];
			$luid     = $_COOKIE['wx-anchor-uid'];
			if ( $loginRef && is_array( $loginRef ) )
			{
				setUserLoginCookie( $loginRef['uid'], $loginRef['encpass'], 30 );
				if ( UserHelp::getUserEncpass( $suid ) && !$isOpenidUsed )
				{
					if ( $suid && $ruid && $suid != $ruid )
					{

						$inviteResult = inviteRecord( $suid, $ruid, $luid, $db );
						mylog( "inviteRecord $suid $ruid " . (int)$inviteResult, WEIXIN_LOG );
					}
				}
				$sessionRet = 'success';
			}
			else
			{
				$sessionRet = 'failed';
			}
			mylog( "sessionRet: $sessionRet", WEIXIN_LOG );

			$reqData = array(
				'u'       => $luid,
				'channel' => 'wechat_callback',
//				'suid'=>$suid
			);
			wx_login_clear_cache( $redis );
			header( 'Location:' . WEB_ROOT_URL . 'h5share/live.php?' . http_build_query( $reqData ) );
		}
		elseif ( $order == 'login' )
		{
			wx_login_clear_cache( $redis );
			threeSideHandleError( $order, threeSideLogin( $openid, $channel, $data, $db ) );
		}
		else
		{
			wx_login_clear_cache( $redis );
			threeSideHandleError( $order, threeSideBind( $openid, $channel, $data, $db ) );
		}
	}
}
else
{
	if ( $_REQUEST['state'] )
	{
		mylog( "certify error", WEIXIN_LOG );
		wx_login_clear_cache( $redis );
		if ( $_REQUEST['state'] == $redis_wx_state )
		{
			threeSideHandleError( 'login', -4069 );
		}
		else
		{
			threeSideHandleError( 'login', -4070 );
		}
	}
	else
	{
		if ( $_REQUEST['isClient'] )
		{
			$login_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=$scope&state=$state#wechat_redirect";
		}
		else
		{
			$login_url = "https://open.weixin.qq.com/connect/qrconnect?appid=$appid&redirect_uri=$redirect_uri&response_type=$response_type&scope=$scope&state=$state";//#wechat_redirect
		}
		header( 'Location:' . $login_url );
	}
}


function wx_login_clear_cache( $redis )
{
	$order            = $_COOKIE['three_notify_order'];
	$redis_cookie_key = '_three_' . $order . "_key";
	$wx_state_key     = $_COOKIE[$redis_cookie_key];

	$redis->del( $wx_state_key );

	$cookieKeyList = [
		$redis_cookie_key,
		'_three_login_key',
		'_three_login_key',
		'_three_share-login_key',
		'three_notify_order',
		'wx-share-user',
		'wx-anchor-uid',
		'three_startPage_order'
	];

	foreach ( $cookieKeyList as $val )
	{
		hpdelCookie( $val );
	}
}