<?php

include '../../../include/init.php';
/**
 * 我的房间管理员列表
 * date 2016-1-21 16:14
 * author yandong@6rooms.com
 * version 0.0
 */

$uid     = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$size    = isset($_POST['size']) ? (int)($_POST['size']) : 11;
$page    = isset($_POST['page']) ? (int)($_POST['page']) : 1;
if (empty($uid) || empty($encpass))
{
    error2(-4013);
}

$auth = new  \service\user\UserAuthService;
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

$roomManagerService = new \service\room\RoomManagerService;
$roomManagerService->setUid($uid);
$roomManagerService->setPage($page);
$roomManagerService->setSize($size);
$totalNum = (int) $roomManagerService->getRoomManagerTotalNum();
$userInfo = [];
if($totalNum > 0 && ($managerData = $roomManagerService->getRoomManagerList()))
{
    $userDataService = new \service\user\UserDataService;
    $userDataService->setCaller('api:'.__FILE__);
    $userDataService->setUid($managerData);
    $userInfo        = $userDataService->getUserInfo();
}

$roomAdminList = [
    'list'  => $userInfo ? array_values($userInfo) : [],
    'total' => $totalNum,
];

render_json($roomAdminList);