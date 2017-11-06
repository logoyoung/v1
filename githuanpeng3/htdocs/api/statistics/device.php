<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/2/22
 * Time: 上午10:14
 * update: 2017-06-16 17:32:00
 */


include '../../../include/init.php';

$deviceType = ['iPhone','iPad','aPhone','aPad'];

$STATISTICS_LOG = LOG_DIR.'statistics/device-'.date("Ymd H:")."00:00.log";
$logDir = dirname($STATISTICS_LOG);
if (!is_dir($logDir))
{
	mkdir($logDir, 0777, true);
}
$data = $_POST;


$deviceRealType = $deviceType[$data['deviceType']];

$data['app'] = 'hp_'.$deviceRealType."_".$data['appVersion'];

//if($data['deviceType'] == 0 || $data['deviceType'] == 1)
//	$data['app'] ="hp_".$data['appVersion'];
//else
//	$data['app'] = "hp_".$data['appVersion'];

$data['ctime'] = date("Y-m-d H:i:s");

file_put_contents( $STATISTICS_LOG, json_encode($data)."\n", FILE_APPEND );

succ();