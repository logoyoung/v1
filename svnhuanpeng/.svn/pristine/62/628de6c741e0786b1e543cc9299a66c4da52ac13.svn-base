<?php





include '../../../include/init.php';

require_once( INCLUDE_DIR . 'lib/WcsHelper.class.php' );
require_once( INCLUDE_DIR . 'lib/CDNHelper.class.php' );
require_once( INCLUDE_DIR . 'lib/User.class.php' );
require_once( INCLUDE_DIR . 'lib/Anchor.class.php' );
require_once( INCLUDE_DIR . 'lib/LiveRoom.class.php' );
require_once( INCLUDE_DIR . 'lib/Live.class.php' );


use lib\Anchor;
use lib\Live;
use LiveRoom;

//用户ID
$uid = 						 isset( $_POST['uid'] ) ? trim( $_POST['uid'] ) : '';
//校验码
$encpass = 					 isset( $_POST['encpass'] ) ? trim( $_POST['encpass'] ) : '';
//直播标题
$liveParams['title'] = 		 isset( $_POST['title'] ) ? trim( $_POST['title'] ) : '';
//游戏名称
$liveParams['gamename'] = 	 isset( $_POST['gameName'] ) ? trim( $_POST['gameName'] ) : '';
//直播画质
$liveParams['quality'] = 	 isset( $_POST['quality'] ) ? trim( $_POST['quality'] ) : 0;
//直播角度
$liveParams['orientation'] = isset( $_POST['orientation'] ) ? trim( $_POST['orientation'] ) : 0;
//设备标识
$liveParams['deviceid'] = 	 isset( $_POST['deviceID'] ) ? trim( $_POST['deviceID'] ) : '';
//直播类型
$liveParams['livetype'] = 	 isset( $_POST['liveType'] ) ? trim( $_POST['liveType'] ) : 0;
//主播所在地经度
$liveParams['longitude'] =   isset( $_POST['longitude'] ) ? trim( $_POST['longitude'] ) : 0;
//主播所在地纬度
$liveParams['latitude'] =    isset( $_POST['latitude'] ) ? trim( $_POST['latitude'] ) : 0;

//必填参数不能为空
if( empty( $uid ) || empty( $encpass ) || empty( $liveParams['deviceid'] ) )
{
	error2( -4013, 2 );
}

$db = new DBHelperi_huanpeng();

//用户类型
if(!Anchor::isAnchor($uid, $db))
	error2(-4057,2);
//登录检测
$Anchor = new Anchor($uid,$db);
$loginErrCode = $Anchor->checkStateError($encpass);
if($loginErrCode!==true)
{
	error2($loginErrCode,2);
}

$Live = new Live($uid,$db);

$liveCreateBack = $Live->createLive($liveParams);
if(!isset($liveCreateBack['liveID']))
	error2($liveCreateBack,2);

$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$Live->anchorStopLive();
succ();
succ(array(
	'server'=>array($liveCreateBack['rtmpServer']),
	'stream' => $liveCreateBack['stream'],
	'hpbean' => 0,
	'fansCount' => 0,
	'userCount'=>0
));
exit;





/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/8/4
 * Time: 上午10:46
 */
include '../../../include/init.php';
include INCLUDE_DIR . 'LiveRoom.class.php';
include_once INCLUDE_DIR . 'Anchor.class.php';
include_once INCLUDE_DIR . 'Live.class.php';

$db = new DBHelperi_huanpeng();

$requestParam = array('uid'=>'int', 'encpass' =>'str', 'liveID' =>'int');
foreach($requestParam as $param => $type){
    $$param = isset($_POST[$param]) ? trim($_POST[$param]) : '';
    $$param = $type == 'int' ? (int)$$param : $$param;
    if(!$$param) error2(-4013);
}

$anchor = new AnchorHelp($uid);
if($loginError = $anchor->checkStateError($encpass)){
    error2($loginError);
}

if(!$anchor->isAnchor()){
    error2(-4057);
}


$lastLiveId = $anchor->getLastLiveid();
if($lastLiveId != $liveID){
    error2(-4013);
}


$live = new LiveHelp($liveID, $db);

if($live->isLiving()){
    $liveroom = new LiveRoom($uid, $db);
    $liveroom->start($liveID);
    succ();
}else{
    error2(-5015);
}