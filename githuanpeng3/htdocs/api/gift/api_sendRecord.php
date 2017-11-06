<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/26
 * Time: 下午2:33
 */

include '../../../include/init.php';
include INCLUDE_DIR.'Anchor.class.php';
use lib\User;
use lib\Gift;
use service\user\UserAuthService;

$db = new DBHelperi_huanpeng();

$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$year = isset($_POST['year']) ? (int)$_POST['year'] : 0;
$month = isset($_POST['month']) ? (int)$_POST['month']: 0;
$size = isset($_POST['size']) ?(int)$_POST['size'] : 15;
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$type = isset($_POST['type']) ? trim($_POST['type']) : '';


if(!$uid || !$enc ||!$size || !$page || !$type )
	error2(-4013);

if($type != 'coin' && $type != 'bean')
	error2(-4013);

$auth = new UserAuthService();
$auth->setUid($uid);
$auth->setEnc($enc);
//校验encpass、用户 登陆状态
if($auth->checkLoginStatus() !== true)
{
    //获取校验结果
    $result    = $auth->getResult();
    //错误码
    $errorCode = $result['error_code'];
    //错误消息
    $errorMsg  = $result['error_msg'];
    //假如是封禁的，可以获取禁时间
    $etime     = isset($result['login_disable_etime']) ? $result['login_disable_etime'] : 0;
    write_log("notice|uid:{$uid};error_code:{$errorCode};error_msg:{$errorMsg};解禁时间:{$etime}|api:".__FILE__,'auth_access');
    error2(-4067,2);
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

$userLibObj =  new User($uid);
if($type == 'coin') {
        $resTmp =  $userLibObj->getHpCoinSendRecord($from, $to, $page, $size,intval($month));
	$record =$resTmp['coin'];
	$allCount = $resTmp['total'];
}else {
	 $resTmp =  $userLibObj->getHpBeanSendRecord($from, $to, $page, $size,intval($month));
	$record =$resTmp['bean'];
	$allCount = $resTmp['total'];
}

$giftInfo = Gift::getGiftList($db);
$list = array();

foreach($record as $key => $row){
	$nick = getUserInfo($row['luid'], $db)[0]['nick'];

	$giftid = $row['giftid'];

	$tmp['giftID'] = $row['giftid'];
	$tmp['ctime'] = date('Y-m-d H:i', strtotime($row['ctime']));
	$tmp['giftNum'] = $row['giftnum'];
	$tmp['giftName'] = $giftInfo[$row['giftid']]['giftname'];
	$tmp['uid'] = $row['luid'];
	$tmp['nick'] = $nick;
    $room= getRoomIdByUid($row['luid'], $db);
    $tmp['roomID']=$room[$row['luid']];
	array_push($list, $tmp);
}
succ(array('list' => $list, 'total' => $allCount));
