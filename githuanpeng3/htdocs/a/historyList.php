<?php

include '../init.php';
/**
 * 获取观看历史纪录列表
 * date 2015-12-14 10:20AM
 * author yandong@6rooms.com
 * version 0.0
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();

/**
 * 获取直播信息
 * @param int $luid
 * @param object $db
 * @return array
 */
function getLiveInfo($luid, $db) {
    $res = $db->field('uid, title, gamename,ctime,status,poster,orientation')->where('uid =' . $luid . '')->order('ctime desc')->limit(1)->select('live');
    return $res;
}

/**
 * 获取历史信息
 * @param int $uid
 * @param int $size
 * @param object $db
 * @return array
 */
function history($uid, $page, $size, $db) {
    $count = $db->field('count(*) as hcount')->where('uid =' . $uid . '')->select('history');
    $tcount = $count[0]['hcount'] == 0 ? 1 : $count[0]['hcount'];
    $page = returnPage($tcount, $size, $page);
    $res = $db->field('uid, luid,stime')->where('uid =' . $uid . '')->order('stime desc')->limit($page, $size)->select('history');
    return array('res' => $res, 'count' => $count[0]['hcount']);
}

$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : 0;
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$page = isset($_POST['page']) ? (int) $_POST['page'] : 1;
$size = isset($_POST['size']) ? (int) $_POST['size'] : 3;

if (empty($uid) || empty($encpass)) {
    error(-1014);
}
$s = checkUserState($uid, $encpass, $db);
if (true !== $s) {
    error(-1015);
}
$result = history($uid, $page, $size, $db);
$history = array();
if (!empty($result)) {
    foreach ($result['res'] as $key => $value) {
        $liveinfo = getLiveInfo($value['luid'], $db);
        if (!empty($liveinfo)) {
            foreach ($liveinfo as $key1 => $val1) {
                $userinfo = getUserinfo($liveinfo[0]['uid'], $db);
                $historyList['anchorUserID'] = $val1['uid'];
                $historyList['liveTitle'] = $val1['title'];
                $historyList['angle'] = $val1['orientation'];
                $historyList['gamename'] = $val1['gamename'];
                $historyList['liveStartTime'] = strtotime($val1['ctime']);
                $historyList['liveStatus'] = $val1['status'];
                $historyList['poster'] = $val1['poster'] ? "http://" . $conf['domain-img'] . '/' . $val1['poster'] : '';
                $historyList['anchorPicURL'] = !empty($userinfo[0]['pic']) ? "http://" . $conf['domain-img'] . '/' . $userinfo[0]['pic'] : DEFAULT_PIC;
                $historyList['anchorNickName'] = !empty($userinfo[0]['nick']) ? $userinfo[0]['nick'] : '';
                $historyList['scanTime'] = strtotime($value['stime']);
                $historyList['viewerCount'] = getLiveRoomUserCount($value['luid'], $db);
                array_push($history, $historyList);
            }
        }
    }
    exit(jsone(array('historyList' => $history, 'count' => $result['count'])));
} else {
    exit(jsone(array('historyList' => array(), 'count' => array())));
}


