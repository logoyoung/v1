<?php

include '../../../include/init.php';
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
        $res = $db->field('gameid,gametid,name,poster,direction,scheme')->where("gameid in ($gameIds) and status=0")->select('game'); //以后加缓存
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
function getRecommendGame($db) {
    $res = $db->field('gameid')->where("type=1")->select('admin_recommend_game');
    if (false !==$res && !empty($res)) {
        $gameids=explode(',',$res[0]['gameid']);
        return $gameids;
    }else{
        return array();
    }
}

function makeDate($list) {
    $lists=array();
    $key=  array_keys($list);
    for ($i = 0, $k = count($key); $i < $k; $i++) {
        $temp['gameId'] = $list[$key[$i]]['gameid'];
        $temp['gameName'] = $list[$key[$i]]['name'];
        $temp['direction'] = $list[$key[$i]]['direction'];
		$temp['scheme'] = $list[$key[$i]]['scheme'];
        array_push($lists, $temp);
    }
    return $lists ? $lists : array();
}



function gameGameLists($db, $redisobj) {
    $res = $db->field('gameid,gametid,name,poster,direction,scheme')->where('status=0')->select('game'); //以后加缓存
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
    $recommend = getRecommendGame($db);
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
succ($res, true);


