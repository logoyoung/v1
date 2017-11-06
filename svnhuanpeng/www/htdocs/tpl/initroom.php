<?php
require('../../include/init.php');

require(INCLUDE_DIR . 'LiveRoom.class.php');

$conf = $GLOBALS['env-def'][$GLOBALS['env']];

$db = new DBHelperi_huanpeng();

/*
定义全局JS对象

$ROOM = {
	anchorNickName:'',
	anchorUserID:'',
	anchorUserPicURL:'',
	anchorIncome:'',
	anchorLevel:'',
	anchorIntegral:'',
	chatServer:[
		"42.62.27.112:8082"
	]
	fansCount:'',
	gameID: "14"
	gameName: "FIFA 15"
	gameTypeID: "38"
	isFollow: "0"
	liveID: "2750"
	liveStartTime: 1437819702
	liveTitle: "精彩直播 07/25 18:21"
	status: "101"
	upCount: "0"
	videolist:{
		leftCount:'',
		videoList:[
			{
				collectCount: "2"
				gameID: "44"
				gameName: "狂野飙车8"
				gameTypeID: "8"
				orientation: "1"
				posterURL: "http://dev-img.huanpeng.com/8/a/8ab0734ab6c33ed35ec33cada7025306.jpg"
				publisherNickName: "jj681"
				publisherUserID: "134"
				publisherUserPicURL: "http://dev-img.huanpeng.com//a/c/ac94adc41e23876cde269747f330644f.png"
				totalViewCount: "69"
				videoID: "320"
				videoPlaybackURL: "http://dev-img.huanpeng.com/v/8/a/8ab0734ab6c33ed35ec33cada7025306.mp4"
				videoTimeLength: "1209"
				videoTitle: "精彩直播 07/24 16:46"
				videoUploadDate: 1437728898
				viewerRate: "6.0000"
			}
		]
	}
	viewerCount:'',
	userLevelList:{
		1:1000,
		2:'2222'
	}
	anchorLevelList:{
		1:'1111',
		2:'2222'
	}
	giftExp:{
		31:'1',
		32:'1',
	}
	treasure:{
		total:0,
		list:[
			{
				ctime:'',
				trid:'',
				unick:'',
				uid:''
			},
		]
	}
}
 */

$luid = isset($_GET['luid']) ? (int)$_GET['luid'] : 0;
$uid = isset($_COOKIE['_uid']) ? (int)$_COOKIE['_uid'] : 0;
$enc = isset($_COOKIE['_enc']) ? trim($_COOKIE['_enc']) : '';
//判断房间是否存在
if (!$luid){
	echo '该主播不存在';
}

if ($uid && $enc) {
	if (true === checkUserState($uid, $enc, $db)) {
		$userInfo['isLogin'] = true;
		$userInfo['user'] = getUserInfo3($uid, $db, $conf);
	} else {
		$userInfo['isLogin'] = false;
	}
} else {
	$userInfo['isLogin'] = false;
}

//设置$ROOM信息给JS
$room = getliveroominfo($luid, $db, $conf, $uid, $enc);
if ($room && is_array($room)) {
	$other['size'] = 8;
	$other['uid'] = $luid;
	$videolist = getVideoList($db, $conf, $other);
	$room['videolist'] = $videolist;

//	$anchorInfo = getUserInfo2($luid, $db, $conf);
	$room['fansCount'] = getFansCount($luid, $db);//$anchorInfo['fansCount'];

	$chatServer = getSocketServer($conf);
	$chatServer = json_decode($chatServer, true);
	$room['chatServer'] = $chatServer['serverList'];
	$room['giftExp'] = getGiftExp($db);
	$room['anchorLevelList'] = getAnchorLevelList($db);
	$room['userLevelList'] = getUserLevelList($db);
	$anchorInfo= getAnchorInfo($luid, $db);
	$room['anchorLevel'] = $anchorInfo['level'];
	$room['anchorIncome'] = (float)($anchorInfo['income'] /1000);
	$room['anchorIntegral'] = $anchorInfo['integral'];
	$room['manageList'] = getRoomAdmin($luid, $db);
	$room['treasure'] = initRoomTreasure(roomTreasure($luid,$uid,$db), $db);
	$room = '<script> var $ROOM = ' . json_encode($room) . '; </script>';
	$pageUser = '<script> var pageUser = ' . json_encode($userInfo) . '; </script>';
} else {
	var_dump($room);
}


function getLiveRoomInfo($luid, $db, $conf, $uid = 0, $enc = '')
{
	if ($uid && $enc){
		$userstatus = checkUserState($uid, $enc, $db);
	}
	$isUser = true === $userstatus ? true : false;

	if (!$luid) return -1035;

	$res = $db->doSql("select * from live where uid = $luid order by liveid desc limit 1");
	if (!$res || !$res[0]) return -1036;

	$row = $res[0];

	$succ['liveID'] = $row['liveid'];
	$succ['gameID'] = $row['gameid'];
	$succ['gameTypeID'] = $row['gametid'];
	$succ['gameName'] = $row['gamename'];
	$succ['anchorUserID'] = $row['uid'];
	$succ['liveTitle'] = $row['title'];
	$succ['liveStartTime'] = strtotime($row['ctime']);
	$succ['upCount'] = $row['upcount'];
	$succ['status'] = $row['status'];
	$succ['isLiving'] = $row['status'] == 100 ? 1 : 0;
	$row = getUserInfo($luid, $db);
	$succ['anchorNickName'] = $row[0]['nick'] ? $row[0]['nick'] : '';

	$url = DOMAIN_PROTOCOL . $conf['domain-img'] . '/';
	$succ['anchorUserPicURL'] = $row[0]['pic'] ? $url . $row[0]['pic'] : DEFAULT_PIC;

	$lroom = new LiveRoom($luid, $db);

	$succ['viewerCount'] = $lroom->getRoomUserCount();

	if ($isUser) {
		$isFollow = isFollow($uid, $luid, $db);
		$succ['isFollow'] = $isFollow ? '1' : '0';
	}

	return $succ;
}

function getVideoList($db, $conf, $other = array())
{

	foreach ($other as $k => $v)
		$$k = $v;

	if (!isset($lastID) || !$lastID) {
		$sql = "select max(videoid) from video where status=" . VIDEO;
		$res = $db->query($sql);
		$row = $res->fetch_row();
		$lastID = $row[0] + 1;
	}

	if (!isset($size) || !$size)
		return -2003;

	$sql = "select * from video where videoid < $lastID and status=" . VIDEO;

	$c = '';

	if (isset($gameid) && $gameid)
		$c .= " and gameid = $gameid";

	if (isset($gametid) && $gametid)
		$c .= " and gametid = $gametid";

	if (isset($uid) && $uid)
		$c .= " and uid = $uid";

	$sql .= " $c order by videoid desc limit $size";

	$res = $db->query($sql);

	$data = array();

	while ($row = $res->fetch_assoc()) {
		$arr = array();
		$arr['videoID'] = $row['videoid'];
		$arr['gameID'] = $row['gameid'];
		$arr['gameTypeID'] = $row['gametid'];
		$arr['gameName'] = $row['gamename'];
		$arr['totalViewCount'] = $row['viewcount'];
		$arr['publisherUserID'] = $row['uid'];
		$arr['videoTitle'] = $row['title'];
		$arr['videoTimeLength'] = $row['length'];
		$arr['videoUploadDate'] = ($row['ctime']) ? strtotime($row['ctime']) : '';
		$arr['posterURL'] = ($row['poster']) ? (DOMAIN_PROTOCOL . $conf['domain-img'] . $row['poster']) : '';
		$arr['videoPlaybackURL'] = ($row['vfile']) ? ($conf['domain-video'] . $row['vfile']) : '';
		$arr['orientation'] = $row['orientation'];
		$videoid = $row['videoid'];

		//获取发布者昵称
		$userid = $row['uid'];
		$sql = "select nick,pic from user where uid=$userid";
		$r = $db->query($sql);
		$ro = $r->fetch_assoc();
		$arr['publisherNickName'] = ($ro['nick']) ? $ro['nick'] : '';
		$arr['publisherUserPicURL'] = ($ro['pic']) ? DOMAIN_PROTOCOL . $conf['domain-img'] . "/" . $ro['pic'] : '';

		//用户收藏数量
		$sql = "select count(*) from videofollow
                where videoid=$videoid";
		$r = $db->query($sql);
		$ro = $r->fetch_row();
		$arr['collectCount'] = $ro[0];

		//用户评分
		$sql = "select avg(rate) from videocomment
                where videoid=$videoid";
		$r = $db->query($sql);
		$ro = $r->fetch_row();
		$arr['viewerRate'] = ($ro[0]) ? $ro[0] : '';

		$data[] = $arr;
	}

	$json['videoList'] = $data;
	if (!$data)
		$json["leftCount"] = 0;

	else {
		$sql = "select count(*) from video where videoid < $videoid and status=" . VIDEO . " $c";
		$res = $db->query($sql);
		$row = $res->fetch_row();
		$json['leftCount'] = (int)$row[0];
	}

	return $json;

}

function getRoomAdmin($luid, $db)
{
	if (!$luid) {
		return false;
	}
	$sql = "select uid from roommanager where luid = $luid";
	$res = $db->query($sql);

	$list = array();

	while ($row = $res->fetch_assoc()) {
		array_push($list, $row['uid']);
	}

	return $list;
}

function getUserInfo3($uid, $db, $conf)
{
	$row = getUserBaseInfo($uid, $db);
	if (!$row) return false;


	$levelIntegral = getLevelIntegral($row['level'], $db);
	if (!$levelIntegral)
		return false;

	$userInfo = array();

	$userInfo['userID'] = $uid;
	$userInfo['nickName'] = $row['nick'];

	$url = DOMAIN_PROTOCOL . $conf['domain-img'] . '/';
	$userInfo['pic'] = $row['pic'] ? $url . $row['pic'] : DEFAULT_PIC;//'http://i1.tietuku.com/596664c92b007bbb.jpg';

	$userInfo['level'] = $row['level'];
	$userInfo['integral'] = $row['integral'];
	$userInfo['readsign'] = $row['readsign'];
	$userInfo['hpbean'] = $row['hpbean'];
	$userInfo['hpcoin'] = $row['hpcoin'];
	$userInfo['levelIntegral'] = $levelIntegral;

	//set user group
	if ($uid == $_GET['luid'] && (int)$_GET['luid']) {
		$userInfo['groupid'] = 5;
	} else {
		$adminlist = getRoomAdmin($_GET['luid'], $db);
		if (in_array($uid, $adminlist)) {
			$userInfo['groupid'] = 4;
		} else {
			$userInfo['groupid'] = 1;
		}
	}

	if($silence = isSilenced($uid, $_GET['luid'], $db)){
		$userInfo['isSilence'] = 1;
		$userInfo['silenceTime'] = $silence;
	}else{
		$userInfo['isSilence'] = 0;
		$userInfo['silenceTime'] = 0;
	}

	//would set super admin group = 2
	$certy = get_userPhoneCertifyStatus($uid, $db);
	$userInfo['phonestatus'] = $certy['phonestatus'];

	return $userInfo;
}

function isSilenced($uid, $luid, $db){
//	$time = date('Y-m-d H:i:s', time()-3600);
	$sql = "select ctime from silencedlist where uid = $uid and luid=$luid ";//and ctime >= '$time'";
	$res = $db->query($sql);
	$row = $res->fetch_assoc();
	if($row['ctime']){
		return strtotime($row['ctime']) + ROOM_SILENCE_TIMEOUT;
	}else{
		return false;
	}
}

function getSocketServer($conf)
{
	$serverList = $conf['socket'];
	shuffle($serverList);
	$json = array("serverList" => $serverList);
	return json_encode($json);
}

function getAnchorLevelList($db){
	$sql = "select * from anchorlevel";
	$res = $db->query($sql);
	$level = array();
	while($row = $res->fetch_assoc()){
		$level[$row['level']] = $row['integral'];
	}
	return $level;
}

function getUserLevelList($db){
	$sql = "select * from anchorlevel";
	$res = $db->query($sql);
	$level = array();
	while($row = $res->fetch_assoc()){
		$level[$row['level']] = $row['integral'];
	}
	return $level;
}

function getGiftExp($db){
	$sql = "select * from gift";
	$res = $db->query($sql);
	$gift = array();
	while($row = $res->fetch_assoc()){
		$gift[$row['id']]['exp'] = $row['exp'];
		$gift[$row['id']]['money'] = $row['money'];
	}
	return $gift;
}

function getAnchorInfo($luid, $db){
	$sql = "select * from anchor where uid = $luid";
	$res = $db->query($sql);

	$row = $res->fetch_assoc();

	return $row;
}

function roomTreasure($luid,$uid,$db){
	$sql = "select id, uid as suid, ctime from treasurebox where status=0 and luid=$luid";
	if($uid){
		$treasureid = uidGetBoxid($luid, $uid, $db);
		if($treasureid){
			$treasureid = '(' . implode(',', $treasureid) .')';
			$sql = "select id, uid as suid, ctime from treasurebox  where status = 0 and luid = $luid and id not in $treasureid";
		}
//		$sql = "select box.id as id, box.uid as suid, box.ctime as ctime from treasurebox as box left join pickTreasure as pick on not(pick.uid=$uid and pick.treasureid=box.id) and box.status=0 and box.luid=$luid";
	}

	$res = $db->query($sql);
	$arr = array();
	while($row = $res->fetch_assoc()){
		array_push($arr, $row);
	}
	return $arr;
}

function uidGetBoxid($luid, $uid, $db){
	$ret = array();
	$sql = "select treasureid from pickTreasure where uid=$uid and luid=$luid";
	$res = $db->query($sql);
	while($row = $res->fetch_assoc()){
		array_push($ret, $row['treasureid']);
	}
	return $ret;
}

function initRoomTreasure($treasure,$db){
	$array = array(
		"total" => 0,
		"list" => array()
	);
	if(!$treasure || !is_array($treasure)){
		return $array;
	}else{
		$array['total'] = count($treasure);
		foreach($treasure as $k => $v){
			$tmp['uid'] = $v['suid'];
			$tmp['trid'] = $v['id'];
			$tmp['ctime'] = strtotime($v['ctime']);

			$nick = getUserInfo($v['suid'], $db);
			$tmp['nick'] = $nick[0]['nick'];
			array_push($array['list'], $tmp);
		}
		return $array;
	}
}