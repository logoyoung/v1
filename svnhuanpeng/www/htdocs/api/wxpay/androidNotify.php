<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/12/12
 * Time: 下午3:01
 */

include '../../../include/init.php';
include "wxNotifyHandle.class.php";
use service\payment\WxpayHP;

WxPayConfig::$client = 'android';
$native_notify       = new WxpayHP();
$native_notify->Handle( false );
//WxPayConfig::$client = 'android';
//mylog('begin notify',LOGFN_WX_PAY);
//$native_notify = new MyNotifyHandle();
//$native_notify->Handle(false);