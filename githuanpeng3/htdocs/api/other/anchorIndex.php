<?php

include '../../../include/init.php';
require(INCLUDE_DIR . 'User.class.php');
require(INCLUDE_DIR . 'LiveRoom.class.php');

use service\live\LiveService;
use service\due\DueTagsService;
use service\due\DueCertService;
use service\room\LiveRoomService;
use service\user\UserDataService;

/**
 * App主播主页
 * date 2016-4-29 15:09
 * author yandong@6rooms.com
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();
$endlist = array();

function getList($luid, $type, $page, $size, $db)
{
    if ($type == 1)
    {//最新
        $vorder = 'videoid';
    }
    if ($type == 2)
    {//最热
        $vorder = 'viewcount'; //视频收藏人数排序
    }
    $checkIsOnLine = getAnchorIsOnLine($luid, $db);
    if ($checkIsOnLine)
    {
        $lives = getAnchorLiveList($luid, $db);
        $videos = getAnchorVideoList($luid, $vorder, $page, $size, $db);
        $newarr = array_merge($lives, $videos);
        $page = returnPage(count($newarr), $size, $page); //校验页数
        $offset = ($page - 1) * $size;
        $res = array_slice($newarr, $offset, $size); //以后分片缓存
    } else
    {
        $videos = getAnchorVideoList($luid, $vorder, $page, $size, $db);
        if ($videos)
        {
            $res = $videos;
        } else
        {
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
function getAnchorLiveList($uid, $db)
{
    $res = $db->field('liveid,uid,gamename,title,ctime,poster,orientation')->where("uid=$uid and status=" . LIVE)->order('liveid DESC')->limit(1)->select('live');
    return $res ? $res : array();
}

/**
 * 获取主播已发布的视频
 * @param int $uid
 * @param object $db
 * @return array
 */
function getAnchorVideoList($uid, $vorder, $page, $size, $db)
{
    if (empty($uid))
    {
        return false;
    }
    $res = $db->field('videoid,uid,gamename,title,ctime,viewcount,poster,vfile,orientation')->where("uid=$uid and status=" . VIDEO)->order("$vorder desc")->limit($page, $size)->select('video');
    return $res ? $res : array();
}

function getLiveAndVideoCount($luid, $db)
{
    $live = $db->field('liveid')->where("uid=$luid and status=" . LIVE)->select('live');
    $video = $db->field('videoid')->where("uid=$luid and status=" . VIDEO)->select('video');
    return count($live) + count($video);
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
if (empty($luid))
{
    error2(-4013);
}
$userobj = new UserHelp($luid, $db);
$userRes = $userobj->getUsers(); //获取头像昵称

$userService = new UserDataService();
$userService->setUid($luid);
$userService->setUserInfoDetail(UserDataService::USER_STATIC_ACTICE_BASE);
$userData    = $userService->getUserInfo();
$realName = $userobj->getRealNameCertifyInfo();
$res = getList($luid, $type, $page, $size, $db);
if ($page == 1)
{//只有第一页才会返回
    $tmp['luid'] = $luid;
    $tmp['nick'] = $userData['nick'];
    if ($realName['status'] == RN_PASS)
    {
        $tmp['isAnchor'] = '1';
    } else
    {
        $tmp['isAnchor'] = '0';
    }
    $tmp['anchorLevel'] = getAnchorLevel($luid, $db);
    $tmp['level'] = $userData['level'];
    $tmp['head'] = $userData['pic']; // ? "http://" . $conf['domain-img'] . "/" . $userRes['pic'] : DEFAULT_PIC;
    if ($uid)
    {
        $isFollow = isOneFollowOne($uid, $luid, $db);
        $tmp['isFollow'] = $isFollow ? "1" : "0";
    } else
    {
        $tmp['isFollow'] = "0";
    }
    $followCount = batchGetFansCount($luid, $db);
    $tmp['followCount'] = isset($followCount[$luid]) ? $followCount[$luid] : "0";
    $room = getRoomIdByUid($luid, $db);
    $tmp['roomID'] = array_key_exists($luid, $room) ? $room[$luid] : 0;
}
$obj = new LiveRoom(1);
if ($res)
{
    foreach ($res as $v)
    {
//        $tmptlist['uid'] = $v['uid'];
//        $tmptlist['head'] = $userRes['pic'] ? $userRes['pic'] : 'http://dev.huanpeng.com/main/static/img/48x48coloruserface.png';
//        $tmptlist['nick'] = $userRes['nick'];
        $tmptlist['title'] = $v['title'];
        $tmptlist['gameName'] = $v['gamename'];
        $tmptlist['orientation'] = $v['orientation'];
        $tmptlist['stime'] = strtotime($v['ctime']);
        if (isset($v['liveid']))
        {
            $tmptlist['lvid'] = $v['liveid'];
            $tmptlist['vtype'] = 1;
            $tmptlist['poster'] = LiveService::getPosterUrl($v['poster']);
            if (LiveService::slaveIsLiving($v['uid']) == LiveService::PLAY_TYPE_02)
            {
                $subPoster = LiveService::getSlaveDataByLiveId($v['liveid']);
                $tmptlist['subPoster'] = isset($subPoster[$v['liveid']]['poster']) ? $subPoster[$v['liveid']]['poster'] : '';
            } else
            {
                $tmptlist['subPoster'] = '';
            }
            
            $liveRoomService    = new LiveRoomService();
            $liveUser           = $liveRoomService->setLuid($v['uid'])->getLiveUserCountFictitious();
            $tmptlist['userCount'] = $liveUser ? $liveUser : 0;
        }
        if (isset($v['videoid']))
        {
            $tmptlist['lvid'] = $v['videoid'];
            $tmptlist['vtype'] = 2;
            if ($luid == 1570)
            {
                $tmptlist['poster'] = LiveService::getPosterUrl($v['poster']);
            } else
            {
                $tmptlist['poster'] = sposter($v['poster']);
            }
            $tmptlist['userCount'] = $v['viewcount'];
            $tmptlist['videoUrl'] = sfile($v['vfile']);
        }
        array_push($endlist, $tmptlist);
    }
    $tmp['list'] = $endlist;
} else
{
    $tmp['list'] = array();
}
$total = getLiveAndVideoCount($luid, $db);
$page = returnPage($total, $size, $page); //当前页数
$pageTotal = ceil($total / $size);
$list = array('list' => $tmp, 'total' => $total, 'page' => $page, 'pageTotal' => $pageTotal);
/**
 * 陪玩信息拉去
 * ---------
 *
 * @author yalongSun <yalong2017@6.cn>
 */
if (isset($_POST['type']) && $_POST['type'] == 1) {
    $tagObj = new DueTagsService();
    $redis = new RedisHelp();
    $data = $tagObj->getUserTagsByUid($luid);
    //检验 redis中是否已经生成tag,没有生成则去库里拉去最近一条
//     if(empty($data)){
//         $data = $tagObj->getLastSqlByUid($luid);
//     }
    $userTags = $tagObj->getTagsByids($data);
    if(!empty($userTags)){
        foreach ($data as $vo){
            foreach ($userTags as $v){
                if($vo == $v['id']){
                    $arrTags[] = $v;
                }
            }
        }
    }else $arrTags = [];
    
    $list['tags'] = !empty($arrTags) ? $arrTags : [];
    $list['is_cert'] = 0;
    $certObj = new DueCertService();
    $certObj->setUid($luid);
    $data = $certObj->getSkillByUid();
    $switch = array_column($data,"switch");
    if(in_array( 1 , $switch)) $list['is_cert'] = 1;
}
//----------------------------------------------------

succ($list);
