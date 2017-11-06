<?php
include '../init.php';
/**
 * 检测用户昵称是否已存在
 * author yandong@6rooms.com
 * date 2016-06-07 12:07
 * copyright@6.cn version 0.0
 */
$db = new DBHelperi_huanpeng();
/**
 * @param type $mobile 手机号码
 * @param type $db
 * @return boolean
 */
function checkUsername($userName, $db) {
    if (empty($userName)) {
        return false;
    }
    $res = $db->where(" nick='$userName'")->limit(1)->select('userstatic');
    if ($res !== false) {
        return true;
    } else {
        return false;
    }
}

/**
 * start
 */
$userName = isset($_POST['userName']) ?  trim($_POST['userName']) : '';
if (empty($userName)) {
    error(-4013);
}
$userName = filterData($userName);
$res = checkUsername($userName, $db);
if ($res) {
    exit(jsone(array('isSuccess' => '1')));
} else {
    exit(jsone(array('isSuccess' => '0')));
}

