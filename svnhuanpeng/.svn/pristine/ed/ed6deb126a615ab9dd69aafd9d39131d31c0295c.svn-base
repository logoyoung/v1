<?php

include '../../init.php';
require_once INCLUDE_DIR . 'redis.class.php';
/*
 * 游戏列表
 * date 2016-04-28 11:00 
 * author yandong@6rooms.com
 * version 0.0
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$redisobj =new RedisHelp();
$db = new DBHelperi_huanpeng();

/**
 * 获取游戏详情
 * @param string $gameIds 游戏ids
 * @param object $db
 * @param object $redisobj
 * @return array
 */
function gamelistByGameIds($gameIds, $db, $redisobj) {

    if (!empty($gameIds)) {
        $res = $db->field('gameid,gametid,name,poster,direction')->where("gameid in ($gameIds) and status=0")->select('game'); //以后加缓存
    }
    if ($res) {
        foreach ($res as $v) {
            $gamelist[$v['gameid']] = $v;
        }
    }
    return $gamelist;
}

/**
 * 获取推荐的游戏ids
 * @param int $client 请求来源
 * @param int $size  数量
 * @param object $db  
 * @param object $redisobj
 * @return array
 */
function getRecommendGame($size, $db, $redisobj) {
    $gameids = array();
    $res = $db->field('gameorderlist')->where("client=1")->select('recommentgame');
    if ($res) {
        $re = explode(',', $res[0]['gameorderlist']);
        $after = array_slice(explode(',', $res[0]['gameorderlist']), 0, $size);
        if ($after) {
            $gameids = $after;
        }
    }
    return $gameids;
}

function makeDate($list) {
    $lists=array();
    $key=  array_keys($list);
    for ($i = 0, $k = count($key); $i < $k; $i++) {
        $temp['gameid'] = $list[$key[$i]]['gameid'];
        $temp['gamename'] = $list[$key[$i]]['name'];
        $temp['direction'] = $list[$key[$i]]['direction']; 
        array_push($lists, $temp);
    }
    return $lists ? $lists : array();
}



function gameGameLists($db, $redisobj) {
    $res = $db->field('gameid,gametid,name,poster,direction')->where('status=0')->select('game'); //以后加缓存
    if ($res) {
        foreach ($res as $v) {
            $gamelist[$v['gameid']] = $v;
        }
    }
    return $gamelist;
}
/**
 * 拼接游戏列表数据
 * @param type $client 请求来源
 * @param type $size  数量
 * @param type $db
 * @param type $redisobj
 * @return array
 */
//getInfoFromGameZone($gameIds, $db);
function gameList($size, $db, $redisobj) {
    $recommend = getRecommendGame($size, $db, $redisobj);
    $lists = gameGameLists($db, $redisobj);
    for ($i = 0, $k = count($recommend); $i < $k; $i++) {
        $hotlist[$recommend[$i]] = $lists[$recommend[$i]];
    }
    @$otherlist = array_diff_assoc($lists, $hotlist);
    $hlist=makeDate($hotlist);
    $olist=makeDate($otherlist);
    return array('hotlist'=>$hlist,'otherlist'=>$olist);
}

$size = isset($_POST['size']) ? (int) $_POST['size'] : 12;
$size = checkInt($size);
$res = gameList($size, $db, $redisobj);
if ($res) {
    exit(jsone($res));
} else {
    exit(jsone(array()));
}

