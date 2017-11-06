<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/1/24
 * Time: 下午4:58
 */


include '../init.php';
$db = new DBHelperi_huanpeng();


$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$bulletin = isset($_POST['bulletin']) ? trim($_POST['bulletin']) : '';
if(!$uid || !$enc)
	exit(json_encode(array('code'=>-1,'desc'=>'参数错误')));

if(!$bulletin)
	exit(json_encode(array('isSuccess'=>1)));

$code = checkUserState($uid, $enc, $db);
if($code !== true)
	exit($code);


$bulletin = $db->realEscapeString($bulletin);

$sql = "insert into livebulletin (luid, bulletin) value($uid, '$bulletin') on duplicate key update bulletin='$bulletin'";
if($db->query($sql))
	exit(json_encode(array('isSuccess' => 1)));

else
	exit(json_encode(array('isSuccess' => 0)));
