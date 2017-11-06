<?php
include '../init.php';
include INCLUDE_DIR . 'LiveRoom.class.php';
$db = new DBHelperi_huanpeng();

function is_hasLiving($luid,$liveid, $db){
	$sql = "select liveid from live where uid = $luid order by liveid desc limit 1";
	$res = $db->query($sql);
	$row = $res->fetch_assoc();

	if(!$row['liveid'])
		return false;

	if($liveid != $row['liveid'])
		return false;

	return true;
}
function is_gift($gid, $type, $db){
	$sql = "select id,money, giftname, exp from gift where id = $gid and type = $type";
	$res = $db->query($sql);
	$row = $res->fetch_assoc();

	if(!$row['id'])
		return false;

	return $row;

}

function getMyProperty($uid, $db){
	$sql = "select hpcoin, hpbean from useractive where uid = $uid";
	$res = $db->query($sql);
	$row = $res->fetch_assoc();

	return $row;
}



function updateUserLevel($uid, $exp, $db){
	$res = $db->query("select level, integral from useractive where uid = $uid");
	$lv = $res->fetch_assoc();

	$res = $db->query('select max(level) as level from userlevel');
	$row = $res->fetch_assoc();
	$maxLevel = $row['level'];

	$exp = $exp + $lv['integral'];

	if($lv == $maxLevel){
		$db->query("update useractive set integral=$exp where uid = $uid");
		return true;
	}

	$res = $db->query("select * from userlevel where integral >= $exp order by level limit 1");
	$row = $res->fetch_assoc();
	$level = $row['level'];
	if($level){
		$db->query("update useractive set integral=$exp, level=$level where uid = $uid");
		return true;
	}else{
		$db->query("update useractive set integral=$exp, level=$maxLevel where uid = $uid");
		return true;
	}
}
function updateAnchorLevel($luid, $exp, $db){
	$res = $db->query("select level, integral from anchor where uid = $luid");
	$lv = $res->fetch_assoc();

	$res = $db->query('select max(level) as `level` from anchorlevel');
	$row = $res->fetch_assoc();

	$maxLevel = $row['level'];
	$exp = $exp + $lv['integral'];

	if($lv == $maxLevel){
		$db->query("update anchor set inegral=$exp where uid = $luid");
		$res = $db->query("select integral from anchorlevel where `level` =  $lv");
		$row = $res->fetch_assoc();
		$level = $lv;
	}else{
		$res = $db->query("select * from anchorlevel where integral >= $exp order by level limit 1");
		$row = $res->fetch_assoc();
		$level = $row['level'];
		if($level){
			$db->query("update anchor set integral=$exp, level=$level where uid = $luid");
		}else{
			$db->query("update anchor set integral=$exp, level=$maxLevel where uid = $luid");
		}
	}

	$anchorlevel['level'] = $level;
	$anchorlevel['percent'] = $exp / $row['integral'];

	return $anchorlevel;
}
function updateAnchorIncome($luid, $income, $db){
	return $db->query("update anchor set income=income+$income where uid = $luid");
}

$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$luid = isset($_POST['luid']) ? (int)$_POST['luid'] : 0;
$liveid = isset($_POST['liveid']) ? (int)$_POST['liveid'] : 0;
$gid = isset($_POST['gid']) ? (int)$_POST['gid'] : 0;
$num = isset($_POST['num']) ? (int)$_POST['num'] : 0;


if(!$uid || !$enc || !$luid || !$liveid || !$gid || !$num)
	exit(json_encode(array('code'=>-1,'desc'=>'参数错误')));

$code = checkUserState($uid, $enc, $db);
if($code !== true)
	exit($code);

$type = 1;

$property = getMyProperty($uid, $db);

if(!is_hasLiving($luid, $liveid, $db)) {
	exit(jsone(array('code' => -2, 'desc' => '无次直播')));
}

$gift= is_gift($gid, $type, $db);
if(!$gift) {
	exit(jsone(array('code' => -3, 'desc' => '礼物类型错误')));
}

$money = $gift['money'];

$amount =  $num;
$myBalance = $property['hpbean'];

if($myBalance < $amount)
	exit(jsone(array('code' => -4, 'desc' => '余额不足')));

$phone = get_userPhoneCertifyStatus($uid, $db);
if($phone['phonestatus'] == 0){
//	exit(josne(array('code' => -5, 'desc' => '请先认证手机')));
}

//验证完毕 进行费用扣除以及纪录的填写
$db->autocommit(false);
$db->query('begin');

$giftRecord = $db->query("insert into giftrecord(luid,liveid,uid,giftid,giftnum) values($luid,$liveid,$uid,$gid,$num)");
//注意 这里条件不满足时 没有扣除余额，但是返回的是true
$updateUserBalance = $db->query("update useractive set hpbean = hpbean - $amount where uid = $uid and hpbean >= $amount and hpbean = $myBalance");
//$updateAnchorBalance = $db->query("update useractive set hpbean = hpbean + $amount where uid = $luid");

if(!$giftRecord || !$updateUserBalance){
	$db->rollback();
	exit(jsone(array('code' => -5, desc => '系统繁忙，请稍后再试')));
}else{
	$db->commit();
	$db->autocommit(true);

	$res = $db->query("select nick from userstatic where uid = $uid");
	$row = $res->fetch_assoc();
	$nick = $row['nick'];

	$lroom = new LiveRoom($luid);

	$msg = array(
		't' => 504,
		'tm' => time(),
		'ouid' => $uid,
		'ounn' => $nick,
		'gid'  => $gid,
		'gnum' => $num,
		'gnm' => $gift['giftname']
	);

	$lroom->sendRoomMsg(json_encode(toString($msg)));

	$exp = $num / $gift['money'] * $gift['exp'];
	updateUserLevel($uid,  $exp, $db);

	$anchorLevel = updateAnchorLevel($luid, $exp, $db);
	//	更新主播贡献值
	updateAnchorIncome($luid, $amount, $db);
	exit(jsone(array('isSuccess' => 1)));
}