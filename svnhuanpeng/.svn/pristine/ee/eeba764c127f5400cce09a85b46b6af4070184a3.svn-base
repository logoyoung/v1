<?php

include '../init.php';
require_once INCLUDE_DIR . 'redis.class.php';
/**
 * 直播间排行榜
 * author yandong@6rooms.com
 * date 2016-02-02 09:33
 * copyright@6.cn version 0.0
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();
$redobj = new RedisHelp();

/**
 * 获取日榜排行
 * @param int $timeType
 * @param int $luid
 * @param int $size
 * @param object $db
 * @param object $redobj
 * @return array
 */
function getTodayRank($timeType, $luid, $size, $db, $redobj) {
    //$cacheKey = "HuanPeng_LiveRoomTodayRankingBy$luid$timeType";
   // $getCatch = $redobj->get($cacheKey);
    $getCatch='';
    if ($getCatch) {
        $res = jsond($getCatch, true);
    } else {
        $beginToday = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d'), date('Y')));
        $endToday = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1);
        $res = $db->field('uid,cost')->where("date >= '$beginToday' and date<='$endToday' and luid=$luid group by uid")
                        ->order('cost DESC')->limit($size)->select('rank_day');
       // $redobj->set($cacheKey, jsone($res), 90);//写缓存
    }
    return $res;
}

/**
 * 获取周榜排行
 * @param int $timeType
 * @param int $luid
 * @param int $size
 * @param object $db
 * @param object $redobj
 * @return array
 */
function getLastWeekRank($timeType, $luid, $size, $db, $redobj) {
    $cacheKey = "HuanPeng_LiveRoomLastWeekRankingBy$luid$timeType";
    //$getCatch = $redobj->get($cacheKey);
    $getCatch='';
    if ($getCatch) {
        $res = jsond($getCatch, true);
    } else {
        $beginThisweek = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") - date("w") + 1, date("Y")));
        $atThisTime = date('Y-m-d H:i:s', time());
        $res = $db->field('uid,cost')->where("date >='$beginThisweek' and date<='$atThisTime' and luid=$luid  group by uid")
                        ->order('cost DESC')->limit($size)->select('rank_week');
       // $redobj->set($cacheKey, jsone($res), $keytime);//写缓存
    }
    return $res;
}

/**
 * 批量获取用户昵称
 * date  2015-12-14
 * @param array $uids
 * @param object $db
 * @return array
 */
function getUserPicAndNicks($uids, $db) {
    $s = implode(',', $uids);
    $ret = array();
    $res = $db->field('uid,nick,pic')->where('uid in (' . $s . ')')->select('userstatic');
    foreach ($res as $key => $val) {
        $ret[$val['uid']] = $val;
    }
    return $ret;
}

function getLeval($uids, $db){
    $s = implode(',', $uids);
    $ret = array();
    $res = $db->field('uid,level')->where('uid in (' . $s . ')')->select('useractive');
    foreach ($res as $key => $val) {
        $ret[$val['uid']] = $val['level'];
    }
    return $ret;
}

/**
 * 获取总榜排行
 * @param int $timeType
 * @param int $luid
 * @param int $size
 * @param object $db
 * @param object $redobj
 * @return array
 */
function getTotalRank($timeType, $luid, $size, $db, $redobj) {
    $cacheKey = "HuanPeng_LiveRoomTotalRankingBy$luid$timeType";
    $getCatch='';
   // $getCatch = $redobj->get($cacheKey);
    if($getCatch){
        $res = jsond($getCatch, true);
    } else {
        $atThisTime = date("Y-m-d H:i:s", time());
        $res = $db->field('uid, cost')->where(" luid=$luid  group by uid")
                        ->order('cost DESC')->limit($size)->select('rank_all');
      //  $redobj->set($cacheKey, jsone($res), $keytime);
    }
    return $res;
}

/**
 * 获取排行
 * @param int $timeType
 * @param int $luid
 * @param int $size
 * @param object $db
 * @param object $redobj
 * @param array $conf
 * @return array
 */
function getRanking($timeType, $luid, $size, $db, $redobj, $conf) {
    $listIds = $rankList = $rankListes = array();
    if ($timeType == 1) {
        $res = getTodayRank($timeType, $luid, $size, $db, $redobj);
    }
    if ($timeType == 2) {
        $res = getLastWeekRank($timeType, $luid, $size, $db, $redobj);
    }
    if ($timeType == 3) {
        $res = getTotalRank($timeType, $luid, $size, $db, $redobj);
    }
    if ($res) {
        foreach ($res as $v) {
            $listIds[] = $v['uid'];
        }
        if ($listIds) {
            $list = getUserPicAndNicks($listIds, $db);
            $level= getLeval($listIds, $db);
            foreach ($res as $k => $v) {
                $rankList['uid'] = $v['uid'];
                $rankList['anchorPicUrl'] = $list[$v['uid']]['pic'] ? "http://" . $conf['domain-img'] . '/' . $list[$v['uid']]['pic'] : DEFAULT_PIC;
                $rankList['nick'] = $list[$v['uid']]['nick'] ? $list[$v['uid']]['nick'] : '';
                $rankList['money'] = $v['cost'];
                $rankList['level'] = $level[$v['uid']] ? $level[$v['uid']] : 1;
                array_push($rankListes, $rankList);
            }
        }
    } else {
        $rankListes = array();
    }
    return $rankListes;
}

/**
 * start
 */
$timeType = isset($_POST['timeType']) ? (int) ($_POST['timeType']) : 1;
$luid = isset($_POST['luid']) ? (int) ($_POST['luid']) : '';
$size = isset($_POST['size']) ? (int) ($_POST['size']) : 10;

if (empty($luid)) {
    error(-993);
}
$timeType = checkInt($timeType);
$luid = checkInt($luid);
$size = checkInt($size);

$res = getRanking($timeType, $luid, $size, $db, $redobj, $conf);
if ($res) {
    exit(jsone(array('rankList' => $res)));
} else {
    exit(jsone(array('rankList' => '')));
}


