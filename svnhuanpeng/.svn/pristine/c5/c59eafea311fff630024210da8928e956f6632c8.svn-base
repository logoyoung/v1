<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/2/2
 * Time: 下午2:05
 */
include '../../../../init.php';
$db = new DBHelperi_huanpeng();

$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';

if(!$uid || !$enc)
	exit(jsone(array('code'=>-1, 'desc' => '参数错误')));

$code = checkUserState($uid, $enc, $db);
if($code !== true)
	error($code);


$sql = "select purchase, ctime, type from billdetail where customerid = $uid and (type = ".BILL_EXCHANGE." or type = ".BILL_CASH.")";

$res = $db->query($sql);

$list = array();
while($row = $res->fetch_assoc()){
	if($row['type'] == BILL_EXCHANGE){
		$data['cash'] = $row['purchase'] / 2000;
	}else{
		$data['cash'] = $row['purchase'];
	}

	$data['type'] = $row['type'];
	$data['date'] = date("Y-m-d H", strtotime($row['ctime']));

	array_push($list, $data);
}

exit(jsone(array('billRecordList'=>$list)));