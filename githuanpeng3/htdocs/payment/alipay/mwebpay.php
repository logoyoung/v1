<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/8/10
 * Time: 15:16
 */


include "../../../include/init.php";

use service\payment\Alipay;

//var_dump( $_COOKIE['_uid'] || $_GET['_uid'] );
//
//var_dump( $_COOKIE['_uid'] ?? $_GET['_uid'] ?? 0 );


$data = array(
	'uid'         => $_GET['uid'] ?? $_COOKIE['_uid'] ?? 0,
	'encpass'     => $_GET['encpass'] ??  $_COOKIE['_enc'] ?? 0,
	'quantity'    => $_GET['quantity'],
	'productID'   => 5,
	'channel'     => 'alipay',
	'client'      => 'h5',
	'refUrl'      => $_GET['refUrl'],
	'promotionID' => $_GET['promotionID']
);
//$data = $_POST;
//$data['productID'] = 5;

//$data['uid'] = '8560';
//$data['encpass'] = '30bed402fc052e90b2381b04468ca601';
//$data['quantity'] = '100';

$db    = new DBHelperi_huanpeng();
$redis = new RedisHelp();


$alipay = new Alipay( $db, $redis );
$alipay->unifiedorder( $data );
