<?php

include '../../../../include/init.php';
/**
 * 修改用户性别
 * date 2016-07-21  10:00
 * author yandong@6rooms.com
 */
$db = new DBHelperi_huanpeng();
use service\event\EventManager;
use service\user\UserAuthService;

/**
 * 修改性别
 * @param type $uid  用户id
 * @param type $sex  性别
 * @param type $db
 * @return boolean
 */
function updateSex($uid,$sex,$db){
    if(empty($uid)){
        return false;
    }
    $data=array(
        'sex'=>$sex
    );
    $res=$db->where("uid=$uid")->update('userstatic',$data);
    if($res !==false){
        return true;
    }else{
        return false;
    }
}
/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$sex = isset($_POST['sex']) ? trim($_POST['sex']) : '';
if (empty($uid) || empty($encpass) || !in_array($sex,array(0,1,2))) {
    error2(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);

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

$res=updateSex($uid,$sex,$db);
if($res){
    $event = new EventManager();
    $event->trigger(EventManager::ACTION_USER_INFO_UPDATE,['uid' => $uid]);
    $event = null;
    succ();
}else{
    error2(-5017);
}
