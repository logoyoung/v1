<?php

include '../../../include/init.php';

$db = new DBHelperi_huanpeng();

$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';

//参数验证
if(!$uid || !$enc)
	exit(jsone(array('code'=>-1, 'desc' => '参数错误')));

$code = checkUserState($uid, $enc, $db);
if($code !== true)
	error($code);

//验证时间。之后每月的1-5号可以提现

//获取用户现金总额
function getAnchorCash($uid, $db){
	$sql = "select sum(purchase) as purchase from billdetail where customerid = $uid and type = " . BILL_EXCHANGE;
	$res = $db->query($sql);
	$row = $res->fetch_assoc();

	$purchase = (int)$row['purchase'];
	if(!$purchase)
		return 0;

	$purchase = $purchase / 2000;

	$sql = "select sum(purchase) as purchase from billdetail where customerid = $uid and type = " . BILL_CASH;
	$res = $db->query($sql);
	$row = $res->fetch_assoc();
	$withdraw = $row['purchase'];

	return $purchase - $withdraw;
}

$anchorCash = getAnchorCash($uid, $db);

exit(json_encode(array('anchorCash' => (int)$anchorCash)));