<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/2/16
 * Time: 下午4:57
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
	'biz_no'=>[
		'type'=>'string',
		'must'=>true
	]
);

$result = array();

if(!checkParam($params,$_POST,$result)){
	error2(-4013);
}

foreach ($result as $key => $val){
	$$key = $val;
}

//$db = new DBHelperi_huanpeng();
//
//$userHelp = new UserHelp($uid, $db);
//
//if($code = $userHelp->checkStateError($encpass)){
//	error2($code);
//}

$businessParam = array(
	'biz_no'=>$biz_no
);

$commonParam = array(
	'app_id'=>ALIPAY_APP_ID,
	'method'=>'zhima.customer.certification.certify',
	'charset' =>'utf-8',
	'sign_type' => "RSA",
	'timestamp'=> date('Y-m-d H:i:s'),
	'version' => '1.0',
	'return_url' => 'https://www.taobao.com',
	'biz_content'=>json_encode(argSort($businessParam))
);

$result = paraFilter($commonParam);

$result['sign_type'] = $commonParam['sign_type'];

$result = argSort($result);

$data = createLinkstring($result);

$private_key_path = $alipay_config['app_private_key_path'];
$sign = rsaSign($data,$private_key_path,'',$result['sign_type']);

$result['sign']=$sign;

$result = createLinkstringUrlencode($result);

$url = 'https://openapi.alipay.com/gateway.do';

//$ret = file_get_contents($url.'?'.$result);

succ(array('back_url'=>$url."?".$result));