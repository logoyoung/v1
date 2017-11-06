<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/3/30
 * Time: 下午4:32
 */
require '../../../../../include/init.php';
include_once INCLUDE_DIR.'loginSDK/ThreePartyCallBack.class.php';
$Three = new ThreePartyCallBack();
$r = $Three->qqCallBack();
exit;

require '../../../../../include/init.php';
include_once INCLUDE_DIR.'loginSDK/qq/qqConnectAPI.php';
include_once INCLUDE_DIR."User.class.php";
include_once INCLUDE_DIR.'loginSDK/ThreePartyLogin.php';



//设置cookie？

//设置页面指令
if(!$_COOKIE['three_notify_order'])
	hpsetCookie('three_notify_order', $_GET['three_notify_order']);
//	$_SESSION['notify_order'] = $_GET['notify_order'];

$orderList = ['login','bind'];


//==============================/
//验证模块
//==============================/
$auth = new Oauth();
if(isset($_REQUEST['code'])){
//	$_SESSION['QC_userData']['state'] = $_GET['state'];
	$accessToken = $auth->qq_callback();
	$openid = $auth->get_openid();
}else{
	$auth->qq_login();
	exit();
}
function qq_request($comm, $timeout){
	$ch = curl_init();
	curl_setopt ($ch, CURLOPT_URL, $comm);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$file_contents = curl_exec($ch);
	curl_close($ch);
	return json_decode($file_contents,1);
}
$stime = microtime(true);

//$comm = "https://graph.qq.com/user/get_user_info?oauth_consumer_key=101368626&access_token=$accessToken&openid=$openid&format=json";
//$res = qq_request($comm, 5);
//
//$runTimelog[] = 'request get_user_info cost time'.(microtime(true)-$stime);
//
//$tm = time();

header("Content-type:text/html;charset=utf-8");
//==============================
/*执行模块*/
//==============================

//print_r($_COOKIE);


$data = array(
	'accessToken'=>$accessToken,
	'openid' => $openid,
);

$channel = LOGIN_CHANNEL_QQ;
$client = LOGIN_CLIENT_WEB;

$order = $_COOKIE['three_notify_order'];
if(in_array($order, $orderList) && $order == $_COOKIE['three_startPage_order']) {
    hpdelCookie('three_startPage_order');
    hpdelCookie('three_notify_order');
	$threeSideLogin = new ThreePartyLogin( $channel, $client, $data );
    //var_dump($order);
    //$result = $threeSideLogin->run( $order );
    if($order == $orderList[0])
	    $result = $threeSideLogin->run( $order );
    else
        $result = $threeSideLogin->run( $order, $_COOKIE['_uid'], $_COOKIE['_enc'] );

	var_dump($result);
	threeSideHandleError( $order, $result );
}else{
	threeSideHandleError($order, '-5001');
}

//if($res['ret'] == 0){
//	$db = new DBHelperi_huanpeng();
//
//	$order = $_COOKIE['three_notify_order'];
//	$data = ['nickname'=>$res['nickname'],'pic'=>$res['figureurl_qq_1']];
//
//	if(in_array($order, $orderList) && $order == $_COOKIE['three_startPage_order']){
////		$_SESSION['startPage_order'] = $_SESSION['notify_order'] = '';
//		hpdelCookie('three_startPage_order');
//		hpdelCookie('three_notify_order');
//
//		if($order == 'login'){
//			$code = threeSideLogin($openid,$channel,$data,$db);
//			threeSideHandleError($order,$code);
//		}else{
//			threeSideHandleError($order,threeSideBind($openid,$channel,$data,$db));
//		}
//	}else{
//		exit('页面访问失败，请从新认证');
//	}
//}
exit;
