<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/4/12
 * Time: 17:22
 */


/*******************************WCS TEST***************************/

include ('../../../include/init.php');
include (INCLUDE_DIR.'wcSDK/src/Wcs/utils.php');
use lib\Video;
use lib\LiveCacheBat;
//use \RedisHelp;


$redis = new RedisHelp();
//$redis->set('mergetest','merge');exit;
/*********************transcode******************/
/*$flvs = array('liverecord-Y-370-5555555--20170109165913.flv');
$saveFile = 'mergeFile11.mp4';
$r = $Video->transcodeFile($flvs[0],$saveFile);
var_dump($r);*/
/*********************merge********************/
/*$flvs = array('liverecord-Y-370-5555555--20170109165913.flv');
$saveFile = 'mergeFile6.mp4';
$r = $Video->mergeFiles($flvs,$saveFile);
var_dump($r);*/

/*******************poster*******************/
/*$videoName = 'dev/v/79592.mp4';
$saveFile = '79592.jpg';
$r = $Video->cutOutVideoPicture($videoName,$saveFile,2);
var_dump($r);*/

/*****************delete********************/

/*$file = 'liverecord-Y-90-1111111--20170109112932.flv';
$files = array('liverecord-Y-15-2222222--20170109171410.flv','liverecord-Y-15-2222222--20170109172126.flv');
$r = $Video->deleteFiles($files);
var_dump($r);*/

/****************downloadUrl***************/
/*$url = Video::getDownloadUrl('http://fvod.huanpeng.com/196185.mp4');
var_dump($url);*/


/*****************batche process*******************/
$LiveCacheBat = new LiveCacheBat();

$file1 = "liverecord-Y-370-5555555--20170109165913.flv/testbat2.mp4";
$file2 = "liverecord-Y-370-5555555--20170109165913.flv/testbat3.mp4";
$r = $LiveCacheBat->produceTanscodeTask($file1);
var_dump($r);
$r = $LiveCacheBat->produceTanscodeTask($file2);
var_dump($r);
//$r = $LiveCacheBat->consumeTanscodeTask();
//var_dump($r);
