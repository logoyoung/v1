<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/5/5
 * Time: 上午10:10
 */

namespace service\payment;

include_once __DIR__ . '/../../payment/alipay/alipay_submit.class.php';
include_once __DIR__ . "/../../payment/alipay/alipay_notify.class.php";
//todo 这个类需要整体重构，现在不方便扩展
class Alipay extends UnifiedOrder
{
	public function __construct( \DBHelperi_huanpeng $db, \RedisHelp $redisHelp )
	{
		parent::__construct( $db, $redisHelp );
	}

	public function unifiedorder( $data )
	{
		$rmb = 0;
		$id  = $this->_unifiedorder( $data, $rmb );
		/**************************请求参数**************************/
		//商户订单号，商户网站订单系统中唯一订单号，必填
		$out_trade_no = $id;//RechargeOrder::$orderid;

		//订单名称，必填
		$subject = $this->_params['productname'];//"欢朋币充值";//HpProduct::$name;

		//付款金额，必填
		$total_fee = $rmb * self::ALIPAY_RMB_RATE;
		if ( $GLOBALS['env'] != 'PRO' )
		{
			$total_fee = '0.01';
		}

		//商品描述，可空
		$body = '';
		/************************************************************/
		$alipay_config = $this->_getAliPayConfig( $this->_params['client'] );
		if ( $this->_params['client'] == 'web' )
		{
			$this->_webPay( "$out_trade_no", $subject, $total_fee, $body, $alipay_config );
		}
		elseif ( $this->_params['client'] == 'h5' )
		{
			$this->_mwebPay( "$out_trade_no", $subject, $total_fee, $body, $alipay_config );
		}
		else
		{
			$this->_mobilePay( "$out_trade_no", $subject, $total_fee, $body, $alipay_config );
		}
	}

	private function _webPay( $out_trade_no, $subject, $total_fee, $body, $alipay_config )
	{
		//构造要请求的参数数组，无需改动
		$parameter = array(
			"service"           => $alipay_config['service'],
			"partner"           => $alipay_config['partner'],
			"seller_email"      => $alipay_config['seller_email'],
			"payment_type"      => $alipay_config['payment_type'],
			"notify_url"        => $alipay_config['notify_url'],
			"return_url"        => $alipay_config['return_url'],
			"anti_phishing_key" => $alipay_config['anti_phishing_key'],
			"exter_invoke_ip"   => $alipay_config['exter_invoke_ip'],
			"out_trade_no"      => $out_trade_no,
			"subject"           => $subject,
			"total_fee"         => "$total_fee",
			"body"              => $body,
			"_input_charset"    => trim( strtolower( $alipay_config['input_charset'] ) ),
//	"enable_paymethod" => 'bankPay'
			//其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.kiX33I&treeId=62&articleId=103740&docType=1
			//如"参数名"=>"参数值"
		);
		header( "Content-type:text/html;charset=utf-8" );
//建立请求
		$alipaySubmit = new \AlipaySubmit( $alipay_config );
		$html_text    = $alipaySubmit->buildRequestForm( $parameter, "get", "" );
		echo $html_text;
	}

	private function _mwebPay( $out_trade_no, $subject, $total_fee, $body, $alipay_config )
	{
		$commonParam = [
			'service'        => 'alipay.wap.create.direct.pay.by.user',
			'partner'        => trim( $alipay_config['partner'] ),
			'seller_id'   => $alipay_config['seller_email'],
			'payment_type'   => $alipay_config['payment_type'],
			'notify_url'     => $alipay_config['h5_notify_url'],
			'return_url'     => $alipay_config['return_url'],
			'out_trade_no'   => $out_trade_no,
			'subject'        => $subject,
			'total_fee'      => "$total_fee",
			'body'           => $body . "mwebpay",
			'show_url'       => WEB_ROOT_URL.'mobile/index.html#/inpour?pay_type=alipay&t=finish&orderid='.$out_trade_no,
			"_input_charset" => trim( strtolower( $alipay_config['input_charset'] ) )
		];

		$alipaySubmit = new \AlipaySubmit( $alipay_config );
		$html_text    = $alipaySubmit->buildRequestForm( $commonParam, "get", "" );

		echo $html_text;
	}

	private function _mobilePay( $out_trade_no, $subject, $total_fee, $body, $alipay_config )
	{
		$commonParam = array(
			'app_id'     => ALIPAY_APP_ID,//'2016072900114850',//
			'method'     => 'alipay.trade.app.pay',
			'charset'    => 'utf-8',
			'sign_type'  => "RSA",
			'timestamp'  => date( 'Y-m-d H:i:s' ),
			'version'    => '1.0',
			'notify_url' => $alipay_config['notify_url'],
		);

		//支付宝业务参数
		$businessParam = array(
			'body'            => $body,
			'subject'         => $subject,
			'out_trade_no'    => $out_trade_no,
			'timeout_express' => '30m',
			'total_amount'    => $total_fee,
			'product_code'    => "QUICK_MSECURITY_PAY"
		);

		$commonParam['biz_content'] = json_encode( argSort( $businessParam ) );

		$result              = paraFilter( $commonParam );
		$result['sign_type'] = $commonParam['sign_type'];

		$result = argSort( $result );

		//对未签名的原始字符串进行签名 不需要urlencode
		$data = createLinkstring( $result );

		$sign = rsaSign( $data, $alipay_config['app_private_key_path'], '', $commonParam['sign_type'] );

		$result['sign'] = $sign;

		$result = createLinkstringUrlencode( $result );

		//设置redis
		$rechargeOrderStatus_redis = "recharge:" . $out_trade_no . "-" . $this->_params['uid'];
		$this->_redis->set( $rechargeOrderStatus_redis, 0, 600 );

		$this->_responseJson( array( 'orderid' => $out_trade_no, 'uid' => $this->_params['uid'], 'totalPrice' => $total_fee, 'params' => $result ) );
	}

	private function _getAliPayConfig( $client )
	{
		$alipay_config = [];

		if ( $client == 'web' )//|| $client == 'h5'
		{
			include_once __DIR__ . "/../../payment/alipay/alipay_web.config.php";
		}
		else
		{
			include_once __DIR__ . "/../../payment/alipay/alipay.config.php";
		}

		if ( $client == 'h5' )
		{
			$alipay_config['sign_type'] = "RSA";
		}

		return $alipay_config;
	}

	public function verifyReturn()
	{
		$client = 'web';
		$data   = $_GET;

		$ret = $this->_notifyVertify( $data, $client );


		mylog( "notifyVertify result is " . json_encode( $ret ), LOG_DIR . "service\\payment\\WxpayHP.log" );

		if ( true === $ret )
		{
			header( 'Location:' . WEB_PERSONAL_URL . "recharge.php" );
		}
		else
		{
			echo "验证失败";
		}
	}

	public function verifyNotify()
	{
		//只要不是web  就可以
		$client = 'ios';
		$data   = $_POST;
		$ret    = $this->_notifyVertify( $data, $client );

		mylog("notifyVertify result is ". json_encode($ret), LOG_DIR."service\\payment\\WxpayHP.log");

		if ( !is_null( $ret ) )
		{
			if ( true === $ret )
			{
				echo 'success';
			}
			elseif ( false === $ret )
			{
				echo 'fail';
			}
		}
	}

	public function verifyNotify_mweb()
	{
		//只要不是web  就可以
		$client = 'h5';
		$data   = $_POST;
		$ret    = $this->_notifyVertify( $data, $client );

		mylog("notifyVertify result is ". json_encode($ret), LOG_DIR."service\\payment\\WxpayHP.log");

		if ( !is_null( $ret ) )
		{
			if ( true === $ret )
			{
				echo 'success';
			}
			elseif ( false === $ret )
			{
				echo 'fail';
			}
		}
	}

	private function _notifyVertify( $data, $client )
	{
		$config = $this->_getAliPayConfig( $client );

		mylog(__FUNCTION__." client is ". json_encode($client), LOG_DIR."service\\payment\\WxpayHP.log");
		mylog(___FUNCTION__." sign_type is". $data['sign_type'], LOG_DIR."service\\payment\\WxpayHP.log");

		$alipayNotify = new \AlipayNotify( $config );
		if ( $client == 'web' )
		{
			$vertify_result = $alipayNotify->verifyReturn();
		}
		else
		{
			$signType = $data['sign_type'];

			if ( $signType == "MD5" )
			{
				$appid          = '';
				$vertify_result = $alipayNotify->verifyNotify();
			}
			elseif ( $signType == "RSA" )
			{

				if($client == 'h5')
				{
					//todo 优化
					$alipayNotify->alipublicKey = "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCnxj/9qwVfgoUh/y2W89L6BkRAFljhNhgPdyPuBV64bfQNN1PjbCzkIM6qRdKBoLPXmKKMiFYnkd6rAoprih3/PrQEB/VsW8OoM8fxn67UDYuyBTqA23MML9q1+ilIZwBC2AQ2UBVOrFXfFl75p6/B5KsiNG9zpgmLCUYuLkxpLQIDAQAB";
//					$alipayNotify->alipublicKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCnxj/9qwVfgoUh/y2W89L6BkRAFljhNhgPdyPuBV64bfQNN1PjbCzkIM6qRdKBoLPXmKKMiFYnkd6rAoprih3/PrQEB/VsW8OoM8fxn67UDYuyBTqA23MML9q1+ilIZwBC2AQ2UBVOrFXfFl75p6/B5KsiNG9zpgmLCUYuLkxpLQIDAQAB';
//					$alipayNotify->alipublicKey = "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCH51FJ6FsvL7k0f+6u/5nz2zM5MSVelnI8jwwTw/bO5B/7D+t1qpGdc+Ue7TqcS2TG7GqgY8Nn9cG1Bsz/8CFJfx0V4fwiXuxWbHZMjryOkQKLZSDPxmPOR+QE0LQNYbHKwm/KT+WBWRCthLfI6owcPQDYds4aLUHRl8fhNTkDzQIDAQAB";
					mylog( ___FUNCTION__ . " ============== alipublickey channge to  ". $alipayNotify->alipublicKey, LOG_DIR . "service\\payment\\WxpayHP.log" );

					$appid = ALIPAY_APP_ID;
					$vertify_result = $alipayNotify->rsaVertifyNotify($config['app_public_key_path']);
				}
				else
				{

					mylog( ___FUNCTION__ . " run RSA vertify ", LOG_DIR . "service\\payment\\WxpayHP.log" );
					$appid          = ALIPAY_APP_ID;
					$vertify_result = $alipayNotify->rsaVertifyNotify( $config['app_public_key_path'] );
				}
			}
			elseif ( $signType == "RSA2" )
			{
				$appid          = ALIPAY_APP_ID;
				$vertify_result = $alipayNotify->rsaVertifyNotify( $config['app_public_key_path'] );
			}

			//auth app id  不在作为安全验证。
			if ( isset($data['auth_app_id'] )  && $data['auth_app_id'] != $appid )
			{
				return false;
			}
		}

		mylog(___FUNCTION__." vertify_result is". $vertify_result, LOG_DIR."service\\payment\\WxpayHP.log");

		if ( $vertify_result )
		{
			//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代

			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

			//获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

			//商户订单号
			$out_trade_no = $data['out_trade_no'];

			//支付宝交易号
			$trade_no = $data['trade_no'];

			//交易状态
			$trade_status = $data['trade_status'];

			//第三方用户id
			$openid = $data['buyer_id'];

			if ( $data['trade_status'] == 'TRADE_FINISHED' )
			{
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
				//如果有做过处理，不执行商户的业务程序

				//注意：
				//退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
				$ret = alipayRechargeHandleFlow( $trade_no, $out_trade_no, $openid, $this->_db );
				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
			}
			else
			{
				if ( $data['trade_status'] == 'TRADE_SUCCESS' )
				{
					//判断该笔订单是否在商户网站中已经做过处理
					//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
					//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
					//如果有做过处理，不执行商户的业务程序

					$ret = alipayRechargeHandleFlow( $trade_no, $out_trade_no, $openid, $this->_db );
					//注意：
					//付款完成后，支付宝系统发送该交易状态通知

					//调试用，写文本函数记录程序运行情况是否正常
					//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
				}
			}

			mylog( "alipayRechargeHandleFlow result is " . json_encode( $ret ), LOG_DIR . "service\\payment\\WxpayHP.log" );

			if ( $ret === true )
			{
				return true;
			}
			else
			{
				return null;
			}
		}
		else
		{
			return false;
		}

	}

}