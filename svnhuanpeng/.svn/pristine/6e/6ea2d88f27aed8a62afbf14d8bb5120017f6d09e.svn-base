<?php

include '../../init.php';
require(INCLUDE_DIR . 'LiveRoom.class.php');
/**
 * 推流成功回调
 * author by  Dylan
 * date 2017-01－07 14:15
 */
$db = new DBHelperi_huanpeng();
function makeNewSign($stream, $ip, $tm, $skey)
{
    $str = $stream . '_' . $ip . '_' . $skey . '_' . $tm;
    return md5(toString($str));
}

/**
 * start
 */
$ip = isset($_GET['ip']) ? trim($_GET['ip']) : '';
$stream = isset($_GET['stream']) ? trim($_GET['stream']) : '';
$node = isset($_GET['node']) ? trim($_GET['node']) : '';
$domain = isset($_GET['domain']) ? trim($_GET['domain']) : '';
$path = isset($_GET['path']) ? trim($_GET['path']) : '';
$tm = isset($_GET['tm']) ? trim($_GET['tm']) : '';
$sign = isset($_GET['sign']) ? trim($_GET['sign']) : '';
Log_for_net('调用推流成功接口', array('come'=>'come'), $db);
//校验参数
if (empty($ip) || empty($stream) || empty($node) || empty($domain) || empty($path) || empty($tm) || empty($sign)) {
    Log_for_net('推流成功:缺少参数', $_GET, $db);
    echo 0;
    exit;
}
$stream = filterWords($stream);
//校验秘钥
$make = makeNewSign($stream, $ip, $tm, STREAM_CALLBACK_SECRET);
if ($make !== $sign) {
    Log_for_net('推流成功:秘钥不一致', array('net'=>$sign,'huan'=>$make), $db);
    echo 0;
    exit;
}
//获取直播详情
$res = getLiveInfoByStream($stream, $db);
if (false !== $res) {
    if (empty($res)) {
        Log_for_net('推流成功:无对应直播', $_GET, $db);
        echo 1;
        exit;
    } else {
//发送直播开始消息
        $liveroom = new LiveRoom($res[0]['uid'], $db);
        if (!$liveroom) {
            Log_for_net('推流成功:无对应直播间', $_GET, $db);
        }
        $liveroom->start($res[0]['liveid']);
        updateStreamRecordStatus($stream,1,$db);//1开始
        Log_for_net('推流成功：成功!', array('res'=>$res,'stream'=>$stream), $db);
        echo 1;
        exit;
    }
} else {
    //写日志记录
    Log_for_net('推流成功：错误!', array('res'=>$res), $db);
    echo 1;
    exit;
}

