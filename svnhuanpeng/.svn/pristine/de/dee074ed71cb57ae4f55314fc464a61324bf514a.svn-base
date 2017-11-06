<?php
/**
 * 注册时获取手机验证码
 * yandong@6rooms.com
 * Data 2016-07-19 15:44
 */
include '../../../include/init.php';
require INCLUDE_DIR . 'class.geetestlib.php';
require INCLUDE_DIR . 'mobileMessage.class.php';
require_once INCLUDE_DIR.'bussiness_flow.fun.php';

use hpBizFun;

session_start();
$db = new DBHelperi_huanpeng();
$mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
$client = isset($_POST['client']) ? trim($_POST['client']) : 0;
$from = isset($_POST['from']) ? (int)($_POST['from']) : 0;
$type = isset($_POST['type']) ? trim($_POST['type']) : 'gt';


if (!in_array($from, array(0, 1, 2, 3, 4, 5))) {
    error2(-4013);
}

if ($from == 3) {
    $uid = isset($_POST['uid']) ? (int)($_POST['uid']) : 0;
    $encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
    if (empty($uid) || empty($encpass)) {
        error2(-4013);
    }
    $code = CheckUserIsLogIn($uid, $encpass, $db);
    if ($code !== true) {
        error2(-4067, 2);
    }
    $phone = get_userPhoneCertifyStatus($uid, $db);
    if (!$phone['phonestatus']) {
        error2(-5026, 2);
    } else {
        $mobile = $phone['phone'];
    }

}

if (empty($mobile)) {
    error2(-4056, 2);
}
if (!in_array($client, array(0, 1))) {
    error2(-4013);
}

$mres = checkMobile($mobile);
if ($mres !== true) {
    error2(-4058, 2);
}
if ($from == 0) {
    $stype = sendMobileMsg::t_register;//注册
}
if ($from == 1) {
    $stype = sendMobileMsg::t_bindobile;  //绑定手机
}
if ($from == 2) {
    $stype = sendMobileMsg::t_getBackPasswd;//忘记密码
}
if ($from == 3) {
    $stype = sendMobileMsg::t_apply;//申请主播
}
if ($from == 4) {
    $stype = sendMobileMsg::t_apply_filed;//申请失败
}
if ($from == 5) {
    $stype = sendMobileMsg::t_apply_success;//申请成功
}

$redis = new RedisHelp();

//geetest 模式
if (in_array($from, array(0, 1))) {
    if ($type == 'gt') {
//		mylog( 'test geetest session used'.json_encode( $_SESSION ) );
//        $GtSdk = $client == '1' ? new GeetestLib(CAPTCHA_APP_ID, PRIVATE_APP_KEY) : new GeetestLib(CAPTCHA_ID, PRIVATE_KEY);
//        if ($_SESSION['gtserver'] == 1) {
//            $user_id = $_SESSION['user_id'];
//            $result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $user_id);
//            if (!$result) {
//                error2(-4031, 2);
//            }
//        } else {
//			mylog( 'geetest run fail_validate' );
//			$result = $GtSdk->fail_validate( $_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'] );
//			mylog( 'geetest run fail_vaildate result'.$result );
//            if ( !$result ) {
//                error2(-4031, 2);
//            }
//        }
		if( !hpBizFun\checkGeetestCode( $_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $client, $redis ) )
		{
            write_log("geetest模式|GeetestCodec校验异常;error_code:-4031;mobile:{$mobile};client:{$client};from:{$from};type:{$type};stype:{$stype}|api/code/mobileCode.php",'send_mobile_code');
			error2( -4031, 2 );
		}
    }
}
$db = new DBHelperi_huanpeng();
$checkMobileRes = checkMobileIsUsed($mobile, $db);

if (in_array($from, array(0, 1))) {
    if ($checkMobileRes) {
        error2(-4060, 2);
    }
}
$is_add_new=false;//同步主播数据
$is_no_bind=false;//自动为用户绑定手机标志位
if (in_array($from, array(2, 3, 4, 5))) {
    if (false === $checkMobileRes) {
        error2(-4059, 2);
    } else {
        if (!empty($checkMobileRes)) {
            if($from == 3){//申请主播
                if($checkMobileRes[0]['uid'] != $uid){
                    error2(-4060, 2);
                }
            }
        } else {
          $is_no_bind=true;//没有绑定手机
        }
    }
}
if ($from == 3) {
    $applicationKey = 'application:' . $uid;
//不要重复发送
//    if ($redis->get($applicationKey)) {
//        error2(-5037, 2);
//    }
}
$isSend = sendMobileMsg::canSendMsg($stype, $mobile, $db);
if (false === $isSend) {
    write_log("error|error_code:-4083;mobile:{$mobile};client:{$client};from:{$from};type:{$type};stype:{$stype};|api/code/mobileCode.php",'send_mobile_code');
    error2(-4083, 2);
}
$coid = sendMobileMsg::createCodeId($stype, $mobile, $mobile, $db);
if (false !== $coid) {
    $issucc = sendMobileMsg::sendMsg($coid, $db);
    if (false !== $issucc) {
        if ($from == 3) {//申请主播
            $redis->set($applicationKey, sendMobileMsg::$mobileCode, 900);
            if(false === RN_MODEL){
                if($is_no_bind){
                    //自动绑定手机
                    $db->where("uid=$uid")->update('userstatic',array('phone'=>$mobile));
                }
            }
        }
        write_log("succ|mobile:{$mobile};client:{$client};from:{$from};type:{$type};stype:{$stype};|api/code/mobileCode.php",'send_mobile_code');
        succ();
    } else {
        write_log("error|error_code:-5038;mobile:{$mobile};client:{$client};from:{$from};type:{$type};stype:{$stype};|api/code/mobileCode.php",'send_mobile_code');
        error2(-5038, 2);
    }
} else {
    write_log("error|error_code:-5038;mobile:{$mobile};client:{$client};from:{$from};type:{$type};stype:{$stype};|api/code/mobileCode.php",'send_mobile_code');
    error2(-5038, 2);
}