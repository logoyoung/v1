<?php

include '../../../../include/init.php';
/**
 * 修改用用户地址
 * date 2016-12-06  10:00
 * author yandong@6rooms.com
 */
$db = new DBHelperi_huanpeng();
use service\event\EventManager;
use service\user\UserAuthService;

/**修改通讯地址
 * @param $uid  用户id
 * @param $province  省份id
 * @param $city 城市id
 * @param $address  详细地址
 * @param $db
 * @return bool
 */
function updateAddr($uid, $province, $city, $address, $db)
{
    if (empty($uid)) {
        return false;
    }
    $data = array(
        'province' => $province,
        'city' => $city,
        'address' => $address

    );
    $res = $db->where("uid=$uid")->update('useractive', $data);
    if ($res !== false) {
        return true;
    } else {
        return false;
    }
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$pid = isset($_POST['pid']) ? (int)($_POST['pid']) : '';
$cid = isset($_POST['cid']) ? (int)($_POST['cid']) : '';
$detail = isset($_POST['detail']) ? trim($_POST['detail']) : '';
if (empty($uid) || empty($encpass)) {
    error2(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
if (empty($pid)) {
    error2(-4071,2);
} else {
    if (!is_numeric($pid)) {
        error2(-4070,2);
    }
}
if (empty($cid)) {
    error(-4071,2);
} else {
    if (!is_numeric($cid)) {
        error2(-4070,2);
    }
}
if (empty($detail)) {
    error2(-4072,2);
}

$auth = new UserAuthService();
$auth->setUid($uid);
$auth->setEnc($encpass);
//校验encpass、用户 登陆状态
if($auth->checkLoginStatus() !== true)
{
    //获取校验结果
    $result    = $auth->getResult();
    //错误码
    $errorCode = $result['error_code'];
    //错误消息
    $errorMsg  = $result['error_msg'];
    //假如是封禁的，可以获取禁时间
    $etime     = isset($result['login_disable_etime']) ? $result['login_disable_etime'] : 0;
    write_log("notice|uid:{$uid};error_code:{$errorCode};error_msg:{$errorMsg};解禁时间:{$etime}|api:".__FILE__,'auth_access');

    error2(-4067,2);
}

$detailLen = mb_strlen($detail, 'utf-8');
if ($detailLen < 10 || $detailLen > 60) {
    error2(-4081,2);
} else {
    if (mb_strlen($detail, 'latin1') < 10 || mb_strlen($detail, 'latin1') > 180) {
        error2(-4081,2);
    }
}

$detail = filterData($detail);
$res = updateAddr($uid, $pid, $cid, $detail, $db);
if ($res) {
    $event = new EventManager();
    $event->trigger(EventManager::ACTION_USER_INFO_UPDATE,['uid' => $uid]);
    $event = null;
    succ();
} else {
    error2(-5017);
}
