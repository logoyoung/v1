<?php

include '../../../../include/init.php';
/**
 * 我的消息列表
 * author yandong@6rooms.com
 * date 2016-01-27 15:40
 * copyright@6.cn version 0.0
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();
use service\event\EventManager;

/**
 * 获取用户消息列表
 * @param int $uid
 * @param object $db
 * @return string
 */
function getMessages($uid, $page, $size, $db)
{
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
    return array('mid' => $msgid, 'total' => $tcount);
}

/**
 * 获取总条数
 * @param type $uid
 * @param type $db
 * @return type
 */
function getAllCount($uid, $db)
{
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
function getleftCount($uid, $lastId, $db)
{
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
function getUserMegList($uid, $page, $size, $db)
{
    $msgids = getMessages($uid, $page, $size, $db);
    if ($msgids['mid']) {
        $ids = $msgids['mid'];
        $res = $db->field('id,title,msg,stime,sendid')->where("id in ($ids)")->order('id DESC')->select('sysmessage');
        if (false !== $res) {
            return array('res' => $res, 'total' => $msgids['total']);
        } else {
            return array('res' => array(), 'total' => 0);
        }

    } else {
        return array('res' => array(), 'total' => 0);
    }
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$size = isset($_POST['size']) ? (int)($_POST['size']) : 4;
$page = isset($_POST['page']) ? (int)($_POST['page']) : 1;

if (empty($uid) || empty($encpass)) {
    error2(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$size = checkInt($size);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error2(-4067,2);
}
$msgData = getUserMegList($uid, $page, $size, $db);
//同步消息数
if(synchroUserMessage($uid, $db)){
    $event = new EventManager();
    $event->trigger(EventManager::ACTION_USER_MSG_UPDATE,[ 'uid' => $uid ]);
    $event = null;
}

if ($msgData['res']) {
    $msglists = $list = array();
    foreach ($msgData['res'] as $v) {
        $list['msgID'] = $v['id'];
        // $list['picUrl'] = "http://" . $conf['domain-img'] . "/".;
        $list['head'] = DEFAULT_SYSTEM_PIC;
        $list['title'] = $v['title'];
//        $list['msgCont'] = strCut($v['msg'], 15);
        $list['comment'] = $v['msg'];
        $list['ctime'] = strtotime($v['stime']);
        $list['overTime'] = time() - (strtotime($v['stime']));
        array_push($msglists, $list);
    }
    succ(array('list' => $msglists, 'total' => $msgData['total'], 'page' => $page, 'pageTotal' => ceil($msgData['total'] / $size)));
} else {
    succ(array('list' => array(), 'total' => 0, 'page' => $page, 'pageTotal' => 0));

}
