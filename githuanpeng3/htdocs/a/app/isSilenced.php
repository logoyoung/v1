<?php

include '../../init.php';
/**
 * 检测是否被禁言
 * date 2016-10-13 15:09
 * author yandong@6rooms.com
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) ($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$luid = isset($_POST['luid']) ? (int) ($_POST['luid']) : '';

if (empty($luid) || empty($uid) || empty($encpass)) {
    error(-4013);
}
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error($s);
}
$res = is_speakOk($uid, $luid, $db);
if ($res) {
    $userInfo['isSilence'] = '1';
    $userInfo['silenceTime'] = "$res";
} else {
    $userInfo['isSilence'] = '0';
    $userInfo['silenceTime'] = '0';
}
exit(json_encode(array('info'=>$userInfo)));