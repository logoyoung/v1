<?php

include '../../../include/init.php';
$db = new DBHelperi_huanpeng();

$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';

if(!$uid || !$enc)
	exit(jsone(array('code'=>-1, 'desc' => '参数错误')));

$code = checkUserState($uid, $enc, $db);
if($code !== true)
	error($code);

$sql = "select purchase, ctime from billdetail where customerid = $uid and beneficiaryid = 0 and type = " . BILL_EXCHANGE;
$res = $db->query($sql);

$data = array();
while($row = $res->fetch_assoc()){
	$tmp['hpcoin'] = $row['purchase'];
	$tmp['rmb'] = (int)($row['purchase'] / 2000);
	$tmp['date'] = date('Y-m', strtotime($row['ctime']));

	array_push($data, $tmp);
}

exit(json_encode(array('exchangeList' => $data)));