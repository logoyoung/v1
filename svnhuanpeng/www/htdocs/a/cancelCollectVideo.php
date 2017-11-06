<?php

include '../init.php';
/**
 * 取消收藏录像
 * date 2015-12-31 10:47 am
 * author yandong@6rooms.com
 * version 0.0
 */
$db = new DBHelperi_huanpeng();

/**
 * 取消收藏录像
 * @param int $uid
 * @param int $videoID
 * @param object $db
 * @return success 0 and fail false
 */
function removeCollectVideo($uid, $videoIDList, $db) {
    $res = $db->where('uid=' . $uid . ' and videoid in (' . $videoIDList . ')')->delete('videofollow');
    if (false !== $res) {
        $res = array('isSuccess' => 1, 'failedList' => '');
    } else {
        $res = array('isSuccess' => 0, 'failedList' => $videoIDList);
    }
    return $res;
}

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
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$videoIDList = isset($_POST['videoIDList']) ? $_POST['videoIDList'] : '';
if (empty($videoIDList)) {
    error(-991);
}
if (empty($uid) || empty($encpass)) {
    error(-993);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$videoIDList = checkVideoId($videoIDList);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error($s);
}
$result = removeCollectVideo($uid, $videoIDList, $db);
exit(jsone($result));
