<?php
header("Content-type:text/html;charset=utf-8");
include '../init.php';
require_once(INCLUDE_DIR . 'redis.class.php');
/*
 * 获取游戏列表和直播大厅正在直播的游戏
 * date 2015-12-29 14:50 
 * author yandong@6rooms.com
 * version 0.0
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$redisobj = new RedisHelp();
$db = new DBHelperi_huanpeng();

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
    if ($gameId) {
        if (!DEBUG) {
            $lives = $db->field('uid,poster,title,gamename,ctime,orientation')->where("gameid=$gameId  and status=" . LIVE)->select('live');
        } else {
//            $lives = $db->field('uid,poster,title,gamename,ctime,orientation')->where("gameid=$gameId group by uid")->order('ctime DESC')->select('live');
//            $live = $db->field('uid,poster,title,gamename,ctime,orientation')->where("gameid=$gameId group by uid")->order('ctime DESC')->select('live');

            $sql = "select uid,poster,title,gamename,ctime,orientation  from (select * from live where gameid=$gameId order by ctime  desc) live  group by uid order by ctime desc;";
            $lives = $db->doSql($sql);
        }
    } else {
        if (!DEBUG) {
            $lives = $db->field('uid,poster,title,gamename,ctime,orientation')->where('status=' . LIVE . '')->select('live');
        } else {
//            $lives = $db->field('uid,poster,title,gamename,ctime,orientation')->where('status !=102')->select('live');
            $sql = "select uid,poster,title,gamename,ctime,orientation  from (select * from live  order by ctime  desc) live  group by uid order by ctime desc;";
            $lives = $db->doSql($sql);
        }
    }

    return $lives;
}

/**
 * 根据游戏id获取该游戏正在直播的场次
 * @param int $gameId
 * @param object $db
 * @return string
 */
function getLiveGameCountByGid($gameId, $db) {
    if (!DEBUG) {
        $count = $db->field('count(liveid) as num')->where("status=" . LIVE . "  and gameid=$gameId")->select('live'); //记得加状态status=' . LIVE . '
    } else {
        $count = $db->field('count(distinct(uid)) as num')->where("gameid=$gameId")->select('live'); //记得加状态status=' . LIVE . '
    }
    return $count[0]['num'];
}

/**
 * 二维数组排序
 * @param array $multi_array 待排序的数组
 * @param string $sort_key   要排序的字段
 * @param string $sort       排序的规则
 * @return array
 */
function multiArraySort($multi_array, $sort_key, $tow_sort_key, $sort = SORT_DESC) {
    if (is_array($multi_array)) {
        foreach ($multi_array as $row_array) {
            if (is_array($row_array)) {
                $key_array[] = $row_array[$sort_key];
                $tow_key_array[] = $row_array[$tow_sort_key];
            } else {
                return false;
            }
        }
    } else {
        return false;
    }
    array_multisort($key_array, $sort, $tow_key_array, $sort, $multi_array);
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
function DataLists($uid, $encpass, $gameId, $size, $page, $db, $conf, $redisobj) {
    $gamelist = $luides = array();
    // $cacheKey = "HuanPeng_HomePageGameListBy$gameId$size$page";
    $getCatch = '';
    // $getCatch = $redisobj->get($cacheKey);
    if ($getCatch) {
        $liveLists = jsond($getCatch, true);
        $luides = array_unique(array_column($liveLists, 'uid'));
    } else {
        $liveLists = getLiveLists($gameId, $db);
        $luides = array_unique(array_column($liveLists, 'uid'));
        //$redisobj->set($cacheKey, jsone($liveLists), 60);
    }
    if ($liveLists) {
        // $luides = array_keys($liveLists);
        $luidString = implode(',', $luides); //主播id
        $luserCount = batchGetLiveRoomUserCount($luidString, $db);
        $fansCount = batchGetFansCount($luidString, $db);
        $autherInfo = getUserNicks($luides, $db);
        foreach ($liveLists as $k => $v) {
            $list['luid'] = $v['uid'];
            $list['gameName'] = $v['gamename'];
            $list['nick'] = array_key_exists($v['uid'], $autherInfo) ? $autherInfo[$v['uid']] : '';
            $list['liveTitle'] = $v['title'];
            $list['ctime'] = strtotime($v['ctime']);
            $list['angle'] = $v['orientation'];
            if ($v['poster']) {
                $list['posterUrl'] = "http://" . $conf['domain-img'] . "/" . $v['poster'];
                $list['ispic'] = '1';
            } else {
                $list['posterUrl'] = CROSS;
                $list['ispic'] = '0';
            }
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
    if (!in_array($type, array(0, 1, 2))) {
        return false;
    }
    if ($type == 0) {
        $sort = 'viewerCount';
        $tow_sort = 'ctime';
    }
    if ($type == 1) {
        $sort = 'ctime';
        $tow_sort = 'viewerCount';
    }
    if ($type == 2) {
        $sort = 'fansCount';
        $tow_sort = 'ctime';
    }
    //$cacheKey = "HuanPeng_HomePageGameOrderListBy$gameId$size$page$sort"; //定义一个缓存的键名
    $getCatch = '';
    // $getCatch = $redisobj->get($cacheKey);
    if ($getCatch) {
        $afterSort = jsond($getCatch, true);
    } else {
        $res = DataLists($uid, $encpass, $gameId, $size, $page, $db, $conf, $redisobj);
        if ($res) {
            $afterSort = multiArraySort($res, $sort, $tow_sort);
            //  $redisobj->set($cacheKey, jsone($afterSort), 70); //加入缓存,第三个参数为缓存时间70s  
        } else {
            $afterSort = array();
        }
    }
    if ($gameId) {
        if ((int) $gameId == 0) {
            $afterSortLength = count($afterSort);
        } else {
            $afterSortLength = getLiveGameCountByGid($gameId, $db);
        }
    } else {
        $afterSortLength = count($afterSort);
    }
    $page = returnPage($afterSortLength, $size, $page);
    $offect = ($page - 1) * $size;
    $finallyLiveLists = array_slice($afterSort, $offect, $size);
    //如果存在uid则去获取是否关注
//    if (!empty($uid)) {
//        foreach ($finallyLiveLists as $fk => $fv) {
//            $isFollowRes = isOneFollowOne($uid, $fv['luid'], $db);
//            if ($isFollowRes) {
//                $finallyLiveLists[$fk]['isFollow'] = 1;
//            }
//        }
//    }
    return $finallyLists = array('list' => $finallyLiveLists, 'count' => $afterSortLength);
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$gameId = isset($_POST['gameId']) ? trim($_POST['gameId']) : '';
$size = isset($_POST['size']) ? trim($_POST['size']) : 4;
$type = isset($_POST['type']) ? trim($_POST['type']) : 0;
if (isset($_POST['lastId'])) {//这三个if主要是为了兼容,以后优化记得改
    $page = !empty($_POST['lastId']) ? trim($_POST['lastId']) : 1;
}
if (isset($_POST['page'])) {
    $page = isset($_POST['page']) ? trim($_POST['page']) : 1;
}
if (!isset($_POST['page']) && !isset($_POST['lastId'])) {
    $page = 1;
}
$result = chooseDataByTypeAndLastId($uid, $encpass, $gameId, $size, $page, $type, $db, $conf, $redisobj);
if ($gameId) {
    if ($gameId == OTHER_GAME) {
        $ref = '其他游戏';
    } else {
        if (empty($result['count'])) {
            $game = getGameNameByGameId($gameId, $db);
            $ref = $game[0]['name'];
        } else {
            $ref = $result['list'][0]['gameName'];
        }
    }
} else {
    $ref = '全部直播';
}
if ($result['list']) {
    exit(jsone(array('liveList' => $result['list'], 'ref' => $ref, 'liveCount' => $result['count'])));
} else {
    exit(jsone(array('liveList' => array(), 'ref' => $ref, 'liveCount' => "0")));
}



    