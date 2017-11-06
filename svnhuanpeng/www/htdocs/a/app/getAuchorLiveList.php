<?php

/**
 * App端 获取当前正在直播的主播列表
 * date 2016-05-20 13:30
 * author yandong@6rooms.com
 */
include '../../init.php';
require(INCLUDE_DIR . 'redis.class.php');
$db = new DBHelperi_huanpeng();
$redisObj = new RedisHelp();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

/**
 * 获取最新列表
 * @param type $redisObj
 * @param type $db
 * @return array
 */
function getNewLiveLists($redisObj, $db) {
    $luid = array();
    $cacheKey = 'GETNEWLIVELISTS_YD';
    $getCatch = $redisObj->get($cacheKey); //取缓存
    if ($getCatch) {
        $luid = json_decode($getCatch, true);
    } else {
        $row = $db->field('uid,poster')->where("status=" . LIVE)->order('ctime DESC')->select('live');
        if ($row) {
            foreach ($row as $v) {
                $luid[$v['uid']] = $v;
            }
            $redisObj->set($cacheKey, json_encode($luid), 60); //写缓存
        } else {
            $luid = array();
        }
    }
    return $luid;
}

/**
 * 获取最热列表
 * @param type $redisObj
 * @param type $db
 * @return type
 */
function getHotLiveLists($redisObj, $db) {
    $luid = array();
    $cacheKey = 'GETHOTLIVELISTS_YD';
    $getCatch = $redisObj->get($cacheKey); //取缓存
    if ($getCatch) {
        $luid = json_decode($getCatch, true);
    } else {
        $row = $db->field('uid,poster')->where("status=" . LIVE)->select('live');
        $liveuser = batchGetLiveRoomUserCount(implode(',', array_column($row, 'uid')), $db); //获取在线用户数
        if ($row) {
            foreach ($row as $v) {
                $temp['uid'] = $v['uid'];
                $temp['poster'] = $v['poster'];
                $temp['count'] = array_key_exists($v['uid'], $liveuser) ? $liveuser[$v['uid']] : 0;
                array_push($luid, $temp);
            }

            $luid = dyadicArray($luid, 'count');
            $redisObj->set($cacheKey, json_encode($luid), 60); //写缓存
        }
    }
    return $luid;
}

/**
 * 获取当前直播列表
 * @param type $type 类型 0最热,1最新
 * @param type $redisObj 
 * @param type $conf
 * @param type $db
 * @return array
 */
function getAuchorList($type, $redisObj, $conf, $db) {
    $list = array();
    if ($type == 0) {
        $res = getHotLiveLists($redisObj, $db); //最热
    }
    if ($type == 1) {
        $res = getNewLiveLists($redisObj, $db);     //最新
    }
    if ($res) {
        foreach ($res as $v) {
            $temp['uid'] = $v['uid'];
            $temp['pic'] = $v['poster'] ? "http://" . $conf['domain-img'] . "/" . $v['poster'] : DEFAULT_PIC;
            array_push($list, $temp);
        }
    }
    return $list;
}

/**
 * 获取直播封面图
 * @param type $uid  主播id
 * @param type $db
 * @return array
 */
function getLivePicByUid($uid, $conf, $db) {
    $list = array();
    $res = $db->field('uid,poster')->where("uid in ($uid)  and status=" . LIVE)->select('live');
    if ($res) {
        foreach ($res as $v) {
            $temp['uid'] = $v['uid'];
            $temp['pic'] = $v['poster'] ? "http://" . $conf['domain-img'] . "/" . $v['poster'] : DEFAULT_PIC;
            array_push($list, $temp);
        }
    }
    return $list;
}

/**
 * 获取直播列表
 * @param type $uid  用户id
 * @param type $encpass 有效验证串
 * @param type $type  最热0，最新1 
 * @param type $follow  用于区分首页和我的关注页面
 * @param type $redisObj
 * @param type $conf
 * @param type $db
 * @return array
 */
function getList($uid, $encpass, $type, $follow, $redisObj, $conf, $db) {
    if ($follow == 0) {//首页
        $list = getAuchorList($type, $redisObj, $conf, $db);
    }
    if ($follow == 1) {//关注页
        $uid = checkInt($uid);
        $encpass = checkStr($encpass);
        $s = CheckUserIsLogIn($uid, $encpass, $db);
        if (true !== $s) {
            error($s);
        }
        $isfollow = userFollow($uid, $db);
        if ($isfollow) {
            $isonline = getAnchorIsOnLine(array_column($isfollow, 'uid2'), $db);
            if ($isonline) {
                $list = getLivePicByUid(implode(',', array_keys($isonline)), $conf, $db);
            } else {
                $list = array();
            }
        } else {
            $list = array();
        }
    }
    return $list;
}

/**
 * start
 */
$type = isset($_POST['type']) ? (int) $_POST['type'] : 0;
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$follow = isset($_POST['follow']) ? (int) $_POST['follow'] : 0;
if (!in_array($type, array(0, 1))) {
    error(-4013);
}
$res = getList($uid, $encpass, $type, $follow, $redisObj, $conf, $db);
exit(json_encode(array('list' => $res)));
