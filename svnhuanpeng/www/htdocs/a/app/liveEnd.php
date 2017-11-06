<?php

/**
 * App结束直播以后 是否自动发布录像
 * date 2016-05-12 13:50
 * author yandong@6rooms.com
 */
include '../../init.php';
$db = new DBHelperi_huanpeng();

/**
 * 直播结束后添加一条录像数据
 * @param type $uid 主播id
 * @param type $liveId  直播id
 * @param type $publish 是否自动发布,,不自动[0],自动[1],默认0
 * @param type $db
 * @return boolean
 */
function addToVideo($uid, $liveId, $publish, $db) {
    if ($publish == 1) {
        $data = array(
            'antopublish' => 1
        );
        $res = $db->where("uid=$uid and liveid=$liveId")->update('live', $data);
        if ($res) {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}

/**
 * start
 */
$liveId = isset($_POST['liveId']) ? (int) $_POST['liveId'] : '';
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$publish = isset($_POST['publish']) ? (int) $_POST['publish'] : 0;
if (empty($liveId) || empty($uid) || empty($encpass)) {
    error(-4013);
}
if (!in_array($publish, array(0, 1))) {
    error(-4013);
}
//检查用户登陆状态
$userState = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $userState) {
    error($userState);
}
$res = addToVideo($uid, $liveId, $publish, $db);
if ($res) {
    exit(jsone(array('isSuccess' => 1)));
} else {
    exit(jsone(array('isSuccess' => 0)));
}