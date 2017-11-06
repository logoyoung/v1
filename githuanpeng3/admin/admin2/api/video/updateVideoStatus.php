<?php
/**
 * 更改状态，包裹通过与不通过
 * jiantao@6.cn
 * date 2017-05-09 11:00
 * 
 */
require '../../includeAdmin/Video.class.php';
require '../../includeAdmin/Admin.class.php';
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_admin();
/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int) $_POST['type'] : 1;
$videoid = isset($_POST['videoid']) ? (int) $_POST['videoid'] : '';
$reason = isset($_POST['reason']) ? (int)($_POST['reason']) : '';
$describe = isset($_POST['describe']) ? trim($_POST['describe']) : '';


if (empty($uid) || empty($encpass) || empty($type) || empty($videoid)|| empty($reason) || empty($describe)) {
    error(-4013);
}

$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$videoObj = new Video();
$res = $videoObj->setVideoPassOrUnpass($videoid, array('status'=>VIDEO_UNPASS));
if ($res !== false) {
    $videoObj->setVideoFinish($uid, $videoid,3);
    $videoObj->setVideoUnpass($videoid, $uid, $reason, $describe);
    $videoInfo=getVInfoByVideoId($videoId,$db);
    if($videoInfo){
        $title='系统消息';
        $message="您的直播视频“".$videoInfo[0]['gamename'].'-'.$videoInfo[0]['title']."”未能通过审核，发布失败。";
        sendMessages($videoInfo[0]['uid'], $title, $message, 0, $db);
    }
    succ();
} else {
    error();
}
