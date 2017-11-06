<?php

/**
 * App端 根据游戏ID获取当前正在直播的主播列表
 * date 2016-06-01 14:15
 * author yandong@6rooms.com
 */
include '../../../include/init.php';

use service\live\LiveService;

$db = new DBHelperi_huanpeng();
$redisObj = new RedisHelp();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

/**
 * 根据游戏id获取对应的直播列表
 * @param type $gameId 游戏id
 * @param type $redisObj
 * @param type $db
 * @return array
 */
function getLiveListByGameId($gameId, $redisObj, $db)
{
    $luid = array();
    $cacheKey = 'GETLIVELIST_BY_GAMEID';
    $getCatch = $redisObj->get($cacheKey); //取缓存
    if ($getCatch)
    {
        $luid = json_decode($getCatch, true);
    } else
    {
        $luid = $db->field('liveid,uid,poster,stream,orientation')->where("gameid=$gameId and  status=" . LIVE)->order('liveid DESC')->select('live');
        if ($luid)
        {
            $redisObj->set($cacheKey, json_encode($luid), 60); //写缓存
        } else
        {
            $luid = array();
        }
    }
    return $luid;
}

/**
 * 根据游戏id获取对应的录像列表
 * @param type $gameId 游戏id
 * @param type $redisObj
 * @param type $db
 * @return array
 */
function getVideoListByGameId($gameId, $redisObj, $db)
{
    $luid = array();
    $cacheKey = 'GETVIDEOLIST_BY_GAMEID';
    $getCatch = $redisObj->get($cacheKey); //取缓存
    if ($getCatch)
    {
        $luid = json_decode($getCatch, true);
    } else
    {
        $luid = $db->field('uid,videoid,poster,vfile')->where("gameid=$gameId and status=" . VIDEO)->order('videoid DESC')->select('video');
        if ($luid)
        {
            $redisObj->set($cacheKey, json_encode($luid), 60); //写缓存
        } else
        {
            $luid = array();
        }
    }
    return $luid;
}

/**
 * 获取列表
 * @param type $gameId 游戏id
 * @param type $type 类型 直播[0],录像[1]
 * @param type $redisObj
 * @param type $conf
 * @param type $db
 * @return type
 */
function getList($gameId, $type, $redisObj, $conf, $db)
{
    $list = array();
    if ($type == 0)
    {// 直播
        $res = getLiveListByGameId($gameId, $redisObj, $db);
    }
    if ($type == 1)
    {//录像
        $res = getVideoListByGameId($gameId, $redisObj, $db);
    }
    if ($res)
    {
        getLiveServerList($streamServer, $notifyServer);
        foreach ($res as $v)
        {
            $temp['uid'] = $v['uid'];
            if ($type == 1)
            {
                $vposter = sposter($v['poster']);
            } else
            {
                $vposter = LiveService::getPosterUrl($v['poster']);
            }
            $temp['poster'] = $vposter;
            if ($type == 0)
            {
                $temp['liveID'] = $v['liveid'];
                $temp['streamInfo'] = array('streamList' => array($streamServer), 'orientation' => $v['orientation'], 'stream' => sstream($v['stream']));
            }
            if ($type == 1)
            {
                $temp['videoID'] = $v['videoid'];
                $temp['videoUrl'] = sfile($v['vfile']);
            }
            array_push($list, $temp);
        }
    }
    return $list;
}

/**
 * start
 */
$gameId = isset($_POST['gameID']) ? (int) $_POST['gameID'] : '';
$type = isset($_POST['type']) ? (int) $_POST['type'] : 0;
if (empty($gameId) || !in_array($type, array(0, 1)))
{
    error2(-4013);
}
$gameId = checkInt($gameId);
$res = getList($gameId, $type, $redisObj, $conf, $db);
succ(array('list' => $res));
