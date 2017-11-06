<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/12/12
 * Time: 下午3:00
 */

include '../../../include/init.php';
include "wxNotifyHandle.class.php";
use service\payment\WxpayHP;

WxPayConfig::$client = 'ios';
$native_notify       = new WxpayHP();
$native_notify->Handle( false );
//WxPayConfig::$client = 'ios';
//mylog('begin notify',LOGFN_WX_PAY);
//$native_notify = new MyNotifyHandle();
//$native_notify->Handle(false);