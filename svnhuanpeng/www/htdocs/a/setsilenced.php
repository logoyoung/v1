<?php

/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/13
 * Time: 上午11:29
 */
include '../init.php';
require_once INCLUDE_DIR . 'redis.class.php';
require(INCLUDE_DIR . 'LiveRoom.class.php');
$db = new DBHelperi_huanpeng();


$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$luid = isset($_POST['luid']) ? (int) $_POST['luid'] : 0;
$targetUid = isset($_POST['targetUid']) ? (int) $_POST['targetUid'] : 0;

if (!$uid || !$enc || !$luid || !$targetUid) {
    exit(json_encode(array('code' => -1, 'desc' => '参数错误')));
}

$code = checkUserState($uid, $enc, $db);
if ($code !== true) {
    error($code);
}

//验证luid 合法性

$lroom = new LiveRoom($luid, $db);

if($targetUid == $uid){
	exit(json_encode(array('code' => -4, 'desc' =>'不能禁言自己')));
}
if($targetUid == $luid){
	exit(json_encode(array('code' => -3, 'desc' =>'不能禁言主播')));
}

if($lroom->isRoomAdmin($targetUid)){
	exit(json_encode(array('code' => -2, 'desc' =>'不能禁言管理员')));
}
$group = 5;
if ($uid !== $luid) {
    if (!$lroom->isRoomAdmin($uid)) {
        exit(json_encode(array('code' => -1, 'desc' => '您还不是管理员哦!'))); //权限不够
    } else {
        $group = 4;
    }
}


$redis = new redishelp();


/**
 * inser into silencedlist (luid, uid) value($luid, $uid) on duplicate key update  ctime = CURRENT_TIMESTAMP
 *
 * create table silencedlist(
 * `luid` int(10) unsigned not null default '0',
 * `uid` int(10) unsignedn not null defaulst '0',
 * `ctime` timestamp not null default current_timestamp,
 * primary key (`luid`, `uid`),
 * key (`uid`),
 * )
 *
 */
function addsilenced($luid, $uid, $db) {
    $time = date("Y-m-d H:i:s");
    $sql = "INSERT INTO `silencedlist` (`luid`,`uid`) VALUES ($luid, $uid) on duplicate key update uid = $uid, luid = $luid, ctime='$time'";
    $res = $db->doSql($sql);
    return $res;
}
$addRes = addsilenced($luid, $targetUid, $db);
$timestamp = time() + ROOM_SILENCE_TIMEOUT;
if ($addRes==true) {
	$redis->set("silenced:$luid:$targetUid", 1, 3600);
    $userinfo = getUserNicks(array($uid,$targetUid), $db);
    $content = array(
        "t" => "505",
        "admin" => "$uid",
        "adminNick" => "$userinfo[$uid]",
        "targetUid" => "$targetUid",
        "targetNick" => "$userinfo[$targetUid]",
        "group" => "$group",
        'outTimestamp'=>$timestamp
    );
    $lroom->sendRoomMsg(json_encode(toString($content)));
	exit(json_encode(array('isSuccess'=> 1, 'timestamp' => $timestamp)));
}else{
	exit(json_encode(array('error'=> -5, 'desc'=>'系统错误，禁言失败')));
}

