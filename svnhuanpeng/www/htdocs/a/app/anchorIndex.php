<?php

include '../../init.php';
require(INCLUDE_DIR . 'User.class.php');
/**
 * App主播主页
 * date 2016-4-29 15:09
 * author yandong@6rooms.com
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();
$endlist = array();

function getList($uid, $encapass, $luid, $type, $page, $size, $db) {
    if ($type == 1) {//最新
        $vorder = 'videoid';
    }
    if ($type == 2) {//最热
        $vorder = 'viewcount'; //视频收藏人数排序
    }
    $checkIsOnLine = getAnchorIsOnLine($luid, $db);
    if ($checkIsOnLine) {
        $lives = getAnchorLiveList($luid, $db);
        $videos = getAnchorVideoList($luid, $vorder, $size, $db);
        $newarr = array_merge($lives, $videos);
        $page = returnPage(count($newarr), $size, $page); //校验页数
        $offset = ($page - 1) * $size;
        $res = array_slice($newarr, $offset, $size); //以后分片缓存
    } else {
        $videos = getAnchorVideoList($luid, $vorder, $size, $db);
        if ($videos) {
            $res = $videos;
        } else {
            $res = array();
        }
    }
    return $res;
}

/**
 * 获取主播正在直播的直播信息
 * @param int $luid
 * @param object $db
 * @return array
 */
function getAnchorLiveList($uid, $db) {
    $res = $db->field('liveid,uid,gamename,title,ctime,poster,orientation')->where("uid=$uid and status=" . LIVE)->order('liveid DESC')->limit(1)->select('live');
    return $res ? $res : array();
}

/**
 * 获取主播已发布的视频
 * @param int $uid
 * @param object $db
 * @return array
 */
function getAnchorVideoList($uid, $vorder, $size, $db) {
    if(empty($uid)){
        return false;
    }
    $res = $db->field('videoid,uid,gamename,title,ctime,viewcount,poster,vfile,orientation')->where("uid=$uid and status=" . VIDEO)->order("$vorder desc")->limit($size)->select('video');
    return $res ? $res : array();
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) ($_POST['uid']) : '';
$encapass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$luid = isset($_POST['luid']) ? (int) ($_POST['luid']) : '';
$type = isset($_POST['type']) ? (int) ($_POST['type']) : 1;
$page = isset($_POST['page']) ? (int) ($_POST['page']) : 1;
$size = isset($_POST['size']) ? (int) ($_POST['size']) : 5;
if (empty($luid)) {
    error(-4013);
}
$userobj = new UserHelp($luid, $db);
$userRes = $userobj->getUsers(); //获取头像昵称
    $realName = $userobj->getRealNameCertifyInfo();
    $res = getList($uid, $encapass, $luid, $type, $page, $size, $db);
 if($page ==1) {//只有第一页才会返回
     $tmp['luid'] = $luid;
     $tmp['nick'] = $userRes['nick'];
     if ($realName['status'] == RN_PASS) {
         $tmp['ispass'] = '1';
     } else {
         $tmp['ispass'] = '0';
     }
     $tmp['level'] = getAnchorLevel($luid, $db);
     $tmp['userlevel'] = getUserLevelByUid($luid, $db);
     $tmp['pic'] = $userRes['pic']; // ? "http://" . $conf['domain-img'] . "/" . $userRes['pic'] : DEFAULT_PIC;
     if ($uid) {
         $isFollow = isOneFollowOne($uid, $luid, $db);
         $tmp['isFollow'] = $isFollow ? "1" : "0";
     } else {
         $tmp['isFollow'] = "0";
     }
     $followCount = batchGetFansCount($luid, $db);
     $tmp['followCount'] = isset($followCount[$luid]) ? $followCount[$luid] : "0";
 }
if ($res) {
    foreach ($res as $v) {
        $tmptlist['uid'] = $v['uid'];
        $tmptlist['pic'] = $userRes['pic'] ? $userRes['pic'] : 'http://dev.huanpeng.com/main/static/img/48x48coloruserface.png';
        $tmptlist['nick'] = $userRes['nick'];
        $tmptlist['title'] = $v['title'];
        $tmptlist['posterUrl'] = $v['poster'] ? ("http://" . $conf['domain-img'] . "/" . $v['poster']) : '';
        $tmptlist['gname'] = $v['gamename'];
        $tmptlist['angle'] = $v['orientation'];
        $tmptlist['ctime'] = strtotime($v['ctime']);
        if (isset($v['liveid'])) {
            $tmptlist['lvid'] = $v['liveid'];
            $tmptlist['vtype'] = 1;
            $liveUser = getLiveRoomUserCount($luid, $db);
            $tmptlist['vCount'] = $liveUser ? $liveUser : 0;
        }
        if (isset($v['videoid'])) {
            $tmptlist['lvid'] = $v['videoid'];
            $tmptlist['vtype'] = 0;
            $tmptlist['vCount'] = $v['viewcount'];
            $tmptlist['vfile'] = $v['vfile'] ? ($conf['domain-video'] . $v['vfile']) : '';
        }
        array_push($endlist, $tmptlist);
    }
    $tmp['list'] = $endlist;
} else {
    $tmp['list'] = array();
}
exit(json_encode(array('resultList' => $tmp)));
