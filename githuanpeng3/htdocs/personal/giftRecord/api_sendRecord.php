<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/26
 * Time: 下午2:33
 */

include_once '../../../include/init.php';
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

$user = new UserHelp($uid, $db);

if($loginError = $user->checkStateError($enc)){
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

if($type == 'coin'){
	$record = $user->sendGiftRecord($from, $to, $page, $size);
	$allCount = $user->sendGiftRecordNumCount($from, $to);
}else{
	$record = $user->sendBeanRecord($from, $to, $page, $size);
	$allCount = $user->sendBeanRecordNumCount($from, $to);
}

$giftInfo = $user->getGiftInfo();

$list = array();

foreach($record as $key => $row){
	$nick = getUserInfo($row['luid'], $db)[0]['nick'];

	$giftid = $row['giftid'];

	$tmp['giftid'] = $row['giftid'];
	$tmp['date'] = date('Y-m-d H:i', strtotime($row['ctime']));
	$tmp['giftNum'] = $row['giftnum'];
	$tmp['giftName'] = $giftInfo[$row['giftid']]['giftname'];
	$tmp['uid'] = $row['luid'];
	$tmp['userNick'] = $nick;

	array_push($list, $tmp);
}

$list = toString($list);
$allCount = toString($allCount);

$recordList = array('list' => $list, 'allCount' => $allCount);

exit(json_encode($recordList));