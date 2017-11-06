<?php

/**
 * app端获取手机验证码
 * yandong@6rooms.com
 * date  2016-5-15
 */
include '../../init.php';
session_start();

/**
 * 检测该手机是否注册
 * @param type $mobile 手机号码
 * @param type $db
 * @return boolean
 */
function checkmobileIsExist($mobile) {
    $db = new DBHelperi_huanpeng();
    $res = $db->where("phone=$mobile")->select('userstatic');
    if ($res) {
        return true;
    } else {
        return false;
    }
}

$mobile = isset($_REQUEST['mobile']) ? trim($_REQUEST['mobile']) : '';
//$type = isset($_REQUEST['type']) ? (int) ($_REQUEST['type']) : '';
if (empty($mobile)) {
    error(-4056);
}
$code = checkMobile($mobile);
if ($code !== true) {
    error(-4013);
}
$checkisExit = checkmobileIsExist($mobile); //检测是否已经注册过
if ($checkisExit === false) {
    error(-4059);
}

$mobile_code = random(6, 1);
$codeid = '6' . date('mdH') . random(2, 1);

$codetype = 123;

$key = "ekxklhuangTSDpengfkjekldc";

$data = array(
    'appid' => 102,
    'code' => (int) $mobile_code,
    'codeid' => (int) $codeid,
    'mobile' => $mobile,
    'type' => $codetype
);
$str = json_encode($data);
$sign = md5($str . $key);
$sendurl = "http://dev.liveuser.6.cn/api/pubSendSmsCodeApi.php?appid=102&type=$codetype&code=$mobile_code&mobile=$mobile&codeid=$codeid&sign=$sign";
//exit(json_encode(array('code'=>1)));
$res = file_get_contents($sendurl);
if ($res) {
    $r = json_decode($res, true);
    if ($r['resuNo'] == 1) {
        $_SESSION['forget_mobile'] = $mobile;
        $_SESSION['forget_mobile_code'] = $mobile_code;
        $_SESSION['forget_codeid'] = $data['codeid'];
        exit(json_encode(array('isSuccess' => '1')));
    } else {
        exit(json_encode(array('isSuccess' => '0')));
    }
} else {
    exit(json_encode(array('isSuccess' =>  '0')));
}