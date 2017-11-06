<?php

include '../init.php';
require_once INCLUDE_DIR . 'redis.class.php';
/*
 * 获取其他游戏列表
 * date 2015-12-29 14:50 
 * author yandong@6rooms.com
 * version 0.0
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$redisobj = new RedisHelp();
$db = new DBHelperi_huanpeng();

function showGame($db) {
    $res = $db->field('gameid')->order("ctime ASC")->select('index_recommend_game');
    if ($res) {
        return array_column($res, 'gameid');
    } else {
        return array();
    }
}

/**
 * 获取首页游戏列表
 * @param int $uid
 * @param string $encpass
 * @param int $gameId
 * @param object $db
 * @return array
 */
function getLiveLists($gameId, $db) {
    $live = array();
    if ($gameId == -1) {
        $showid = showGame($db);
        if ($showid) {
            $gameId = implode(',', $showid);
        }
    }
    if ($gameId) {
//        $lives = $db->field('uid,poster,title,gamename,ctime')->where("gameid=$gameId  and status=" . LIVE)->select('live');
        $lives = $db->field('uid,poster,title,gamename,ctime')->where("gameid not in ($gameId)")->select('live');
    }
//    if (!empty($lives)) {
//        foreach ($lives as $lv) {
//            $live[$lv['uid']] = $lv;
//        }
//    }
    return $lives;
}

/**
 * 批量获取多个主播的观众数量
 * @param array $luid
 * @param object $db
 * @return type
 */
//function batchGetLiveRoomUserCount($luid, $db) {
//    $rows = array();
//    $row = $db->field('luid,count(*) as count')->where('luid in (' . $luid . ') group by luid')->select('liveroom');
//    foreach ($row as $rv) {
//        $rows[$rv['luid']] = $rv['count'];
//    }
//    return $rows;
//}
//
///**
// * 批量获取多个主播的粉丝数量
// * @param type $uid2
// * @param type $db
// * @return type
// */
//function batchGetFansCount($uid2, $db) {
//    $fan = $db->field('uid2,count(*) as fans')->where('uid2 in (' . $uid2 . ') group by uid2')->select('userfollow');
//    foreach ($fan as $fv) {
//        $fans[$fv['uid2']] = $fv['fans'];
//    }
//    return $fans;
//}

/**
 * 二维数组排序
 * @param array $multi_array 待排序的数组
 * @param string $sort_key   要排序的字段
 * @param string $sort       排序的规则
 * @return array
 */
function multiArraySort($multi_array, $sort_key, $sort = SORT_DESC) {
    if (is_array($multi_array)) {
        foreach ($multi_array as $row_array) {
            if (is_array($row_array)) {
                $key_array[] = $row_array[$sort_key];
            } else {
                return false;
            }
        }
    } else {
        return false;
    }
    array_multisort($key_array, $sort, $multi_array);
    return $multi_array;
}

/**
 * 获取所有正在直播的游戏
 * @param int $uid
 * @param string $encpass
 * @param int $gameId
 * @param int $type  0最热,1最新,2关注数
 * @param type $db
 * @param type $conf
 * @return array
 */
function DataLists($uid, $encpass, $gameId, $db, $conf, $redisobj) {
    $gamelist = $luides = array();
    $cacheKey = "HuanPeng_antherGameListBy$gameId";
    $getCatch = '';
    // $getCatch = $redisobj->get($cacheKey);
    if ($getCatch) {
        $liveLists = jsond($getCatch, true);
    } else {
        $liveLists = getLiveLists($gameId, $db);

        //$redisobj->set($cacheKey, jsone($liveLists), 60);
    }
    if ($liveLists) {
        foreach ($liveLists as $v) {
            array_push($luides, $v['uid']);
        }
        $luidString = implode(',', $luides); //主播id
        $luserCount = batchGetLiveRoomUserCount($luidString, $db);
        $fansCount = batchGetFansCount($luidString, $db);
        $autherInfo = getUserNicks($luides, $db);
        foreach ($liveLists as $k => $v) {
            $list['luid'] = $v['uid'];
            $list['gameName'] = $v['gamename'];
            $list['posterUrl'] = $v['poster'] ? "http://" . $conf['domain-img'] . '/' . $v['poster'] : '';
            $list['nick'] = $autherInfo[$v['uid']] ? $autherInfo[$v['uid']] : '';
            $list['liveTitle'] = $v['title'];
            $list['ctime'] = $v['ctime'];
            $list['viewerCount'] = array_key_exists($v['uid'], $luserCount) ? $luserCount[$v['uid']] : 0;
            $list['fansCount'] = array_key_exists($v['uid'], $fansCount) ? $fansCount[$v['uid']] : 0;
            $list['isFollow'] = 0;
            array_push($gamelist, $list);
        }
    } else {
        $gamelist = array();
    }
    return $gamelist;
}

/**
 * 根据条件获取相应数据
 * @param int $uid
 * @param string $encpass
 * @param int $gameId
 * @param int $size
 * @param int $page
 * @param int $type
 * @param object $db
 * @param array $conf
 * @return array
 */
function chooseDataByTypeAndLastId($uid, $encpass, $gameId, $size, $page, $type, $db, $conf, $redisobj) {
    $finallyLiveLists = array();
    if ($type == 0) {
        $sort = 'viewerCount';
    }
    if ($type == 1) {
        $sort = 'ctime';
    }
    if ($type == 2) {
        $sort = 'fansCount';
    }
    $cacheKey = "HuanPeng_antherGameOrderListBy$gameId$sort"; //定义一个缓存的键名
    // $getCatch = $redisobj->get($cacheKey);
    $getCatch = '';
    if ($getCatch) {
        $afterSort = jsond($getCatch, true);
    } else {
        $res = DataLists($uid, $encpass, $gameId, $db, $conf, $redisobj);
        if ($res) {
            $afterSort = multiArraySort($res, $sort);
            //  $redisobj->set($cacheKey, jsone($afterSort), 70); //加入缓存,第三个参数为缓存时间60s  
        } else {
            $afterSort = array();
        }
    }
    $afterSortLength = count($afterSort);
    $page = returnPage($afterSortLength, $size, $page);
    $offect = ($page - 1) * $size;
    $finallyLiveLists = array_slice($afterSort, $offect, $size);
    //如果存在uid则去获取是否关注
    if (!empty($uid)) {
        foreach ($finallyLiveLists as $fk => $fv) {
            $isFollowRes = isOneFollowOne($uid, $fv['luid'], $db);
            if ($isFollowRes) {
                $finallyLiveLists[$fk]['isFollow'] = 1;
            }
        }
    }
    return $finallyLists = array('list' => $finallyLiveLists, 'count' => $afterSortLength);
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$gameId = isset($_POST['gameIds']) ? trim($_POST['gameIds']) : '';
$size = isset($_POST['size']) ? trim($_POST['size']) : 8;
$page = isset($_POST['page']) ? trim($_POST['page']) : 1;
$type = isset($_POST['type']) ? trim($_POST['type']) : 0;
if (empty($gameId)) {
    error(-993);
}
$result = chooseDataByTypeAndLastId($uid, $encpass, $gameId, $size, $page, $type, $db, $conf, $redisobj);
if ($result['list']) {
    if ($gameId) {
        $ref = '其他游戏';
    } else {
        $ref = '所有直播';
    }
    exit(jsone(array('liveList' => $result['list'], 'ref' => $ref, 'liveCount' => $result['count'])));
}

