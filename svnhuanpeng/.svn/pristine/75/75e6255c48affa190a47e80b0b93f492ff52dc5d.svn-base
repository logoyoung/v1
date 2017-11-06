<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/12/12
 * Time: 下午3:00
 */

include '../../../include/init.php';
//include "wxNotifyHandle.class.php";
include_once INCLUDE_DIR."payment/wx/WxPay.Config.php";
//include_once INCLUDE_DIR."service/payment/Wxpay.class.php";

use service\payment\WxpayHP;

WxPayConfig::$client = 'web';

$stime = microtime(true);

mylog("runtime ".($etime-$stime),LOG_DIR."newPay.log");

$native_notify       = new WxpayHP();
$native_notify->Handle( false );

$etime = microtime(true);

mylog("runtime ".($etime-$stime),LOG_DIR."newPay.log");

//mylog('begin notify',LOGFN_WX_PAY);
//$native_notify = new MyNotifyHandle();
//$native_notify->Handle(false);