<?php

/**
 * 获取录像详情
 * data 2016-04-05 17:17
 * author yandong@6rooms.com
 */
include '../init.php';
require(INCLUDE_DIR . 'Anchor.class.php');
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$videoid = isset($_POST['videoID']) ? trim($_POST['videoID']) : '';

$db = new DBHelperi_huanpeng();
$flag = false;
if ($uid && $encpass) {
    $uid = checkInt($uid);
    $encpass = checkStr($encpass);
    $code = checkUserState($uid, $encpass, $db);
    if ($code !== true)
        error($code);
    $flag = true;
}

/**
 * 获取录像详情
 * @param int $videoid
 * @param object $db
 * @return array
 */
function getVideoInfoByVid($videoid, $db) {
    $videoid = checkInt($videoid);
    $res = $db->where("videoid=$videoid and status=" . VIDEO)->select('video');
    return $res ? $res[0] : array();
}

/**
 * 获取主播的所有录像数
 * @param int $uid
 * @param object $db
 * @return string
 */
function getAuthorVideoCount($uid, $db) {
    $res = $db->field('count(*) as vcount')->where("uid=$uid and status=" . VIDEO)->select('video');
    return $res ? $res[0]['vcount'] : 0;
}

/**
 * 获取主播等级
 * @param int $uid
 * @param object $db
 * @return string
 */
function getAuthorLevelByUid($uid, $db) {
    $res = $db->field('level')->where("uid=$uid")->select('anchor');
    return $res ? $res[0]['level'] : 0;
}

/**
 * 获取主播是否在直播
 * @param int $uid
 * @param object $db
 * @return int
 */
function getAuthorIsLive($uid, $db) {
    $res = $db->where("uid=$uid and status=" . LIVE)->select('live');
    return $res ? 1 : 0;
}

/**
 * 判断用户是否点赞
 * @param int $videoid
 * @param int $uid
 * @param object $db
 * @return int
 */
function getUserIsUpVideo($videoid, $uid, $db) {
    $res = $db->where("uid=$uid and videoid=$videoid")->select('isupvideo');
    return $res ? 1 : 0;
}

$row = getVideoInfoByVid($videoid, $db);
if (!$row) {
    error("-2017");
} else {
    $arr = array();
    $arr['videoID'] = $row['videoid'];
    $arr['gameID'] = $row['gameid'];
    $arr['gameTypeID'] = $row['gametid'];
    $arr['gameName'] = $row['gamename'];
    $arr['totalViewCount'] = $row['viewcount'];
    $arr['videoTitle'] = $row['title'];
    $arr['videoTimeLength'] = $row['length'];
    $arr['videoUploadDate'] = ($row['ctime']) ? strtotime($row['ctime']) : '';
    if ($row['poster']) {
        $arr['posterURL'] = ($row['poster']) ? ("http://" . $conf['domain-img'] . $row['poster']) : '';
        $arr['ispic'] = '1';
    } else {
        $arr['posterURL'] = CROSS;
        $arr['ispic'] = '0';
    }
    $arr['videoPlaybackURL'] = ($row['vfile']) ? ($conf['domain-video'] . $row['vfile']) : '';
    $arr['orientation'] = $row['orientation'];
    $arr['publisherUserID'] = $row['uid'];
    $arr['anchorLevel'] = getUserLevelByUid($row['uid'], $db);
    $userinfo = getUserInfo($row['uid'], $db); //用户信息
    $arr['publisherNickName'] = ($userinfo[0]['nick']) ? $userinfo[0]['nick'] : '';
    $arr['publisherUserPicURL'] = ($userinfo[0]['pic']) ? "http://" . $conf['domain-img'] . "/" . $userinfo[0]['pic'] : DEFAULT_PIC;
    $VCount = getAuthorVideoCount($row['uid'], $db);
    $arr['publisherVideoCount'] = $VCount;
    $authorLevel = getAuthorLevelByUid($row['uid'], $db);
    $arr['publisherLevel'] = $authorLevel;
    $authorIsLive = getAuthorIsLive($row['uid'], $db);
    $arr['publisherIsLiving'] = $authorIsLive;
    $collectCount = getVideoCount($row['videoid'], $db); //获取收藏
    $arr['collectCount'] = $collectCount ? $collectCount : 0;
    $arr['upCount'] = $row['upcount'];
    $arr['fansCount'] = getFansCount($row['uid'], $db); //粉丝
    $videoComment = getVideoCommentCountByVideoId($row['videoid'], $db); //评论总数
    $arr['commentCount'] = !empty($videoComment[$row['videoid']]) ? $videoComment[$row['videoid']] : 0;
    $arr['isUp'] = 0;
    $arr['isCollect'] = 0;
    $arr['isFollow'] = 0;
    //是否验证
    $Anchor = new AnchorHelp($row['uid']);
    $isCertify = $Anchor->getCertifyInfo();
    if ($isCertify['emailstatus'] == EMAIL_PASS && $isCertify['identstatus'] == RN_PASS && $isCertify['phonestatus'] == 1) {
        $arr['isCertify'] = "1";
    } else {
        $arr['isCertify'] = "0";
    }
}
if ($flag) {
    $collectRes = getVideoIsCollect($uid, $videoid, $db);
    $arr['isCollect'] = ($collectRes) ? 1 : 0;
    $FollowRes = isOneFollowOne($uid, $row['uid'], $db);
    $arr['isFollow'] = ($FollowRes) ? 1 : 0;
    $isup = getUserIsUpVideo($row['videoid'], $uid, $db);
    $arr['isUp'] = $isup;
}
$json = json_encode($arr);
echo $json;
//获取成功后录像观看次数加一
$sql = "update video set viewcount=viewcount+1
        where videoid=$videoid";
$res = $db->query($sql);
exit;
