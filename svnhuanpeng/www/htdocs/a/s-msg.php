<?php

require_once( '../../include/init.php' );

use lib\LiveRoom;
use lib\MsgPackage;
use service\rule\TextService;

$uid      = isset( $_GET['userid'] ) ? (int)$_GET['userid'] : 0;
$roomid   = isset( $_GET['roomid'] ) ? (int)$_GET['roomid'] : 0;
$addr     = isset( $_GET['addr'] ) ? $_GET['addr'] : '';
$httpport = isset( $_GET['httpport'] ) ? $_GET['httpport'] : '';
$content  = isset( $_GET['content'] ) ? $_GET['content'] : '';

if ( !$uid or !$content or !$roomid or !$addr or !$httpport )
{
	roomerror( '-1015' );
}

$uid    = checkInt2( $uid, null, null, 'roomerror' );
$roomid = checkInt2( $roomid, null, null, 'roomerror' );

$liveroom = new LiveRoom( $roomid );
if ( !$liveroom )
{
	roomerror( -3001 );
}

// 心跳特殊处理
if ( $content == 'y8vPLwAA' )
{
	$r = $liveroom->userHB( $uid );
	if ( !$r )
	{
		roomerror( -3004 );
	}
	echo "+OK";
	exit;
}

// 游客不能说话
if ( $uid >= LIVEROOM_ANONYMOUS )
{
	$content = MsgPackage::socketMsgDecode( $content );
	$liveroom->log( $content );
	$content = json_decode( $content, true );
	if ( $content['t'] == 104 )
	{
		$r = $liveroom->successEnter( $uid, $content );
		if ( !$r )
		{
			roomerror( -3009 );
		}
		echo "+OK";

		exit;
	}
	roomerror( -3008 );
}


// 检查消息类型
$content = MsgPackage::socketMsgDecode( $content );

$liveroom->log( $content );

$content = json_decode( $content, true );

if ( $content['t'] == 100 )          // 房间发言
{
//	if(isset($content['msg']) && $content['msg'])
//	{
//		$textService = new TextService();
//		$textService->setCaller('api:'.__FILE__.';line:'.__LINE__);
//		$textService->addText($content['msg'],$uid)->setRoom($roomid);
//		//反垃圾过滤
//		if(!$textService->checkStatus())
//		{
//			write_log("notice|聊天包含敏感内容;msg:{$content['msg']};roomid:{$roomid};uid:{$uid}",'room_filter_msg');
//			echo "+OK";
//			exit;
//		}
//	}

	$r = $liveroom->userMsg( $uid, $content, $addr, $httpport );
	if ( !$r )
	{
		roomerror( -3006 );
	}

}
elseif ( $content['t'] == 102 )
{
	$r = $liveroom->sendGift( $uid, $content );
	if ( !$r )
	{
		roomerror( -3009 );
	}
}
elseif ( $content['t'] == 103 )
{
	$r = $liveroom->sendGift( $uid, $content );
	if ( !$r )
	{
		roomerror( -3009 );
	}
}
elseif ( $content['t'] == 104 )
{
	$r = $liveroom->successEnter( $uid, $content );
	if ( !$r )
	{
		roomerror( -3009 );
	}
}
elseif ( $content['t'] == 105 )
{
	$r = $liveroom->shareRoomMsg( $uid );

	if ( !$r )
	{
		roomerror( -3009 );
	}
}
else
{
	roomerror( -3005 );
}

echo "+OK";
exit;

?>