<?php

include '../../../include/init.php';
use service\user\UserDataService;
use service\event\EventManager;
use service\user\UserAuthService;

/**
 * 同步用户站内信
 * author yandong@6rooms.com
 * date 2016-03-8 10:22
 * copyright@6.cn version 0.0
 */
$db = new DBHelperi_huanpeng();

/**
 * 获取用户最后一条站内信的id
 * @param int $uid
 * @param object $db
 * @return string
 */
function getLastUserMsgId($uid, $db) {
    $res = $db->field('MAX(msgid) as lastumid')->where("uid=$uid")->select('usermessage');
    return $res ? $res[0]['lastumid'] : '';
}

/**
 * 获取用户是否有新的消息
 * @param object $db
 * @return array
 */
function getIsNewMsg($msgid, $db) {
    $res = $db->field('id')->where(" id>$msgid and type=2")->select('sysmessage');
    return $res ? $res : array();
}

/**
 * 更改用户站内信数量
 * @param int $uid
 * @param object $db
 * @return bool
 */
function upUserMailStatus($uid, $num, $db) {
    $sql = "update useractive set readsign=readsign+$num where uid=$uid";
    $res = $db->doSql($sql);
    return $res;
}

/**
 * 跟新用户消息列表
 * @param int $uid
 * @param int $msgid
 * @param object $db
 * @return string
 */
function addNewUserMsg($uid, $msgid, $db) {
    $data = array(
        'uid' => $uid,
        'msgid' => $msgid
    );
    $res = $db->insert('usermessage', $data);
    return $res;
}

/**
 * 获取用户注册时间
 * @param type $uid
 * @param type $db
 * @return type
 */
function getUserRegisterTime($uid, $db) {
    $res = $db->field('rtime')->where(" uid=$uid")->select('userstatic');
    return $res ? $res[0]['rtime'] : '';
}

/**
 * 获取用户的消息数量
 * @param int $uid
 * @param object $db
 * @return string
 */
function getUserMailStatusByUid($uid, $db) {
    $res = $db->field('readsign')->where(" uid=$uid")->select('useractive');
    return $res ? $res[0]['readsign'] : '0';
}

/**
 * 获取消息
 * @param type $regtime
 * @param type $db
 * @return type
 */
function getUserMsgBytime($time, $db) {
    $res = $db->field('id')->where("type=2 and  stime>'$time'")->select('sysmessage');
    return $res ? $res : array();
}

/**
 * 同步消息&&更新未读消息
 * @param type $uid
 * @param type $newmid
 * @param type $db
 * @return type
 */
function addMsgAndUpMailStatus($uid, $newmid, $db) {
    foreach ($newmid as $v) {
        addNewUserMsg($uid, $v['id'], $db);
    }
    $upres = upUserMailStatus($uid, count($newmid), $db);
    if($upres)
    {
        $event = new EventManager();
        $event->trigger(EventManager::ACTION_USER_MSG_UPDATE,[ 'uid' => $uid ]);
        $event = null;
    }
    return $upres;
}

/**
 * 检测用户消息
 * @param type $uid
 * @param type $db
 * @return type
 */
function checkUsrMsg($uid, $db,$regtime = '') {
    $lastmsgid = getLastUserMsgId($uid, $db);

    if (empty($lastmsgid)) {
        if(!$regtime)
        {
            $regtime = getUserRegisterTime($uid, $db);
        }
        $msgids = getUserMsgBytime($regtime, $db);

        if ($msgids) {
            addMsgAndUpMailStatus($uid, $msgids, $db);
        }
    } else {
        $msgids = getIsNewMsg($lastmsgid, $db);
        if ($msgids) {
            addMsgAndUpMailStatus($uid, $msgids, $db);
        }
    }
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) ($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
if (empty($uid) || empty($encpass)) {
    error2(-993);
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

$userService = new UserDataService();
$userService->setUid($uid);
$userService->setCaller('api:'.__FILE__);
$staticData  = $userService->getUserInfo();

checkUsrMsg($uid, $db, $staticData['rtime']);

//$msgnum = getUserMailStatusByUid($uid, $db);

$userService->setUserInfoDetail(UserDataService::USER_ACTICE_BASE);
//这里有可能第一次请求不准确情况,第二次正常，因为改为redis缓存，更新会有时间短暂数据同步问题
$activeData = $userService->getUserInfo();
$msgnum     = isset($activeData['readsign']) ? $activeData['readsign'] : 0;

render_json(['unreadMsg' => ($msgnum ? $msgnum : 0)]);