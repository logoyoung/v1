<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/10
 * Time: 下午9:55
 */
include '../init.php';

$db = new DBHelperi_huanpeng();

$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$luid = isset($_POST['luid']) ? (int)$_POST['luid'] : 0;
$mid = isset($_POST['messageid']) ?(int)$_POST['messageid'] : 0;
$muid = isset($_POST['reportUid']) ? (int)$_POST['reportUid'] : 0;

if(!$uid || !$enc || !$luid || !$mid || !$muid){
	exit(json_encode(array('error' => '参数错误')));
}

if(!checkUserState($uid, $enc, $db)){
	exit(json_encode(array('error'=> '登录失败')));
}

if(!isLiveMessage($mid, $muid, $luid, $db)){
	exit(json_encode(array('error' => '无此消息')));
}

$sql = "insert into reportLiveMsg(uid, msgid) value($uid, $mid) on duplicate key update msgid = $mid";
if($db->query($sql)){
	exit(json_encode(array('isSuccess' => '1')));
}else{
	exit(json_encode(array('error' => '-1', 'desc' => '系统错误')));
}

function isLiveMessage($mid, $muid, $luid, $db){
	$sql = "select msgid from livemsg where msgid=$mid and luid=$luid and uid=$muid";
	$res = $db->query($sql);
	$row = $res->fetch_assoc();

	return (int)$row['msgid'] ? true : false;
}