<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/6/23
 * Time: 下午3:42
 */

include '../../../include/init.php';

require_once( INCLUDE_DIR . 'lib/WcsHelper.class.php' );
require_once( INCLUDE_DIR . 'lib/CDNHelper.class.php' );
require_once( INCLUDE_DIR . 'lib/User.class.php' );
require_once( INCLUDE_DIR . 'lib/Anchor.class.php' );
require_once( INCLUDE_DIR . 'lib/LiveRoom.class.php' );
require_once( INCLUDE_DIR . 'lib/Live.class.php' );
use lib\Anchor;
use lib\CDNHelper;
use LiveRoom;
use lib\Live;

//用户ID
$uid = isset( $_POST['uid'] ) ? (int)$_POST['uid'] : '';
//校验码
$encpass = isset( $_POST['encpass'] ) ? trim( $_POST['encpass'] ) : '';
//直播标题
$liveID = isset( $_POST['liveID'] ) ? (int)$_POST['liveID'] : '';

//必填参数不能为空
if( empty( $uid ) || empty( $encpass ) || empty( $liveID ) )
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
//$Live->sendClientMsg();
//$Live->startLive();
succ();

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

if(!$isAnchor = $anchor->isAnchor()){
	error2(-4057, 2);
}

if(!$anchor->isRealAnchor($isAnchor))
{
	error2(-4057, 2);
}

$lastLiveId = $anchor->getLastLiveid();
if($lastLiveId != $liveID){
    error2(-4013);
}

$live = new LiveHelp($liveID, $db);
//添加
$liveroom = new LiveRoom($uid, $db);
$liveroom->start($liveID);
succ();

if(!$live->isClientCreateStatus()){
    error2(-5007);
}



if($live->clientStart()){
    $liveroom = new LiveRoom($uid, $db);
    $liveroom->start($liveID);
    succ();
}else{
    error2(-5007);
}