<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/11/24
 * Time: 下午8:10
 */


//$requestParam = array(
//	'channel'=>'string',
//	'accessToken'=>'string',
//	'code'=>'string',
//	'openid'=>'string',
//	'tm'=>'string',
//	'client'=>'', //ios,android
//	'sign'=>'string'
//);

include '../../../include/init.php';
include INCLUDE_DIR . "User.class.php";
include INCLUDE_DIR . '/loginSDK/ThreePartyLogin.php';

$must = array(
	'channel' => array(
		'type'   => 'string',
		'must'   => true,
		'values' => [ 'weibo', 'wechat', 'qq' ]
	),
	'client'  => array(
		'type'   => 'string',
		'must'   => true,
		'values' => [ 'android', 'ios' ]
	)
);

$wechatMust = array(
	'code' => array( 'type' => 'string', 'must' => true )
);

$weiboMust = $qqMust = array(
	'accessToken' => array( 'type' => 'string', 'must' => true ),
	'openid'      => array( 'type' => 'string', 'must' => true ),
);

$data   = $_POST;
$params = array();

if ( checkParam( $must, $data, $params ) )
{
	foreach ( $params as $key => $val ) $$key = $val;
}
else
{
	error2( -4013 );
}

$channelMust = $channel . "Must";
if ( checkParam( $$channelMust, $data, $params ) )
{
	foreach ( $params as $key => $val ) $$key = $val;
}
else
{
	error2( -4013 );
}

$channelID = hp_getRequestChannelID();

write_log(json_encode($_POST),'threeparthlogin');

unset( $_POST['channelID'] );
//验证认证结果
if ( !verifySign( $_POST, SECRET_KEY ) )
{
	$data = $_POST;
	unset( $data['sign'] );
//	error2(-4024,1,true,['tm'=>$_POST['tm'],'cm'=>time(),'sign'=>buildSign($data,SECRET_KEY)]);
	error2( -4024, 1 );
}

if ( $channel == 'wechat' )
{
	$data['code'] = $code;
	$data['channelid'] = $channelID;
}
else
{
	$data['accessToken'] = $accessToken;
	$data['openid']      = $openid;
	$data['channelid'] = $channelID;
}

write_log(json_encode($data), 'threeparthlogin');

$db        = new DBHelperi_huanpeng();
$threePart = new ThreePartyLogin( $channel, $client, $data, $db );
if ( !$threePart )
{
	error2( -4069, 2 );
}
mylog( 'loginstart--' . microtime( true ), LOG_DIR . 'Live.error.log' );
$ret = $threePart->run( 'login' );
if ( $ret && is_array( $ret ) )
{
	//更改邀请任务奖励表
	$ruid = $ret['uid'];
	$data = array( 'status' => 1 );//可领取状态
	$db->where( "ruid=$ruid" )->update( 'invite_record', $data );
	mylog( json_encode( $ret ) . '--' . microtime( true ), LOG_DIR . 'Live.error.log' );
	succ( $ret );
}
else
{
	error2( -1111, 1, true, jsond( $ret, true ) );
}

