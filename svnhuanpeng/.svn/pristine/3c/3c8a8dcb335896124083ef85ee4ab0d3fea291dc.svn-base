<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/2/16
 * Time: 下午9:23
 */

include '../../../include/init.php';
include '../../../../include/zmopSDK/ZmopSdk.php';
//include '../../../../include/zmopSDK/zmop/request/ZhimaCustomerCertificationInitializeRequest.php';

class TestZhiMaCustomerCertificationInitialize
{
	//芝麻信用网关地址
	public $gatewayUrl = "https://zmopenapi.zmxy.com.cn/openapi.do"; //'https://openapi.alipay.com/gateway.do';//
	//商户私钥文件
	public $privateKeyFile = "/usr/local/huanpeng/include/zmopSDK/app_private_key.pem";
	//芝麻公钥文件
	public $zmPublicKeyFile = "/usr/local/huanpeng/include/zmopSDK/zm_public_key.pem";
	//数据编码格式
	public $charset = "UTF-8";
	//芝麻分配给商户的 appId
	public $appId = '1001878';

	public function run($cert_name,$cert_no,$uid)
	{
		$identityParam = array(
			'identity_type'=>'CERT_INFO',
			'cert_type'=>'IDENTITY_CARD',
			'cert_name'=>$cert_name,
			'cert_no'=>$cert_no
//			'principal_id'=>$uid
		);

		$client = new ZmopClient($this->gatewayUrl,$this->appId,$this->charset,$this->privateKeyFile,$this->zmPublicKeyFile);
		$request = new ZhimaCustomerCertificationInitializeRequest();
		$request->setPlatform("zmop");
		$request->setTransactionId($this->getTransactionid());// 必要参数
		$request->setProductCode("w1010100000000002978");// 必要参数
		$request->setBizCode("FACE");// 必要参数
		$request->setIdentityTypeParam(2);
		$request->setIdentityParam(json_encode($identityParam));// 必要参数
		$request->setExtBizParam("{}");// 必要参数

		$result = $client->getExecuteParams($request);

//		return $result;
		$response = $client->execute($request);
		print_r($response);
		return $response;
//		echo json_encode($response);
	}

	public function getTransactionid()
	{
		return 'HPUC'.date('YmdHis').rand(1000,9999).'_'.rand(100000000,999999999);
	}

}

$uid = 15;

$cert_name='苏杭';

$cert_no = '340403199206292433';

$certInit = new TestZhiMaCustomerCertificationInitialize();

$ret = $certInit->run($cert_name,$cert_no,$uid);

succ($ret);


