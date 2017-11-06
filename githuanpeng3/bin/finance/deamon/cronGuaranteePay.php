<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/6/13
 * Time: 下午2:36
 */


include __DIR__ . "/../../../include/init.php";

use lib\Finance;

$db    = new DBHelperi_huanpeng();
$redis = new RedisHelp();

echo "[".getmypid()."]".date("Y-m-d H:i:s")." run script";

$financeObj = new Finance( $db, $redis );

$financeObj->doGuaranteeCronTab();
