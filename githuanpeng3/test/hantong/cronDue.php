<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/6/13
 * Time: 下午2:36
 */


include __DIR__ . "/../../include/init.php";

use lib\Finance;

$db    = new DBHelperi_huanpeng();
$redis = new RedisHelp();

$financeObj = new Finance( $db, $redis );


while ( true )
{
	$financeObj->doGuaranteeCronTab();
	sleep(1);
}