<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/8/21
 * Time: 17:26
 */

include '../../../include/init.php';
//include "wxNotifyHandle.class.php";
include_once INCLUDE_DIR."payment/wx/WxPay.Config.php";
//include_once INCLUDE_DIR."service/payment/Wxpay.class.php";

use service\payment\WxpayHP;

WxPayConfig::$client = 'wxjs';

$stime = microtime(true);

mylog("runtime ".($etime-$stime),LOG_DIR."newPay.log");

$native_notify       = new WxpayHP();
$native_notify->Handle( false );

$etime = microtime(true);

mylog("runtime ".($etime-$stime),LOG_DIR."newPay.log");