<?php
//require('../../../../include/init.php');
include '../../../include/init.php' ;
//require(INCLUDE_DIR.'redis.class.php');
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
function gamelistByGameIds($gameIds, $db, $redisobj)
{

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
 * @param int $size 数量
 * @param object $db
 * @param object $redisobj
 * @return array
 */
function getRecommendGame($type, $db, $redisobj)
{
    $res = $db->field('gameid')->where("type=$type")->select('admin_recommend_game');
    if (false !== $res && !empty($res)) {
        return $res[0]['gameid'];
    } else {
        return array();
    }
}

/**
 * 拼接游戏列表数据
 * @param type $client 请求来源
 * @param type $size 数量
 * @param type $db
 * @param type $redisobj
 * @return array
 */
function gameList($type,$db, $redisobj)
{
    $lists = array();
    $gameid = getRecommendGame($type, $db, $redisobj);
    if ($gameid) {
        $gameids = explode(',', $gameid);
        $gamelist = gamelistByGameIds($gameid, $db, $redisobj);//以后加缓存
    }
    if ($gamelist) {
        for ($i = 0, $k = count($gameids); $i < $k; $i++) {
            $list['gameID'] = $gamelist[$gameids[$i]]['gameid'];
            $list['gameName'] = $gamelist[$gameids[$i]]['name'];
            array_push($lists, $list);
        }
    }
    return $lists;
}

/**
 * start
 */
$type = isset($_POST['type']) ? (int)$_POST['type'] : 1;  //
if (!in_array($type, array(1, 3))) {
    error2(-4013);
}

$res = gameList($type, $db, $redisobj);
if ($res) {
    succ(array('list' => $res));
} else {
    succ(array('list' => array()));
}


