<?php

/**
 * 发起直播
 * @author guanlong
 * revise  by yandong at the time 2016-1-21 10:30
 * @copyright 6.cn
 * @version 1.0.3 
 * 直播信息中status字段 代表直播状态
 * 0:直播创建;1:直播创建，未获取直播流名称;2:直播创建，非正常结束，视频数据丢失;
 * 3:直播超时;100:正在直播;101:直播正常结束;102:直播结束且生成录像
 */
include '../init.php';
require(INCLUDE_DIR . 'Anchor.class.php');
require(INCLUDE_DIR . 'Live.class.php');
require(INCLUDE_DIR . 'LiveRoom.class.php');
$db = new DBHelperi_huanpeng();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

/**
 * 生成直播流名称
 * @param type $liveid  直播id
 * @return string
 */
function getLiveStream($liveid) {
    $stream = "Y-" . $liveid . "-" . rand(1000000, 9999999);
    return $stream;
}

/**
 * 根据游戏名称获取游戏信息
 * @param type $db
 * @param type $gamename 游戏名称
 * @return  array()
 */
function getGameIdByNT($db, $gamename) {
    $res = $db->field('gameid,gametid')->where("name='$gamename'")->limit(1)->select('game');
    return $res ? $res : array();
}

/**
 * 直播入库
 * @param type $gamename 游戏名称
 * @param type $uid  主播id
 * @param type $orientation 旋转角度
 * @param type $title  直播标题
 * @param type $device  设备唯一标识
 * @param type $db  
 * @param type $conf  系统配置
 * @return array()
 */
function checkData($gamename, $uid, $quality, $orientation, $title, $device, $db, $conf) {
    $data = array();
    if ($gamename) {
        $gamename = checkStr($gamename);
        $gamename = $db->realEscapeString($gamename);
        $gameInfo = getGameIdByNT($db, $gamename);
        if ($gameInfo) {
            $gameid = $gameInfo[0]['gameid'];
            $gametid = $gameInfo[0]['gametid'];
            $gamename = $gamename;
        } else {
            $gameid = OTHER_GAME; //其他游戏
            $gametid = '';
            $gamename = checkStr($gamename);
            $gamename = $db->realEscapeString($gamename);
        }
    } else {
        error(-4013);
    }
    $ip = fetch_real_ip($port);
    $ip = ip2long($ip);
    $server = $db->realEscapeString($conf['stream-pub']);
    $title = $db->realEscapeString($title);
    $data = array(
        'server' => $server,
        'uid' => $uid,
        'gametid' => $gametid,
        'gameid' => $gameid,
        'gamename' => $gamename,
        'title' => $title,
        'ip' => $ip,
        'port' => $port,
        'quality' => $quality,
        'orientation' => $orientation,
        'deviceid' => $device
    );
    if ($gameid) {
        $data['gameid'] = $gameid;
    }
    $res = $db->insert('live', $data);
    return $res;
}

/**
 * 添加流名称
 * @param type $liveid  直播id
 * @param type $stream 直播流名称
 * @param type $db
 * @return type
 */
function setStream($liveid, $stream, $db) {
    $data = array(
        'stream' => $stream
    );
    $res = $db->where("liveid=$liveid")->update('live', $data);
    return $res;
}

/**
 * 更新直播状态
 * @param type $liveid
 * @param type $db
 * @return type
 */
function setLiveStatus($liveid, $db) {
    $data = array(
        'status' => LIVE
    );
    $res = $db->where("liveid=$liveid")->update('live', $data);
    return $res;
}

/**
 * 直播创建，未获得直播流名称，更改直播状态
 * @param type $liveid
 * @param type $db
 * @return type
 */
function noGetStream($liveid, $db) {
    $data = array(
        'status' => LIVE_CREATE_NOSTREAM
    );
    $res = $db->where("liveid=$liveid")->update('live', $data);
    return $res;
}

/**获取最后一次的直播标题
 * @param $uid  主播id、
 * @param $db
 * @return bool
 */
function getLastLiveInfo($uid,$db){
$res=$db->field('titel')->where("uid=$uid")->order('ctime DESC')->limit(1)->select('live');
    if(!empty($res) && false !==$res){
        return  $res;
    }else{
        return false;
    }
}
/**
 * 停止直播
 * @param int $uid
 * @param object $db
 * @return boolean
 */
function stopUserLive($db, $uid) {
    $data = array('status' => LIVE_STOP);
    $res = $db->where("uid=$uid and status=" . LIVE)->update('live', $data);
    return true;
}

//header(”Content-Type: text/html; charset=UTF-8″);
//header("Content-Type:text/html;charset=UTF-8");
/**
 * start
 */
$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$title = isset($_POST['liveTitle']) ? trim($_POST['liveTitle']) : '';
$gamename = isset($_POST['gameName']) ? trim($_POST['gameName']) : '';
$quality = isset($_POST['videoQuality']) ? trim($_POST['videoQuality']) : 0;
$orientation = isset($_POST['orientation']) ? trim($_POST['orientation']) : 0;
$device = isset($_POST['device']) ? trim($_POST['device']) : '';

$uid = checkInt($uid);
$encpass = checkStr($encpass);
$title = checkStr($title);
if (empty($uid) || empty($encpass) || empty($title) || empty($gamename) || empty($device)) {
    error(-4013);
}
if (mb_strlen($title, 'utf-8') < 3 || mb_strlen($title, 'utf-8') > 20) {
    error(-4062);
}else{
    if(mb_strlen($title, 'latin1') < 3 || mb_strlen($title, 'latin1') > 60){
       error(-4062);  
    }
}
if (mb_strlen($gamename, 'utf-8') < 3 || mb_strlen($gamename, 'utf-8') > 20) {
    error(-4063);
}else{
    if(mb_strlen($gamename, 'latin1') < 3 || mb_strlen($gamename, 'latin1') > 60){
      error(-4063);  
    }
}
//$quality = checkInt($quality);
//旋转角度验证必须为,0:正常，1:逆时针90度，2:逆时针180度，3:逆时针270度
//$orientation = checkInt($orientation);
$orientarr = array(0, 1, 2, 3);
if (!in_array($orientation, $orientarr)) {
    error("-2007");
}
$qualitarr = array(0, 1, 2); //0:普清1:高清2:超清

if (!in_array($quality, $qualitarr)) {
    error("2004");
}
//登录验证及可选参数验证
$code = checkUserState($uid, $encpass, $db); //是否为登录状态
if ($code !== true) {
    error($code);
}
$isExistLive=checkAuchorExistLive($uid,$db);//检测有无未结束的直播
if($isExistLive){
    error(-4066);
}
$AnchorObj = new AnchorHelp($uid);
$checkIsAnchor = $AnchorObj->isAnchor(); //检测是不是主播
if (true !== $checkIsAnchor) {
    error(-4057);
}
$isblack = $AnchorObj->isBlack(); //判断是否在黑名单
if ($isblack) {
    error(-5030);
}
////停止主播其他直播
//stopUserLive($db, $uid);
$nick=getUserInfo($uid, $db);
$chechTitleMode=checkMode(CHECK_TITLE,$db);//校验审核模式
if(!empty($chechTitleMode) && $chechTitleMode !==false){
    //先发后审
    $status= LIVE_TITLE_AUTO_PASS ;//机审通过
}else{
    //先审后发
    $listTitle=getLastLiveInfo($uid,$db);
    if($listTitle){
        $title=$listTitle[0]['title'];
    }else{
        $title=$nick[0]['nick'] .'的直播间';
    }
    $status=USER_NICK_WAIT;//人工待审核
}
$liveid = checkData($gamename, $uid, $quality, $orientation, $title, $device, $db, $conf);
if (!$liveid) {
    error("-2002"); //发起直播失败 
}
$stream = getLiveStream($liveid);
$result = setStream($liveid, $stream, $db);
if (!$result) {//直播创建，未获得直播流名称，等该直播状态
    noGetStream($liveid, $db);
    error("-2010");
}
$res = setLiveStatus($liveid, $db);
if (!$res) {
    error("-2016");
}
//$onLineUser = getLiveRoomUserCount($uid, $db);
$followuser = getFansCount($uid, $db); //关注人数
$liveObj = new LiveHelp($liveid);
$liveObj->addLiveRecord($conf['stream-pub'], $stream);
$Anchor = new AnchorHelp($uid);
$bean = $Anchor->getProperty();
$anchorBean = $Anchor->exchangeToBean($bean['bean']);
$Bean = $anchorBean ? $anchorBean : 0;
/* 返回直播流服务器地址数组 */
echo jsone(array("liveID" => $liveid,
    "liveUploadAddressList" => array($conf['stream-pub']),
    "notifyServer" => $conf['stream-stop-notify'],
    "liveStreamName" => $stream,
    "anchorBean" => $Bean,
//    "onLineUser" => $onLineUser ? $onLineUser : 0,
    "follow" => $followuser ? $followuser : 0
));
// 发送直播房间消息
$liveroom = new LiveRoom($uid, $db);
if (!$liveroom) {
    roomerror(-3001);
}
$liveroom->start($liveid);
//同步到标题审核表
setLiveTitleToAdmin($liveid, $title,$uid,$nick[0]['nick'], $db,$status);
exit;
