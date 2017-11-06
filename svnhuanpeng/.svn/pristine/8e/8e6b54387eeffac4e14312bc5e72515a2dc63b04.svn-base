<?php

/**
 * 取消推荐
 * yandong@6rooms.com
 * date 2016-07-18 10:25
 * 
 */
require '../../includeAdmin/init.php';
$db = new DBHelperi_admin();
/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int) $_POST['type'] : 1;
$liveid = isset($_POST['liveid']) ? (int) $_POST['liveid'] : 30545;

//if (empty($uid) || empty($encpass) || empty($type) || empty($liveid)) {
//    error(-4013);
//}
//$adminHelp = new AdminHelp($uid, $type);
//$err = $adminHelp->loginError($encpass);
//if ($err) {
//    error($err);
//}
/**
 * 获取推荐中的直播
 * @param type $db
 * @return array()
 */
function unRecommendLive($liveid, $db) {
    $res = $db->where("liveid=$liveid")->delete('index_recommend_Live');
    if ($res !== false) {
        return '1';
    } else {
        return '0';
    }
}

$result = unRecommendLive($liveid, $db);
if (false !== $result) {
    succ($result);
} else {
    error(-1012);
}