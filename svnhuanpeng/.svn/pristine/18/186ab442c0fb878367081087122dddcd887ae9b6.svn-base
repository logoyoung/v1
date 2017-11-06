<?php

/**
 * 审核录像通过
 * yandong@6rooms.com
 * date 2016-07-01 11:00
 * 
 */
require '../../includeAdmin/Video.class.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();
/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int) $_POST['type'] : 1;
$videoid = isset($_POST['videoid']) ? (int) $_POST['videoid'] : '';


if (empty($uid) || empty($encpass) || empty($type) || empty($videoid)) {
    error(-4013);
}



$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$videoObj = new Video();
$res = $videoObj->setVideoPassOrUnpass($videoid, array('status'=>VIDEO));
if ($res !== false) {
    $videoInfo=getVInfoByVideoId($videoId,$db);
    if($videoInfo){
        $isauto=checkisAutoPublish($videoInfo[0]['liveid'],$db);
        $title='系统消息';
        if($isauto){
            $message="您的直播视频“".$videoInfo[0]['gamename'].'-'.$videoInfo[0]['title']."”已生成并发布成功！";
        }else{
            $message="您的直播视频“".$videoInfo[0]['gamename'].'-'.$videoInfo[0]['title']."”已发布成功！";
        }
        sendMessages($videoInfo[0]['uid'], $title, $message, 0, $db);
    }
    $videoObj->setVideoFinish($uid, $videoid,2);
    succ();
} else {
    error();
}
