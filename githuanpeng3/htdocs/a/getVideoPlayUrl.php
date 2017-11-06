<?php

include '../init.php';
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
 * @param type $uid      主播id
 * @param type $videoId  录像Id
 * @param type $db
 * @return array()
 */
function getVideoPlayUrl($uid, $videoId, $db) {
    if (empty($uid) || empty($videoId)) {
        return false;
    }
    $res = $db->field('vfile,orientation')->where("uid=$uid and videoid=$videoId")->select('video');
    return $res ? $res : array();
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) ($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$videoId = isset($_POST['videoId']) ? (int) ($_POST['videoId']) : '';
if (empty($uid) || empty($encpass) || empty($videoId)) {
    error(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$videoId = checkInt($videoId);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error($s);
}
$res = getVideoPlayUrl($uid, $videoId, $db);
if ($res) {
    exit(json_encode(array('playUrl' => $conf['domain-video'] . $res[0]['vfile'],'angle'=>$res[0]['orientation'])));
} else {
    exit(json_encode(array('playUrl' => '','angle'=>'0')));
}