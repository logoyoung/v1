<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/12/5
 * Time: 下午7:19
 */


include_once INCLUDE_DIR."payment/wx/WxPay.Config.php";
include_once INCLUDE_DIR.'payment/wx/WxPay.Api.php';
include_once INCLUDE_DIR.'payment/wx/WxPay.Notify.php';
include_once INCLUDE_DIR.'RechargeApi.class.php';
include_once INCLUDE_DIR."User.class.php";

class MyNotifyHandle extends WxPayNotify {
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}
	public function NotifyProcess($data, &$msg){
		mylog($msg,LOGFN_WX_PAY);

		mylog(jsone($data),LOGFN_WX_PAY);
		$notfiyOutput = array();

		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}

		$attach = json_decode($data['attach'],true);

		if(!$attach && !in_array($attach['client'],['wechat','weibo','qq'])){
			mylog("can't find the channel values",LOGFN_WX_PAY);
			return false;
		}
		WxPayConfig::$client = $attach['client'];
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}
		$db = new DBHelperi_huanpeng();
		wxRechargeHandleflow($data['transaction_id'],$data['out_trade_no'],$data['openid'],$db);
//		RechargeOrder::$orderid = $data['out_trade_no'];
//		RechargeOrder::setdb($db);
//
//		if(!RechargeOrder::getInfo()){
//			//todo 没有该订单信息的处理流程
//			mylog('no order in the huanpeng system', LOGFN_WX_PAY);
//			return false;
//		}
//
//		$redis = new RedisHelp();
//		$rechargeOrderStatus_redis = "recharge:".RechargeOrder::$orderid."-".RechargeOrder::$uid;
//		$redis->set($rechargeOrderStatus_redis,1,600);
//		mylog('rechargeOrder redis status is '.$redis->get($rechargeOrderStatus_redis), LOGFN_WX_PAY);
//		$orderStatus = RechargeOrder::$status;
//		if($orderStatus == 1){
//			mylog('order is finished', LOGFN_WX_PAY);
//			return true;
//		}else{
//			if($orderStatus === '0'){
//				//设置成功状态
//				$db->autocommit(false);
//
//				$successPayResult = RechargeOrder::successPay($data['transaction_id'],$data['openid']);
//				$rechargeHandleResult = rechargeHandleFlow(RechargeOrder::$quantity,RechargeOrder::$uid,RechargeOrder::$id,$db,null,$redis);
//				if( $successPayResult&&$rechargeHandleResult)
//				{
//					$db->commit();
//					$db->autocommit(true);
//					mylog('order handle finished', LOGFN_WX_PAY);
//					return true;
//				}else{
//					mylog('successPayResult is '. $successPayResult, LOGFN_WX_PAY);
//					mylog('rechargeHandleResult is '. $rechargeHandleResult, LOGFN_WX_PAY);
//					mylog('order handle failed the error is '.$db->errstr(), LOGFN_WX_PAY);
//					$db->rollback();
//					return false;
//				}
//			}else{
//				mylog('some where is errored', LOGFN_WX_PAY);
//				return false;
//			}
//		}
	}
}



function wxRechargeHandleflow($transactionId,$outTradeId,$openid,$db){

	$ret  = rechargeHandleFlow($transactionId,$outTradeId,$openid,$db);
	if($ret == false)
	{
		return false;
	}
	else
	{
		return true;
	}

	//查看订单状态，如果已经完成，则返回true
	RechargeOrder::$orderid = $outTradeId;
	RechargeOrder::setdb($db);
	if(!RechargeOrder::getInfo()){
		mylog('no order in the huanpeng system', LOGFN_WX_PAY);
		return false;
	}
	$redis = new RedisHelp();
	$rechargeOrderStatus_redis = "recharge:".RechargeOrder::$orderid."-".RechargeOrder::$uid;
	$redis->set($rechargeOrderStatus_redis,1,600);
	mylog('rechargeOrder redis status is '.$redis->get($rechargeOrderStatus_redis), LOGFN_WX_PAY);
	$orderStatus = RechargeOrder::$status;
	if($orderStatus == 1){
		mylog('order is finished', LOGFN_WX_PAY);
		return true;
	}else{
		if($orderStatus === '0'){
			//设置成功状态
			$db->autocommit(false);
			$successPayResult = RechargeOrder::successPay($transactionId, $openid);
			$rechargeHandleResult = rechargeHandleFlow(RechargeOrder::$quantity,RechargeOrder::$uid,RechargeOrder::$id,$db,null,$redis);
			if( $successPayResult&&$rechargeHandleResult)
			{
				$db->commit();
				$db->autocommit(true);
				mylog('order handle finished', LOGFN_WX_PAY);
				return true;
			}else{
				mylog('successPayResult is '. $successPayResult, LOGFN_WX_PAY);
				mylog('rechargeHandleResult is '. $rechargeHandleResult, LOGFN_WX_PAY);
				mylog('order handle failed the error is '.$db->errstr(), LOGFN_WX_PAY);
				$db->rollback();
				return false;
			}
		}else{
			mylog('some where is errored', LOGFN_WX_PAY);
			return false;
		}
	}
}


