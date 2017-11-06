<?php

include '../../init.php';
require(INCLUDE_DIR . 'LiveRoom.class.php');
/**
 * 断流成功回调
 * author by  Dylan
 * date 2017-01－07 14:15
 */
$db = new DBHelperi_huanpeng();
function makeNewSign($stream, $ip, $tm, $skey)
{
    $str = $stream . '_' . $ip . '_' . $skey . '_' . $tm;
    return md5(toString($str));
}

function getCallBackType()
{
    $res = $GLOBALS['env-def'][$GLOBALS['env']];
    if ($GLOBALS['env'] == 'DEV') {
        $search = DOMAIN_PROTOCOL . $res['domain'] . '/main/api/server/liveEndCallBack.php/cdn/ws/';
    } else {
        $search = DOMAIN_PROTOCOL . $res['domain'] . '/api/server/liveEndCallBack.php/cdn/ws/';
    }
    return strstr(str_replace($search, '', DOMAIN_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']), '.php?', true);
}

function updateLiveStime($liveid,$db){
    if(empty($liveid)){
        return false;
    }
    $isStime=$db->field('stime')->where("liveid='$liveid'")->limit(1)->select('live');
    if($isStime[0]['stime'] !== '0000-00-00 00:00:00'){
        return  true;
    }else{
        $res=$db->where("liveid='$liveid'")->update('live',array('stime'=>date('Y-m-d H:i:s',time())));
        if(false !==$res){
            return true;
        }else{
            return false;
        }
    }
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
$type = getCallBackType();
Log_for_net('来源', array('type' => $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']), $db);
Log_for_net('网宿接口地址', array('type' => $type), $db);
if (!in_array($type, array('livestart', 'liveend'))) {
    Log_for_net('推流｜断流成功类型非法', array('type' => $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']), $db);
    echo 0;
    exit;
}
$res = getLiveInfoByStream($stream, $db);
if ($type == 'livestart') {
    if($res){
        updateLiveStime($res[0]['liveid'],$db);//推流成功更新直播开始时间
    }
    $errTitle = '推流成功';
    $lstatus = 1;
} else {
    $errTitle = '断流成功';
    $lstatus = 0;
}
Log_for_net($errTitle.':接受参数', $_GET, $db);
//校验参数
if (empty($ip) || empty($stream) || empty($node) || empty($domain) || empty($path) || empty($tm) || empty($sign)) {
    Log_for_net($errTitle . ':缺少参数', $_GET, $db);
    echo 0;
    exit;
}
$stream = filterWords($stream);
//校验秘钥
$make = makeNewSign($stream, $ip, $tm, STREAM_CALLBACK_SECRET);
if ($make !== $sign) {
    Log_for_net($errTitle.':秘钥不一致', array('net' => $sign, 'huan' => $make), $db);
    echo 0;
    exit;
}
//获取直播详情
if (false !== $res) {
    if (empty($res)) {
        Log_for_net($errTitle.':无对应直播', $_GET, $db);
        echo 1;
        exit;
    } else {
        $liveroom = new LiveRoom($res[0]['uid'], $db);
        if (!$liveroom) {
            Log_for_net($errTitle . ':无对应直播间', $_GET, $db);
        }
        Log_for_net($errTitle . ':更新状态', array('lstatus'=>$lstatus), $db);
        if ($lstatus) {
//            $liveroom->start($res[0]['liveid']);
            $up=updateStreamRecordStatus($res[0]['liveid'],$stream, 1, $db);//1开始
            Log_for_net($errTitle . ':更新结果', array('res'=>$up), $db);
        } else {
           // $liveroom->stop($res[0]['liveid']);
            $up=updateStreamRecordStatus($res[0]['liveid'],$stream,2,$db);//2结束
            Log_for_net($errTitle . ':更新结果', array('res'=>$up), $db);
        }
        Log_for_net($errTitle . '：成功!', array('res' => $res, 'stream' => $stream), $db);
        echo 1;
        exit;
    }
} else {
    //写日志记录
    Log_for_net($errTitle . '：错误!', array('res' => $res), $db);
    echo 1;
    exit;
}

