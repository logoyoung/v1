<?php

/**
 * 获取当前直播列表
 * date 2017-02-24 09:45
 * author yandong@6rooms.com
 */
include '../../../include/init.php';
$db = new DBHelperi_huanpeng();
$redisObj = new RedisHelp();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];


/**
 * 获取最新列表
 * @param type $redisObj
 * @param type $db
 * @return array
 */
function getNew($redisObj, $page, $size, $conf, $db)
{
    $luid = array();
    $cacheKey = '6CN_GETNEWLIVELISTS:' . $page . $size;
    $getCatch = $redisObj->get($cacheKey); //取缓存
    if ($getCatch) {
        $luid = json_decode($getCatch, true);
    } else {
        $count = getLiveNumber($db);
        if ($count) {
            $page = returnPage($count, $size, $page);
            $row = $db->field('liveid,title,uid,poster,gamename')->where("status=" . LIVE)->order('liveid DESC')->limit($page, $size)->select('live');
            $liveuser = batchGetLiveRoomUserCount(implode(',', array_column($row, 'uid')), $db); //获取在线用户数
            if ($row) {
                foreach ($row as $v) {
                    $temp['title'] = $v['title'];
                    $temp['poster'] = $v['poster'] ? DOMAIN_PROTOCOL . $conf['domain-img'] . "/" . $v['poster'] : CROSS;
                    $temp['gameName'] = $v['gamename'];
                    $temp['viewCount'] = array_key_exists($v['uid'], $liveuser) ? $liveuser[$v['uid']] : 0;
                    $temp['url'] = ROOM_URL . $v['uid'];
                    array_push($luid, $temp);
                }
                $redisObj->set($cacheKey, json_encode($luid), 60); //写缓存
            } else {
                $luid = array();
            }
        }
    }
    return array('list' => $luid, 'page' => $page, 'total' => $count ? $count : 0);
}

function getLiveNumber($db)
{
    $res = $db->field("count(*)  as liveid")->where("status=" . LIVE)->select('live');
    if (false !== $res && !empty($res)) {
        return $res[0]['liveid'];
    } else {
        return 0;
    }
}

/**
 * 获取最热列表
 * @param type $redisObj
 * @param type $db
 * @return type
 */
function getHot($redisObj, $page, $size, $conf, $db)
{
    $luid = array();
    $cacheKey = '6CN_GETHOTLIVELISTS$:' . $page . $size;
    $getCatch = $redisObj->get($cacheKey); //取缓存
    if ($getCatch) {
        $luid = json_decode($getCatch, true);
    } else {
        $count = getLiveNumber($db);
        if($count){
            $row = $db->field('liveid,title,uid,poster,gamename')->where("status=" . LIVE)->select('live');
            $liveuser = batchGetLiveRoomUserCount(implode(',', array_column($row, 'uid')), $db); //获取在线用户数
            if ($row) {
                foreach ($row as $v) {
                    $temp['title'] = $v['title'];
                    $temp['poster'] = $v['poster'] ? DOMAIN_PROTOCOL . $conf['domain-img'] . "/" . $v['poster'] : CROSS;
                    $temp['gameName'] = $v['gamename'];
                    $temp['viewCount'] = array_key_exists($v['uid'], $liveuser) ? $liveuser[$v['uid']] : 0;
                    $temp['url'] = ROOM_URL . $v['uid'];
                    array_push($luid, $temp);
                }
                $luid = dyadicArray($luid, 'viewCount');
                $page = returnPage($count, $size, $page);
                $offect = ($page - 1) * $size;
                $luid = array_slice($luid, $offect, $size);
                $redisObj->set($cacheKey, json_encode($luid), 60); //写缓存
            }
        }
    }
    return array('list' => $luid, 'page' => $page, 'total' => $count ? $count : 0);
}



/**
 * start
 */
$type = isset($_POST['type']) ? (int)$_POST['type'] : 0;//0观众数 ／1时间
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$size = isset($_POST['size']) ? (int)$_POST['size'] : 10;
if (!in_array($type, array(0, 1))) {
    error2(-4013);
}
if(!is_numeric($page) || !is_numeric($size)){
    error2(-4013);
}
if ($type) {
    $res = getNew($redisObj, $page, $size, $conf, $db);
} else {
    $res = getHot($redisObj, $page, $size, $conf, $db);

}
succ(array('list' => $res['list'], 'page' => $res['page'], 'total' => $res['total']));
