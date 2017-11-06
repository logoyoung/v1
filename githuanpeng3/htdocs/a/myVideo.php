<?php

include '../init.php';
require_once INCLUDE_DIR . 'redis.class.php';
/*
 * 我的录像
 * date 2016-01-18 11:30
 * author yandong@6rooms.com
 * version 0.0
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$redisobj = new RedisHelp();
$db = new DBHelperi_huanpeng();

/**
 * 我的录像列表
 * @param int $uid       用户id
 * @param int $gameid    游戏id
 * @param int $gametid   游戏类型id
 * @param string $order  排序
 * @param object $db
 * @return array
 */
function getMyVideoLists($uid, $gameid, $gametid, $order, $type, $page, $size, $db) {
    $live = array();
    if ($type == 0) {//待发布
        $where = 1;
        if (!empty($uid)) {
            $where .=" and uid=$uid ";
        }
        if (!empty($gameid)) {
            $where .=" and gameid=$gameid ";
        }
        if (!empty($gametid)) {
            $where .=" and gametid=$gametid ";
        }
        $total = $db->field('count(*) as total')->where('status in(' . VIDEO_WAIT . ',' . VIDEO_UNPUBLISH . ') and ' . $where . '')->select('video');
        $tcount = ($total[0]['total'] == 0) ? 1 : $total[0]['total'];
        $page = returnPage($tcount, $size, $page);
        $lives = $db->field('videoid,gameid,gametid,gamename,vfile,length,poster,title,ctime,status,upcount,orientation')->where('status in(' . VIDEO_WAIT . ',' . VIDEO_UNPUBLISH . ')  and ' . $where . '')->order("$order desc")->limit($page, $size)->select('video');
    }
//    if ($type == 1) {//审核中
//        $lives = $db->field('videoid,gameid,gametid,gamename,length,poster,title,ctime,vfile,viewcount,status,orientation')->where("uid=$uid and status=" . VIDEO_UNPUBLISH)->order("ctime desc")->limit($page, $size)->select('video');
//        $total = $db->field('count(*) as total')->where("uid=$uid and status=" . VIDEO_UNPUBLISH)->select('video');
//    }
    if ($type == 2) {//已发布
        $total = $db->field('count(*) as total')->where("uid=$uid and status=" . VIDEO)->select('video');
        $tcount = ($total[0]['total'] == 0) ? 1 : $total[0]['total'];
        $page = returnPage($tcount, $size, $page);
        $lives = $db->field('videoid,gameid,gametid,gamename,length,poster,title,ctime,vfile,viewcount,status,upcount,orientation')->where("uid=$uid and status=" . VIDEO)->order("ctime desc")->limit($page, $size)->select('video');
    }
    return array('lives' => $lives, 'total' => $total[0]['total']);
}

/**
 * 获取数据
 * @param int $uid       用户id
 * @param int $gameid    游戏id
 * @param int $gametid   游戏类型id
 * @param string $order  排序方式
 * @param int $page      页码
 * @param int $size      页数
 * @param object $db     数据库对象
 * @param array $conf    服务器配置数组
 * @param object $redisobj redis对象
 * @return array
 */
function getDatas($uid, $gameid, $gametid, $order, $type, $page, $size, $db, $conf, $redisobj) {
    $finallyLiveLists = array();
    if ($order == 0) {
        $order = 'ctime';
    }
    if ($order == 1) {
        $order = 'length';
    }
//    $cacheKey = "HuanPeng_MyVideoPageListBy$uid$gameid$gametid$type$page$size$order"; //定义一个缓存的键名
//    $getCatch = $redisobj->get($cacheKey);
    $getCatch = '';
    if ($getCatch) {
        $CatchData = jsond($getCatch, true);
        $afterSort = $CatchData['lives'];
        $afterSortLength = $CatchData['total'];
    } else {
        $res = getMyVideoLists($uid, $gameid, $gametid, $order, $type, $page, $size, $db);
        if ($res) {
            $afterSort = $res['lives'];
            $afterSortLength = $res['total'];
//            $redisobj->set($cacheKey, jsone($res), 60); //加入缓存,第三个参数为缓存时间60s  
        } else {
            $afterSort = array();
        }
    }
    if ($afterSortLength) {
        foreach ($afterSort as $v) {
            $finallyLive['videoID'] = $v['videoid'];
            $finallyLive['gameID'] = $v['gameid'];
            $finallyLive['gameTypeID'] = $v['gametid'];
            $finallyLive['gameName'] = $v['gamename'];
            $finallyLive['angle'] = $v['orientation'];
            $finallyLive['AppTimeLength'] = $v['length'];
            $finallyLive['videoTimeLength'] = (SecondFormat($v['length']) !== false) ? SecondFormat($v['length']) : '';
            $finallyLive['videoUploadDate'] = strtotime($v['ctime']);  
            if($v['poster']){
                 $finallyLive['posterURL'] = $v['poster'] ? "http://" . $conf['domain-img'] . '/' . $v['poster'] : '';
                 $finallyLive['ispic'] ='1';
            }else{
                 $finallyLive['posterURL'] = CROSS;
                 $finallyLive['ispic'] = '0';
            }
            $finallyLive['videoTitle'] = $v['title'];
            $finallyLive['upCount'] = $v['upcount'];
            $finallyLive['videoStatus'] = $v['status'];
            $finallyLive['videoPlaybackURL'] = ($v['vfile']) ? ($conf['domain-video'] . $v['vfile']) : '';
            if ($v['status'] == VIDEO_WAIT) {
                $timeOut = timediff(time(), strtotime($v['ctime']));
                if (!empty($timeOut['days'])) {
                    if ($timeOut['days'] >= VIDEO_TIMEOUT) {
                        $finallyLive['timeOut'] = 0;
                    } else {
                        $finallyLive['timeOut'] = VIDEO_TIMEOUT - $timeOut['days'];
                    }
                } else {
                    $finallyLive['timeOut'] = VIDEO_TIMEOUT;
                }
            }
            if ($type == 1 || $type == 2) {
                $finallyLive['totalViewCount'] = $v['viewcount'];
                $finallyLive['collectCount'] = getVideoCount($v['videoid'], $db);
            }

            array_push($finallyLiveLists, $finallyLive);
        }
    } else {
        $finallyLiveLists = array();
    }
    return $finallyLists = array('list' => $finallyLiveLists, 'count' => $afterSortLength);
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) ($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? $_POST['encpass'] : '';
$gameid = isset($_POST['gameid']) ? (int) ($_POST['gameid']) : '';
$gametid = isset($_POST['gametid']) ? (int) ($_POST['gametid']) : '';
$order = isset($_POST['order']) ? (int) ($_POST['order']) : 0;
$size = isset($_POST['size']) ? (int) ($_POST['size']) : 4;
$page = isset($_POST['page']) ? (int) ($_POST['page']) : 1;
$type = isset($_POST['type']) ? (int) ($_POST['type']) : 0;
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error($s);
}
if (!in_array($type, array(0, 2))) {
    error(-4013);
}
$result = getDatas($uid, $gameid, $gametid, $order, $type, $page, $size, $db, $conf, $redisobj);
if ($result) {
    exit(jsone(array('videoList' => $result['list'], 'videoCount' => $result['count'])));
} else {
    exit(jsone(array('videoList' => '', 'videoCount' => '')));
}

