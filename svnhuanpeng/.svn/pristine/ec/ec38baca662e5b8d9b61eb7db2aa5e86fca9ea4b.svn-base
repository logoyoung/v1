<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/5/22
 * Time: 14:58
 */

include ('../../../include/init.php');
use lib\WcsHelper;

$url = isset($_GET['url'])?$_GET['url']:'';
if(!$url)
	exit('为传入url');
echo 'start->'.microtime(true),"\n";
$WS = new WcsHelper();
$r = $WS->forbidLive($url);
echo 'stop->'.microtime(true),"\n";
var_dump($r);