<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/12/26
 * Time: 下午4:06
 */

include_once '../../../include/init.php';
//include_once INCLUDE_DIR.'payment/alipay/alipay_submit.class.php';
//include_once INCLUDE_DIR."payment/alipay/alipay_web.config.php";
//include_once INCLUDE_DIR."redis.class.php";

use service\payment\Alipay;

$data = array(
	'uid'=>$_COOKIE['_uid'],
	'encpass'=>$_COOKIE['_enc'],
	'quantity'=>$_GET['quantity'],
	'productID' => 5,
	'channel'=>'alipay',
	'client'=>'web',
	'refUrl'=>$_GET['refUrl'],
	'promotionID'=>$_GET['promotionID']
);

$db = new \DBHelperi_huanpeng();
$redis = new \RedisHelp();

$alipay = new Alipay($db, $redis);
$alipay->unifiedorder($data);

exit();

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

if(!checkParam($params,$data,$result)){
	error2(-4013);
}

foreach ($result as $key=>$val){
	$$key = $val;
}

$db = new DBHelperi_huanpeng();
$redis = new \RedisHelp();

$userHelp = new User($uid,$db, $redis);

$code = $userHelp->checkStateError($encpass);
if(true !== $code)
{
	error2($code);
}


$financeObj = new Finance($db,$redis);


//todo  change
$rmb = intval($_GET['rmb']) ? intval($_GET['rmb']) : $quantity/10;

//insert into huanpeng record and get tid

$id = $financeObj->rechargeOrderCreate($uid,$rmb,$channel,$client,$refUrl,$promationID,json_encode([]), 0);
if(!$id)
{
	error2(-5554);
}

//HpProduct::$db = RechargeOrder::$db = $db;
//
//HpProduct::setproductid($productID);
//if(!HpProduct::getifno()){
//	error2();//无此商品
//}
//
//$id = RechargeOrder::createOrder($uid,$quantity,$channel,$client,$refUrl,$promationID);
//if(!$id){
//	error2(-5554);
//}


/**************************请求参数**************************/
//商户订单号，商户网站订单系统中唯一订单号，必填
$out_trade_no = $id;//RechargeOrder::$orderid;

//订单名称，必填
$subject = "欢朋币充值";//HpProduct::$name;

//付款金额，必填
$total_fee = $rmb/100;
if($GLOBALS['env'] == 'DEV'){
	$total_fee = '0.01';
}

//商品描述，可空
$body = '';
/************************************************************/

//构造要请求的参数数组，无需改动
$parameter = array(
	"service"       => $alipay_config['service'],
	"partner"       => $alipay_config['partner'],
	"seller_email"  => $alipay_config['seller_email'],
	"payment_type"	=> $alipay_config['payment_type'],
	"notify_url"	=> $alipay_config['notify_url'],
	"return_url"	=> $alipay_config['return_url'],

	"anti_phishing_key"=>$alipay_config['anti_phishing_key'],
	"exter_invoke_ip"=>$alipay_config['exter_invoke_ip'],
	"out_trade_no"	=> $out_trade_no,
	"subject"	=> $subject,
	"total_fee"	=> "$total_fee",
	"body"	=> $body,
	"_input_charset"	=> trim(strtolower($alipay_config['input_charset'])),
//	"enable_paymethod" => 'bankPay'
	//其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.kiX33I&treeId=62&articleId=103740&docType=1
	//如"参数名"=>"参数值"

);
header("Content-type:text/html;charset=utf-8");
//建立请求
$alipaySubmit = new AlipaySubmit($alipay_config);
$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "");
echo $html_text;

?>
