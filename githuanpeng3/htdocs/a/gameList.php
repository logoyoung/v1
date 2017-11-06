<?php
include '../init.php';
require_once(INCLUDE_DIR . 'redis.class.php');
/*
 * 游戏列表
 * date 2016-04-28 11:00 
 * author yandong@6rooms.com
 * version 0.0
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$redisobj = new RedisHelp();
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
        $res = $db->field('gameid,gametid,name,poster')->where("gameid in ($gameIds)")->select('game');//以后加缓存
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
function getRecommendGame($client, $size, $db, $redisobj) {
    $gameids = '';
    $res = $db->field('gameorderlist')->where("client=$client")->select('recommentgame');
    if ($res) {
        $re=explode(',', $res[0]['gameorderlist']);
        $after = array_slice(explode(',', $res[0]['gameorderlist']), 0, $size);
        if ($after) {
            $gameids = $after;
        }
    }
    return $gameids;
}
/**
 * 拼接游戏列表数据
 * @param type $client 请求来源
 * @param type $size  数量
 * @param type $db
 * @param type $redisobj
 * @return array
 */
function gameList($client, $size, $db, $redisobj) {
    $lists = array();
    $gameid = getRecommendGame($client, $size, $db, $redisobj);
    if ($gameid) {
        $gameids=  implode(',', $gameid);
        $gamelist = gamelistByGameIds($gameids, $db, $redisobj);//以后加缓存
    }
    if ($gamelist) {
         for($i=0,$k=count($gameid);$i<$k;$i++){
            $list['gameid'] = $gamelist[$gameid[$i]]['gameid'];
            $list['gamename'] = $gamelist[$gameid[$i]]['name'];
            array_push($lists, $list);
        }
    }
    return $lists;
}
/**
 * start
 */
$client = isset($_POST['client']) ? (int) $_POST['client'] : 2;
$size = isset($_POST['size']) ? (int) $_POST['size'] : 12;
$client=checkInt($client);
$size=checkInt($size);
if(!in_array($client, array(1,2,3))){
    error(-4013);
}
$res = gameList($client, $size, $db, $redisobj); 
if ($res) {
    exit(jsone(array('gameList' => $res)));
} else {
    exit(jsone(array('gameList' => '')));
}


