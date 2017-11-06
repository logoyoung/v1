<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/12/6
 * Time: 上午11:54
 */

include '../../../include/init.php';
//include_once INCLUDE_DIR."User.class.php";
//include_once INCLUDE_DIR."payment/wx/WxPay.Config.php";
//include_once INCLUDE_DIR."payment/wx/WxPay.Api.php";
//include_once INCLUDE_DIR."payment/wx/NativePay.class.php";
//include_once INCLUDE_DIR.'RechargeApi.class.php';
//include_once INCLUDE_DIR."redis.class.php";

use service\payment\WxpayUnified;

$db    = new \DBHelperi_huanpeng();
$redis = new \RedisHelp();

$wxUnified = new WxpayUnified( $db, $redis );

$wxUnified->unifiedorder( $_POST );

exit;

$params = array(
	'uid'         => [
		'type' => 'int',
		'must' => true
	],
	'encpass'     => [
		'type' => 'string',
		'must' => true,
	],
	'quantity'    => [//数量
		'type' => 'int',
		'must' => true,
	],
	'productID'   => [
		'type' => 'int',
		'must' => true
	],
	'channel'     => [
		'type'   => 'string',
		'must'   => true,
		'values' => [ 'wechat', 'alipay' ]
	],
	'client'      => [
		'type'   => 'string',
		'must'   => true,
		'values' => [ 'web', 'android', 'ios' ]
	],
	'refUrl'      => 'string',
	'promationID' => 'int'
);

$result = array();

if ( !checkParam( $params, $_POST, $result ) )
{
	error2( -4013 );
}
foreach ( $result as $key => $val )
{
	$$key = $val;
}

$tradeTypeList = array(
	'web'     => 'NATIVE',
	'android' => 'APP',
	'ios'     => 'APP'
);

$tradeType = $tradeTypeList[$client];


$db = new DBHelperi_huanpeng();

$userHelp = new UserHelp( $uid, $db );

if ( $code = $userHelp->checkStateError( $encpass ) )
{
	error2( $code );
}


HpProduct::$db = RechargeOrder::$db = $db;

HpProduct::setproductid( $productID );
if ( !HpProduct::getifno() )
{
	error2( -4082, 2 );//无此商品
}

$id = RechargeOrder::createOrder( $uid, $quantity, $channel, $client, $refUrl, $promationID );

if ( !$id )
{
	error2( -5554 );
}


//if($client == 'web' || $client == 'ios'){
//	WxPayConfig::$client =  'ios';
//}else{
//	WxPayConfig::$client = $client;
//}

WxPayConfig::$client = $client;


$notify = new NativePay();

$input = new WxPayUnifiedOrder();
$input->setBody( HpProduct::$name );
//$input->SetAppid();
$input->SetAttach( json_encode( [ 'client' => $client ] ) );
$input->SetOut_trade_no( RechargeOrder::$orderid );
$input->SetTotal_fee( '' . RechargeOrder::$total_price );
if ( $GLOBALS['env'] == 'DEV' )
{
	$input->SetTotal_fee( '1' );
}
$input->SetTime_start( date( "YmdHis" ) );
$input->SetTime_expire( date( "YmdHis", time() + 600 ) );
$input->SetGoods_tag( "test" );
$input->SetNotify_url( WEB_ROOT_URL . "api/wxpay/" . WxPayConfig::$client . "Notify.php" );
$input->SetTrade_type( $tradeType );
$input->SetProduct_id( HpProduct::$product_id );

strtotime( $input->GetTime_expire() );

$redis                     = new RedisHelp();
$rechargeOrderStatus_redis = "recharge:" . RechargeOrder::$orderid . "-" . $uid;
$redis->set( $rechargeOrderStatus_redis, 0, 600 );

$result = $notify->GetPayUrl( $input );

$backParam = array(
	'APP'    => array( 'prepay_id', 'sign', 'nonce_str' ),//'mch_id'
	"NATIVE" => array( 'code_url' )
);

$resultParam = array();

if ( $result['return_code'] == 'SUCCESS' )
{
	if ( $result['result_code'] == 'SUCCESS' )
	{
		foreach ( $backParam[$tradeType] as $val )
		{
			$resultParam[$val] = $result[$val];
		}
	}
	else
	{
		error2( wxErrorToHuanPeng( $result['error_code'] ) );
	}
}
else
{
	exit( json_encode( $result ) );
}

$content = array(
	'orderid'    => RechargeOrder::$orderid,
	'uid'        => $uid,
	'totalPrice' => RechargeOrder::$total_price,
	'tm'         => time()
);


if ( $tradeType == 'APP' )
{
	$nonce_str = substr( md5( time() . '' . rand( 1, 99999 ) . $resultParam['nonce_str'] ), 0, 20 );
	$data      = array(
		'appid'     => WxPayConfig::getConstValue( 'APPID' ),
		'timestamp' => '' . $content['tm'],
		'noncestr'  => $nonce_str,
		'package'   => 'Sign=WXPay',
		'partnerid' => WxPayConfig::getConstValue( 'MCHID' ),//$resultParam['mch_id'],
		'prepayid'  => $resultParam['prepay_id']
	);
	ksort( $data );
	$str = '';
	foreach ( $data as $k => $v )
	{
		if ( $k != 'sign' && $v != "" && !is_array( $v ) )
		{
			$str .= $k . "=" . $v . '&';
		}
	}
	$str = trim( $str, '&' );
	$str = $str . "&key=" . WxPayConfig::getConstValue( 'KEY' );;//WxPayConfig::KEY;

	$sign                     = strtoupper( md5( $str ) );
	$resultParam['nonce_str'] = $nonce_str;
	$resultParam['sign']      = $sign;
	$resultParam['version']   = 'test9';
	$resultParam['data']      = $data;
	$resultParam['string']    = $str;
}

succ( array_merge( $content, $resultParam ) );

function wxErrorToHuanPeng( $code )
{
	exit( json_encode( $code ) );
}