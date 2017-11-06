<?php

include '../../../include/init.php';
/**
 * 取消房间管理预员
 * date 2016-1-11 10:14
 * author yandong@6rooms.com
 * version 0.0
 */

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$adminID = isset($_POST['adminID']) ? trim($_POST['adminID']) : '';
if (empty($uid) || empty($encpass) || empty($adminID)) {
    error2(-993);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$adminID = checkInt($adminID);

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
$roomManagerService->setManagerUid($adminID);
if($roomManagerService->isRoomManager() !== true)
{
    write_log("warning|不是房管;无需取消; anchorUid:{$uid};mamangerUid:{$adminID}; line:".__LINE__.";api:".__FILE__,'room_manager_access');
    error2(-5022,2);
}

if($roomManagerService->deleteRoomManager())
{
    succ();
}

error2(-5017,2);
