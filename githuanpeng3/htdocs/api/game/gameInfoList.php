<?php

include '../../../include/init.php';
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
function gamelistByGameIds($gameIds, $db, $redisobj)
{
    $gamelist = array();
    if (!empty($gameIds)) {
        $res = $db->where("gameid in ($gameIds)  and status=0")->select('game');
    } else {
        $res = $db->where('status=0')->order('ord desc')->select('game');
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
function getAllgameNum($db)
{
    $total = $db->field('count(gameid) as total ')->where('status=0')->select('game');
    return $total[0]['total'];
}

function getRecommentGame($db)
{
    $res = $db->field('gameid')->where('type=2')->select('admin_recommend_game');
    if (false !==$res && !empty($res)) {
        return $res[0]['gameid'];
    } else {
        return array();
    }
}

/**
 * 获取关注数
 * @param int $gameid
 * @param object $db
 * @return array
 */
function getGameFollow($gameid, $db)
{
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
function getVideoCountByGameId($gameid, $db)
{
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
function getLiveCountByGameId($gameid, $db)
{
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
function getGameList($gameIds, $db, $redisobj, $conf, $type, $base)
{
    $gameLists = $gameList = array();
    if (empty($type)) {
        $result = gamelistByGameIds($gameIds, $db, $redisobj);
    } else {
        $game = getRecommentGame($db);
        if ($game) {
            $games = explode(',', $game);
            $result = gamelistByGameIds($game, $db, $redisobj);
        } else {
            $games = array();
            $result = array();
        }

    }

    if ($gameIds) {
        $newids = explode(',', $gameIds);
        $gameid = $gameIds;
    } else {
        if ($type) {
            $newids = $games;
            $gameid = implode(',', $games);
        } else {
            $newids = array_keys($result);
            $gameid = implode(',', $newids);
        }

    }

    $lcount = getLiveCountByGameId($gameid, $db);
    if ($base) {
        $follow = getGameFollow($gameid, $db);
        $vcount = getVideoCountByGameId($gameid, $db);
    }
    if ($result) {
        for ($i = 0, $j = count($newids); $i < $j; $i++) {
            $gameList['gameID'] = $result[$newids[$i]]['gameid'];
            $gameList['gameName'] = $result[$newids[$i]]['name'];
            $gameList['ord'] = $result[$newids[$i]]['ord'];
            $gameList['poster'] = $result[$newids[$i]]['poster'] ? DOMAIN_PROTOCOL . $conf['domain-img'] . $result[$newids[$i]]['poster'] : '';
            $gameList['liveTotal'] = array_key_exists($newids[$i], $lcount) ? $lcount[$newids[$i]] : 0;
            if ($base) {
                $gameList['gameTypeID'] = $result[$newids[$i]]['gametid'];
                $gameList['gamepic'] = $result[$newids[$i]]['gamepic'] ? DOMAIN_PROTOCOL . $conf['domain-img'] .$result[$newids[$i]]['gamepic'] : '';
                $gameList['bgpic'] = $result[$newids[$i]]['bgpic'] ? DOMAIN_PROTOCOL . $conf['domain-img'] . $result[$newids[$i]]['bgpic'] : '';
                $gameList['description'] = $result[$newids[$i]]['description'] ? $result[$newids[$i]]['description'] : '';
                $gameList['fansCount'] = array_key_exists($newids[$i], $follow) ? $follow[$newids[$i]] : 0;
                $gameList['videoTotal'] = array_key_exists($newids[$i], $vcount) ? $vcount[$newids[$i]] : 0;
            }
            array_push($gameLists, $gameList);
        }
    }
    return $gameLists;
}

/**
 * start
 */
$gameIds = isset($_POST['gameID']) ? trim($_POST['gameID']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '0';
$base = isset($_POST['base']) ? trim($_POST['base']) : '0';
if (!empty($gameIds)) {
    $gameIds = rtrim($gameIds, ',');
}
$res = getGameList($gameIds, $db, $redisobj, $conf, $type, $base);
if ($res) {
    if($type){
        $total = getAllgameNum($db);
    }else{
        $total = count($res);
    }
//    $res=dyadicArray($res, 'liveTotal',SORT_DESC);
    $res=twoKeyOrder($res,'liveTotal',SORT_DESC,'ord',SORT_DESC);
    succ(array('list' => $res, 'total' => $total));
} else {
    succ(array('list' => array(), 'total' => '0'));
}


