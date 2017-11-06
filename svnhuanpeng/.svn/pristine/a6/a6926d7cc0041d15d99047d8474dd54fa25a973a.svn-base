<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/1/24
 * Time: 下午4:38
 */

include '../init.php';
$db = new DBHelperi_huanpeng();


$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';

if(!$uid)
	exit(json_encode(array('code'=>-1,'desc'=>'参数错误')));

//$code = checkUserState($uid, $enc, $db);
//if($code !== true)
//	exit($code);

$sql = "select bulletin from livebulletin where luid=$uid";
$res = $db->query($sql);
$row = $res->fetch_assoc();
if($row['bulletin'])
	exit(json_encode(array('message'=>$row['bulletin'])));

else
	exit(json_encode(array('message'=>'#主播还没有编辑公告')));


