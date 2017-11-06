<?php

/**
 * 手机短信验证码校验（找回密码）
 * yandong@6rooms.com
 * Date 2016-07-05 14:48
 */
include '../../../include/init.php';
require INCLUDE_DIR . 'mobileMessage.class.php';
$db          = new DBHelperi_huanpeng();
$mobile_code = isset( $_REQUEST['mcode'] ) ? trim( $_REQUEST['mcode'] ) : '';
$mobile      = isset( $_REQUEST['mobile'] ) ? trim( $_REQUEST['mobile'] ) : '';
if ( empty( $mobile_code ) || empty( $mobile ) )
{
	error2( -4031, 2 );
}
$mobileRes = checkMobile( $mobile );
if ( $mobileRes !== true )
{
	error2( -4031, 2 );
}

$checkmcode = sendMobileMsg::checkSuccess( sendMobileMsg::t_getBackPasswd, $mobile, $mobile_code, $db, sendMobileMsg::$redis, 0, false );
if ( $checkmcode )
{
	sendMobileMsg::sendMsgCallBack( sendMobileMsg::$codeid, $db );

	$redis = new RedisHelp();
	$key   = "resetpasswdAuth_" . $mobile;
	$redis->set( $key, 1, 600 );

	succ();
}
else
{
	error2( -4031, 2 );
}




