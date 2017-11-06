<?php

/**
 * flase 推荐
 * date 2016-05-30 11:01
 * author yandong@6rooms.com
 */
include '../../../include/init.php';
include_once (INCLUDE_DIR . 'Anchor.class.php');
include_once (INCLUDE_DIR . 'lib/LiveRoom.php');

use service\live\LiveService;
use lib\LiveRoom;

$db = new DBHelperi_huanpeng();

$conf = $GLOBALS['env-def'][$GLOBALS['env']];

function live_anchor($type, $uids, $uid, $size, $db)
{
    if ($type)
    {
        $res = $db->field('uid,poster,gamename,orientation')->where("uid !=$uid  and  uid in ($uids)  and status=" . LIVE)->order("rand()")->limit($size)->select('live');
    } else
    {
        $res = $db->field('uid,poster,gamename,orientation')->where("status=" . LIVE)->order("rand()")->limit($size)->select('live');
    }
    if (false !== $res)
    {
        if ($res)
        {
            return $res;
        } else
        {
            return array();
        }
    } else
    {
        return array();
    }
}

function other_anchor($uid, $size, $db)
{
    if ($uid)
    {
        $res = $db->field('uid,poster,gamename,orientation')->where("status in (" . LIVE . "," . LIVE_STOP . "," . LIVE_COMPLETE . ")  and  uid !=$uid group by uid")->order("liveid desc")->limit($size)->select('live');
    } else
    {
        $res = $db->field('uid,poster,gamename,orientation')->where("status in (" . LIVE . "," . LIVE_STOP . "," . LIVE_COMPLETE . ") group by uid")->order("liveid desc")->limit($size)->select('live');
    }
    if (false !== $res)
    {
        if ($res)
        {
            return $res;
        } else
        {
            return array();
        }
    } else
    {
        return false;
    }
}

function gethistoryAnchor($uid, $db)
{
    if (empty($uid))
    {
        return false;
    }
    $res = $db->field('uid')->where("uid = $uid")->select('history');
    if (false !== $res)
    {
        if ($res)
        {
            return $res;
        } else
        {
            return array();
        }
    } else
    {
        return false;
    }
}

function getRecommendList($uid, $size, $db)
{
    $livelist = array();
    if ($uid)
    {//存在uid  
    	//获取 当前用户关注的两个 主播
        $follow = array_column(userFollow($uid, $db), 'uid2'); //获取关注列表
        if ($follow)
        {
            $livelist = live_anchor(1, implode(',', $follow), $uid, $size, $db);
        }
        if (count($livelist) < $size)
        {
            $uids = gethistoryAnchor($uid, $db);      //获取浏览历史
            if ($uids)
            {
                $uids = array_column($uids, 'uid');
                $livelist = live_anchor(1, implode(',', $uids), $uid, $size, $db);
            }
        }

        if (!empty($livelist) && count($livelist) < $size)
        {
            $live = live_anchor(0, '', $uid, 1, $db);
            if ($live)
            {
                $livelist = array_merge($livelist, $live);
            } else
            {
                $flive = other_anchor($livelist[0]['uid'], 1, $db);
                $livelist = array_merge($livelist, $flive);
            }
        } else
        {
            $livelist = live_anchor(0, '', $uid, $size, $db);
            if (count($livelist) < $size)
            {
                if (empty($livelist))
                {
                    $livelist = other_anchor('', $size, $db);
                } else
                {
                    $flive = other_anchor($livelist[0]['uid'], 1, $db);
                    $livelist = array_merge($livelist, $flive);
                }
            }
        }
    } else
    {//不存在uid
        $live = live_anchor(0, '', $uid, $size, $db);
        if (!empty($live) && count($live) < $size)
        {
            $flive = other_anchor($live[0]['uid'], 1, $db);
            if (empty($flive))
            {
                $flive = array();
            }
            $livelist = array_merge($live, $flive);
        } else
        {
            if (empty($live))
            {
                $livelist = other_anchor('', $size, $db);
            } else
            {
                $livelist = $live;
            }
        }
    }
    return $livelist;
}

$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$size = isset($_POST['size']) ? (int) $_POST['size'] : 2;
if ($uid && (int) $uid > 0)
{
    if (!is_numeric($uid))
    {
        error2(-4070);
    }
    $uid = checkInt($uid);
}
$res = getRecommendList($uid, $size, $db);
if ($res)
{
    $list = array();
    $usernick = getUserNicks(array_column($res, 'uid'), $db);
    //$userCount = batchGetLiveRoomUserCount(implode(',', array_column($res, 'uid')), $db); 
    foreach ($res as $k => $v)
    {
    	$liveRoom = new LiveRoom($v['uid']);
    	$usercount = $liveRoom->getLiveRoomUserCountFictitious();
        if ($k == 0)
        {
            $AnchorHelp = new AnchorHelp($v['uid'], $db);
            $roomid = $AnchorHelp->getRoomID();
            $tem['firstPicURL'] = !empty($v['poster']) ? LiveService::getPosterUrl($v['poster']) : CROSS;
            $tem['firstLiveRoomURL'] = WEB_ROOT_URL . $roomid;
            $tem['firstHostName'] = isset($usernick[$v['uid']]) ? $usernick[$v['uid']] : "欢客";
            //$tem['firstAudienceNumber'] = isset($userCount[$v['uid']]) ? $userCount[$v['uid']] : "0";
            
            $tem['firstAudienceNumber'] = $usercount;
            $tem['firstGameName'] = $v['gamename'];
            if (in_array($v['orientation'], array(1, 4)))
            {
                $tem['firstScreenDirection'] = 'horizontal';
            } else
            {
                $tem['firstScreenDirection'] = 'vertical';
            }
        }
        if ($k == 1)
        {
            $AnchorHelp = new AnchorHelp($v['uid'], $db);
            $roomid = $AnchorHelp->getRoomID();
            $temp['secondPicURL'] = !empty($v['poster']) ? LiveService::getPosterUrl($v['poster']) : CROSS;
            //$temp['secondLiveRoomURL'] = WEB_ROOT_URL.'room.php?luid='.$v['uid'];
            $temp['secondLiveRoomURL'] = WEB_ROOT_URL . $roomid;
            $temp['secondHostName'] = isset($usernick[$v['uid']]) ? $usernick[$v['uid']] : "欢客";
            //$temp['secondAudienceNumber'] = isset($userCount[$v['uid']]) ? $userCount[$v['uid']] : "0";
            $temp['secondAudienceNumber'] = $usercount;
            //file_put_contents("/data/logs/yalong_20170526.log",$v['uid']."-".$usercount);
            $temp['secondGameName'] = $v['gamename'];
            if (in_array($v['orientation'], array(1, 4)))
            {
                $temp['secondScreenDirection'] = 'horizontal';
            } else
            {
                $temp['secondScreenDirection'] = 'vertical';
            }
        }
    }
    array_push($list, $tem, $temp);
    exit(json_encode(array('status' => 0, 'content' => array('list' => $list, 'moreLive' => WEB_ROOT_URL . 'LiveHall.php'))));
} else
{
    exit(json_encode(array('status' => 0, 'content' => array('list' => array(), 'moreLive' => ''))));
}



