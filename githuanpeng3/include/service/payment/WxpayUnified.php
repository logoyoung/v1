<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/5/8
 * Time: 上午10:03
 */

namespace service\payment;
include_once INCLUDE_DIR . "payment/wx/WxPay.Config.php";
include_once INCLUDE_DIR . 'payment/wx/WxPay.Api.php';
include_once INCLUDE_DIR . 'payment/wx/WxPay.Notify.php';
include_once INCLUDE_DIR . "payment/wx/NativePay.class.php";

class WxpayUnified extends UnifiedOrder
{

	private $_backResult = [];
	private $_content = [];

	public function __construct( \DBHelperi_huanpeng $db, \RedisHelp $redisHelp )
	{
		parent::__construct( $db, $redisHelp );
	}

	public function unifiedorder( $data )
	{
		$rmb = 0;
		$id  = $this->_unifiedorder( $data, $rmb );

		$tradeTypeList = array(
			'web'     => 'NATIVE',
			'android' => 'APP',
			'ios'     => 'APP',
			'h5' => 'MWEB',
			'wxjs' => 'JSAPI'
		);

		$rmb = $rmb * self::WXPAY_RMB_RATE;

		$tradeType = $tradeTypeList[$this->_params['client']];

		\WxPayConfig::$client = $this->_params['client'];

		$notify = new \NativePay();
		$input  = new \WxPayUnifiedOrder();

		$input->SetBody( $this->_params['productname'] );
		$input->SetAttach( json_encode( [ 'client' => $this->_params['client'] ] ) );
		$input->SetOut_trade_no( $id );
		$input->SetTotal_fee( $rmb );
		if ( $GLOBALS['env'] != 'PRO' )
		{
			$input->SetTotal_fee( '1' );
		}
		$input->SetTime_start( date( "YmdHis" ) );
		$input->SetTime_expire( date( "YmdHis", time() + 600 ) );
		$input->SetGoods_tag( "test" );
		$input->SetNotify_url( PAY_API_NOTIFY_URL . "wxpay/" . \WxpayConfig::$client . "Notify.php" );
		$input->SetTrade_type( $tradeType );
		$input->SetProduct_id( $this->_params['productID'] );

		//支付内部已经设置了ip地址，所以这里无需获取
//		if($tradeType == "MWEB")
//		{
//			$port = '';
//			$ip = fetch_real_ip($port);
//
//			mylog("create order ip is $ip",LOG_DIR."service\\payment\\WxpayHP.log");
//			$input->SetSpbill_create_ip($ip);
//		}

		if($tradeType == 'JSAPI')
		{
			$input->SetOpenid($this->_params['openid']);
		}

		$rechargeOrderStatus_redis = "recharge:" . $id . "-" . $this->_params['uid'];
		$this->_redis->set( $rechargeOrderStatus_redis, 0, 600 );

		$result = $notify->GetPayUrl( $input );

		$backParam = array(
			'APP'    => array( 'prepay_id', 'sign', 'nonce_str' ),//'mch_id'
			"NATIVE" => array( 'code_url' ),
			'MWEB' => array('mweb_url'),
			'JSAPI'=> array('prepay_id','sign', 'nonce_str')//待定
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

		mylog("create orderid is $id",LOG_DIR."service\\payment\\WxpayHP.log");
		mylog("create orderid ".json_encode($resultParam),LOG_DIR."service\\payment\\WxpayHP.log");

		$content = array(
			'orderid'    => "$id",
			'uid'        => $this->_params['uid'],
			'totalPrice' => $rmb,
			'tm'         => time()
		);
		$this->_content = $content;

		mylog("create orderid ".json_encode($content),LOG_DIR."service\\payment\\WxpayHP.log");

		$this->_backResult = $resultParam;


		$resultCallFunc = "_getResult_".$tradeType;


		$result = call_user_func([$this, $resultCallFunc]);

		$this->_responseJson($result);

		if ( $tradeType == 'APP' )
		{
			$nonce_str = substr( md5( time() . '' . rand( 1, 99999 ) . $resultParam['nonce_str'] ), 0, 20 );
			$data      = array(
				'appid'     => \WxpayConfig::getConstValue( 'APPID' ),
				'timestamp' => '' . $content['tm'],
				'noncestr'  => $nonce_str,
				'package'   => 'Sign=WXPay',
				'partnerid' => \WxpayConfig::getConstValue( 'MCHID' ),//$resultParam['mch_id'],
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
			$str = $str . "&key=" . \WxpayConfig::getConstValue( 'KEY' );;//\WxpayConfig::KEY;

			$sign                     = strtoupper( md5( $str ) );
			$resultParam['nonce_str'] = $nonce_str;
			$resultParam['sign']      = $sign;
//			$resultParam['version']   = 'test9';
			$resultParam['data']      = $data;
//			$resultParam['string']    = $str;
		}
		elseif($tradeType == 'JSAPI')
		{
			$this->_getResult_JSAPI();
		}

		$content['callbackUrl'] = PAY_API_NOTIFY_URL . "wxpay/" . \WxpayConfig::$client . "Notify.php";

		$result = array_merge( $content, $resultParam );
		mylog("create orderid responseJson".json_encode($content),LOG_DIR."service\\payment\\WxpayHP.log");
		$this->_responseJson( $result );
	}

	private function _getResult_APP()
	{
		$nonce_str = $this->_getNonstr();

		$data      = array(
			'appid'     => \WxpayConfig::getConstValue( 'APPID' ),
			'timestamp' => '' . $this->_content['tm'],
			'noncestr'  => $nonce_str,
			'package'   => 'Sign=WXPay',
			'partnerid' => \WxpayConfig::getConstValue( 'MCHID' ),//$resultParam['mch_id'],
			'prepayid'  => $this->_backResult['prepay_id']
		);

		$sign = $this->_buildSign($data);

		$this->_backResult['nonce_str'] = $nonce_str;
		$this->_backResult['sign'] = $sign;
		$this->_backResult['data'] = $data;

		$return = [
			'nonce_str' => $nonce_str,
			'sign' => $sign,
			'data' => $data,
			'prepay_id' => $data['prepayid']
		];

		return array_merge($this->_content, $return);
	}

	private function _getResult_JSAPI()
	{
		$nonce_str = $this->_getNonstr();

		$data =[
			'appid' => \WxPayConfig::getConstValue("APPID"),
			'timestamp' => ''.$this->_content['tm'],
			'noncestr' => $nonce_str,
			'package' => "prepay_id=".$this->_backResult['prepay_id'],
			'signtype' => "MD5"
		];

		$copyData = [
			'appId' => $data['appid'],
			'timeStamp' => $data['timestamp'],
			'nonceStr' => $data['noncestr'],
			'package' => $data['package'],
			'signType' => $data['signtype']
		];

		$sign = $this->_buildSign($copyData);

		$data['paySign'] = $sign;
		$data['signType'] = "MD5";


		return array_merge($this->_content, $data);
	}

	private function _getResult_NATIVE()
	{
		$data['code_url'] = $this->_backResult['code_url'];

		$this->_content['callbackUrl'] = PAY_API_NOTIFY_URL."wxpay/". \WxPayConfig::$client."Notify.php";

		return array_merge($this->_content, $data);
	}

	private function _getResult_MWEB()
	{
//		var_dump($this->_backResult);
		$data['mweb_url'] = $this->_backResult['mweb_url'] ."&redirect_url=".urlencode(WEB_ROOT_URL.'mobile/index.html#/inpour?pay_type=alipay&t=finish&orderid='.$this->_content['orderid']);

		return array_merge($this->_content,$data);
	}

	private function _getNonstr()
	{
		return substr( md5( time() . '' . rand( 1, 99999 ) . $this->_backResult['nonce_str'] ), 0, 20 );
	}

	private function _buildSign($data, $signType="MD5")
	{
		ksort($data);
		$str = '';

		foreach ( $data as $k => $v )
		{
			if ( $k != 'sign' && $v != "" && !is_array( $v ) )
			{
				$str .= $k . "=" . $v . '&';
			}
		}
		$str = trim( $str, '&' );
		$str = $str . "&key=" . \WxpayConfig::getConstValue( 'KEY' );;//\WxpayConfig::KEY;

		if($signType == "MD5")
		{
			$sign = strtoupper(md5($str));
		}
		elseif($signType == "SHA256")
		{
			//todo
			$sign = '';
		}

		return $sign;
	}
}