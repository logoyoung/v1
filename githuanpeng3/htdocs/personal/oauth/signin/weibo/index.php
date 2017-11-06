<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/3/30
 * Time: 下午4:40
 */

require '../../../../../include/init.php';
include_once INCLUDE_DIR.'loginSDK/ThreePartyCallBack.class.php';
session_start();
$Three = new ThreePartyCallBack();
$r = $Three->weiboCallBack();
exit;
require '../../../../../include/init.php';
include INCLUDE_DIR . 'loginSDK/weibo/config.php';
include INCLUDE_DIR. 'loginSDK/weibo/saetv2.ex.class.php';
include INCLUDE_DIR."User.class.php";
session_start();

//设置页面指令

if($_GET['three_notify_order']){
	hpsetCookie('three_notify_order', $_GET['three_notify_order']);
}


$orderList = ['login','bind'];
$channel = 'weibo';//qq,weibo,wechat

//==============================/
//验证模块
//==============================/
$oauth = new SaeTOAuthV2(WB_AKEY, WB_SKEY);
//$oauth->debug = true;

if($_GET['error_code']){
	$code = $_GET['error_code'];
	$wb_error = array(
		21330 => '用户或授权服务器拒绝授予数据访问权限',
		21327 => 'token过期'
	);
	exit($wb_error[$code]);
}

if(isset($_REQUEST['code'])) {
	$keys = array();
	$keys['code'] = $_REQUEST['code'];
	$keys['redirect_uri'] = WB_CALLBACK_URL;
	try {
		$token = $oauth->getAccessToken('code', $keys);
	} catch (OAuthException $e) {
//		print_r($e);
	}
}else{
	$login_url = $oauth->getAuthorizeURL(WB_CALLBACK_URL);
	header("Location:$login_url");
	exit;
}

if(!$token){
	//todo error 验证错误
	exit('token error');
}

//$_SESSION['token'] = $token;
setcookie( 'weibojs_'.$oauth->client_id, http_build_query($token) );
$c = new SaeTClientV2( WB_AKEY , WB_SKEY , $token['access_token']);//$_SESSION['token']['access_token'] );
$uid_get = $c->get_uid();
$uid = $uid_get['uid'];
$userMsg = $c->show_user_by_id($uid);

$tm = time();
//==============================
/*执行模块*/
//==============================

//print_r($userMsg);



if($userMsg['id']){
	$db = new DBHelperi_huanpeng();
//	$order = $_SESSION['notify_order'];
	$order = $_COOKIE['three_notify_order'];
	hpdelCookie('three_notify_order');

	$openid = $userMsg['id'];
	$data = ['nickname'=>$userMsg['name'],'pic'=>$userMsg['profile_image_url']];
	if(in_array($order, $orderList) && $order == $_COOKIE['three_startPage_order']){
		hpdelCookie('three_startPage_order');
//		$_SESSION['startPage_order'] = $_SESSION['notify_order'] = '';
		if($order == 'login'){
			threeSideHandleError($order,threeSideLogin($openid,$channel,$data,$db));
		}else{
			threeSideHandleError($order,threeSideBind($openid,$channel,$data,$db));
		}
	}else{
		exit('order 错误');
	}
}else{
	exit('页面访问失败，请从新认证');
}
