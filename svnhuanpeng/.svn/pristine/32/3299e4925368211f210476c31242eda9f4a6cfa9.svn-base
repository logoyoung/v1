<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/7/10
 * Time: 18:15
 */

include __DIR__."/../../include/init.php";

use lib\MsgPackage;
use lib\ApplePush;


$applePush = new ApplePush();

$custom = [
	'type'=>'10',
	'data' => []
];
$mid = time();
$content = MsgPackage::getDueOrderNewOrderApplePushMsgPackage('1ff8879c53c0e7ad2e6b7ece97984e0f599bdf02d45b6560e2cac5602a28f5cd', $mid, $custom);

$applePush->send($content);

