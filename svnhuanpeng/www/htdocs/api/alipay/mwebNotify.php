<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/8/11
 * Time: 14:24
 */
include __DIR__."/../../../include/init.php";
use service\payment\Alipay;

$db = new DBHelperi_huanpeng();
$redis= new RedisHelp();

$alipayNotify = new Alipay($db,$redis);

$alipayNotify->verifyNotify_mweb();