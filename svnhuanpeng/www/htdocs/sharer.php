<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/12/9
 * Time: 下午1:47
 */

include '../include/init.php';

$channel = $_GET['channel'];
$luid = $_GET['luid'];
$suid = $_GET['suid'];

$ref = $_GET['datamain'];
$patten = '/.6\,cn/';
if( $ref == '6cn' || ( !empty( $_SERVER['HTTP_REFERER'] ) && preg_match( $patten, $_SERVER['HTTP_REFERER'] ) ) )
{
	hpsetCookie("datamain", '6cn');
}


if(isMobile()){
	if($channel == 'wechat') {
		$data = array(
			'u'=>$luid,
			'suid'=>$suid,
			'channel'=>'wechat',
			'client_share'=>'1'
		);
		$url = WEB_ROOT_URL."mobile/room/room.html?" . http_build_query($data);
	}else{
		$url = WEB_ROOT_URL."mobile/room/room.html?u=".$luid;
	}
}else{
	$url = WEB_ROOT_URL."room.php?luid=".$luid;
}

header("Location:".$url);


