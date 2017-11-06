<?php

/*
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/29
 * Time: 上午10:34
 */
include '../init.php';
require(INCLUDE_DIR . 'User.class.php');
$db = new DBHelperi_huanpeng();

/**
 * 获取任务列表
 * @param object $userobj  用户对象
 * @return array 
 */
function getTaskLists($userobj) {
    $task = array();
    $res = $userobj->myTaskList();
    if ($res) {
        foreach ($res as $v) {
            $tmp['id'] = $v['id'];
            $tmp['bean'] = $v['bean'];
            $tmp['type'] = $v['type'];
            $tmp['title'] = $v['title'];
            $tmp['status'] = $v['status'];
            array_push($task, $tmp);
        }
    }
    return $task;
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
if (empty($uid) || empty($encpass)) {
    error(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$code = checkUserState($uid, $encpass, $db); //是否为登录状态
if ($code !== true) {
    error($code);
}
$userobj = new UserHelp($uid,$db);
$taskRes = getTaskLists($userobj);
if ($taskRes) {
    exit(json_encode(toString(array('list' => $taskRes))));
} else {
    exit(json_encode(array('list' => array())));
}