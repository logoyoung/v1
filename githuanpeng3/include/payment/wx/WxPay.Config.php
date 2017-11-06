<?php
/**
* 	配置账号信息
*/

class WxPayConfig
{
	//=======【基本信息设置】=====================================
	//

	/**
	 * @var string 微信支付渠道，在应用WXPayConfig 是 必须设置，用来筛选不同渠道的不同appId 等相关配置信息
	 */
	static $client = '';

	/**
	 * TODO: 修改这里配置为您自己申请的商户信息
	 * 微信公众号信息配置
	 * 
	 * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
	 * 
	 * MCHID：商户号（必须配置，开户邮件中可查看）
	 * 
	 * KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
	 * 设置地址：https://pay.weixin.qq.com/index.php/account/api_cert
	 * 
	 * APPSECRET：公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置），
	 * 获取地址：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN
	 * @var string
	 */
	const IOS_APPID = 'wxe17443ad004d42a6';
	const IOS_MCHID = "1415754802";
	const IOS_KEY = "f6db803a518caf532bfe4bfde534bd5a";//'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456';
	const IOS_APPSECRET = '01c6d59a3f9024db6336662ac95c8e74';

	const ANDROID_APPID = "wxd463714f5fd0b48e";
	const ANDROID_MCHID = "1419175502";
	const ANDROID_KEY = 'k874kfDhg5839sd2Ad6193wAJHa9f7R8';//'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456';
	const ANDROID_APPSECRET = '01c6d59a3f9024db6336662ac95c8e74';

	const WEB_APPID = 'wx79c0b818ca367bc6';
	const WEB_MCHID = '1425294902';
	const WEB_KEY = '1e42c53d6b1cd24c09ef89f22d48d5d1';//'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456';
	const WEB_APPSECRET = '01c6d59a3f9024db6336662ac95c8e74';

	const H5_APPID = self::WEB_APPID;
	const H5_MCHID = self::WEB_MCHID;
	const H5_KEY = self::WEB_KEY;
	const H5_APPSECRET = self::WEB_APPSECRET;

	const WXJS_APPID = self::WEB_APPID;
	const WXJS_MCHID = self::WEB_MCHID;
	const WXJS_KEY = self::WEB_KEY;
	const WXJS_APPSECRET = self::WEB_APPSECRET;

	//=======【证书路径设置】=====================================
	/**
	 * TODO：设置商户证书路径
	 * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
	 * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
	 * @var path
	 */
	const SSLCERT_PATH = 'cert/apiclient_cert.pem';
	const SSLKEY_PATH = 'cert/apiclient_key.pem';

	const IOS_SSLCERT_PATH = 'cert/ios/apiclient_cert.pem';
	const IOS_SSLKEY_PATH = 'cert/ios/apiclient_key.pem';

	const ANDROID_SSLCERT_PATH = 'cert/android/apiclient_cert.pem';
	const ANDROID_SSLKEY_PATH = 'cert/android/apiclient_key.pem';

	const WEB_SSLCERT_PATH = 'cert/web/apiclient_cert.pem';
	const WEB_SSLKEY_PATH = 'cert/web/apiclient_key.pem';

	const H5_SSLCERT_PATH = self::WEB_SSLCERT_PATH;
	const H5_SSLKEY_PATH = self::WEB_SSLKEY_PATH;

	const WXJS_SSLCERT_PATH = self::WEB_SSLCERT_PATH;
	const WXJS_SSLKEY_PATH = self::WEB_SSLKEY_PATH;
	//=======【curl代理设置】===================================
	/**
	 * TODO：这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
	 * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
	 * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
	 * @var unknown_type
	 */
	const CURL_PROXY_HOST = "0.0.0.0";//"10.152.18.220";
	const CURL_PROXY_PORT = 0;//8080;
	
	//=======【上报信息配置】===================================
	/**
	 * TODO：接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
	 * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
	 * 开启错误上报。
	 * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
	 * @var int
	 */
	const REPORT_LEVENL = 1;

	public static function getConstValue($param){
		$client = strtoupper(static::$client);
		$const = $client.'_'.$param;

		return constant('WxPayConfig::'.$const);
	}
}
