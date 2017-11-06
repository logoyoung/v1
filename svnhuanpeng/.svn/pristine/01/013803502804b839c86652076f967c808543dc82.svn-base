<?php

include '../../../include/init.php';

/**
 * App端游戏分类
 * date 2016-04-13 18:00
 * author yandong@6rooms.com
 * version 0.0
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$redisobj = new RedisHelp();
$db = new DBHelperi_huanpeng();

/**
 * 获取游戏
 * @param object $db
 * @return array
 */
function getGames($db) {
    $res = $db->where('status=0')->order('ord  desc')->select('game');
    if ($res) {
        foreach ($res as $v) {
            $game[$v['gameid']] = $v;
        }
    }
    return $game ? $game : array();
}

/**
 * 获取游戏直播数
 * @param string $gameid
 * @param object $redisobj
 * @param object $db
 * @return array
 */
function getGamebyliveNumber($gameid, $redisobj, $db) {
    $res = $db->field('gameid,count(*) as number')->where("gameid in($gameid) and status=" . LIVE . " group by gameid")->select('live');
    if ($res) {
        foreach ($res as $v) {
            $gamecount[$v['gameid']] = $v['number'];
        }
    }else{
        $gamecount=array();
    }
    return $gamecount;
}

/**
 * 拼接数据
 * @param object $redisobj
 * @param object $db
 * @return array
 */
function getlists($conf, $redisobj, $db) {
    $lists = array();
    $gameres = getGames($db);
    $gameid = implode(',', array_keys($gameres));
    $result = getGamebyliveNumber($gameid, $redisobj, $db);
    foreach ($gameres as $v) {
        $list['gameID'] = $v['gameid'];
        $list['gameName'] = $v['name'];
        $list['ord'] = $v['ord'];
        $list['poster'] = $v['poster'] ? (DOMAIN_PROTOCOL . $conf['domain-img'] .$v['poster']) : '';
        $list['liveTotal'] = array_key_exists($v['gameid'], $result) ? $result[$v['gameid']] : 0;
        array_push($lists, $list);
    }
    $lists=twoKeyOrder($lists,'liveTotal',SORT_DESC,'ord',SORT_DESC);
    return $lists ? $lists : array();
}
$res = getlists($conf, $redisobj, $db);
succ(array('list'=>$res));
