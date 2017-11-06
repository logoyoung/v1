<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/1/17
 * Time: 10:05
 */

include '../../init.php';
/**
 * 直播结束回调入库
 * date 2016-05-09 14:35
 * author yandong@6rooms.com
 * copyright 6.cn version 0.0
 */
$db = new DBHelperi_huanpeng();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];


function get_Video_merge_recoed($posterid, $db)
{
    if (empty($posterid)) {
        return false;
    }
    $res = $db->field('liveid')->where("posterid='$posterid'")->limit(1)->select('video_merge_record');
    if (false !== $res) {
        if (!empty($res)) {
            return $res[0]['liveid'];
        } else {
            return array();
        }
    } else {
        return false;
    }
}

function set_Video_merge_recoed_status($posterid, $db)
{
    if (empty($posterid)) {
        return false;
    }
    $res = $db->where("posterid='$posterid'")->update('video_merge_record', array('status' => 3));
    if (false !== $res) {
        if (!empty($res)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}


function update_video_poster($liveid, $poster, $db)
{
    if (empty($liveid) || empty($poster)) {
        return false;
    }
    $res = $db->where("liveid='$liveid'")->update('video', array('poster' => $poster));
    if (false !== $res) {
        if (!empty($res)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}


/**
 * start
 */
$body = @file_get_contents('php://input');
Log_for_net('截图回调', array('body' => json_decode(base64_decode($body), true)), $db);
if (empty($body)) {
    echo 0;
    exit;
} else {
    $unbody = json_decode(base64_decode($body), true);
    $posterid = $unbody['id'];
    $poster = str_replace('http://fvod.huanpeng.com'. '/', '', urldecode($unbody['items'][0]['url']));
    $liveid = get_Video_merge_recoed($posterid, $db);
    $up = update_video_poster($liveid, $poster, $db);
    set_Video_merge_recoed_status($posterid, $db);
    if ($up) {
        $liveinfo = getLiveInfoByUid($liveid, $db);
        if ($liveinfo) {
            $uid = $liveinfo[0]['uid'];
            $title = '系统消息';
            $message = "您的直播视频“" . $liveinfo[0]['gamename'] . '-' . $liveinfo[0]['title'] . "”已生成，可以到我的空间发布哦～";
            sendMessages($uid, $title, $message, 0, $db);
        }
    }
    echo 1;
}
