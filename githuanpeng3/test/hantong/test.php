<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/9/11
 * Time: 上午11:07
 */


include __DIR__."/../../include/init.php";


$sql = "select * from liveroom where luid=:luid";

$db = \system\DbHelper::getInstance('huanpeng');

$bindparam = ['luid' => 1 ];

var_dump($db->query($sql, $bindparam));

//include __DIR__."/../../include/commonFunction.php";


//var_dump(hp_getRechargeActive(1));


//$number = "";
//
//
//$number  = explode(",", $number);
//
//var_dump($number);
//
//
//var_dump(empty(array_filter($number, function($var){
//	return ($var & 1);
//})));

