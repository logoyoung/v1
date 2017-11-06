<?php

/**
 * App端 获取最热,最新录像列表的用户信息
 * date 2016-06-28 14:30
 * author yandong@6rooms.com
 */
include '../../../include/init.php';
$db = new DBHelperi_huanpeng();
$redisObj = new RedisHelp();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

function getVideoForApp($type,$db, $redisObj) {
    if ($type == 0) {//热门
        $res = $db->field('videoid,vfile,uid,ctime,poster,COUNT(DISTINCT uid)')->where("status=" . VIDEO . "  and  videoid not  in (".HUANPENG_VIDEO.") GROUP BY uid")->order("viewcount DESC")->limit(20)->select('video');
    } else {//最新
        $res = $db->field('videoid,vfile,uid,ctime,poster,COUNT(DISTINCT uid)')->where("status=" . VIDEO . "  and  videoid not  in (".HUANPENG_VIDEO.")  GROUP BY uid")->order("videoid DESC")->limit(20)->select('video');
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
    error2(-4013);
}
$res = getVideoForApp($type, $db, $redisObj);
if ($res) {
    $list = array();
    foreach ($res as $v) {
        $temp['uid'] = $v['uid'];
        $temp['videoID'] = $v['videoid'];
        $temp['poster'] = sposter($v['poster']);
        $temp['videoUrl'] = sfile($v['vfile']);
        array_push($list, $temp);
    }
    succ(array('list' => $list));
} else {
    succ(array('list' => array()));
}

