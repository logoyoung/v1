<?php

/**
 * 绑定手机号
 * author yandong@6rooms.com
 * date 2016-11-27 21:34
 * version 0.1
 */
include '../../../include/init.php';
require INCLUDE_DIR.'mobileMessage.class.php';
use service\due\DueActivityService;
use service\activity\RegisterActivityService;
//header("Content-type:text/html;charset=utf-8");
$db = new DBHelperi_huanpeng();

function bindMobile($uid, $mobile,$password, $db)
{
    if (empty($uid) || empty($mobile)) {
        return false;
    }
    $data=array(
        'phone' => $mobile,
        'password'=>md5password($password)
    );
    $res = $db->where("uid=$uid")->update('userstatic', $data);
    if (false !== $res) {
        return true;
    } else {
        return false;
    }
}

/**
 * start
 */
$must = array(
    'uid' => array(
        'type' => 'string',
        'must' => true
    ),
    'encpass' => array(
        'type' => 'string',
        'must' => true
    ),
    'mobile' => array(
        'type' => 'string',
        'must' => true
    ),
    'mobileCode' => array(
        'type' => 'string',
        'must' => true
    ),
    'password' => array(
        'type' => 'string',
        'must' => true
    ),
    'password2' => array(
        'type' => 'string',
        'must' => true
    )
);
$data = $_POST;
$params = array();
if (!checkParam($must, $data, $params)) {
    error2(-4013, 2);
}
$s = CheckUserIsLogIn($data['uid'], $data['encpass'], $db);
if (true !== $s) {
    error2(-4067, 2);
}
if (empty($data['mobile'])) {
    error2(-4056, 2);
}
$isbind=checkUserIsBindMobile($data['uid'], $db);
if($isbind){
    error2(-4076,2);
}
$mobileRes = checkMobile($data['mobile']);
if (true !== $mobileRes) {
    error2(-4058, 2);
}
$checkMobileRes = checkMobileIsUsed($data['mobile'], $db);
if ($checkMobileRes) {
    error2(-4060, 2);
}
$checkmcode = sendMobileMsg::checkSuccess(sendMobileMsg::t_bindobile, $data['mobile'], $data['mobileCode'], $db);
if ($checkmcode) {
    sendMobileMsg::sendMsgCallBack(sendMobileMsg::$codeid, $db);
} else {
    error2(-4031, 2);
}
if ($data['password'] !== $data['password2']) {
    error(-4014);
}
$passLeng =checkPasswordLeng($data['password']);
if (true !== $passLeng) {
    error2(-1003, 2);
}
$password = filterData($data['password']);
//绑定函数
$result=bindMobile($data['uid'], $data['mobile'],$password, $db);
if (false === $result) {
    error(-5017);
} else {
    $acti = new DueActivityService();
    $res = $acti->updateUserCouponUidByPhone($data['mobile'], $data['uid']);
    $m = new RegisterActivityService();
    $m->addUser($data['uid']);
    succ();
}

