<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/1/19
 * Time: 下午4:00
 */

include_once '/usr/local/huanpeng/include/init.php';

$start = strtotime('2016-11-23 09:00:00');
//$start = strtotime('2017-01-12 00:05:00');
$between = 600;

$ENV = $argv[1];

while($start < time() + 3600){

//	echo "current date is" .date('Y-m-d H:',$start)."00:00 \n\n";

	$cmd = "php sync-anchor-inco.php $ENV $start";
	$ret = `$cmd`;
//	if(preg_match('/+OK/',$ret))exit;
	$start += $between;

}