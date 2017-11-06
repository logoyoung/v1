<?php

include '../init.php';
require_once(INCLUDE_DIR . 'redis.class.php');
/*
 * 获取其他游戏列表
 * date 2016-02-03 15:57 
 * author yandong@6rooms.com
 * version 0.0
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$redisobj = new RedisHelp();
$db = new DBHelperi_huanpeng();

/**
 * 根据游戏id获取游戏列表
 * @param string $gameIds
 * @param object $db
 * @param object $redisobj
 * @return array
 */
function gamelistByGameIds($gameIds, $db, $redisobj) {
    $gamelist = array();
    if (!empty($gameIds)) {
        $res = $db->field('gameid,gametid,name,poster')->where("gameid in ($gameIds)  and status=0")->select('game');
    } else {
        $res = $db->field('gameid,gametid,name,poster')->where('status=0')->order('ord desc')->select('game');
    }
    if ($res) {
        foreach ($res as $v) {
            $gamelist[$v['gameid']] = $v;
        }
    }
    return $gamelist;
}

/**
 * 获取游戏总数
 * @param object $db
 * @return string
 */
function getAllgameNum($db) {
    $total = $db->field('count(gameid) as total ')->where('status=0')->select('game');
    return $total[0]['total'];
}

/**
 * 获取关注数
 * @param int $gameid
 * @param object $db
 * @return array
 */
function getGameFollow($gameid, $db) {
    $followList = array();
    $res = $db->field('gameid,count(uid) as follow')->where("gameid in ($gameid) group by gameid")->select('gamefollow');
    if ($res) {
        foreach ($res as $v) {
            $followList[$v['gameid']] = $v['follow'];
        }
    }
    return $followList;
}

/**
 * 获取录像数
 * @param int $gameid
 * @param object $db
 * @return array
 */
function getVideoCountByGameId($gameid, $db) {
    $videoCount = array();
    $res = $db->field('gameid,count(videoid) as vcount')->where("gameid in ($gameid) and status=" . VIDEO . " group by gameid")->select('video');
    if ($res) {
        foreach ($res as $v) {
            $videoCount[$v['gameid']] = $v['vcount'];
        }
    }
    return $videoCount;
}

/**
 * 获取直播数
 * @param int $gameid
 * @param object $db
 * @return array
 */
function getLiveCountByGameId($gameid, $db) {
    $liveCount = array();
    $res = $db->field('gameid,count(liveid) as lcount')->where("gameid in ($gameid) and status=" . LIVE . " group by gameid")->select('live');
    if ($res) {
        foreach ($res as $v) {
            $liveCount[$v['gameid']] = $v['lcount'];
        }
    }
    return $liveCount;
}

/**
 * 获取游戏列表
 * @param string $gameIds
 * @param object $db
 * @param object $redisobj
 * @return array
 */
function getGameList($gameIds, $db, $redisobj, $conf) {
    $gameLists = $gameList = array();
    $result = gamelistByGameIds($gameIds, $db, $redisobj);
    if ($gameIds) {
        $newids = explode(',', $gameIds);
        $gameid = $gameIds;
    } else {
        $newids = array_keys($result);
        $gameid = implode(',', $newids);
    }
    $follow = getGameFollow($gameid, $db);
    $vcount = getVideoCountByGameId($gameid, $db);
    $lcount = getLiveCountByGameId($gameid, $db);
    $game_zone=getInfoFromGameZone($gameid, $db);
    if ($result) {
        for ($i = 0, $j = count($newids); $i < $j; $i++) {
            $gameList['gameID'] = $result[$newids[$i]]['gameid'];
            $gameList['gameName'] = $result[$newids[$i]]['name'];
            $gameList['gameTypeID'] = $result[$newids[$i]]['gametid'];
            $gameList['posterURL'] = array_key_exists($newids[$i], $game_zone) ? 'http://' . $conf['domain-img'] . $game_zone[$newids[$i]]['poster'] : '';
            $gameList['gamepic'] = array_key_exists($newids[$i], $game_zone) ? 'http://' . $conf['domain-img'] . $game_zone[$newids[$i]]['gamepic'] : '';
            $gameList['bgpic'] = array_key_exists($newids[$i], $game_zone) ? 'http://' . $conf['domain-img'] . $game_zone[$newids[$i]]['bgpic'] : '';
            $gameList['description'] = array_key_exists($newids[$i], $game_zone) ? $game_zone[$newids[$i]]['description'] : '';
            $gameList['followCount'] = array_key_exists($newids[$i], $follow) ? $follow[$newids[$i]] : 0;
            $gameList['videoCount'] = array_key_exists($newids[$i], $vcount) ? $vcount[$newids[$i]] : 0;
            $gameList['liveCount'] = array_key_exists($newids[$i], $lcount) ? $lcount[$newids[$i]] : 0;
            array_push($gameLists, $gameList);
        }
    }
    return $gameLists;
}

/**
 * start
 */
$gameIds = isset($_POST['gameIds']) ? trim($_POST['gameIds']) : '';
if (!empty($gameIds)) {
    $gameIds = rtrim($gameIds, ',');
}
$res = getGameList($gameIds, $db, $redisobj, $conf);
if ($res) {
    $gtotal = getAllgameNum($db);
    exit(jsone(array('gameList' => $res, 'allCount' => $gtotal)));
} else {
    exit(jsone(array('gameList' => '', 'allCount' => '')));
}


