<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/5/22
 * Time: 14:58
 */

include ('../../../include/init.php');
use lib\WcsHelper;


$limit = isset($_GET['limit'])?$_GET['limit']:'';
$prefix = isset($_GET['prefix'])?$_GET['prefix']:'';
$marker = isset($_GET['marker'])?$_GET['marker']:'';

/*
if(!$url)
	exit('为传入url');
echo 'start->'.microtime(true),"\n";*/
$WS = new WcsHelper();
$r = $WS->bucketList('6huanpeng-test001',$limit,$prefix,0,$marker);
//echo 'stop->'.microtime(true),"\n";

$r = json_decode($r,true);
$marker = $r['marker'];
var_dump($r);
$list = [];
foreach ($r['items'] as $v ){
	$tmp['key'] = $v['key'];
	$tmp['time'] = date('Y-m-d',$v['putTime']/1000);
	//$tmp['timeunix'] = $v['putTime'];
	$list[] = $tmp;
}
var_dump($list);