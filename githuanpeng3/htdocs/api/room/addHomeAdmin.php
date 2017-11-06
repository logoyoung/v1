<?php

include '../../../include/init.php';
/**
 * 添加房间管理员
 * date 2016-1-10 17:14
 * author yandong@6rooms.com
 * version 0.0
 */

$uid       = isset($_POST['uid'])     ? (int) $_POST['uid'] : '';
$encpass   = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$adminNick = isset($_POST['nick'])    ? xss_clean(trim($_POST['nick'])) : '';
if (empty($uid) || empty($encpass) || empty($adminNick)) {
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

$userService = new \service\user\UserDataService;
$userService->setNick($adminNick);
$managerUid  = $userService->getUidByNick();
if(!$managerUid)
{
    write_log("error|没有获取到相关用户; anchorUid:{$uid};nick:{$adminNick}; line:".__LINE__.";api:".__FILE__,'room_manager_access');
    error2(-5020,2);
}

if($uid == $managerUid)
{
    error2(-5021,2);
}

$userData = $userService->getUserInfo();
if(!$userData)
{
    write_log("error|获取用户数数异常; anchorUid:{$uid};mamangerUid:{$managerUid}; line:".__LINE__.";api:".__FILE__,'room_manager_access');
    error2(-5020,2);
}

$rest = [
    'uid'  => $userData['uid'],
    'nick' => $userData['nick'],
    'head' => $userData['pic'],
];

$roomManagerService = new \service\room\RoomManagerService;
$roomManagerService->setUid($uid);
$roomManagerService->setManagerUid($rest['uid']);
if($roomManagerService->isRoomManager())
{
    write_log("warning|已经是房管;无需添加; anchorUid:{$uid};mamangerUid:{$rest['uid']}; line:".__LINE__.";api:".__FILE__,'room_manager_access');
    error2(-5022,2);
}

if(!$roomManagerService->addRoomManager())
{
    error2(-5022,2);
}

render_json($rest);