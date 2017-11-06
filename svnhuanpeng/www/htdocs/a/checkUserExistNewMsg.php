<?php

include '../init.php';
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
    $res = $db->field('id')->where(" stime>'$time' and type=2")->select('sysmessage');
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
    return $upres;
}

/**
 * 检测用户消息
 * @param type $uid
 * @param type $db
 * @return type
 */
function checkUsrMsg($uid, $db) {
    $lastmsgid = getLastUserMsgId($uid, $db);
    if (empty($lastmsgid)) {
        $regtime = getUserRegisterTime($uid, $db);
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
    error(-993);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error($s);
}
checkUsrMsg($uid, $db);
$msgnum = getUserMailStatusByUid($uid, $db);
if ($msgnum) {
    exit(jsone(array('mailstatus' => $msgnum)));
} else {
    exit(jsone(array('mailstatus' => '0')));
}

