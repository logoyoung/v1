<?php
include '../../../include/init.php';

$db = new DBHelperi_huanpeng();

function getAnchorIncome($uid, $date, $db){
	$stime = $date . " 00:00:00";
	$etime = $date . " 23:59:59";

	$sql = "select sum(income) as income from billdetail where ctime BETWEEN '$stime' and '$etime' and beneficiaryid=$uid and type=0";
	$res = $db->query($sql);
	$row = $res->fetch_assoc();

	return (int)$row['income'];
}

$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';

$year = isset($_POST['year']) ? (int)$_POST['year'] : 0;
$month = isset($_POST['month']) ? (int)$_POST['month']: 0;

if(!$uid || !$enc)
	exit(jsone(array('code'=>-1, 'desc' => '参数错误')));

$code = checkUserState($uid, $enc, $db);
if($code !== true)
	error($code);

if(!$year || $year < 2015)
	$year = date('Y');

if(!$month || $month > 12)
	$month = date('n');


if($year == date('Y') && $month > date('n')){
	$month = date('n');
}

if($year == date('Y') && $month == date('n')){
	$day = date("j");
}else{
	$day = date('t', strtotime($year."-".$month."-01"));
}

$month = $month >= 10 ? $month : "0".$month;


$data = array();
for($i = 1; $i <= $day; $i++){
	$days = $i < 10 ? "0$i" : "$i";
	$date = $year.'-'.$month.'-'.$days;
	$tmp = getAnchorIncome($uid, $date, $db);
	if($tmp){
		$arr['income'] = $tmp;
		$arr['time'] = $year . "." . $month . "." . $days;
		array_push($data, $arr);
	}
}

exit(jsone(array('incomeList' => $data)));

