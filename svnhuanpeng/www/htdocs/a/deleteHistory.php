<?php

include '../init.php';
/**
 * 删除浏览历史纪录
 * date 2016-05-09 11:43 am
 * author yandong@6rooms.com
 * version 0.0
 */
$db = new DBHelperi_huanpeng();

/**
 * 删除浏览历史
 * @param int $uid
 * @param int $videoID
 * @param object $db
 * @return success 0 and fail false
 */
function removeHistory($uid, $history, $db) {
    $res = $db->where('uid=' . $uid . ' and luid in (' . $history . ')')->delete('history');
    return $res ? $res : array();
}

/**
 * 校验videoIDList
 * @param type $videoIDList
 * @return type
 */
function checkVideoId($uidList) {
    $arr = array_filter(explode(',', $uidList));
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
$history = isset($_POST['history']) ? $_POST['history'] : '';
if (empty($history) || empty($uid) || empty($encpass)) {
    error(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$history = checkVideoId($history);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error($s);
}
$result = removeHistory($uid, $history, $db);
if($result){
    exit(jsone(array('isSuccess'=>1,'failedList'=>'')));
}else{
    exit(jsone(array('isSuccess'=>0,'failedList'=>$history)));
}

