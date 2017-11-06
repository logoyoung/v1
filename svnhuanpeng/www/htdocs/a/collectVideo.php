<?php

include '../init.php';
/**
 * 收藏录像
 * date 2015-12-31 10:47 am
 * author yandong@6rooms.com
 * version 0.0
 */
$db = new DBHelperi_huanpeng();

/**
 * 收藏录像
 * @param int $uid
 * @param int $videoID
 * @param object $db
 * @return success 0 and fail false
 */
function addCollectVideo($uid, $videoID, $db) {
    $sql = "INSERT INTO `videofollow` (`uid`,`videoid`) VALUES ($uid, $videoID) on duplicate key update uid = $uid, videoid = $videoID";
    $res = $db->doSql($sql);
    if (false !== $res) {
        $res = array('isSuccess' => 1, 'videoID' => $videoID);
    } else {
        $res = array('isSuccess' => 0, 'videoID' => $videoID);
    }
    return $res;
}

$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$videoID = isset($_POST['videoID']) ? (int) $_POST['videoID'] : '';
if (empty($uid) || empty($encpass)) {
    error(-993);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$videoID = checkInt($videoID);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error($s);
}
$result = addCollectVideo($uid, $videoID, $db);
exit(jsone($result));


