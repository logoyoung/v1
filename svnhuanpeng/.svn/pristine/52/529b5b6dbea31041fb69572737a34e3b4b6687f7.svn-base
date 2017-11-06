<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/6/23
 * Time: 下午2:18
 */

include '../../../include/init.php';

require_once( INCLUDE_DIR . 'lib/WcsHelper.class.php' );
require_once( INCLUDE_DIR . 'lib/CDNHelper.class.php' );
require_once( INCLUDE_DIR . 'lib/User.class.php' );
require_once( INCLUDE_DIR . 'lib/Anchor.class.php' );
require_once( INCLUDE_DIR . 'lib/LiveRoom.class.php' );



use lib\Anchor;
use lib\Live;
use LiveRoom;

//用户ID
$uid = isset( $_POST['uid'] ) ? trim( $_POST['uid'] ) : '';
//校验码
$encpass = isset( $_POST['encpass'] ) ? trim( $_POST['encpass'] ) : '';
//直播标题
$liveParams['title'] = isset( $_POST['title'] ) ? trim( $_POST['title'] ) : '';
//游戏名称
$liveParams['gamename'] = isset( $_POST['gameName'] ) ? trim( $_POST['gameName'] ) : '';
//直播画质
$liveParams['quality'] = isset( $_POST['quality'] ) ? trim( $_POST['quality'] ) : 0;
//直播角度
$liveParams['orientation'] = isset( $_POST['orientation'] ) ? trim( $_POST['orientation'] ) : 0;
//设备标识
$liveParams['deviceid'] = isset( $_POST['deviceID'] ) ? trim( $_POST['deviceID'] ) : '';
//直播类型
$liveParams['livetype'] = isset( $_POST['liveType'] ) ? trim( $_POST['liveType'] ) : 0;
//主播所在地经度
$liveParams['longitude'] = isset( $_POST['longitude'] ) ? trim( $_POST['longitude'] ) : 0;
//主播所在地纬度
$liveParams['latitude'] = isset( $_POST['latitude'] ) ? trim( $_POST['latitude'] ) : 0;

//必填参数不能为空
if( empty( $uid ) || empty( $encpass ) || empty( $liveParams['title'] ) || empty( $liveParams['gamename'] ) || empty( $liveParams['deviceid'] ) )
{
	error2( -4013, 2 );
}

$db = new DBHelperi_huanpeng();

//用户类型
if( !Anchor::isAnchor( $uid, $db ) )
{
	error2( -4057, 2 );
}
//登录检测
$Anchor       = new Anchor( $uid, $db );
$loginErrCode = $Anchor->checkStateError( $encpass );
if( $loginErrCode !== true )
{
	error2( $loginErrCode, 2 );
}

$Live = new Live( $uid, $db );

$liveCreateBack = $Live->createLive( $liveParams );
if( !isset( $liveCreateBack['liveID'] ) )
{
	error2( $liveCreateBack, 2 );
}

$conf = $GLOBALS['env-def'][$GLOBALS['env']];
succ( array(
	'liveID'       => $liveCreateBack['liveID'],
	'ctime'        => date( 'Y-m-d H:i:s', time() ),
	'notifyServer' => $conf['stream-stop-notify'],
	'server'       => $liveCreateBack['rtmpServer'],
	'stream'       => $liveCreateBack['stream'],
	'hpbean'       => 0,
	'fansCount'    => 0
) );
exit;

include '../../../include/init.php';
include INCLUDE_DIR . 'Anchor.class.php';
include INCLUDE_DIR . 'Live.class.php';


$db = new DBHelperi_huanpeng();

$requestParam = array('uid', 'encpass', 'deviceID', 'title', 'gameName', 'quality');

$qualityArray = array('high'=>2, 'normal'=>1);


foreach($requestParam as $param){
    $$param = isset($_POST[$param]) ? trim($_POST[$param]) : '';
    if(!$$param) error2(-4013);
}

if((int)$qualityArray[$quality] <= 0){
    error2(-4013);
}

$quality = $qualityArray[$quality] - 1;

$anchor = new AnchorHelp($uid, $db);

if($loginError = $anchor->checkStateError($encpass)){
    error2($loginError);
}

if(!$isAnchor = $anchor->isAnchor()){
    error2(-4057, 2);
}

if(!$anchor->isRealAnchor($isAnchor))
{
	error2(-4057, 2);
}

if($anchor->isLiving()){
    error2(-5029);
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



if(!$liveid) {
    error2(-5007);
}

$live = new LiveHelp($liveid, $db);

$server = $live->getStreamServer();
$stream = $live->getLiveStream();
//if($uid==90){
//$stream='Y-90-1111111';
//}
//if($uid==15){
//    $stream='Y-15-2222222';
//}
//if($uid==365){
//    $stream='Y-365-3333333';
//}
//if($uid==370){
//    $stream='Y-370-5555555';
//}
//if($uid==375){
//    $stream='Y-375-6666666';
//}
if(!$live->updateLiveStream($server, $stream))
    error2(-5007);

if(!$live->addLiveRecordClient($server,$stream))
	error2(-5007);


succ(array('liveID'=>$liveid, 'server' => $server, 'stream' => LiveHelp::getStreamCallBackString($stream,$liveid,$uid)));

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