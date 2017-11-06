<?php

include '../init.php';
require_once(INCLUDE_DIR . 'redis.class.php');
/*
 * App游戏分类->游戏详情
 * date 2016-04-14 10:30 
 * author yandong@6rooms.com
 * version 0.0
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$redisobj = new RedisHelp();
$db = new DBHelperi_huanpeng();

/**
 * 根据游戏Id获取当前正在直播中的该游戏
 * @param int $gameId
 * @param object $redisobj
 * @param object $db
 * @return array
 */
function getLiveListsByGameId($gameId, $redisobj, $db) {
//      $cacheKey = "HuanPengApp_LiveGameBy$gameId";
//      $result = $redobj->get($cacheKey);
    $result = '';
    if (empty($result)) {
        $res = $db->field('liveid,stream,server,uid,gameid,gamename,title,ctime,poster,orientation')->where(" gameid=$gameId and status=" . LIVE)->select('live');
        foreach ($res as $v) {
            $result[$v['uid']] = $v;
        }
        // $redobj->set($cacheKey, $result, $keytime);//写缓存
    }
    return $result;
}

/**
 * 根据游戏id获取直播
 * @param int $gameId
 * @param int $page
 * @param int $size
 * @param object $redisobj
 * @param array $conf
 * @param object $db
 * @return array
 */
function getGameLiveLists($gameId, $page, $size, $redisobj, $conf, $db) {
//    $cacheKey = "HuanPengApp_LiveGameBy" . $gameId . $page . $size; //分片缓存
//      $Livelist = $redobj->get($cacheKey);
    $Livelist = getLiveListsByGameId($gameId, $redisobj, $db); //以后加缓存 
    if ($Livelist) {
        $lists = array();
        $luids = array_keys($Livelist);
        $anthorInfo = getUserInfo($luids, $db);
        $liveUser = batchGetLiveRoomUserCount(implode(',', $luids), $db);
        foreach ($Livelist as $v) {
            $list['liveId'] = $v['liveid'];
            $list['stream'] = $v['stream'];
            $list['server'] = $v['server'];
            $list['uid'] = $v['uid'];
            $list['gameId'] = $v['gameid'];
            $list['gameName'] = $v['gamename'];
            $list['angle'] = $v['orientation'];
            $list['title'] = $v['title'];
            $list['ctime'] = strtotime($v['ctime']);
            $list['pic'] = $v['poster'] ? ("http://" . $conf['domain-img'] . '/' . $v['poster']) : '';
            $list['nick'] = array_key_exists($v['uid'], $anthorInfo) ? $anthorInfo[$v['uid']]['nick'] : '';
            $list['userpic'] = (array_key_exists($v['uid'], $anthorInfo) && !empty($anthorInfo[$v['uid']]['pic'])) ? "http://" . $conf['domain-img'] . '/' . $anthorInfo[$v['uid']]['pic'] : DEFAULT_PIC;
            $list['viewCount'] = array_key_exists($v['uid'], $liveUser) ? $liveUser[$v['uid']] : 0;
            array_push($lists, $list);
        }
        $lists = dyadicArray($lists, 'viewCount');
        $page = returnPage(count($lists), $size, $page);
        $offect = ($page - 1) * $size;
        $aftercut = array_slice($lists, $offect, $size); //以后加缓存
        $number = array('data' => $aftercut, 'total' => count($lists));
    } else {
        $number = array('data' => array(), 'total' => 0);
    }
    return $number;
}

/**
 * 根据gameid获取录像数
 * @param int $gameId
 * @param object $redisobj
 * @param object $db
 * @return array
 */
function getVideoListsByGameId($gameId, $redisobj, $db) {
    $result = '';
    //      $cacheKey = "HuanPengApp_VideoGameBy$gameId";
//      $result = $redobj->get($cacheKey);
    if (empty($result)) {
        $result = $db->field('videoid,uid,gameid,gamename,title,ctime,poster,viewcount,vfile,orientation')->where(" gameid=$gameId and status=" . VIDEO)->select('video');
//        foreach ($res as $v) {
//            $result[$v['uid']] = $v;
//        }
        // $redobj->set($cacheKey, $result, $keytime);//写缓存
    }
    return $result;
}

/**
 * 根据游戏id获取录像
 * @param int $gameId
 * @param int $page
 * @param int $size
 * @param object $redisobj
 * @param array $conf
 * @param object $db
 * @return array
 */
function getGameVideoLists($gameId, $page, $size, $redisobj, $conf, $db) {
    $videolist = getVideoListsByGameId($gameId, $redisobj, $db); //以后加缓存  
    if ($videolist) {
        $lists = array();
        $luids = array_unique(array_column($videolist, 'uid'));
        $anthorInfo = getUserInfo($luids, $db);
        $liveUser = batchGetLiveRoomUserCount(implode(',', $luids), $db);
        foreach ($videolist as $v) {
            $list['videoid'] = $v['videoid'];
            $list['uid'] = $v['uid'];
            $list['gameId'] = $v['gameid'];
            $list['gameName'] = $v['gamename'];
            $list['title'] = $v['title'];
            $list['angle'] = $v['orientation'];
            $list['ctime'] = strtotime($v['ctime']);
            $list['pic'] = $v['poster'] ? ("http://" . $conf['domain-img'] . $v['poster']) : '';
            $list['nick'] = array_key_exists($v['uid'], $anthorInfo) ? $anthorInfo[$v['uid']]['nick'] : '';
            $list['userpic'] = (array_key_exists($v['uid'], $anthorInfo) && !empty($anthorInfo[$v['uid']]['pic'])) ? "http://" . $conf['domain-img'] . '/' . $anthorInfo[$v['uid']]['pic'] : DEFAULT_PIC;
            $list['viewCount'] = $v['viewcount'] ? $v['viewcount'] : 0;
            $list['vfile'] = $v['vfile'] ? ($conf['domain-video'] . $v['vfile']) : '';
            array_push($lists, $list);
        }
        $lists = dyadicArray($lists, 'viewCount');
        $page = returnPage(count($lists), $size, $page);
        $offect = ($page - 1) * $size;
        $aftercut = array_slice($lists, $offect, $size); //以后加缓存
        $number = array('data' => $aftercut, 'total' => count($lists));
    } else {
        $number = array('data' => array(), 'total' => 0);
    }
    return $number;
}

/**
 * 获取游戏列表信息
 * @param int $gameId
 * @param int $type
 * @param int $page
 * @param int $size
 * @param object $redisobj
 * @param array $conf
 * @param object $db
 * @return array
 */
function getGameInfoListBygGameId($gameId, $type, $page, $size, $redisobj, $conf, $db) {
    //直播
    if ($type == 1) {
        $res = getGameLiveLists($gameId, $page, $size, $redisobj, $conf, $db);
    }
    //录像
    if ($type == 2) {
        $res = getGameVideoLists($gameId, $page, $size, $redisobj, $conf, $db);
    }
    return $res;
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$gameId = isset($_POST['gameId']) ? (int) ($_POST['gameId']) : '2';
$type = isset($_POST['type']) ? (int) ($_POST['type']) : 1;
$page = isset($_POST['page']) ? (int) ($_POST['page']) : 1;
$size = isset($_POST['size']) ? (int) ($_POST['size']) : 8;
if (empty($gameId) || !in_array($type, array(1, 2))) {
    error(-993);
}

$res = getGameInfoListBygGameId($gameId, $type, $page, $size, $redisobj, $conf, $db);
if ($res) {
    if ($type == 1) {
        exit(jsone(array("liveList" => $res['data'], 'videoList' => array(), 'allCount' => $res['total'])));
    }
    if ($type == 2) {
        exit(jsone(array("livelist" => array(), 'videoList' => $res['data'], 'allCount' => $res['total'])));
    }
} else {
    exit(jsone(array("livelist" => array(), 'videoList' => array(), 'allCount' => 0)));
}

