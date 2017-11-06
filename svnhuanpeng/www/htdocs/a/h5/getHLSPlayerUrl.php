<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/8/22
 * Time: 上午10:17
 */

include '../../init.php';
include_once INCLUDE_DIR.'Anchor.class.php';

$db = new DBHelperi_huanpeng();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$luid = isset($_GET['luid']) ? (int)$_GET['luid'] : 0;

Var_dump($_GET);

if(!$luid) error(-1111);

$anchorHelp = new AnchorHelp($luid);

if(!$anchorHelp->isAnchor()){
    error(-1112);
}

if(!$liveid = $anchorHelp->isLiving()){
    error(-1113);
}

$ret = get_hls_name(live_stream_name($liveid, $db), $conf);
$hls = handleRet($ret, $conf, $liveid, $db);
if($hls)
    exit(json_encode(array('playUrl'=>$hls)));
else
    error(-1114);

function handleRet($ret, $conf, $liveid, $db){
    if(!$ret){
        return false;
    }
    echo $ret;
    $ret = explode(' ', $ret);
    if($ret[0] == '[+OK]'){
        if($ret[2] && $ret[2] == 'Delay'){
            return handleRet(get_hls_name(live_stream_name($liveid, $db),$conf, $ret[1]), $conf, $liveid,$db);
        }else{
            $hls = $ret[1];
            return getM3U8List($hls, $conf);
        }
    }else if($ret[0]=='[-Error]'){
        return false;
    }
}


function live_stream_name($liveid, $db){
    $sql = "select stream from live where liveid=$liveid";
    $res = $db->query($sql);
    $row = $res->fetch_assoc();
    return $row['stream'];
}

function get_hls_name($stream, $conf, $id=0){
    $stream = base64_encode($stream);
    $server = 'http://'.$conf['hls-server'];
    $path =  $id!=0 ?  '/playstreamwithid?StreamName='.$stream."&id=$id" : '/playstream?StreamName=' . $stream;
    $url = $server.$path;

    return file_get_contents($url);
}

function getM3U8List($hls, $conf){
    $video = '/playlist.m3u8';
    $audio = '/audio_playlist.m3u8';
    $pre = 'http://'.$conf['hls-server']."/$hls";
    return array('video'=>$pre.$video, 'audio'=>$pre.$audio);
}