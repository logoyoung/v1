<?php

/**
 * 获取录像详情
 * data 2016-04-05 17:17
 * author yandong@6rooms.com
 */
include '../../../include/init.php';
require(INCLUDE_DIR . 'Anchor.class.php');
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$videoid = isset($_POST['videoID']) ? trim($_POST['videoID']) : '';
if (empty($videoid)) {
    error2(-4013, 2);
}
$db = new DBHelperi_huanpeng();
$flag = false;
if ($uid && $encpass) {
    $uid = checkInt($uid);
    $encpass = checkStr($encpass);
    $code = checkUserState($uid, $encpass, $db);
    if ($code !== true) {
        error2(-4067, 2);
    }

    $flag = true;
}

/**
 * 获取录像详情
 * @param int $videoid
 * @param object $db
 * @return array
 */
function getVideoInfoByVid($videoid, $db)
{
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
function getAuthorVideoCount($uid, $db)
{
    $res = $db->field('count(*) as vcount')->where("uid=$uid and status=" . VIDEO)->select('video');
    return $res ? $res[0]['vcount'] : 0;
}

/**
 * 获取主播等级
 * @param int $uid
 * @param object $db
 * @return string
 */
function getAuthorLevelByUid($uid, $db)
{
    $res = $db->field('level')->where("uid=$uid")->select('anchor');
    return $res ? $res[0]['level'] : 0;
}

/**
 * 获取主播是否在直播
 * @param int $uid
 * @param object $db
 * @return int
 */
function getAuthorIsLive($uid, $db)
{
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
function getUserIsUpVideo($videoid, $uid, $db)
{
    $res = $db->where("uid=$uid and videoid=$videoid")->select('isupvideo');
    return $res ? 1 : 0;
}

$row = getVideoInfoByVid($videoid, $db);
if (!$row) {
    error2("-2017");
} else {
    $arr = array();
    $huanVlist=explode(',',HUANPENG_VIDEO);
    $arr['videoID'] = $row['videoid'];
    $arr['gameID'] = $row['gameid'];
    $arr['gameTypeID'] = $row['gametid'];
    $arr['gameName'] = $row['gamename'];
    $arr['viewCount'] = $row['viewcount'];
    $arr['title'] = $row['title'];
    $arr['videoTimeLength'] = $row['length'];
    $arr['videoUploadDate'] = ($row['ctime']) ? strtotime($row['ctime']) : '';
    if ($row['poster']) {
        if(in_array($row['videoid'],$huanVlist)){
            $arr['poster'] = ($row['poster']) ? (DOMAIN_PROTOCOL . $conf['domain-img'] . "/". $row['poster']) : '';
        }else{

            $arr['poster'] = sposter($row['poster']);
        }
        $arr['ispic'] = '1';
    } else {
        $arr['poster'] = CROSS;
        $arr['ispic'] = '0';
    }
    if(in_array($row['videoid'],$huanVlist)){
		if($row['videoid']==14835){//小助手
			$arr['videoUrl']=OFFICIALVIDEO_CLIENT;
		}
		if($row['videoid']==14840){//安卓
			$arr['videoUrl']=OFFICIALVIDEO_ANDROID;
		}
		if($row['videoid']==14845){//IOS
			$arr['videoUrl']=OFFICIALVIDEO_IOS;
		}
    }else{
        $vfile=sfile($row['vfile']);
        $arr['videoUrl'] = ($row['vfile']) ? $vfile : '';
    }
    $arr['orientation'] = $row['orientation'];
    $arr['uid'] = $row['uid'];
    $arr['level'] = getUserLevelByUid($row['uid'], $db);
    $userinfo = getUserInfo($row['uid'], $db); //用户信息
    $arr['nick'] = ($userinfo[0]['nick']) ? $userinfo[0]['nick'] : '';
    $arr['head'] = ($userinfo[0]['pic']) ? DOMAIN_PROTOCOL . $conf['domain-img'] . "/" . $userinfo[0]['pic'] : DEFAULT_PIC;
    $VCount = getAuthorVideoCount($row['uid'], $db);
    $arr['total'] = $VCount;
    $authorLevel = getAuthorLevelByUid($row['uid'], $db);
    $arr['anchorLevel'] = $authorLevel;
    $authorIsLive = getAuthorIsLive($row['uid'], $db);
    $arr['isLiving'] = $authorIsLive;
    $roomid=getRoomIdByUid($row['uid'], $db);
    $arr['roomID'] =$roomid[$row['uid']] ? $roomid[$row['uid']] : 0;
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
    if ($isCertify['identstatus'] == RN_PASS && $isCertify['phonestatus'] == 1) {
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

//获取成功后录像观看次数加一
$sql = "update video set viewcount=viewcount+1
        where videoid=$videoid";
$res = $db->query($sql);
succ($arr);

