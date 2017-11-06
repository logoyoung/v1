<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/5/5
 * Time: 下午1:55
 */

namespace service\payment;
include_once INCLUDE_DIR . "payment/wx/WxPay.Config.php";
include_once INCLUDE_DIR . 'payment/wx/WxPay.Api.php';
include_once INCLUDE_DIR . 'payment/wx/WxPay.Notify.php';
include_once INCLUDE_DIR . "payment/wx/NativePay.class.php";


class WxpayHP extends \WxPayNotify
{
	public function Queryorder( $transaction_id )
	{
		//TODO //
//		return false;
//
		$input = new \WxPayOrderQuery();
		$input->SetTransaction_id( $transaction_id );
		$result = \WxPayApi::orderQuery( $input );
		if ( array_key_exists( "return_code", $result )
			&& array_key_exists( "result_code", $result )
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS"
		)
		{
			return true;
		}

		return false;
	}

	public function QueryOrderByHPOrderID($orderid, &$ret)
	{
		$input = new \WxPayOrderQuery();
		$input->SetOut_trade_no($orderid);
		$result = \WxPayApi::orderQuery($input);
		if ( array_key_exists( "return_code", $result )
			&& array_key_exists( "result_code", $result )
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS"
			&& $result['trade_state'] == 'SUCCESS'
		)
		{
			$ret = $result;
			return true;
		}
		else
		{
			return false;
		}
	}

	public function NotifyProcess( $data, &$msg )
	{
		mylog( __FUNCTION__."run here", LOG_DIR."newPay.log" );

		mylog( jsone( $data ), LOG_DIR."newPay.log" );
		$notfiyOutput = array();

		if ( !array_key_exists( "transaction_id", $data ) )
		{
			$msg = "输入参数不正确";
			$this->_log($msg);
			return false;
		}

		$attach = json_decode( $data['attach'], true );

		if ( !$attach && !in_array( $attach['client'], [ 'wechat', 'weibo', 'qq' ] ) )
		{
			mylog( "can't find the channel values", LOGFN_WX_PAY );
			$msg="can't find the channel values";
			$this->_log($msg);
			return false;
		}
		\WxPayConfig::$client = $attach['client'];
		//查询订单，判断订单真实性
		$queryStime = microtime(true);
		if ( !$this->Queryorder( $data["transaction_id"] ) )
		{
			$msg = "订单查询失败";
			$this->_log($msg);
			return false;
		}
		$queryEtime = microtime(true);
		mylog( "query order time ".($queryEtime-$queryStime), LOG_DIR."newPay.log" );

		$db = new \DBHelperi_huanpeng();

		return $this->_succHandle( $data['transaction_id'], $data['out_trade_no'], $data['openid'], $db );
	}

	private function _succHandle( $transactionId, $outTradeId, $openid, $db )
	{
		$this->_log("order success");
		$ret = rechargeHandleFlow( $transactionId, $outTradeId, $openid, $db );
		$this->_log("order success rechargeHandleFlow $ret");
		return $ret == false ? false : true;
	}

	private function _log($msg){
		$dir = LOG_DIR . __CLASS__ . ".log";
		mylog( $msg, $dir );
	}
}

