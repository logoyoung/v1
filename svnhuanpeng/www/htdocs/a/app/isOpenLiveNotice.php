<?php

include '../../init.php';
/**
 * App端是否开启开播提示
 * date 2016-05-26 16:29
 * author yandong@6rooms.com
 */
$db = new DBHelperi_huanpeng();

/**
 * 是否开启直播通知
 * @param type $uid    用户id
 * @param type $status 开启1, 关闭0
 * @param type $db
 * @return type
 */
function setNoticeStatus($uid, $status, $db) {
    $data = array(
        'isnotice' => $status
    );
    $res = $db->where('uid=' . $uid)->update('useractive', $data);
    return $res;
}

/**
 * 开启||关闭 直播开播通知
 * @param type $uid   用户id
 * @param type $luid  主播id
 * @param type $db
 */
function addNoNoticeAnchor($uid, $luid, $status, $db) {
    if ($status == 0) {//删除
        $res = $db->where("uid=$uid  and luid=$luid")->delete('live_notice');
    }
    if ($status == 1) {//添加
        $data = array(
            'uid' => $uid,
            'luid' => $luid
        );
        $res = $db->where("uid=$uid  and luid=$luid")->select('live_notice');
        if ($res) {
            $res = true;
        } else {
            $res = $db->insert('live_notice', $data);
        }
    }
    return $res;
}

/**
 * 开启或关闭直播通知
 * @param type $uid  用户id
 * @param type $luid  主播id
 * @param type $status  状态 1开启 ,0关闭
 * @param type $db
 */
function openOrCloseNotice($uid, $luid, $status, $db) {
    if ($luid) {
        $res = addNoNoticeAnchor($uid, $luid, $status, $db); //对单个主播开关
    } else {
        $res = setNoticeStatus($uid, $status, $db); //总开关
    }
    return $res;
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) ($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$status = isset($_POST['status']) ? (int) ($_POST['status']) : '';
$luid = isset($_POST['luid']) ? (int) ($_POST['luid']) : '';
if (empty($uid) || empty($encpass) || !in_array($status, array(0, 1))) {
    error(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error($s);
}
$res = openOrCloseNotice($uid, $luid, $status, $db);
if ($res !== false) {
    if ($luid) {
        exit(jsone(array('isSuccess' => '1', 'luid' => $luid)));
    } else {
        exit(jsone(array('isSuccess' => '1',)));
    }
} else {
    if ($luid) {
        exit(jsone(array('isSuccess' => '0', 'luid' => $luid)));
    } else {
        exit(jsone(array('isSuccess' => '0',)));
    }
}