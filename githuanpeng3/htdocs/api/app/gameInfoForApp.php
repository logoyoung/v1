<?php

include '../../../include/init.php';
require(INCLUDE_DIR . 'LiveRoom.class.php');

use service\live\LiveService;
use service\live\LiveListDataService;

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
 * 根据游戏id获取直播
 * @param int $gameId
 * @param int $page
 * @param int $size
 * @param object $redisobj
 * @param array $conf
 * @param object $db
 * @return array
 */
function getGameLiveLists($gameId, $page, $size, $redisobj, $conf, $db, $channel, $ischeck)
{

    $service = new LiveListDataService();
    $service->setPage($page);
    $service->setSize($size);

    if ($gameId == LiveListDataService::DOUBLE_SCREEN_ID)
    {
        $res = $service->getDoubleScreenLiveList();
    } else
    {
        $service->setLiveType(LiveListDataService::LIVE_TYPE_HOT);
        $service->setGameId($gameId);

        $res = $service->getLiveListByLiveTypeAndGameId();
    }

    if (!$res || $res['total'] <= 0)
    {
        $number = array('data' => [], 'total' => 0);
    }

    $lists = array();
    foreach ($res['list'] as $v)
    {
        $list['liveID'] = $v['liveid'];
//        $list['stream'] = sstream($v['stream']);
//        $list['server'] = $v['server'];
        $list['uid'] = $v['uid'];
        $list['gameID'] = $gameId;
        $list['gameName'] = $v['gameName'];
        $list['orientation'] = $v['orientation'];
        $list['title'] = $v['title'];
        $list['stime'] = $v['stime'];
        $list['poster'] = $v['poster'];
        
        if (LiveService::slaveIsLiving($v['uid']) == LiveService::PLAY_TYPE_02)
        {
            $list['subPoster'] = $v['subPoster'];
        } else
        {
            $list['subPoster'] = '';
        }

        $list['nick'] = $v['nick'];
        $list['head'] = $v['head'];
        $list['viewCount'] = $v['viewCount'];
        $list['userCount'] = $v['fansCount'];
        array_push($lists, $list);
    }

    $number = array('data' => $lists, 'total' => $res['total']);

    return $number;
}

/**
 * 根据gameid获取录像数
 * @param int $gameId
 * @param object $redisobj
 * @param object $db
 * @return array
 */
function getVideoListsByGameId($gameId, $redisobj, $db, $channel, $ischeck)
{
    $result = '';
    //      $cacheKey = "HuanPengApp_VideoGameBy$gameId";
//      $result = $redobj->get($cacheKey);
    if (empty($result))
    {
        $result = $db->field('videoid,uid,gameid,gamename,title,ctime,poster,viewcount,vfile,orientation')->where(" gameid=$gameId and status=" . VIDEO)->select('video');
//        foreach ($res as $v) {
//            $result[$v['uid']] = $v;
//        }
        // $redobj->set($cacheKey, $result, $keytime);//写缓存
    }
    if (false !== $result)
    {
        if ($channel == 8004)
        {
            if ($ischeck == 1)
            {
                $result = $db->field('videoid,uid,gameid,gamename,title,ctime,poster,viewcount,vfile,orientation')->where("status=" . VIDEO . "  order by RAND()")->limit(1)->select('video');
            }
        }
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
function getGameVideoLists($gameId, $page, $size, $redisobj, $conf, $db, $channel, $ischeck)
{
    $videolist = getVideoListsByGameId($gameId, $redisobj, $db, $channel, $ischeck); //以后加缓存
    if ($videolist)
    {
        $lists = array();
        $luids = array_unique(array_column($videolist, 'uid'));
        $anthorInfo = getUserInfo($luids, $db);
        $liveUser = batchGetLiveRoomUserCount(implode(',', $luids), $db);
        foreach ($videolist as $v)
        {
            $list['videoID'] = $v['videoid'];
            $list['uid'] = $v['uid'];
            $list['gameID'] = $v['gameid'];
            $list['gameName'] = $v['gamename'];
            $list['title'] = $v['title'];
            $list['orientation'] = $v['orientation'];
            $list['stime'] = strtotime($v['ctime']);
            $list['poster'] = sposter($v['poster']);
            $list['nick'] = array_key_exists($v['uid'], $anthorInfo) ? $anthorInfo[$v['uid']]['nick'] : '';
            $list['head'] = (array_key_exists($v['uid'], $anthorInfo) && !empty($anthorInfo[$v['uid']]['pic'])) ? DOMAIN_PROTOCOL . $conf['domain-img'] . '/' . $anthorInfo[$v['uid']]['pic'] : DEFAULT_PIC;
            $list['viewCount'] = $v['viewcount'] ? $v['viewcount'] : 0;
            $list['userCount'] = $v['viewcount'] ? $v['viewcount'] : 0;
            $list['videoUrl'] = sfile($v['vfile']);
            array_push($lists, $list);
        }
        $lists = dyadicArray($lists, 'viewCount');
        $page = returnPage(count($lists), $size, $page);
        $offect = ($page - 1) * $size;
        $aftercut = array_slice($lists, $offect, $size); //以后加缓存
        $number = array('data' => $aftercut, 'total' => count($lists));
    } else
    {
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
function getGameInfoListBygGameId($gameId, $type, $page, $size, $redisobj, $conf, $db, $channel, $ischeck)
{
    //直播
    if ($type == 1)
    {
        $res = getGameLiveLists($gameId, $page, $size, $redisobj, $conf, $db, $channel, $ischeck);
    }
    //录像
    if ($type == 2)
    {
        $res = getGameVideoLists($gameId, $page, $size, $redisobj, $conf, $db, $channel, $ischeck);
    }
    return $res;
}

/**
 * start
 */
$gameId = isset($_POST['gameID']) ? (int) ($_POST['gameID']) : '';
$type = isset($_POST['type']) ? (int) ($_POST['type']) : 1;
$page = isset($_POST['page']) ? (int) ($_POST['page']) : 1;
$size = isset($_POST['size']) ? (int) ($_POST['size']) : 8;
$channel = isset($_POST['channel']) ? (int) ($_POST['channel']) : '';
$ischeck = isset($_POST['ischeck']) ? (int) ($_POST['ischeck']) : '';
if (empty($gameId) || !in_array($type, array(1, 2)))
{
    error2(-4013);
}
$res = getGameInfoListBygGameId($gameId, $type, $page, $size, $redisobj, $conf, $db, $channel, $ischeck);
if ($res)
{
    if ($type == 1)
    {
        succ(array("liveList" => $res['data'], 'videoList' => array(), 'total' => $res['total']));
    }
    if ($type == 2)
    {
        succ(array("liveList" => array(), 'videoList' => $res['data'], 'total' => $res['total']));
    }
} else
{
    succ(array("liveList" => array(), 'videoList' => array(), 'total' => 0));
}

