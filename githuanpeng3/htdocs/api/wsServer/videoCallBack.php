<?php

include '../../init.php';
/**
 * 直播结束回调入库
 * date 2016-05-09 14:35
 * author yandong@6rooms.com
 * copyright 6.cn version 0.0
 */
$db = new DBHelperi_huanpeng();


/**同步到直播流记录表中
 * @param $liveid  直播id
 * @param $url  下载地址
 * @param $db
 * @return bool
 */
function addStreamRecord($liveid,$stream, $bucket, $keys, $urls, $length, $db)
{
    if (empty($stream) || empty($liveid)) {
        return false;
    }
    $data = array(
        'liveid'=>$liveid,
        'stream'=>$stream,
        'bucket' => $bucket,
        'keys' => json_encode($keys),
        'urls' => json_encode($urls),
        'length' => $length,
    );
    $res = $db->insert('live_VideoRecord', $data);
    if (false !== $res) {
        Log_for_net('添加成功', array('res' =>$res), $db);
        return true;
    } else {
        return false;
    }
}

function getLiveIdByStream($stream, $db)
{
    if (empty($stream)) {
        return false;
    }
    $res = $db->field('liveid')->where("stream='$stream'")->order('id  desc')->limit(1)->select('liveStreamRecord');
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

function getLiveStstusByLiveId($liveid, $db)
{
    if (empty($liveid)) {
        return false;
    }
    $res = $db->field('status')->where('liveid=' . $liveid)->limit(1)->select('live');
    if (false !== $res) {
        return $res[0]['status'];
    } else {
        return false;
    }
}

function changeVideoSaveQueueStatus($liveid, $db)
{
    if (empty($liveid)) {
        return false;
    }
    $res = $db->where('liveid=' . $liveid)->update('videosave_queue', array('go' => 1));
    Log_for_net('go是否存在', array('res' => $res), $db);
    if (false !== $res) {
        Log_for_net('go成功', array('res' => $res), $db);
        return true;
    } else {
        return false;
    }
}


/**
 * start
 */
$body = @file_get_contents('php://input');
Log_for_net('录像回调', array('body' => base64_decode($body)), $db);
if (empty($body)) {
    echo 0;
    exit;
} else {
    $unbody = json_decode(base64_decode($body),true);
    if (isset($unbody['items'])) {
        Log_for_net('生成录像回调成功', array('stream' => $unbody['items'][0]['streamname']), $db);
        $stream = str_replace('.flv', '', str_replace('liverecord-', '', $unbody['items'][0]['streamname']));
        $bucket = $unbody['items'][0]['bucket'];
//        $ops = $unbody['items']['ops'];
        $keys = $unbody['items'][0]['keys'];
        $urls = $unbody['items'][0]['urls'];
        $length = $unbody['items'][0]['detail'][0]['duration'];
        $stream = filterWords($stream);
        Log_for_net('获取参数', array('stream' => $stream,'legth'=>$length,'key'=>$keys), $db);
        $liveid = getLiveIdByStream($stream, $db);//获取liveid
        Log_for_net('获取直播id', array('liveid'=>$liveid), $db);
        if (false !== $liveid) {
            if($liveid){
                $liveStatus = getLiveStstusByLiveId($liveid, $db);//获取直播状态
                Log_for_net('获取直播状态', array('liveid'=>$liveStatus), $db);
                addStreamRecord($liveid,$stream, $bucket, $keys, $urls, $length, $db);
                if ($liveStatus == LIVE_STOP) {
                    changeVideoSaveQueueStatus($liveid, $db);
                }
            }else{
                echo 1;
                exit;
            }
            echo 1;
            exit;
        }else{
            echo 1;
            exit;
        }
    } else {
        echo 1;
        exit;
    }
}

