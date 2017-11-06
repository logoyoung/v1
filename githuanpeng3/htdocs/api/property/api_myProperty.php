<?php

/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/21
 * Time: 上午10:38
 */
/**
 * 主播财产详情
 */
include '../../../include/init.php';
include INCLUDE_DIR . 'Anchor.class.php';

use lib\User;
use lib\Anchor;
use lib\FinanceStatistic;
use lib\Finance;
use lib\Convert;
use service\user\UserAuthService;

$db = new DBHelperi_huanpeng();
$ratio = 20; //比率
//三月份提现数组
$alreadyWithdraw = array(
    '2290' => array('bean' => 23, 'coin' => 137),
    '2625' => array('bean' => 23, 'coin' => 234),
    '3055' => array('bean' => 23, 'coin' => 414),
    '3345' => array('bean' => 7, 'coin' => 88),
    '3490' => array('bean' => 13, 'coin' => 109),
    '3635' => array('bean' => 8, 'coin' => 128),
    '3700' => array('bean' => 75, 'coin' => 703),
    '4100' => array('bean' => 7, 'coin' => 557),
    '4465' => array('bean' => 10, 'coin' => 158),
    '8415' => array('bean' => 7, 'coin' => 73),
    '9100' => array('bean' => 24, 'coin' => 359),
    '9460' => array('bean' => 9, 'coin' => 207),
    '12000' => array('bean' => 0, 'coin' => 309),
    '13845' => array('bean' => 0, 'coin' => 73),
    '14445' => array('bean' => 6, 'coin' => 117),
    '24420' => array('bean' => 16, 'coin' => 118),
    '24895' => array('bean' => 10, 'coin' => 49),
    '25990' => array('bean' => 1, 'coin' => 112),
    '26080' => array('bean' => 2, 'coin' => 115),
    '26980' => array('bean' => 3, 'coin' => 111)
);

/**
 * @param $uid  主播id
 * @param $db
 * @return bool
 */
function getHpCoinByUid($uid, $db) {
    if (empty($uid)) {
        return false;
    }
    $userObj = new User($uid, $db);
    $data = $userObj->getUserProperty();
    $res = array();
    if (false !== $data) {
        $res[0]['hpbean'] = $data['bean'];
        $res[0]['hpcoin'] = $data['coin'];
        return $res;
    } else {
        return false;
    }
}

function receiveExchange($uid, $db) {
    if (empty($uid)) {
        return false;
    }
}

$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int) ($_POST['type']) : 0;

if (empty($uid) || empty($enc)) {
    error2(-4013);
}
if (!in_array($type, array(0, 1))) {
    error2(-4070, 2);
}

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

$anchor = new AnchorHelp($uid);
$property = array();

$pcoin = $anchor->getCoin();

$anchorFinance = new Finance();
$anchorResult = $anchorFinance->getBalance($uid);
$property['coin'] = Convert::property($anchorResult['gb']);
$property['bean'] = Convert::property($anchorResult['gd']);
$rateHb2Gb = $anchorFinance->getRate($uid, Finance::EXC_HB_GB);
/*
  $anchorObj = new Anchor($uid);
  $anchorProperty  = $anchorObj->getAnchorProperty();
  $property['coin'] = $anchorProperty['coin'];
  if(array_key_exists($uid,$alreadyWithdraw)){
  $property['coin']=$property['coin']-$alreadyWithdraw[$uid]['coin'];
  if($property['coin']<0){
  $property['coin']=0;
  }
  $property['bean'] = $anchorProperty['bean']  -$alreadyWithdraw[$uid]['bean'];
  if($property['bean']<0){
  $property['bean']=0;
  }
  }else{
  $property['bean'] = $anchorProperty['bean'];
  }
 */
if ($type == 0) {
    $financeObj = new FinanceStatistic();
    $nowTime = time();
    $monthIncome = $financeObj->getUserMonthIncome($nowTime, $uid);
    $dayIncome = $financeObj->getUserDayIncome($nowTime, $uid);
    $property['todayCoin'] = Convert::property($dayIncome['gb'] / $rateHb2Gb * 0.6);
    $property['todayBean'] = Convert::property($dayIncome['gd']);
    $property['monthCoin'] = Convert::property($monthIncome['gb']);
    $property['monthBean'] = Convert::property($monthIncome['gd']);
//    $property['todayCoin'] = $anchor->todayReceiveCoinCount();
//    $property['todayBean'] = $anchor->todayReceiveBeanCount();
//    $property['monthCoin'] = $anchor->monthReceiveCoinCount();
//    $property['monthBean'] = $anchor->monthReceiveBeanCount();
    $property['ratio'] = $ratio . '%';
    $property['reward'] = $property['monthCoin'] * ($ratio / 100);
} else {
    $userAccount = getHpCoinByUid($uid, $db);
    if (false !== $userAccount) {
        $property['hpcoin'] = Convert::property($userAccount[0]['hpcoin']);
    } else {
        $property['hpcoin'] = 0.00;
    }
}
$cid = getCidByUid($uid, $db); //获取经纪公司id
if ($cid == 15) {
    $property['basicSalary'] = 600;
} else {
    $property['basicSalary'] = 0;
}
succ($property);
