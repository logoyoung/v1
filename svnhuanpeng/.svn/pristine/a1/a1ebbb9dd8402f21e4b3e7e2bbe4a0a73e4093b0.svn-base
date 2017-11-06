<?php

include '../init.php';
/**
 * 获取相似录像
 * data 2016-05-17 14:26
 * author yandong@6rooms.com
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();

/**
 * 获取录像详情
 * @param int $videoid //录像id
 * @param object $db
 * @return array
 */
function getVideoInfoByVideoId($videoid, $db) {
    if (empty($videoid)) {
        return false;
    }
    $videoid = checkInt($videoid);
    $res = $db->field('gameid,gametid')->where("videoid=$videoid")->select('video');
    if ($res) {
        $resbygid = $db->where('gameid=' . $res[0]['gameid'] . ' and videoid !=' . $videoid . ' and status=' . VIDEO)->select('video');
        $resbytid = $db->where('gametid=' . $res[0]['gametid'] . ' and videoid !=' . $videoid . ' and status=' . VIDEO)->select('video');
        $result = array_merge($resbygid, $resbytid);
    } else {
        $result = array();
    }
    return $result;
}

/**
 * start
 */
$videoId = isset($_POST['videoId']) ? (int) ($_POST['videoId']) : '';
$size = isset($_POST['size']) ? (int) ($_POST['size']) : 6;
if (empty($videoId)) {
    error(-4013);
}
$res = getVideoInfoByVideoId($videoId, $db); //以后加缓存
if ($res) {
    $start = rand(0, (count($res) - $size));
    $cutres = array_slice($res, $start, $size); //随机获取六条  
} else {
    $cutres = array();
}

if ($cutres) {
    $data = array();
    $videoid = array_column($cutres, 'videoid');
    $comment = getVideoCommentCountByVideoId($videoid, $db);
    foreach ($cutres as $vdk => $row) {
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
//        $arr['posterURL'] = ($row['poster']) ? ("http://" . $conf['domain-img'] . $row['poster']) : '';
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
        $arr['commentCount'] = array_key_exists($row['videoid'], $comment) ? $comment[$row['videoid']] : '0';
        $score = getVideoRate($row['videoid'], $db);
        $arr['viewerRate'] = $score ? $score : '';
        $videoid = $row['videoid'];
        array_push($data, $arr);
    }
    if ($data) {
        exit(jsone(array("list" => $data)));
    } else {
        exit(jsone(array('list' => '')));
    }
} else {
    exit(jsone(array('list' => '')));
}
