<?php

include '../../../include/init.php';

use lib\Anchor;
use lib\Live;
use service\user\UserAuthService;
use lib\live\LiveLog;

$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$liveid = isset($_POST['liveID']) ? trim($_POST['liveID']) : '';

if (empty($uid) || empty($encpass))
{
    error2(-4013, 2);
}

$db = new DBHelperi_huanpeng();

/* //用户类型
  if(!Anchor::isAnchor($uid, $db))
  error2(-4057,2);
  //登录检测
  $Anchor = new Anchor($uid,$db);
  $loginErrCode = $Anchor->checkStateError($encpass);
  if($loginErrCode!==true)
  {
  error2($loginErrCode,2);
  } */

//权限校验

$auth = new UserAuthService();
$auth->setUid($uid);
$auth->setEnc($encpass);

if ($auth->checkLoginStatus() !== true)
{
    $errorCode = $result['error_code'];
    error2('-1013', 2);
    exit;
}
if (!Anchor::isAnchor($uid, $db))
{
    error2(-4057, 2);
}

$Live = new Live($uid, $db);
$r = $Live->anchorStopLive();

//mylog("用户{$uid}停止了直播：{$liveid}", LOG_DIR . 'Live.error.log');
LiveLog::applog("record:用户{$uid}停止了直播：{$liveid}");
succ();
