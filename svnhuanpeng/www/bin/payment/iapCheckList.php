<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/5/31
 * Time: 下午4:06
 */


include __DIR__."/../../include/init.php";

use service\payment\IAPHelper;


$db = new \DBHelperi_huanpeng();
$redis = new \RedisHelp();
$iapHelper = new IAPHelper($db,$redis);


$iapHelper->checkListRun();