<?php

include '../init.php';
include INCLUDE_DIR . 'LiveRoom.class.php';
/**
 * 获取直播列表
 * @author yandong@6rooms.com
 * @copyright 6.cn
 * @version 1.0.4  */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();
static $keepwhere = '';

/**
 * 获取max直播id
 * @param object $db
 * @return string
 */
function getMaxLiveId($db) {
    $res = $db->field('max(liveid) as maxlid')->where('status=' . LIVE . '')->select('live');
//    $res = $db->field('max(liveid) as maxlid')->select('live');
    return $res[0]['maxlid'] ? $res[0]['maxlid'] : 0;
}

/**
 * 获取直播总数
 * @param int $liveid
 * @param object $db
 * @return string
 */
function getTotalCount($where, $db) {
    $res = $db->field('count(*) as count')->where('status=' . LIVE . '' . $where . '')->select('live');
//    $res = $db->field('count(*) as count')->where( $where )->select('video');
    return $res[0]['count'];
}

/**
 * 剩余元素数量
 * @param int $liveid
 * @param object $db
 * @return string
 */
function getLeftCount($lastliveid, $where, $db) {
    $res = $db->field('count(*) as count')->where('status=' . LIVE . ' and liveid <' . $lastliveid . '' . $where . '')->select('live');
//    $res = $db->field('count(*) as count')->where('liveid <' . $lastliveid . '' . $where . '')->select('live');
    return $res[0]['count'];
}

//        var_dump(getLeftCount(410,$db));exit;
/**
 * 获取直播列表
 * @param int $gametid
 * @param int $gameid
 * @param int $uid
 * @param int $lastID
 * @param int $page
 * @param int $size
 * @param object $db
 * @return array
 */
function getLiveLists($gametid, $gameid, $uid, $lastID, $page, $size, $db) {
    $where = '';
    if ($gametid) {
        $gametid = checkInt($gametid);
        $where .=' and gametid=' . $gametid . '';
    }
    if ($gameid) {
        $gameid = checkInt($gameid);
        $where .=' and gameid=' . $gameid . '';
    }
    if ($uid) {
        $uid = checkInt($uid);
        $where .=' and uid=' . $uid . '';
    }
    if ($page) {
        $count = $db->field('count(*) as numb')
                ->where('status=' . LIVE . '' . $where . '');
//                ->where($where)->select('live');
        $page = returnPage($count[0]['numb'], $size, $page);
        $rows = $db->field('liveid,gameid,gametid,gamename,uid,title,ctime,poster,upcount,orientation')
                        ->where('status=' . LIVE . '' . $where . '')
//                ->where($where)
                        ->order('liveid desc')->limit($page, $size)->select('live');
    } else {
        if ($lastID) {
            $lastID = checkInt($lastID) - 1;
            $rows = $db->field('liveid,gameid,gametid,gamename,uid,title,ctime,poster,upcount,orientation')
                            ->where('status=' . LIVE . ' and liveid <= ' . $lastID . ' ' . $where . '')
//                            ->where('liveid <= ' . $lastID . ' ' . $where . '')
                            ->order('liveid desc')->limit($size)->select('live');
        } else {
            $lastID = getMaxLiveId($db);
            if ($lastID) {
                $rows = $db->field('liveid,gameid,gametid,gamename,uid,title,ctime,poster,upcount,orientation')
                                ->where('status=' . LIVE . ' and liveid <= ' . $lastID . '' . $where . '')
//                            ->where('liveid <= ' . $lastID . '' . $where . '')
                                ->order('liveid desc')->limit($size)->select('live');
            } else {
                $rows = array();
            }
        }
    }

    return array('rows' => $rows, 'where' => $where);
}

/**
 * start
 */
$gametid = isset($_POST['gameTypeID']) ? trim($_POST['gameTypeID']) : '';
$gameid = isset($_POST['gameID']) ? trim($_POST['gameID']) : '';
$uid = isset($_POST['userID']) ? trim($_POST['userID']) : '';
$lastID = isset($_POST['lastID']) ? trim($_POST['lastID']) : '';
$size = isset($_POST['size']) ? trim($_POST['size']) : 8;
$page = isset($_POST['page']) ? trim($_POST['page']) : '';
//必须参数验证
$row = getLiveLists($gametid, $gameid, $uid, $lastID, $page, $size, $db);
$arr = $data = array();
if ($row['rows']) {
    foreach ($row['rows'] as $rk => $rv) {
        $arr['liveID'] = $rv['liveid'];
        $arr['gameID'] = $rv['gameid'];
        $arr['gameTypeID'] = $rv['gametid'];
        $arr['gameName'] = $rv['gamename'];
        $arr['anchorUserID'] = $rv['uid'];
        $arr['liveTitle'] = $rv['title'];
        $arr['liveStartTime'] = ($rv['ctime']) ? strtotime($rv['ctime']) : '';
        if ($rv['poster']) {
            $arr['posterURL'] = "http://" . $conf['domain-img'] . '/' . $rv['poster'];
            $arr['ispic'] = '1';
        } else {
            $arr['posterURL'] = CROSS;
            $arr['ispic'] = '0';
        }
        $arr['angle'] = $rv['orientation'];
        $liveroom = new LiveRoom($rv['uid'], $db);
        $arr['viewerCount'] = $liveroom->getRoomUserCount();
        $autheInfo = getUserInfo($rv['uid'], $db);
        $arr['anchorNickName'] = $autheInfo[0]['nick'] ? $autheInfo[0]['nick'] : '';
        $arr['anchorUserPicURL'] = $autheInfo[0]['pic'] ? "http://" . $conf['domain-img'] . "/" . $autheInfo[0]['pic'] : DEFAULT_PIC;
        $liveid = $rv['liveid'];
        array_push($data, $arr);
    }
    if ($data) {
        if ($page) {
            $countname = 'allCount';
            $count = getTotalCount($row['where'], $db);
        } else {
            $lastarray = end($data);
            $lastliveid = $lastarray['liveID'];
            $countname = 'leftCount';
            $count = getLeftCount($lastliveid, $row['where'], $db);
        }
        exit(jsone(array("liveList" => $data, $countname => $count)));
    }
} else {
    exit(jsone(array('liveList' => '', 'allCount' => 0)));
}
