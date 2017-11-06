<?php

include '../../../include/init.php';
/**
 * 获取录像播放地址
 * date 2016-5-19 11:30 am
 * author yandong@6rooms.com
 * version 0.0
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();

/**
 * 获取录像播放地址
 * @param type $videoId  录像Id
 * @param type $db
 * @return array()
 */
function getVideoPlayUrl($videoId, $db) {
    if (empty($videoId)) {
        return false;
    }
    $res = $db->field('vfile,orientation')->where("videoid=$videoId")->select('video');
    return $res ? $res : array();
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) ($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$videoId = isset($_POST['videoID']) ? (int) ($_POST['videoID']) : '';
if (empty($uid) || empty($encpass) || empty($videoId)) {
    error2(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$videoId = checkInt($videoId);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error2(-4067,2);
}
$res = getVideoPlayUrl($videoId, $db);
$huanVlist=explode(',',HUANPENG_VIDEO);
if ($res) {
    if(in_array($videoId,$huanVlist)){
        succ(array('videoUrl' => $conf['huan-video'] ."/". $res[0]['vfile'],'orientation'=>$res[0]['orientation']));
    }else{
        $vfile=sfile($res[0]['vfile']);
        succ(array('videoUrl' => $vfile,'orientation'=>$res[0]['orientation']));
    }

} else {
    succ(array('videoUrl' => '','orientation'=>'0'));
}