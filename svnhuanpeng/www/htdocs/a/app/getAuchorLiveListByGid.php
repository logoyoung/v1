<?php

/**
 * App端 根据游戏ID获取当前正在直播的主播列表
 * date 2016-06-01 14:15
 * author yandong@6rooms.com
 */
include '../../init.php';
require_once INCLUDE_DIR . 'redis.class.php';
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
function getLiveListByGameId($gameId, $redisObj, $db) {
    $luid = array();
    $cacheKey = 'GETLIVELIST_BY_GAMEID';
    $getCatch = $redisObj->get($cacheKey); //取缓存
    if ($getCatch) {
        $luid = json_decode($getCatch, true);
    } else {
        $luid = $db->field('uid,poster')->where("gameid=$gameId and  status=" . LIVE)->order('ctime DESC')->select('live');
        if ($luid) {
            $redisObj->set($cacheKey, json_encode($luid), 60); //写缓存
        } else {
            $luid = array();
        }
    }
    return $luid;
}

/**
 * 根据游戏id获取对应的录像列表
 * @param type $gameId  游戏id
 * @param type $redisObj
 * @param type $db
 * @return array
 */
function getVideoListByGameId($gameId, $redisObj, $db) {
    $luid = array();
    $cacheKey = 'GETVIDEOLIST_BY_GAMEID';
    $getCatch = $redisObj->get($cacheKey); //取缓存
    if ($getCatch) {
        $luid = json_decode($getCatch, true);
    } else {
        $luid = $db->field('uid,videoid,poster')->where("gameid=$gameId and status=" . VIDEO)->order('ctime DESC')->select('video');
        if ($luid) {
            $redisObj->set($cacheKey, json_encode($luid), 60); //写缓存
        } else {
            $luid = array();
        }
    }
    return $luid;
}

/**
 * 获取列表
 * @param type $gameId  游戏id
 * @param type $type   类型 直播[0],录像[1]
 * @param type $redisObj
 * @param type $conf
 * @param type $db
 * @return type
 */
function getList($gameId, $type, $redisObj, $conf, $db) {
    $list = array();
    if ($type == 0) {// 直播
        $res = getLiveListByGameId($gameId, $redisObj, $db);
    }
    if ($type == 1) {//录像
        $res = getVideoListByGameId($gameId, $redisObj, $db);
    }
    if ($res) {
        foreach ($res as $v) {
            $temp['uid'] = $v['uid'];
            $temp['pic'] = $v['poster'] ? "http://" . $conf['domain-img'] . "/" . $v['poster'] : DEFAULT_PIC;
            if ($type == 1) {
                $temp['videoid'] = $v['videoid'];
            }
            array_push($list, $temp);
        }
    }
    return $list;
}

/**
 * start
 */
$gameId = isset($_POST['gameId']) ? (int) $_POST['gameId'] : '';
$type = isset($_POST['type']) ? (int) $_POST['type'] : 0;
if (empty($gameId) || !in_array($type, array(0, 1))) {
    error(-4013);
}
$gameId = checkInt($gameId);
$res = getList($gameId, $type, $redisObj, $conf, $db);
exit(json_encode(array('list' => $res)));
