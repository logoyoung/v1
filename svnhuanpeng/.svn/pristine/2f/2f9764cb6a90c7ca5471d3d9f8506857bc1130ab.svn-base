<?php

require( '../../include/init.php' );

use lib\LiveRoom;

$uid    = isset( $_GET['userid'] ) ? (int)$_GET['userid'] : 0;
$roomid = isset( $_GET['roomid'] ) ? (int)$_GET['roomid'] : 0;
$addr   = isset( $_GET['addr'] ) ? $_GET['addr'] : '';
//	mylog("catch user exit params".json_encode($_GET));

$uid    = checkInt2( $uid, null, null, 'roomerror' );
$roomid = checkInt2( $roomid, null, null, 'roomerror' );

if ( $uid == 8560 && $roomid == 1860 )
{
	write_log( "User::UserExit", "socketTrace.log" );
}

$liveroom = new LiveRoom( $roomid );
if ( !$liveroom )
{
	roomerror( -3001 );
}

$r = $liveroom->userExit( $uid, $addr );
if ( !$r )
{
	roomerror( -3003 );
}

echo "+OK";
exit;


?>