<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/2/16
 * Time: 下午9:23
 */

include('../../../include/init.php');
include (INCLUDE_DIR.'payment/alipay/alipay.config.php');
include(INCLUDE_DIR.'zmopSDK/zmop/ZmopClient.php');
class TestZhimaCustomerCertificationCertify
{
	//芝麻信用网关地址
	public $gatewayUrl = "https://zmopenapi.zmxy.com.cn/openapi.do";
	//商户私钥文件
	public $privateKeyFile = '/usr/local/huanpeng/include/payment/alipay/app_private_key.pem';
	//芝麻公钥文件
	public $zmPublicKeyFile = "/usr/local/huanpeng/include/payment/alipay/app_public_key.pem";
	//数据编码格式
	public $charset = "UTF-8";
	//芝麻分配给商户的 appId
	public $appId = ALIPAY_APP_ID;

	public function run($biz_no)
	{
		$client = new ZmopClient($this->gatewayUrl,$this->appId,$this->charset,$this->privateKeyFile,$this->zmPublicKeyFile);
		$request = new ZhimaCustomerCertificationCertifyRequest();
		$request->setPlatform("zmop");
		$request->setBizNo($biz_no);// 必要参数
		$request->setReturnUrl("http://www.taobao.com");// 必要参数
		$response = $client->execute($request);

		return $response;
	}
}

$biz_no = $_POST['biz_no'];


$certCertify = new TestZhimaCustomerCertificationCertify();

$ret = $certCertify->run($biz_no);

print_r($ret);