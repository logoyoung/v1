<?php

include '../init.php';
/**
 * 我的消息详情
 * author yandong@6rooms.com
 * date 2016-04-18 12:08
 * copyright@6.cn version 0.0
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();
/**
 * 
 * @param int $uid
 * @param int $msgId
 * @param object $db
 * @return string
 */
function checkExistMessages($uid, $msgId, $db) {
    $res = $db->field('msgid')->where("uid=$uid and msgid=$msgId and status=0")->limit(1)->select('usermessage');
    return $res ? $res[0]['msgid'] : '';
}

/**
 * 获取消息详情
 * @param int $uid
 * @param int $lastId
 * @param int $page
 * @param int $size
 * @param object $db
 * @return array || ''
 */
function getUserMegInfo($uid, $msgId, $db) {
    $res = '';
    $msgids = checkExistMessages($uid, $msgId, $db);
    if ($msgids) {
        $res = $db->field('id,title,msg,stime,sendid')->where("id=$msgids")->limit(1)->select('sysmessage');
    }
    return $res ? $res : '';
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) ($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$msgId = isset($_POST['msgId']) ? (int) ($_POST['msgId']) : '';

if (empty($uid) || empty($encpass) || empty($msgId)) {
    error(-993);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$msgId = checkInt($msgId);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error($s);
}
$msgData = getUserMegInfo($uid, $msgId, $db);
if ($msgData) {
    foreach ($msgData as $v) {
        $list['msgId'] = $v['id'];
        // $list['picUrl'] = "http://" . $conf['domain-img'] . "/".;
        $list['picUrl'] = 'http://dev-img.huanpeng.com/a/a/aa02a056f520be09ea0f5e436954a2a9.png';
        $list['msgTitle'] = $v['title'];
        $list['msgCont'] = $v['msg'];
        $list['msgTime'] = strtotime($v['stime']);
    }
    exit(jsone(array('msgImfo' =>$list)));
} else {
    exit(jsone(array('msgImfo' => '')));
}
