<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/5/11
 * Time: 10:07
 */

/*****************************文件转码批处理*************************************/

include( __DIR__ . '/../../include/init.php' );
use lib\LiveCacheBat;

if( !LiveCacheBat::doBat() )
{
	echo '[' . getmypid() . '] [' . get_datetime() . '] ' . "任务未开启\n";
	exit;
}
$LiveCacheBat = new LiveCacheBat();
//消费任务队列
$r = $LiveCacheBat->consumeTanscodeTask();
echo '[' . getmypid() . '] [' . get_datetime() . '] ' . "转码处理完成\n";
