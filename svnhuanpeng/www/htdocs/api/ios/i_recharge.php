<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/2/6
 * Time: 上午10:04
 */

include '../../../include/init.php';
include_once INCLUDE_DIR.'User.class.php';
include_once INCLUDE_DIR.'RechargeApi.class.php';


$acceptUid = 2600;

$param = array(
	'uid'=>[
		'type'=>'int',
		'must'=>true,
		'values'=>[$acceptUid]
	],
	'encpass'=>[
		'type'=>'string',
		'must'=>true
	],
	'quantity'=>[
		'type'=>'int',
		'must'=>true
	],
);

$result = array();

if(!checkParam($param,$_POST,$result)){
	error2(-4013);
}

foreach ($result as $key => $value){
	$$key = $value;
}

$productID = 5;

$db = new DBHelperi_huanpeng();

$userHelp = new UserHelp($uid,$db);

if($code = $userHelp->checkStateError($encpass)){
	error2($code);
}

HpProduct::$db = $db;

HpProduct::setproductid($productID);
if(!HpProduct::getifno()){
	error2(-4082,2);
}

if($userHelp->upHpcoin($quantity)){
	$property = $userHelp->getProperty();
	succ($property);
}
else
	error2(-5001,2);