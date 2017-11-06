<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/1/9
 * Time: 10:27
 */
//define('DEBUG',true);
include (__DIR__.'/../include/init.php');
define('CURL_CMD', '/usr/bin/curl -Ss');
define('UNDOWN',0);
define('DOWNED',1);
define('DST_DIR','/data/tmp/v/');
//log
if($GLOBALS['env']=='DEV') {
    define('LOG_FILE', '/data/logs/wsdown/ws.dev.downvideo.log');
    define('DST_DIR','/data/tmp/v/dev/');
}
else {
    define('LOG_FILE', '/data/logs/wsdown/ws.pro.downvideo.log');
    define('DST_DIR','/data/tmp/v/pro/');
}
define('SLEEP_INTERVAL', 1);
//
function _my_log($msg){
    $pid = '['.getmypid().']';
    $tm = date('Y-m-d H:i:s');
    $msg = $pid.'['.$tm.'] '.$msg;
    return file_put_contents(LOG_FILE, $msg."\n", FILE_APPEND);
}

function _get_task($db){
    if(!$db) return 0;
    //预先获取一个任务id
    $ids = $db->field('id,liveid,url')->where('status='.UNDOWN.' LIMIT 1')->select('video_download_record');
    $ids = isset($ids[0])?$ids[0]:0;
    if(!$ids)
        return 0;
    //上锁
    $db->where("`id`={$ids['id']}")->update('video_download_record',array('status'=>DOWNED));
    if(!$db->affectedRows)//资源被抢占
        return 0;
    else
        return $ids;
}

function _check_file($filename, &$error){
    // TODO: more strict file test
    // file size smaller then 100 bytes
    sleep(SLEEP_INTERVAL);
    if(!is_file($filename))
        return false;
    if (filesize($filename)<=100) {
        $error = file_get_contents($filename);
        return false;
    }
    return true;
}

function _destroy_task($id,$status, $db){
    $db->where("id={$id}")->update('video_download_record',array('status'=>$status,'utime'=>date('Y-m-d H:i:s',time())));
    return $db->affectedRows;
}

/**************************main**************************/
$db = new DBHelperi_huanpeng();
while(true){
    //获取任务
    $ids = _get_task($db);
    if(!$ids) {
        sleep(SLEEP_INTERVAL);
        _my_log("get task id : {$ids}");
        continue;
    }
    $ext = pathinfo($ids['url'],PATHINFO_EXTENSION);
    $dstFile = DST_DIR.date('Ymd',time())."/{$ids['liveid']}.$ext";
    if(!mkdirs(dirname($dstFile))){
        _my_log('create folder failed');
        _destroy_task($ids['id'],UNDOWN,$db);
        continue;
    }
    //获取重定向跳转url
    $cmd = CURL_CMD." -I \"{$ids['url']}\"";
    //echo "$cmd\n";
    $head = `$cmd`;
    $pat = '/^HTTP\/1.+(302)[\s\S]*Location:(.*)\r\n/';
    $n = preg_match($pat, $head, $mat);
    if($mat[1]=='302')
        $ids['url'] = trim($mat[2]);

    //添加防盗链
    $tmp = explode('?',$ids['url']);
    $ids['url'] = $tmp[0];
    $key = basename($ids['url']);
    $key = createSecurityChain($key);
    $ids['url'] .= $key;
    unset($tmp);
    unset($key);

    //var_dump($ids['url']);
    $cmd = CURL_CMD." \"{$ids['url']}\" -o $dstFile";
    //echo "$cmd\n";
    `$cmd`;
    $r = _check_file($dstFile,$err);
    if(!$r){
        _my_log("download {$ids['liveid']} failed, $err");
        _destroy_task($ids['id'],DOWNED,$db);
        continue;
    }
    _my_log("download {$ids['liveid']} success");
    _destroy_task($ids['id'],DOWNED,$db);
    continue;
}