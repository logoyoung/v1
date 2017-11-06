<?php
/**
 * Created by PhpStorm. 用与后台直播管理处理警告/断流/封号
 * User: dong
 * Date: 17/5/25
 * Time: 下午5:02
 */
include '../../../../include/init.php';

$luid = isset($_GET['luid']) ? (int)$_GET['luid'] : 0;
$order = isset($_GET['order']) ? (int)$_GET['order'] : 0;
$tm = isset($_GET['tm']) ? trim($_GET['tm']) : 0;
$reason = isset($_GET['reason']) ? trim($_GET['reason']) : 0;
$sign = isset($_GET['sign']) ? trim($_GET['sign']) : 0;

if(empty($luid) || !in_array($order,array(1,2,3))){
	error(-4013);
}
$db = new DBHelperi_huanpeng();
$liveObj=new \lib\live\LiveHelper($luid);

if($order == 1){//警告
	$content = \lib\MsgPackage::getBackManageWaringMsgSocketPackage($luid,$reason);
	\lib\SocketSend::sendMsg($content,$db);
	$r = true;
}else if($order == 2){//断流
	$content =\lib\MsgPackage::getBackManageStopLiveMsgSocketPackage($luid,$reason);
	\lib\SocketSend::sendMsg($content,$db);
	$r = $liveObj->adminstoplive();
}else if($order == 3){//封号
	$content =\lib\MsgPackage::getBackManageClosureMsgSocketPackage($luid,$reason);
	\lib\SocketSend::sendMsg($content,$db);
	$r = $liveObj->adminstoplive();
}else{

}
if( $r )
	succ();
else
	exit;




