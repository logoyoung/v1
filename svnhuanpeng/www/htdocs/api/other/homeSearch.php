<?php

include '../../../include/init.php';
require(INCLUDE_DIR . 'LiveRoom.class.php');
/**
 * 首页搜索
 * author yandong@6rooms.com
 * date 2016-02-26 09:33
 * copyright@6.cn version 0.0
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();
$redobj = new RedisHelp();

/**
 * 根据关键字获取直播列表
 * @param string $keyword
 * @param object $db
 * @return array
 */
function searchLiveListbyKeyword($keyword, $type, $page, $size, $conf, $db)
{
    $livelists = $Totallives = array();
    $total = $db->field('count(*) as total')->where("status= " . LIVE . "  and binary ucase(title) like ucase('%$keyword%')")->select('live');
    $tcount = ($total[0]['total'] == 0) ? 1 : $total[0]['total'];
    $page = returnPage($tcount, $size, $page);
    if ($type == 0) {
        $res = $db->field('uid,gamename,title,poster,ctime,orientation')->where("status= " . LIVE . "  and binary ucase(title) like ucase('%$keyword%')")->order('liveid desc')->limit($page, $size)->select('live');
    } else {
        $res = $db->field('uid,gamename,title,poster,ctime,orientation')->where("status= " . LIVE . "  and binary ucase(title) like ucase('%$keyword%')")->order('liveid desc')->limit($page, $size)->select('live');
    }
    if ($res) {
        foreach ($res as $k => $v) {
            $List[$v['uid']] = $v;
        }
        $obj=new LiveRoom(1);
        $luid = implode(',', array_keys($List));
        $fansCount = batchGetFansCount($luid, $db);
        $userInfo = getUserNicks(array_keys($List), $db);
        $roomids=getRoomIdByUid(implode(',', array_keys($List)), $db);
        foreach ($res as $k => $v) {
            $livelist['uid'] = $v['uid'];
            $livelist['gameName'] = $v['gamename'];
            $livelist['title'] = $v['title'];
            $livelist['roomID'] = array_key_exists($v['uid'], $roomids) ? $roomids[$v['uid']] : 0;
            $livelist['nick'] = array_key_exists($v['uid'], $userInfo) ? $userInfo[$v['uid']] : '';
            $livelist['stime'] = strtotime($v['ctime']);
            if ($v['poster']) {
                $livelist['poster'] = $v['poster'] ? $conf['domain-lposter'] . '/' . $v['poster'] : '';
                $livelist['ispic'] = '1';
            } else {
                $livelist['poster'] = CROSS;
                $livelist['ispic'] = '0';
            }
            $livelist['orientation'] = $v['orientation'];
            $livelist['viewCount'] = $obj-> getLiveUserCountByLuid($v['uid']);
            $livelist['fansCount'] = array_key_exists($v['uid'], $fansCount) ? $fansCount[$v['uid']] : "0";
            array_push($livelists, $livelist);
        }
    }
    return array('lives' => $livelists, 'total' => $total[0]['total']);
}

/**
 * 获取主播直播
 * @param string $keyword
 * @param int $type
 * @param object $db
 * @return array
 */
function searchAnchorLivebyKeyword($keyword, $db)
{
    $uids = array();
    $nickres = $db->field('uid,nick,pic')->where("binary ucase(nick) like  ucase('%$keyword%')")->select('userstatic');
    if ($nickres) {
        foreach ($nickres as $v) {
            $temp[$v['uid']] = $v;
        }
        $liveresult = array('lives' => $temp, 'total' => count($nickres));
    } else {
        $liveresult = array('lives' => '', 'total' => "0");
    }
    return $liveresult;
}

/**
 * 根据关键字获取录像数
 * @param sting $keyword
 * @param int $type
 * @param object $db
 * @return array
 */
function searchVideobyKeyword($keyword, $type, $page, $size, $db)
{
    $vlist = '';
    $total = $db->field('count(*) as total')->where("status= " . VIDEO . "  and binary ucase(title) like  ucase('%$keyword%')")->select('video');
    $tcount = ($total[0]['total'] == 0) ? "1" : $total[0]['total'];
    $page = returnPage($tcount, $size, $page);
    if ($type == 0) {
        $res = $db->field('videoid,uid,gamename,title,poster,vfile,viewcount,orientation')->where("status= " . VIDEO . "  and binary ucase(title) like ucase('%$keyword%')")->order('videoid desc')->limit($page, $size)->select('video');
    } else {
        $res = $db->field('videoid,uid,gamename,title,poster,vfile,viewcount,orientation')->where("status= " . VIDEO . "  and binary ucase(title) like ucase('%$keyword%')")->order('videoid desc')->limit($page, $size)->select('video');
    }
    if ($res) {
        foreach ($res as $k => $v) {
            $vlist[$v['videoid']] = $v;
        }
    }
    return array('video' => $vlist, 'total' => $total[0]['total']);
}

/**
 * 批量获取评论数量
 * @param type $videoids
 * @param type $db
 * @return type
 */
function getVideoComment($videoids, $db)
{
    $videoids = implode(',', $videoids);
    $res = $db->field(' videoid,count(*) as vtotal')->where("videoid in ($videoids) group by videoid")->select('videocomment');
    if ($res) {
        foreach ($res as $k => $v) {
            $videoList[$v['videoid']] = $v['vtotal'];
        }
    } else {
        $videoList = array();
    }
    return $videoList ? $videoList : array();
}

/**
 * 获取直播列表
 * @param string $keyword
 * @param int $page
 * @param int $size
 * @param array $conf
 * @param object $db
 * @return array
 */
function getLiveListbyKeyword($keyword, $type, $page, $size, $conf, $db)
{
    $res = searchLiveListbyKeyword($keyword, $type, $page, $size, $conf, $db);
    $livelists = array(
        'liveList' => $res['lives'],
        'videoList' => array(),
        'keyword' => $keyword,
        'type' => $type
    );
        $livelists['total'] = $res['total'] ? $res['total'] : "0";
    return $livelists ? $livelists : '';
}

/**
 * 获取主播直播
 * @param string $keyword
 * @param int $type
 * @param int $page
 * @param int $size
 * @param object $db
 * @return array
 */
function getAnchorLivebyKeyword($keyword, $type, $page, $size, $conf, $db, $client)
{
    $livelists = $livelist = array();
    if (!empty($client)  ){
        $size = 20;
    }
    $res = searchAnchorLivebyKeyword($keyword, $db);
    $anchorLive = $res['lives'];
    if ($anchorLive) {
        $cheackisAnchor = checkUserIsAnchor(array_keys($anchorLive), $db); //去除非主播
        if ($cheackisAnchor) {
            $fansCount = batchGetFansCount(implode(',', $cheackisAnchor), $db);
            $isOnline = getAnchorIsOnLine(array_keys($cheackisAnchor), $db);
            $roomids=getRoomIdByUid(implode(',', $cheackisAnchor), $db);
            foreach ($cheackisAnchor as $k => $v) {
                $list['uid'] = $v;
                $list['roomID'] = array_key_exists($v, $roomids) ? $roomids[$v] : 0;
                $list['nick'] = $anchorLive[$v]['nick'] ? $anchorLive[$v]['nick'] : '';
                $list['head'] = $anchorLive[$v]['pic'] ? DOMAIN_PROTOCOL . $conf['domain-img'] . '/' . $anchorLive[$v]['pic'] : DEFAULT_PIC;
                $list['fansCount'] = array_key_exists($v, $fansCount) ? $fansCount[$v] : "0";
                $list['isliving'] = array_key_exists($v, $isOnline) ? "1" : "0";
                array_push($livelist, $list);
            }
        } else {
            $livelist = array();
        }
    }
    if ($livelist) {
        $livelist = dyadicArray($livelist, 'fansCount');
        $page = returnPage(count($livelist), $size, $page);
        $offset = ($page - 1) * $size;
        $livelist = array_slice($livelist, $offset, $size);
    } else {
        $livelist = array();
    }
    if (empty($anchorLive)) {
        $cheackisAnchor = array();
    }
    $livelists['anchorList'] = $livelist;
    $livelists['videoList'] = array();
    $livelists['keyword'] = $keyword;
    $livelists['type'] = $type;
    $livelists['total'] = count($cheackisAnchor);
    return $livelists ? $livelists : '';
}

/**
 * 获取录像
 * @param string $keyword
 * @param int $type
 * @param int $page
 * @param int $size
 * @param object $db
 * @return array
 */
function getVideobyKeyword($keyword, $type, $page, $size, $conf, $db)
{
    $videoids = $uids = $livelist = array();
    $res = searchVideobyKeyword($keyword, $type, $page, $size, $db);
    $videos = $res['video'];
    if ($videos) {
        $comment = getVideoComment(array_keys($videos), $db);
        foreach ($videos as $k => $v) {
            $list['videoID'] = $v['videoid'];
            $list['gameName'] = $v['gamename'];
            $list['title'] = $v['title'];
            $list['viewCount'] = $v['viewcount'];
            $list['orientation'] = $v['orientation'];
            if ($v['poster']) {
                $list['poster'] =sposter($v['poster']);
                $list['ispic'] = '1';
            } else {
                $list['poster'] = CROSS;
                $list['ispic'] = '0';
            }
            $list['videoUrl'] = sfile($v['vfile']);
            $list['commentCount'] = array_key_exists($v['videoid'], $comment) ? $comment[$v['videoid']] : "0";
            $list['uid'] = $v['uid'];
            array_push($uids, $v['uid']);
            array_push($livelist, $list);
        }
        $userInfo = getUserNicks($uids, $db);
        $roomids=getRoomIdByUid(implode(',', $uids), $db);
        for ($i = 0, $j = count($livelist); $i < $j; $i++) {
            $livelist[$i]['nick'] = array_key_exists($livelist[$i]['uid'], $userInfo) ? $userInfo[$livelist[$i]['uid']] : '';
            $livelist[$i]['roomID'] = array_key_exists($livelist[$i]['uid'], $roomids) ? $roomids[$livelist[$i]['uid']] : '';
            unset($livelist[$i]['uid']);
        }
    }
    $livelists['liveList'] = array();
    $livelists['videoList'] = $livelist;
    $livelists['keyword'] = $keyword;
    $livelists['type'] = $type;
    $livelists['total'] = $res['total'];
    return $livelists;
}

/**
 * 搜索
 * @param int $uid
 * @param string $encpass
 * @param string $keyword
 * @param int $type
 * @param object $db
 * @return array
 */
function search($uid, $encpass, $keyword, $type, $page, $size, $conf, $db, $client)
{
    $livelists = $livelist = array();
    if ($type == 0) {
        $live = getLiveListbyKeyword($keyword, $type, $page, $size, $conf, $db); //直播
        $AuchorLive = getAnchorLivebyKeyword($keyword, $type, $page, $size, $conf, $db, $client); //主播直播
        $video = getVideobyKeyword($keyword, $type, $page, $size, $conf, $db); //录像数
        $livelists = array(
            'liveList' => $live['liveList'] ? $live['liveList'] : array(),
            'anchorList' => $AuchorLive['anchorList'] ? $AuchorLive['anchorList'] : array(),
            'videoList' => $video['videoList'] ? $video['videoList'] : array(),
            'allCount' => ($live['total']) + ($AuchorLive['total']) + ($video['total']),
            'liveCount' => $live['total'] ? $live['total'] : "0",
            'anchorLiveCount' => $AuchorLive['total'] ? $AuchorLive['total'] : "0",
            'videoCount' => $video['total'] ? $video['total'] : "0",
            'keyword' => $keyword,
            'type' => $type
        );
    }
    if ($type == 1) {
        $livelists = getLiveListbyKeyword($keyword, $type, $page, $size, $conf, $db); //直播
    }
    if ($type == 2) {
        $livelists = getAnchorLivebyKeyword($keyword, $type, $page, $size, $conf, $db,$client); //主播直播
    }
    if ($type == 3) {
        $livelists = getVideobyKeyword($keyword, $type, $page, $size, $conf, $db);
    }
    return $livelists ? $livelists : array();
}

/**
 * 推荐直播 [现在是取最新开播的8个直播]
 * @param type $conf
 * @param type $db
 * @return array
 */
function getRecommend($conf, $db)
{
    $list = array();
//    $res = $db->field('liveid,gameid,gametid,gamename,uid,title,ctime,poster,upcount,orientation')->where("status=" . LIVE)->order("ctime DESC")->limit(8)->select('live');
    $res = $db->field('liveid,gameid,gametid,gamename,uid,title,ctime,poster,upcount,orientation')->where(' status= ' . LIVE . ' and 1 group by uid')->order("liveid DESC")->limit(8)->select('live');
    if ($res) {
        foreach ($res as $iv) {
            $tmp[$iv['uid']] = $iv;
        }
        $liveids = array_keys($tmp);
        $liveuser = batchGetLiveRoomUserCount(implode(',', $liveids), $db); //获取在线用户数
        $userInfo = getUserNicks($liveids, $db);
        foreach ($res as $v) {
            $temp['liveID'] = $v['liveid'];
            $temp['uid'] = $v['uid'];
            $temp['gameName'] = $v['gamename'];
            $temp['title'] = $v['title'];
            $temp['orientation'] = $v['orientation'];
            if ($v['poster']) {
                $temp['poster'] = $v['poster'] ? $conf['domain-lposter'] . '/' . $v['poster'] : '';
                $temp['ispic'] = '1';
            } else {
                $temp['poster'] = CROSS;
                $temp['ispic'] = '0';
            }
            $temp['nick'] = array_key_exists($v['uid'], $userInfo) ? $userInfo[$v['uid']] : '';
            $temp['stime'] = strtotime($v['ctime']);
            $temp['viewCount'] = array_key_exists($v['uid'], $liveuser) ? $liveuser[$v['uid']] : rand(10, 100);
            array_push($list, $temp);
        }
    }
    return $list;
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';
$client = isset($_POST['client']) ? ($_POST['client']) : '';
$type = isset($_POST['type']) ? (int) ($_POST['type']) : 0;
$page = isset($_POST['page']) ? (int) ($_POST['page']) : 1;
$size = isset($_POST['size']) ? (int) ($_POST['size']) : 4;
if (empty($keyword)) {
    error2(-987,2);
}
$keyword = urldecode($keyword);
$keyword = filterData($keyword);
$res = search($uid, $encpass, $keyword, $type, $page, $size, $conf, $db, $client);
if (empty($res['liveList']) && empty($res['anchorList']) && empty($res['videoList'])) {
    if (empty($client)) {
        $recommend = getRecommend($conf, $db);
        $res['recommend'] = $recommend;
    }
    render_json($res);
} else {
    render_json($res);
}