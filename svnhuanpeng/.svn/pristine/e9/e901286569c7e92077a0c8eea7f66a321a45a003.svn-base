<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/5/25
 * Time: 16:24
 */
include ('../../../include/init.php');
use lib\Live;

echo "start:".date('Y-m-d H:i:s')."\n";
$stream = $_GET['stream'];

$r = Live::stopWsPubStream($stream);

var_dump($r);
echo "\n";

echo "end:".date('Y-m-d H:i:s')."\n";