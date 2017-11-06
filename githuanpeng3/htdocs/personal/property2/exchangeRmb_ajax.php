<?php
include '../../../include/init.php';

$db = new DBHelperi_huanpeng();
session_start();

$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$exBalance = isset($_POST['hpcoin']) ? (int)$_POST['hpcoin'] : '';


//参数验证
if(!$uid || !$enc || !$exBalance || $exBalance != $_SESSION['exBalance'])
	exit(jsone(array('code'=>-1, 'desc' => '参数错误')));

$code = checkUserState($uid, $enc, $db);
if($code !== true)
	error($code);

//检查本月是否兑换过
function isExchange($uid, $db){
	$start = date('Y-m') . "-01 00:00:00";
	$end = date('Y-m-t') . " 23:59:59";

	$type = BILL_EXCHANGE;

	$sql = "select id from billdetail where ctime between '$start' and '$end' and type = $type and customerid = $uid";
	$res = $db->query($sql);
	$row = $res->fetch_assoc();
	if($row['id']){
		return true;
	}

	return false;
}

if(isExchange($uid, $db))
	exit(jsone(array('code' => -11, 'desc' => '已经兑换过')));

function calBalance($uid, $db){

	$sql = "select sum(income) as income from billdetail where beneficiaryid = $uid and type=" . BILL_GIFT;
	$res = $db->query($sql);
	$row = $res->fetch_assoc();
	$income = (int)$row['income'];

	$sql = "select sum(purchase) as purchase from billdetail where customerid = $uid and type = " . BILL_EXCHANGE;
	$res = $db->query($sql);
	$row = $res->fetch_assoc();
	$purchase = (int)$row['purchase'];

	return $income - $purchase;
}

$balance = calBalance($uid, $db);
$x = (int)($balance / 2000);
if($x == 0 || $balance < $exBalance ){
	exit(jsone(array('code' => -12, 'desc' => '您的余额不足')));
}

$sql = "insert into billdetail(customerid,purchase,income,type) values($uid, $exBalance, $exBalance, ". BILL_EXCHANGE .")";
if($db->query($sql))
	exit(jsone(array('isSuccess' => 1)));

exit(jsone(array('isSuccess' => 0)));
