<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/12/13
 * Time: 下午5:44
 */

include '../../../include/init.php';
//include INCLUDE_DIR."RechargeApi.class.php";
//include INCLUDE_DIR."payment/alipay/alipay.config.php";
//include INCLUDE_DIR."payment/alipay/alipay_submit.class.php";
//include_once INCLUDE_DIR."User.class.php";
//include_once INCLUDE_DIR."redis.class.php";

use service\payment\Alipay;

$db = new \DBHelperi_huanpeng();
$redis = new \RedisHelp();

$alipay = new Alipay($db, $redis);
$alipay->unifiedorder($_POST);
exit;

$params = array(
	'uid'=>[
		'type'=>'int',
		'must'=>true
	],
	'encpass'=>[
		'type'=>'string',
		'must'=>true,
	],
	'quantity'=>[//数量
		'type'=>'int',
		'must'=>true,
	],
	'productID'=>[
		'type'=>'int',
		'must'=>true
	],
	'channel'=>[
		'type'=>'string',
		'must'=>true,
		'values'=>['wechat','alipay']
	],
	'client'=>[
		'type'=>'string',
		'must'=>true,
		'values'=>['web','android','ios']
	],
	'refUrl'=>'string',
	'promationID'=>'int'
);

$result = array();

if(!checkParam($params,$_POST,$result)){
	error2(-4013);
}

foreach ($result as $key=>$val){
	$$key = $val;
}

$db = new DBHelperi_huanpeng();

$userHelp = new UserHelp($uid,$db);
if($code = $userHelp->checkStateError($encpass)){
	error2($code);
}

HpProduct::$db = RechargeOrder::$db = $db;

HpProduct::setproductid($productID);
if(!HpProduct::getifno()){
	error2();//无此商品
}

$id = RechargeOrder::createOrder($uid,$quantity,$channel,$client,$refUrl,$promationID);

if(!$id){
	error2(-5554);
}


$payment_type = 1;

$notify_url = WEB_ROOT_URL."api/alipay/notify.php";

$out_trade_no = RechargeOrder::$orderid;

$subject = HpProduct::$name; //订单名称

$total_fee = RechargeOrder::$total_price/100; //注意这里和微信支付不同，注意转换
if($GLOBALS['env'] == 'DEV'){
	$total_fee = '0.01';
}

$body = HpProduct::$name;

$private_key_path = $alipay_config['app_private_key_path'];//'/usr/local/huanpeng/include/payment/alipay/app_private_key.pem';

$parameter = array(
	"service" => "create_direct_pay_by_user",
	"partner" => trim($alipay_config['partner']),
	"seller_email" => trim($alipay_config['seller_email']),
	"payment_type"	=> $payment_type,
	"notify_url"	=> $notify_url,
	"return_url"	=> $return_url,
	"out_trade_no"	=> $out_trade_no,
	"subject"	=> $subject,
	"total_fee"	=> "$total_fee",
	"body"	=> $body,
	"show_url"	=> $show_url,
	"anti_phishing_key"	=> $anti_phishing_key,
	"exter_invoke_ip"	=> $exter_invoke_ip,
	"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
);

$alipaySubmit = new AlipaySubmit($alipay_config);

if($client !='web'){
	//支付宝公共参数
	$commonParam = array(
		'app_id' =>ALIPAY_APP_ID,//'2016072900114850',//
		'method' =>'alipay.trade.app.pay',
		'charset' =>'utf-8',
		'sign_type' => "RSA",
		'timestamp'=> date('Y-m-d H:i:s'),
		'version' => '1.0',
		'notify_url' => $notify_url
	);

	//支付宝业务参数
	$businessParam = array(
		'body' => $body,
		'subject' =>$subject,
		'out_trade_no' => $out_trade_no,
		'timeout_express' => '30m',
		'total_amount' => $total_fee,
		'product_code' => "QUICK_MSECURITY_PAY"
	);

	$commonParam['biz_content'] = json_encode(argSort($businessParam));

	$result = paraFilter($commonParam);
	$result['sign_type'] = $commonParam['sign_type'];

	$result = argSort($result);

	//对未签名的原始字符串进行签名 不需要urlencode
	$data = createLinkstring($result);

	$sign = rsaSign($data, $private_key_path, '', $commonParam['sign_type']);

	$result['sign'] = $sign;

	$result = createLinkstringUrlencode($result);

	//设置redis
	$redis = new RedisHelp();
	$rechargeOrderStatus_redis = "recharge:".RechargeOrder::$orderid."-".$uid;
	$redis->set($rechargeOrderStatus_redis,0,600);

	succ(array('orderid'=>$out_trade_no,'uid'=>$uid,'totalPrice'=>RechargeOrder::$total_price,'params'=>$result));
}

