<?php

include '../init.php';
/**
 * 我的消息列表
 * author yandong@6rooms.com
 * date 2016-01-27 15:40
 * copyright@6.cn version 0.0
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();

/**
 * 获取用户消息列表
 * @param int $uid
 * @param object $db
 * @return string
 */
function getMessages($uid, $page, $size, $db) {
    $total = $db->field('count(*) as total')->where("uid=$uid and status=0")->select('usermessage');
    $tcount = ($total[0]['total'] == 0) ? 1 : $total[0]['total'];
    $page = returnPage($tcount, $size, $page);
    $res = $db->field('msgid')->where("uid=$uid and status=0")->order('msgid desc')->limit($page, $size)->select('usermessage');
    if ($res) {
        foreach ($res as $v) {
            $lists[] = $v['msgid'];
        }
        $msgid = implode(',', $lists);
    } else {
        $msgid = array();
    }
    return $msgid;
}

/**
 * 获取总条数
 * @param type $uid
 * @param type $db
 * @return type
 */
function getAllCount($uid, $db) {
    $allcount = '';
    $res = $db->field('count(*) as mcount')->where("uid=$uid and status=0")->select('usermessage');
    if (!empty($res)) {
        $allcount = $res[0]['mcount'];
    }
    return $allcount;
}

/**
 * 获取剩余元素数量
 * @param type $uid
 * @param type $lastId
 * @param type $db
 * @return type
 */
function getleftCount($uid, $lastId, $db) {
    $leftcount = '';
    $res = $db->field('msgid')->where("uid=$uid and msgid<$lastId and status=0")->select('usermessage');
    if ($res) {
        $leftcount = count($res);
    }
    return $leftcount;
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
function getUserMegList($uid, $page, $size, $db) {
    $res = '';
    $msgids = getMessages($uid, $page, $size, $db);
    if ($msgids) {
        $res = $db->field('id,title,msg,stime,sendid')->where("id in ($msgids)")->order('stime DESC')->select('sysmessage');
    }
    return $res ? $res : '';
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) ($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$size = isset($_POST['size']) ? (int) ($_POST['size']) : 4;
$page = isset($_POST['page']) ? (int) ($_POST['page']) : 1;

if (empty($uid) || empty($encpass)) {
    error(-993);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$size = checkInt($size);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error($s);
}
$msgData = getUserMegList($uid, $page, $size, $db);
synchroUserMessage($uid,$db);//同步消息数
if ($msgData) {
    $msglists = $list = array();
    foreach ($msgData as $v) {
        $list['msgId'] = $v['id'];
        // $list['picUrl'] = "http://" . $conf['domain-img'] . "/".;
        $list['picUrl'] = DEFAULT_PIC;
        $list['msgTitle'] = $v['title'];
//        $list['msgCont'] = strCut($v['msg'], 15);
        $list['msgCont'] = $v['msg'];
        $list['msgTime'] = strtotime($v['stime']);
        array_push($msglists, $list);
    }
    if ($msglists) {
        if ($page) {
            $pname = 'allCount';
            $count = getAllCount($uid, $db);
        }
    }
    exit(jsone(array('messageslists' => $msglists, $pname => "$count")));
} else {
    exit(jsone(array('messageslists' => array(), 'allCount' => '0')));
}
