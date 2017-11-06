<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/1/20
 * Time: 下午2:13
 */

include_once '/usr/local/huanpeng/include/init.php';

$start = strtotime('2016-11-23 09:00:00');
//$start = strtotime('2017-01-12 00:05:00');
$between = 3600;

$ENV = $argv[1];

while($start < time() + 3600){

//	echo "current date is" .date('Y-m-d H:',$start)."00:00 \n\n";

	$cmd = "php sync-anchor-inco-month.php $ENV $start";
	$ret = `$cmd`;
//	if(preg_match('/+OK/',$ret))exit;
	$start += $between;

}