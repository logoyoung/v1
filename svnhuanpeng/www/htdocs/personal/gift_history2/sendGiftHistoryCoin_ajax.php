
<?php
include '../../../include/init.php';
$db = new DBHelperi_huanpeng();

$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$year = isset($_POST['year']) ? (int)$_POST['year'] : 0;
$month = isset($_POST['month']) ? (int)$_POST['month']: 0;
$size = isset($_POST['size']) ?(int)$_POST['size'] : 15;
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;



if(!$uid || !$enc || !$size || !$page)
	exit(jsone(array('code'=>-1, 'desc' => '参数错误')));

$code = checkUserState($uid, $enc, $db);
if($code !== true)
	error($code);

if(!$year || $year < 2015)
	$year = date('Y');

if(!$month || $month > 12)
	$month = date('n');


if($year == date('Y') && $month > date('n')){
	$month = date('n');
}

if($year == date('Y') && $month == date('n')){
	$day = date("j");
}else{
	$day = date('t', strtotime($year."-".$month."-01"));
}

$month = $month >= 10 ? $month : "0".$month;

function giftInfo($db){
	$sql = "select * from gift";
	$res = $db->query($sql);

	while($row = $res->fetch_assoc()){
		$gift[$row['id']] = $row;
	}
	return $gift;
}
function sendHpCoinGiftHistory($uid, $date, $size, $page, $db){

	$gift = giftInfo($db);

	$start = $date . "-01 00:00:00";
	$end = date('Y-m-t H:i:s', strtotime($date."-01"));

	$from = ($page - 1) * $size;

	$sql = "select * from giftrecordcoin where uid=$uid and ctime between '$start' and '$end' order by id desc limit $from, $size";
	$res = $db->query($sql);

	$data['sendGiftRecord'] = array();
	while($row = $res->fetch_assoc()){
		$user = getUserInfo($row['luid'], $db);

		$tmp['giftid'] = $row['giftid'];
		$tmp['date'] = date('Y-m-d', strtotime($row['ctime']));
		$tmp['time'] = date('H:i', strtotime($row['ctime']));
		$tmp['giftnum'] = $row['giftnum'];
		$tmp['giftname'] = $gift[$row['giftid']]['giftname'];
		$tmp['giftcost'] = $gift[$row['giftid']]['money'];
		$tmp['luid'] = $row['luid'];
		$tmp['anchorNick'] = $user[0]['nick'];

		array_push($data['sendGiftRecord'], $tmp);
	}

	$sql = "select count(*) as count from giftrecordcoin where uid=$uid and ctime between '$start' and '$end' order by id desc";
	$res = $db->query($sql);
	$row = $res->fetch_assoc();

	$data['allCount'] = (int)$row['count'];

	return $data;
}

$date = $year . "-" . $month;
$record = sendHpCoinGiftHistory($uid, $date, $size, $page ,$db);

exit(json_encode($record));



