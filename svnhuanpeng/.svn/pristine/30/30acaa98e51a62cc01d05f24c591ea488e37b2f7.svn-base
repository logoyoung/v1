<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/26
 * Time: 下午2:33
 */

include '../../../include/init.php';
include INCLUDE_DIR.'Anchor.class.php';
use lib\FinanceStatistic;
use service\user\UserAuthService;

$db = new DBHelperi_huanpeng();

$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : 0;

if(!$uid || !$enc){
	error2(-4013);
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

$financeObj = new FinanceStatistic();
$res = $financeObj->getUserDaySendPurchase(time(), $uid);


//$costArray['hpbean'] = (int)$user->todaySendHpBeanCount();
//$costArray['hpcoin'] = (int)$user->todaySendHpCoinCount();

$costArray['hpbean'] = $res['hd'];
$costArray['hpcoin'] = $res['hb'];

succ($costArray);
