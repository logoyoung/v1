<?php

/**
 * App端 获取最热,最新录像列表的用户信息
 * date 2016-06-28 14:30
 * author yandong@6rooms.com
 */
include '../../init.php';
require_once INCLUDE_DIR . 'redis.class.php';
$db = new DBHelperi_huanpeng();
$redisObj = new RedisHelp();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

function getVideoForApp($type, $db, $redisObj) {
    if ($type == 0) {//热门
        $res = $db->field('videoid,uid,ctime,poster,COUNT(DISTINCT uid)')->where("status=" . VIDEO . " GROUP BY uid")->order("viewcount DESC")->limit(20)->select('video');
    } else {//最新
        $res = $db->field('videoid,uid,ctime,poster,COUNT(DISTINCT uid)')->where("status=" . VIDEO . " GROUP BY uid")->order("ctime DESC")->limit(20)->select('video');
    }
    if ($res) {
        return $res;
    } else {
        return array();
    }
}

/**
 * start
 */
$type = isset($_POST['type']) ? (int) $_POST['type'] : 0;
if (!in_array($type, array(0, 1))) {
    error(-4013);
}
$res = getVideoForApp($type, $db, $redisObj);
if ($res) {
    $list = array();
    foreach ($res as $v) {
        $temp['videoid'] = $v['videoid'];
        $temp['pic'] = $v['poster'] ? "http://" . $conf['domain-img'] . "/" . $v['poster'] : '';
        array_push($list, $temp);
    }
    exit(json_encode(array('list' => $list)));
} else {
    exit(json_encode(array('list' => array())));
}

