<?php

include '../init.php';
/**
 * 我的房间管理员列表
 * date 2016-1-21 16:14
 * author yandong@6rooms.com
 * version 0.0
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();

/**
 * 获取房管
 * @param int $uid
 * @param object $db
 * @return array
 */
function getHomeAdmin($uid, $auserId, $client, $size, $page, $db) {
    $count = $db->field('count(*) as cuid')->where("luid=$uid")->select('roommanager');
    if ($count) {
        if ($client == 1) {
            if (empty($auserId)) {
                $res = $db->field('uid')->where("luid=$uid")->order('ctime desc')->limit($size)->select('roommanager');
            } else {
                $time = getadminTimebyLuid($uid, $auserId, $db);
                $res = $db->field('uid')->where("luid=$uid and ctime<'$time'")->order('ctime desc')->limit($size)->select('roommanager');
            }
        } else {
            $page = returnPage($count[0]['cuid'], $size, $page);
            $res = $db->field('uid')->where("luid=$uid")->order('ctime desc')->limit($page, $size)->select('roommanager');
        }
        $uids = array();
        foreach ($res as $v) {
            $uids[] = $v['uid'];
        }
    } else {
        $uids = array();
    }
    return $uids;
}

/**
 * 获取添加房管时间
 * @param int $uid
 * @param int $auserId
 * @param object $db
 * @return string
 */
function getadminTimebyLuid($uid, $auserId, $db) {
    $res = $db->field('ctime')->where("luid=$uid and uid=$auserId")->select('roommanager');
    return $res ? $res[0]['ctime'] : 0;
}

/**
 * 批量获取放房管头像昵称
 * @param type $uids
 * @param type $db
 * @return array
 */
function getAdminInfos($uids, $db, $conf) {
    $list = $lists = array();
    for ($i = 0, $k = count($uids); $i < $k; $i++) {
        $res = $db->field('uid,nick,pic')->where("uid=$uids[$i]")->select('userstatic');
        if ($res) {
            $list['adminUserID'] = $res[0]['uid'];
            $list['adminNick'] = ($res[0]['nick']) ? $res[0]['nick'] : '';
            $list['adminUserPicURL'] = $res[0]['pic'] ? "http://" . $conf['domain-img'] . "/" . $res[0]['pic'] : DEFAULT_PIC;
            array_push($lists, $list);
        }
    }
    return $lists;
}

/**
 * 获取房管数量
 * @param int $uid
 * @param object $db
 * @return type
 */
function getCountAdmin($uid, $db) {
    $res = $db->field('count(uid) as total')->where("luid=$uid")->select('roommanager');
    return $res[0]['total'];
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '1512';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '30bed402fc052e90b2381b04468ca601';
$client = isset($_POST['client']) ? ($_POST['client']) : '';
$auserId = isset($_POST['auserId']) ? (int) ($_POST['auserId']) : '';
$size = isset($_POST['size']) ? (int) ($_POST['size']) : 11;
$page = isset($_POST['page']) ? (int) ($_POST['page']) : 1;
if (empty($uid) || empty($encpass)) {
    error(-993);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$size = checkInt($size);
$page = checkInt($page);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error($s);
}
$uids = getHomeAdmin($uid, $auserId, $client, $size, $page, $db);
if ($uids) {
    $roomAdminList = getAdminInfos($uids, $db, $conf);
    $count = getCountAdmin($uid, $db);
} else {
    $roomAdminList = '';
    $count = 0;
}
if ($roomAdminList) {
    exit(jsone(array('roomAdminList' => $roomAdminList, 'adminerCount' => $count)));
} else {
    exit(jsone(array('roomAdminList' => '', 'adminerCount' => '')));
}
