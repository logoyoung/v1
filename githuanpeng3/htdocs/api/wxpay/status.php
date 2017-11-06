<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/12/7
 * Time: 下午6:12
 */


include '../../../include/init.php';
//include_once INCLUDE_DIR.'RechargeApi.class.php';
//include_once INCLUDE_DIR."redis.class.php";
//include_once INCLUDE_DIR."User.class.php";

use lib\Finance;
use lib\User;
use lib\Task;


$params = array(
	'orderid' => array( 'must' => true, 'type' => 'int' ),
	'uid'     => array( 'must' => true, 'type' => 'int' ),
	'encpass' => array( 'must' => true, 'type' => 'string' )
);


$result = array();

if ( !checkParam( $params, $_POST, $result ) )
{
	error2( -4013 );
}
foreach ( $result as $key => $val )
{
	$$key = $val;
}

$db = new \DBHelperi_huanpeng();

$userHelp = new User( $uid, $db );

$code = $userHelp->checkStateError( $encpass );

if ( true !== $code )
{
	error2( $code );
}

$redis = new \RedisHelp();

$rechargeOrderStatus_redis = "recharge:" . $orderid . "-" . $uid;

$status = $redis->get( $rechargeOrderStatus_redis );
mylog( "getRedis $uid >>>$rechargeOrderStatus_redis>>> $status", LOG_DIR . "service\\payment\\WxpayHP.log" );
if ( !$status )
{
	succ( array( 'step' => 'wait' ) );
}
else
{
	$financeObj = new Finance( $db, $redis );

	$info = $financeObj->getRechargeOrderInfo( $orderid );
	mylog( "finance info  " . json_encode( $info ), LOG_DIR . "service\\payment\\WxpayHP.log" );
	if ( $info['status'] == Finance::RECHARGE_STATUS_FINISH )
	{
		$property  = $userHelp->getUserProperty();
		$channelID = getUserBindChannel( $uid, $db );

		$task = new Task( $uid, $db );
		//todo for the is first pay
//		$isFirstPay = 0;
		$isFirstPay = $userHelp->getRechargeNumber() == 1 ? 1 : 0;

		$result         = array( 'step' => 'finish', 'hpcoin' => $property['coin'], 'hpbean' => $property['bean'], 'channelID' => $channelID, 'isFirstPay' => $isFirstPay );
		$rechargeResult = hp_getRechargeActive( $orderid );
		$result         = array_merge( $result, $rechargeResult );

		succ( $result );
	}
	else
	{
		succ( array( 'step' => 'wait' ) );
	}
//
//	RechargeOrder::$orderid = $orderid;
//	RechargeOrder::setdb( new DBHelperi_huanpeng() );
//	RechargeOrder::getInfo();
//
//	if ( RechargeOrder::$status == RECHARGE_ORDER_FINISH )
//	{
//		$property   = $userHelp->getProperty();
//		$channelID  = getUserBindChannel( $uid, $db );
//		$isFirstPay = RechargeOrder::payCount( $uid, $db );
//		succ( array( 'step' => 'finish', 'hpcoin' => $property['hpcoin'], 'hpbean' => $property['hpbean'], 'channelID' => $channelID, 'isFirstPay' => $isFirstPay ) );
//	}
//	else
//	{
//		succ( array( 'step' => 'wait' ) );
//	}
}