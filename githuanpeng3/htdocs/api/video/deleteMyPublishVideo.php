<?php

include '../../../include/init.php';
/**
 * 删除用户已发布的录像
 * author yandong@6rooms.com
 * date 2016-01-22 15:40
 * version 0.0
 */
$db = new DBHelperi_huanpeng();

/**
 * 校验videoIDList
 * @param type $videoIDList
 * @return type
 */
function checkVideoId($videoIDList) {
    $arr = array_filter(explode(',', $videoIDList));
    $newarr = $new = array();
    foreach ($arr as $v) {
        if (is_numeric($v)) {
            $new = checkInt((int) $v);
            array_push($newarr, $new);
        }
    }
    $lids = implode(',', $newarr);
    return $lids;
}

/**
 * 删除已发布视频
 * @param int $uid
 * @param string $videoList
 * @param object $db
 * @return bool
 */
function deleteVideo($uid, $videoList, $db) {
    $data = array('status' => VIDEO_DEL);
    $res = $db->where(" videoid in($videoList) and uid=$uid")->update('video', $data);
    return $res;
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$videoList = isset($_POST['videoList']) ? trim($_POST['videoList']) : '';

if (empty($videoList)) {
    error2(-991,2);
}
if (empty($uid) || empty($encpass)) {
    error2(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$videoList = checkVideoId($videoList);
$code = checkUserState($uid, $encpass, $db);
if ($code !== true) {
    error2(-4067,2);
}
$res = deleteVideo($uid, $videoList, $db);
if (!$res) {
    $delresult = array('failedList' => $videoList);
} else {
    $delresult = array('failedList' => '');
}
succ($delresult);
