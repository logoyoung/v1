<?php

include '../init.php';
require_once(INCLUDE_DIR . 'redis.class.php');
/*
 * 获取录像分页列表
 * date 2016-01-14 17:11
 * author yandong@6rooms.com
 * version 0.0
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$redisobj = new RedisHelp();
$db = new DBHelperi_huanpeng();

/**
 * 获取录像数量
 * @param int $gameId
 * @param string $order
 * @param object $db
 * @return array
 */
function getVideoByGameId($gameId, $db) {
    if ($gameId) {
        $lives = $db->field('videoid,gamename,poster,uid,title,ctime,viewcount,orientation')->where('status=' . VIDEO . ' and gameid=' . $gameId . '')->select('video');
    } else {
        $lives = $db->field('videoid,gamename,poster,uid,title,ctime,viewcount,orientation')->where('status=' . VIDEO . '')->select('video');
    }
    return $lives ? $lives : array();
}

/**
 * 根据传入的参数获取对应的录像分页列表信息
 * @param int $gameId
 * @param int $size
 * @param int $page
 * @param int $type
 * @param object $db
 * @param array $conf
 * @return array
 */
function getDatas($gameId, $page, $size, $type, $db, $conf, $redisobj) {
    $finallyLiveLists = array();
    if ($type == 0) {
        $order = 'viewcount'; //播放数
    }
    if ($type == 1) {
        $order = 'ctime'; //时间
    }
    if ($type == 2) {
        $order = 'videoFollow'; //视频收藏
    }
    $cacheKey = "HuanPeng_getVideoPageListBy$gameId$page$size$order"; //定义一个缓存的键名
    $getCatch = $redisobj->get($cacheKey);
    if ($getCatch) {
        $afterSort = jsond($getCatch, true);
    } else {
        $res = getVideoByGameId($gameId, $db);
        if ($res) {
            if ($type == 2) {//按获取视频收藏
                $ids = array_column($res, 'videoid');
                $vfollow = getVideoCountByVideoId(implode(',', $ids), $db);
                for ($i = 0, $k = count($res); $i < $k; $i++) {
                    if (array_key_exists($res[$i]['videoid'], $vfollow)) {
                        $res[$i]['videoFollow'] = $vfollow[$res[$i]['videoid']];
                    } else {
                        $res[$i]['videoFollow'] = 0;
                    }
                }
//                if (array_key_exists($vfollow, $finallyLiveLists)) {
//                    
//                }
            }
            $afterSort = dyadicArray($res, $order);
            $redisobj->set($cacheKey, jsone($afterSort), 60); //加入缓存,第三个参数为缓存时间60s
        } else {
            $afterSort = array();
        }
    }
    $page = returnPage(count($afterSort), $size, $page);
    $offset = ($page - 1) * $size;
    $afterCut = array_slice($afterSort, $offset, $size); //以后加缓存
    if ($afterCut) {
        $vids = array_column($afterCut, 'videoid');
        $comment = getVideoCommentCountByVideoId($vids, $db);
    }
    if ($afterCut) {
        foreach ($afterCut as $v) {
            $finallyLive['videoId'] = $v['videoid'];
            if($v['poster']){
                  $finallyLive['posterUrl'] =  "http://" . $conf['domain-img'] . '/' . $v['poster'];
                  $finallyLive['ispic']='1';
            }else{
                  $finallyLive['posterUrl'] = CROSS;
                  $finallyLive['ispic']='0';
            }
            $finallyLive['videoTitle'] = $v['title'];
            $finallyLive['angle'] = $v['orientation'];
            $finallyLive['gameName'] = $v['gamename'];
            $finallyLive['commentCount'] = array_key_exists($v['videoid'], $comment) ? $comment[$v['videoid']] : '0';
            if ($type == 2) {
                $finallyLive['viewCount'] = $v['videoFollow'];
            } else {
                $finallyLive['viewCount'] = $v['viewcount'];
            }
//            $finallyLive['giftCount'] = 0;
            array_push($finallyLiveLists, $finallyLive);
        }
    } else {
        $finallyLiveLists = array();
    }
    return $finallyLists = array('list' => $finallyLiveLists, 'count' => count($afterSort));
}

/**
 * start
 */
$gameId = isset($_POST['gameId']) ? (int) ($_POST['gameId']) : '';
$size = isset($_POST['size']) ? trim($_POST['size']) : 4;
$page = isset($_POST['page']) ? trim($_POST['page']) : 1;
$type = isset($_POST['type']) ? trim($_POST['type']) : 0;
$result = getDatas($gameId, $page, $size, $type, $db, $conf, $redisobj);
if ($gameId) {
    if ($gameId == OTHER_GAME) {
        $ref = '其他视频';
    } else {
        $game = getGameNameByGameId($gameId, $db);
        $ref = $game[0]['name'];
    }
} else {
    $ref = '全部视频';
}
if ($result) {
    exit(jsone(array('liveList' => $result['list'], 'ref' => $ref, 'liveCount' => $result['count'])));
} else {
    exit(jsone(array('liveList' => $result['list'], 'ref' => $ref, 'liveCount' => $result['count'])));
}
