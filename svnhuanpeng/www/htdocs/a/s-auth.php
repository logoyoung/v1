<?php
require( '../../include/init.php' );
//    require(INCLUDE_DIR.'LiveRoom.class.php');

use \RedisHelp;
use lib\LiveRoom;

$uid      = isset( $_GET['userid'] ) ? (int)$_GET['userid'] : 0;
$encpass  = isset( $_GET['encpass'] ) ? trim( $_GET['encpass'] ) : '';
$roomid   = isset( $_GET['roomid'] ) ? (int)$_GET['roomid'] : 0;
$addr     = isset( $_GET['addr'] ) ? $_GET['addr'] : '';
$httpport = isset( $_GET['httpport'] ) ? $_GET['httpport'] : '';

if ( !$uid or !$encpass or !$roomid or !$addr or !$httpport )
{
	roomerror( '-1015' );
}

$uid    = checkInt2( $uid, null, null, 'roomerror' );
$roomid = checkInt2( $roomid, null, null, 'roomerror' );

$db = new DBHelperi_huanpeng();

if($uid==8560 && $roomid == 1860)
{
	write_log("User::userEnter","socketTrace.log");
}

/*
    //检查用户登陆状态
    if ($uid<LIVEROOM_ANONYMOUS)        // 游客状态
    {
        $userState = checkUserState($uid, $encpass, $db);
        if(true !== $userState) roomerror($userState);
    }
*/
mylog( "recive ip address is " . fetch_real_ip( $port ), LOGFN_SEND_MSG_ERR );
//	mylog('recive socket enter', LOGFN_SEND_MSG_ERR);
mylog( 'recive socket enter roomid :' . $roomid . "======start=====", LOGFN_SEND_MSG_ERR );

$redisHelp = new RedisHelp();

$liveroom = new LiveRoom( $roomid, $db, $redisHelp );
if ( !$liveroom )
{
	roomerror( -3001 );
}

mylog( 'recive socket enter roomid :' . $roomid . "======end=====", LOGFN_SEND_MSG_ERR );

$r = $liveroom->userEnter( $uid, $addr, $httpport );
mylog( 'send user enter msg:' . json_encode( $r ), LOGFN_SEND_MSG_ERR );

if ( !$r )
{
	roomerror( -3002 );
}
//header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
echo trim( "+OK" );
exit;
