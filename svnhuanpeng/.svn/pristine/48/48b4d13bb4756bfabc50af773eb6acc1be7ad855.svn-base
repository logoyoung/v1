<?php

include '../init.php';
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
/**
 * 获取视频收藏列表接口
 * 根据输入的用户ID，查看用户收藏的视频列表，成功返回视频列表，失败返回错误信息
 * @auth hantong<hantong@6rooms.com>
 * revise date 2015-12-30  16:24  yandong@6rooms.com
 * @version 0.1
 */
$db = new DBHelperi_huanpeng();

/**
 * 获取录像收藏数
 * @param int $videoID
 * @param $db
 * @return mixed
 */
function getCollectedCounts($videoID, $db) {
    $rows = $db->field('COUNT(*) AS collectCount')->where('videoid=' . $videoID . '')->select('videofollow');
    return $rows[0]['collectCount'];
}

/**
 * 获取录像平均分
 * @param int $videoID
 * @param object $db
 * @return string
 */
function getViewerRates($videoID, $db) {
    $rate = $db->field('avg(rate) as avgRate')->where('videoid=' . $videoID . '')->select('videocomment');
    return round($rate[0]['avgRate'], 2);
}

/**
 * 获取录像评论数
 * @param int $videoID
 * @param object $db
 * @return string
 */
function getCommentCounts($videoID, $db) {
    $total = $db->field('count(comment) as tcomment')->where('videoid=' . $videoID . '')->select('videocomment');
    return $total[0]['tcomment'];
}

/**
 * 获取最新收藏的视频时间
 * @param int $uid
 * @param object $db
 * @return array
 */
function getLastCollectVideoId($uid, $db) {
    $result = $db->field('tm')->where("uid=$uid")->order('tm DESC')->limit(1)->select('videofollow');
    return $result[0]['tm'];
}

/**
 * 获取用户收藏的视频ID
 * @param type $uid
 * @param type $db
 * @return type
 */
function getCollectVideoId($uid, $page, $size, $db) {
    $collectvid = array();
    $countVideo = $db->field('count(*) as videoid')->where('uid=' . $uid . '')->select('videofollow');
    $count = $countVideo[0]['videoid'] != 0 ? $countVideo[0]['videoid'] : 1;
    $page = returnPage($count, $size, $page);
    $result = $db->field('videoid')->where('uid=' . $uid . '')->order('tm DESC')->limit($page, $size)->select('videofollow');
    foreach ($result as $v) {
        array_push($collectvid, $v['videoid']);
    }
    return $collectvid;
}

/**
 * 获取用户总的收藏视频数
 * @param type $uid
 * @param type $db
 * @return type
 */
function userCollectVideoNum($uid, $db) {
    $number = $db->field('count(videoid) as totalNum')->where('uid=' . $uid . '')->select('videofollow');
    return $number[0]['totalNum'];
}

/**
 * 获取录像信息列表
 * @param int $collectVids
 * @param int $page
 * @param int $size
 * @param object $db
 * @return array
 */
function getVideoList($collectVids, $db) {
    $videoIds = implode(',', $collectVids);
    $res = $db->field('videoid,gameid,gametid,gamename,viewcount,uid,title,ctime,length,poster,vfile,orientation')
                    ->where('videoid in (' . $videoIds . ')')->select('video');
    if ($res) {
        foreach ($res as $v) {
            $result[$v['videoid']] = $v;
        }
    } else {
        $result = array();
    }
    return $result;
}

/**
 * 获取收藏视频列表
 * @param int $uid
 * @param int $lastID
 * @param int $size
 * @param int $page
 * @param object $db
 * @return array
 */
function getCollectList($uid, $page, $size, $db) {
    $sortList = array();
    $collectVids = getCollectVideoId($uid, $page, $size, $db); //获取收藏的vid
    if ($collectVids) {
        $res = getVideoList($collectVids, $db);
    } else {
        $res = array();
    }
    if ($res) {
        for ($a = 0, $b = count($collectVids); $a < $b; $a++) {
            if (array_key_exists($collectVids[$a], $res)) {
                array_push($sortList, $res[$collectVids[$a]]);
            }
        }
    }
    return $sortList;
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$size = isset($_POST['size']) ? (int) $_POST['size'] : 9;
$page = isset($_POST['page']) ? (int) $_POST['page'] : 1;

if (!$uid || !$encpass) {
    error('-1015');
}
//检查用户登陆状态
$userState = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $userState) {
    error($userState);
}

$collectList = $collectListes = array();
$result = getCollectList($uid, $page, $size, $db);
if ($result) {
    foreach ($result as $v) {
        $collectList['videoID'] = $v['videoid'];
        $collectList['gameName'] = $v['gamename'];
        $collectList['totalViewCount'] = $v['viewcount'];
        $collectList['publisherUserID'] = $v['uid'];
        $userInfo = getUserInfo($v['uid'], $db);
        $collectList['publisherNickName'] = $userInfo[0]['nick'];
        $collectList['videoTitle'] = $v['title'];
        $collectList['angle'] = $v['orientation'];
        if ($v['poster']) {
            $collectList['posterURL'] = "http://" . $conf['domain-img'] . '/' . $v['poster'];
            $collectList['ispic'] = '1';
        } else {
            $collectList['posterURL'] = CROSS;
            $collectList['ispic'] = '0';
        }
        $collectList['collectCount'] = getCollectedCounts($v['videoid'], $db);
        $collectList['videoPlaybackURL'] = $v['vfile'] ? ($conf['domain-video'] . $v['vfile']) : '';
        $collectList['commentCount'] = getCommentCounts($v['videoid'], $db);
        array_push($collectListes, $collectList);
    }
    if ($collectListes) {
        $collectNum = userCollectVideoNum($uid, $db);
        exit(jsone(array('collectList' => $collectListes, 'allCount' => $collectNum)));
    } else {
        exit(jsone(array('collectList' => array(), 'allCount' => 0)));
    }
} else {
    exit(jsone(array('collectList' => array(), 'allCount' => 0)));
}

