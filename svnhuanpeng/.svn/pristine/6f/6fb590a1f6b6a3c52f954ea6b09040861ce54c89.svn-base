<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/2/16
 * Time: 下午1:44
 */

//require source


//request data check

/*
 * uid
 * encpass
 * cert_name
 * cert_no
 * cert_type
 * principal_id:uid
 * client => web, android, ios
 *
 */

include '../../../include/init.php';
include_once INCLUDE_DIR.'payment/alipay/alipay.config.php';
include_once INCLUDE_DIR.'payment/alipay/alipay_core.function.php';
include_once INCLUDE_DIR.'User.class.php';

$params =array(
//	'uid'=>[
//		'type'=>'int',
//		'must'=>true,
//	],
//	'encpass'=>[
//		'type'=>'string',
//		'must'=>true
//	],
	'cert_name'=>[
		'type'=>'string',
		'must'=>true
	],
	'cert_no'=> [
		'type'=>'string',
		'must'=>true
	]
);

$paperstype = 0;

$result = array();

if(!checkParam($params,$_POST,$result)){
	error2(-4013);
}

foreach ($result as $key=>$val){
	$$key = $val;
}

//if(identCodeValid($cert_no)){
//	error2(-4037,2);
//}


$db = new DBHelperi_huanpeng();

//$userHelp = new UserHelp($uid, $db);

//if($code = $userHelp->checkStateError($encpass)){
//	error2($code);
//}

//if(checkIdentID($cert_no, $paperstype, $db)){
//	error2(-4095);
//}


//check is user has identity certify

$name = $db->realEscapeString($cert_name);
$date = date('Y-m-d H:i:s');

//$sql = "insert into userrealname (`username`,`papersid`, `ctime`, `uid`) value('$name', '$cert_no', '$date', $uid)";
//if(!$db->query($sql)){
//	error2(-4041, 2);
//}

$identity_param = array(
	'identity_type'=>'CERT_INFO',
	'cert_type'=>'IDENTITY_CARD',
	'cert_name'=>$cert_name,
	'cert_no'=>$cert_no,
//	'principal_id'=>$uid
);

$identity_param = json_encode($identity_param);

$businessParam = array(
	'transaction_id'=>'HPUC'.date('YmdHis').rand(1000,9999).'_'.rand(100000000,999999999),
	'product_code' => 'w1010100000000002978',
	'biz_code'=>'FACE',
	'identity_param'=>$identity_param,
);

$commonParam = array(
	'app_id'=>ALIPAY_APP_ID,
	'method'=>'zhima.customer.certification.initialize',
	'charset' =>'utf-8',
	'sign_type' => "RSA",
	'timestamp'=> date('Y-m-d H:i:s'),
	'version' => '1.0',
	'biz_content'=>json_encode(argSort($businessParam))
);

$result = paraFilter($commonParam);

$result['sign_type'] = $commonParam['sign_type'];

$result = argSort($result);

$data = createLinkstring($result);

$private_key_path = $alipay_config['app_private_key_path'];
$sign = rsaSign($data,$private_key_path,'',$result['sing_type']);

$result['sign'] = $sign;

$val = $result;

$result = createLinkstringUrlencode($result);

$url = 'https://openapi.alipay.com/gateway.do';

$ret = file_get_contents($url.'?'.$result);

//on success should record
if($ret && $ret = json_decode($ret,true)){

	succ(array('biz_no'=>$ret,'val'=>$val));
	$info = $ret['zhima_customer_certification_initialize_response'];

	$code = $info['code'];

	if(!empty($code) && $code ){
		//todo record
//		succ(array('biz_no'=>$info['biz_no']));
	}
}

error2();//
