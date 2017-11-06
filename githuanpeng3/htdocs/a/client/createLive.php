<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/6/23
 * Time: 下午2:18
 */

include '../../init.php';
include INCLUDE_DIR . 'Anchor.class.php';
include INCLUDE_DIR . 'Live.class.php';


$db = new DBHelperi_huanpeng();

$requestParam = array('uid', 'encpass', 'deviceid', 'title', 'gameName', 'quality');

$qualityArray = array('high'=>2, 'normal'=>1);


foreach($requestParam as $param){
    $$param = isset($_POST[$param]) ? trim($_POST[$param]) : '';
    if(!$$param) error(-4013);
}

if((int)$qualityArray[$quality] <= 0){
    error(-4013);
}

$quality = $qualityArray[$quality] - 1;

$anchor = new AnchorHelp($uid, $db);

if($loginError = $anchor->checkStateError($encpass)){
    error($loginError);
}

if(!$anchor->isAnchor()){
    error(-4057);
}

if($anchor->isLiving()){
    error(-5029);
}




$gameInfo = getGameIdByNT($db, $gameName);
$gameid = $gameInfo['gameid'];
$gametid = $gameInfo['gametid'];
$title = $db->realEscapeString($title);
$ip = fetch_real_ip($port);

$data = array(
    'uid' => $uid,
    'gametid' => $gametid,
    'gameid' => $gameid,
    'gamename' => $gameName,
    'title' => $title,
    'ip' => $ip,
    'port' => $port,
    'orientation' => 4,
    'deviceid' => $deviceid,
    'quality' => $quality,
    'status' => LIVE_CLIENT_CREATE
);

$lastLiveID = $anchor->getLastLiveid();
if($lastLiveID){
    $live = new LiveHelp($lastLiveID, $db);
    if($live->isClientCreateStatus()){
        $liveid = $lastLiveID;
        $db->where("liveid=$liveid")->update('live', $data);
    }else{
        $liveid = $db->insert('live', $data);
    }
}else{
    $liveid = $db->insert('live', $data);
}

//create living;



if(!$liveid) error(-5007);

$live = new LiveHelp($liveid, $db);

$server = $live->getStreamServer();
$stream = $live->getLiveStream();

if(!$live->updateLiveStream($server, $stream))
    error(-5007);

exit(json_encode(array('liveid'=>$liveid, 'server' => $server, 'stream' => $stream)));

/**
 * 根据游戏名称获取游戏信息
 * @param type $db
 * @param type $gamename 游戏名称
 * @return  array()
 */
function getGameIdByNT($db, $gamename) {
    $res = $db->field('gameid,gametid')->where("name='$gamename'")->select('game');
    return $res[0] ? $res[0] : array('gameid'=>'', 'gametid'=>'');
}
?>