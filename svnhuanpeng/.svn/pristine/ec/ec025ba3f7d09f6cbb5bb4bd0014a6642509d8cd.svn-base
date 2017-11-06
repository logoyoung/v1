<?php

include '../init.php';
/**
 * 获取录像列表
 * @author guanlong
 * @copyright 6.cn
 * @version 1.0.3  
 * revise 2015-12-16 by yandong
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();
/* if($GLOBALS['env']=='DEV'){
  $IMG_HOST = "http://".DOMAINNAME_DEV_IMG;
  }else{
  $IMG_HOST = "http://".DOMAINNAME_PRO_IMG;
  }
 */

/**
 * 获取max直播id
 * @param object $db
 * @return string
 */
function getMaxVideoId($db) {
    $res = $db->field('max(videoid) as maxvid')->where('status=' . VIDEO . '')->select('video');
    return $res[0]['maxvid'];
}

/**
 * 获取录像总数
 * @param int $liveid
 * @param object $db
 * @return string
 */
function getTotalCount($where, $db) {
    $res = $db->field('count(*) as count')->where('status=' . VIDEO . '' . $where . '')->select('video');
    return $res[0]['count'];
}

/**
 * 获取剩余录像数
 * @param type $videoid
 * @param type $where
 * @param type $db
 * @return type
 */
function getLeftCount($lastVideoID, $where, $db) {
    $res = $db->field('count(*) as count')->where('status=' . VIDEO . ' and videoid <' . $lastVideoID . '' . $where . '')->select('video');
    return $res[0]['count'];
}

/**
 * 获取录像列表
 * @param int $gametid
 * @param int $gameid
 * @param int $uid
 * @param int $lastID
 * @param int $page
 * @param int $size
 * @param object $db
 * @return array
 */
function getVideoLists($gametid, $gameid, $uid, $db) {
    $where = '';
    if ($gametid) {
        $gametid = checkInt($gametid);
        $where = $where . ' ' . 'and gametid=' . $gametid . '';
    }
    if ($gameid) {
        $gameid = checkInt($gameid);
        $where = $where . ' ' . 'and gameid=' . $gameid . '';
    }
    if ($uid) {
        $uid = checkInt($uid);
        $where = $where . ' ' . 'and uid=' . $uid . '';
    }
    $rows = $db->field('videoid,gameid,gametid,gamename,viewcount,uid,title,length,ctime,poster,vfile,orientation')
                    ->where('status=' . VIDEO . ' ' . $where . '')
                    ->order('ctime desc')->select('video');

    return array('rows' => $rows ? $rows : array(), 'where' => $where);
}

/**
 * start
 */
$gametid = isset($_POST['gameTypeID']) ? (int) ($_POST['gameTypeID']) : '';
$gameid = isset($_POST['gameID']) ? (int) ($_POST['gameID']) : '';
$uid = isset($_POST['userID']) ? (int) ($_POST['userID']) : '';
$size = isset($_POST['size']) ? (int) ($_POST['size']) : '';
$page = isset($_POST['page']) ? (int) ($_POST['page']) : 1;

$results = getVideoLists($gametid, $gameid, $uid, $db);
$count = count($results['rows']);
if (!empty($size)) {
    $page = returnPage($count, $size, $page);
    $results['rows'] = array_slice($results['rows'], ($page - 1) * $size, $size);
}
$arr = $data = array();
if ($results['rows']) {
    foreach ($results['rows'] as $vdk => $row) {
        $arr['videoID'] = $row['videoid'];
        $arr['gameID'] = $row['gameid'];
        $arr['gameTypeID'] = $row['gametid'];
        $arr['gameName'] = $row['gamename'];
        $arr['totalViewCount'] = $row['viewcount'];
        $arr['publisherUserID'] = $row['uid'];
        $author = getUserInfo($row['uid'], $db);
        $arr['publisherNickName'] = ($author[0]['nick']) ? $author[0]['nick'] : '';
        $arr['publisherUserPicURL'] = ($author[0]['pic']) ? "http://" . $conf['domain-img'] . "/" . $author[0]['pic'] : DEFAULT_PIC;
        $arr['videoTitle'] = $row['title'];
        $arr['videoTimeLength'] = $row['length'];
        $arr['videoUploadDate'] = ($row['ctime']) ? strtotime($row['ctime']) : '';
        $arr['videoPlaybackURL'] = ($row['vfile']) ? ($conf['domain-video'] . $row['vfile']) : '';
        $arr['angle'] = $row['orientation'];
        if ($row['poster']) {
            $arr['posterURL'] = "http://" . $conf['domain-img'] . "/" . $row['poster'];
            $arr['ispic'] = '1';
        } else {
            $arr['posterURL'] = CROSS;
            $arr['ispic'] = '0';
        }
        $arr['collectCount'] = getVideoCount($row['videoid'], $db);
        $score = getVideoRate($row['videoid'], $db);
        $comment = getVideoCommentCountByVideoId($row['videoid'], $db);
        $arr['commentCount'] = !empty($comment[$row['videoid']]) ? $comment[$row['videoid']] : 0;
        $arr['viewerRate'] = $score ? $score : '';
        $videoid = $row['videoid'];
        array_push($data, $arr);
    }
    if ($data) {
        exit(jsone(array("videoList" => $data, 'allCount' => $count)));
    } else {
        exit(jsone(array('videoList' => '', 'allCount' => 0)));
    }
} else {
    exit(jsone(array('videoList' => '', 'allCount' => 0)));
}
