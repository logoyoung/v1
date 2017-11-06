<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/21
 * Time: 上午11:28
 */

include '../../../include/init.php';
include INCLUDE_DIR.'Anchor.class.php';
$db = new DBHelperi_huanpeng();

$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$year = isset($_POST['year']) ? (int)$_POST['year'] : 0;
$month = isset($_POST['month']) ? (int)$_POST['month']: 0;
$size = isset($_POST['size']) ?(int)$_POST['size'] : 15;
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$type = isset($_POST['type']) ? trim($_POST['type']) : '';


if(!$uid || !$enc ||!$size || !$page || !$type )
	error(-4013);

if($type != 'coin' && $type != 'bean')
	error(-4013);

$anchor = new AnchorHelp($uid, $db);

if($loginError = $anchor->checkStateError($enc)){
	error($loginError);
}

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
$day = $day >= 10 ? $day : "0".$day;

$from = $year ."-".$month."-01 00:00:00";
$to = $year."-".$month."-".$day." 23:59:59";

if($type == 'coin') {
	$receiveRecord = $anchor->receiveCoinRecord($from, $to, $page, $size);
	$allCount = $anchor->receiveCoinRecordNumCount($from, $to);
}else {
	$receiveRecord = $anchor->receiveBeanRecord($from, $to, $page, $size);
	$allCount = $anchor->receiveBeanRecordNumCount($from, $to);
}

$giftInfo = $anchor->getGiftInfo();

$list = array();

foreach($receiveRecord as $key => $row ){
	$user = getUserInfo($row['uid'], $db);
	$giftid = $row['giftid'];
	if($type == 'coin')
		$benefit = $anchor->exchangeToCoin($giftInfo[$giftid]['money'] * $row['giftnum']);
	else
		$benefit = $anchor->exchangeToBean($row['giftnum']);////$anchor->exchangeToBean($row['giftnum']);

	$tmp['giftid'] = $row['giftid'];
	$tmp['date'] = date('Y-m-d H:i', strtotime($row['ctime']));
	$tmp['giftNum'] = $row['giftnum'];
	$tmp['giftName'] = $giftInfo[$row['giftid']]['giftname'];
	$tmp['uid'] = $row['uid'];
	$tmp['userNick'] = $user[0]['nick'];
	$tmp['benefit'] = $benefit;

	array_push($list, $tmp);
}

$list = toString($list);
$allCount = toString($allCount);

$recordList = array('list' => $list, 'allCount' => $allCount);

exit(json_encode($recordList));