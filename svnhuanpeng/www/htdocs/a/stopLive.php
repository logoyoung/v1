<?php
include '../init.php';
require (INCLUDE_DIR . 'LiveRoom.class.php');
require (INCLUDE_DIR . 'Live.class.php');

$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$liveid = isset($_POST['liveID']) ? trim($_POST['liveID']) : '';
// $liveScreenShot = isset($_POST['liveScreenShot']) ? trim($_POST['liveScreenShot']) : '';
$uid = checkInt($uid);
$liveid = checkInt($liveid);
$encpass = checkStr($encpass);

$db = new DBHelperi_huanpeng();
$code = checkUserState($uid, $encpass, $db);

if (true !== $code)
    error($code);

$live = new LiveHelp($liveid);
$rs = $live->stopLive();
$rv = $live->addLive2VideoRecord(VIDEO_SAVETYPE_CALL);
if (! $rs || ! $rv)
    $success = 0;
else
    $success = 1;
    // if(!$live) exit(json_encode($value));
    
/*
 * $sql ="update live"." "
 * ."set status=".LIVE_STOP." "
 * ."where liveid=$liveid"." "
 * ."and status=".LIVE;
 * $res = $db->query($sql);
 */

if($success == 1) liveStatusMsgToAdmin($liveid, 0);

$json = array(
    "isSuccess" => $success
);
$json = json_encode($json);

$liveroom = new LiveRoom($uid, $db);
if (! $liveroom)
    roomerror(- 3001);
$liveroom->stop($liveid);
echo $json;
exit();

?>