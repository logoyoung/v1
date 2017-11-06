<?php

/**
 * 忘记密码 找回密码 设置新密码
 * yandong@6rooms.com
 * Date 2016-07-05 14:48
 */
include '../../../../include/init.php';
require INCLUDE_DIR . 'mobileMessage.class.php';
session_start();
$db = new DBHelperi_huanpeng();
use service\event\EventManager;

/**
 * 检测新密码长度以及是否包含中文
 *
 * @param type $password 密码
 *
 * @return boolean
 */
function checkPwd( $password )
{
	if ( mb_strlen( $password ) < 6 || mb_strlen( $password ) > 12 )
	{
		return false;
	}
	//密码中是否包含中文
	preg_match( '/[\x{4e00}-\x{9fa5}]+/u', $password, $matches_c );
	if ( $matches_c )
	{
		return false;
	}
	else
	{
		return true;
	}
}

$mobile    = isset( $_REQUEST['mobile'] ) ? trim( $_REQUEST['mobile'] ) : '';
$password  = isset( $_REQUEST['password'] ) ? trim( $_REQUEST['password'] ) : '';
$password2 = isset( $_REQUEST['password2'] ) ? trim( $_REQUEST['password2'] ) : '';
if ( empty( $mobile ) || empty( $password ) || empty( $password2 ) )
{
	error2( -4013 );
}
if ( $password !== $password2 )
{
	error2( -4014, 2 );
}
$password = filterData( $password );
$Pwdres   = checkPwd( $password );
if ( $Pwdres === false )
{
	error2( -4003, 2 );
}

$checkmobile = checkMobile( $mobile );
write_log( "收到忘记密码重置 mobile:{$mobile},checkmobile:{$checkmobile}|forgetPassword.php" );

if ( !$checkmobile )
{
	error2( -4058, 2 );
}



$forgetMobil = sendMobileMsg:: getCache( sendMobileMsg::t_getBackPasswd, $mobile );
if ( !$forgetMobil )
{
	write_log( "非法的密码重置 mobile:{$mobile},forgetMobil:{$forgetMobil}|forgetPassword.php" );
	error2( -4058, 2 );
}

sendMobileMsg::clearCache( sendMobileMsg::t_getBackPasswd, $mobile );//清理redis

$redis = new RedisHelp();
$key   = "resetpasswdAuth_" . $mobile;
$auth = $redis->get( $key );

if( $auth != 1 )
{
	render_error_json("请求非法，请重新找回密码", -4070, 2);
}

$redis->del($key);

//todo 通过mobile查找UID

$user = new \lib\User();

$uid = \lib\User::getUidByPhoneNumber( $mobile );
if ( !$uid )
{
	error2( -5017 );
}

$userObj = new lib\User( $uid );

if ( $userObj->updatePassword( $password ) )
{
	$event = new EventManager();
	$event->trigger( EventManager::ACTION_USER_INFO_UPDATE, [ 'phone' => $mobile ] );
	succ();
}
else
{
	error2( -5017 );
}


//$password = md5password($password);
//$sql = "update userstatic set password='$password' where phone= $mobile";
//$res = $db->query($sql);
//if ($res) {
//    write_log("密码重置成功 mobile:{$mobile}|forgetPassword.php");
//
//    $event = new EventManager();
//    $event->trigger(EventManager::ACTION_USER_INFO_UPDATE,['phone' => $mobile]);
//    $event = null;
//
//    succ();
//} else {
//   error2(-5017);
//}




