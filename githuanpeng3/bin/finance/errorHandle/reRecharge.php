<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/5/23
 * Time: 下午10:55
 */

include __DIR__ . "/../../../include/init.php";
include INCLUDE_DIR."payment/wx/WxPay.Config.php";

use service\payment\WxpayHP;
use lib\Finance;

$redis   = new \RedisHelp();
$db      = new \DBHelperi_huanpeng();

$finance = new Finance( $db, $redis );


$stime = "2017-05-23 00:00:00";

$sql = "select id,status,client from hpf_rechargeRecord_201705 where ctime > '$stime' and status=" . Finance::RECHARGE_STATUS_CREATE;
$res = $db->query( $sql );

while ( $row = $res->fetch_assoc() )
{
	WxPayConfig::$client = $row['client'];
	$wxPay   = new WxpayHP();
	$result = [];
	if ( $wxPay->QueryOrderByHPOrderID( $row['id'], $result ) )
	{
		var_dump( $row['id'] . " status {$row['status']} recharge success and back failed" );

		$transaction_id = $result['transaction_id'];
		$out_trade_no = $row['id'];
		$openid = $result['openid'];
		$timeend = strtotime($result['time_end']);
		rechargeHandleFlow($transaction_id, $row['id'], $openid, $db,$timeend);
	}
	else
	{
		var_dump( $row['id'] . " status {$row['status']} recharge failed" );
	}
}
