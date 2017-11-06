<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/1/10
 * Time: 11:34
 */

include '/usr/local/huanpeng/include/init.php';
include (INCLUDE_DIR.'wcSDK/vendor/autoload.php');
include_once (INCLUDE_DIR.'wcSDK/src/Wcs/PersistentFops/Fops_huanpeng.class.php');
include (INCLUDE_DIR.'videoHelper.class.php');


use \Wcs\PersistentFops\Fops_huanpeng;
use \Wcs\Config;
use \Wcs\MgrAuth;

define('WCS_VIDEO_MERGING',0);//正在合并
define('WCS_OPT',1);//合并操作
define('SLEEP_INTERVAL', 1);//延时1秒
define('NOTIFY_URL',WEB_ROOT_URL.'api/server/mergeVideoCallBack.php');

echo $GLOBALS['env'];
if($GLOBALS['env']=='DEV')
    define('LOG_FILE', '/data/logs/saverecord.log');
else
    define('LOG_FILE', '/data/logs/saverecord.log');

/*$msg = array(
    'title'=>'录像生成成功',
    'notAuto'=>'您的直播视频“{gamename}-{title}”已生成，可以到我的空间发布哦～',
    'auto'=>'您的直播视频“{gamename}-{title}”已生成并发布成功！'
);*/

/*********************** FUNCTIONS **********************/

function get_timedate($tm=null)
{
    if (!$tm) $tm = time();
    return date( "Y-m-d H:i:s", $tm );
}

function _my_log($msg)
{
    $pid = '['.getmypid().']';
    $tm = get_timedate();
    $msg = $pid.'['.$tm.'] '.$msg;
    return file_put_contents(LOG_FILE, $msg."\n", FILE_APPEND);
}

/*function reporter($msg = array()){
    //TODO:report the error
    $msg = http_build_query($msg);
    return url_get_contents(REPORT_URL.'?'.$msg);
}*/

/********************** MAIN ***********************/
$vinstance = videoHelper::getInstance();
$ak = Config::WCS_ACCESS_KEY;
$sk = Config::WCS_SECRET_KEY;
$auth = new MgrAuth($ak, $sk);
while (true)
{
    //get liveid for saving
    $liveidr = $vinstance->getVId();
    if (DEBUG) _my_log('got id: '.$liveidr);
    $liveid = (int)$liveidr;
    if ($liveid<0)
    {
        _my_log('get liveid failed with result:'.$liveidr);
		sleep(SLEEP_INTERVAL);
        continue;
    }
    if ($liveid==0)
    {
        sleep(SLEEP_INTERVAL);
        continue;
    }

    // get live info
    $content = $vinstance->getKeysListByLive($liveid);
    //$content = $vinstance->getKeysListByLive(3520);
    if (DEBUG) _my_log('get task keys liveid:'.$liveid.' result:'.$content);
    $list = json_decode($content, true);
    if (!is_array($list['keys']) or count($list['keys'])==0)
    {
        $errStr = 'get task keys failed with result:'.$content;
        _my_log($errStr);
        $vinstance->report(array('liveid'=>$liveid,'type'=>1));
        continue;
    }
    //var_dump(count($list['keys']));
    //merge file
    $list['liveid'] = $liveid;
    $client = new Fops_huanpeng($auth, $list['bucket']);
    //多个文件拼接转码
    if(count($list['keys'])>1)
        $mergeret = $client->merge($list, NOTIFY_URL);
    //单个文件只转码
    else
        $mergeret = $client->transcode($list, NOTIFY_URL);
    //var_dump($mergeret);
    $mergeret = json_decode($mergeret,'true');
    if(!isset($mergeret['persistentId'])){
        $errStr = 'merge failed:'.$mergeret['message'];
        _my_log($errStr);
        $vinstance->report(array('liveid'=>$liveid,'type'=>4));
        //echo "------视频拼接失败----\n";
        continue;
    }
    //echo "------视频拼接成功----\n";
    //todo report
	_my_log('start merge');
    //todo record
    $saveAsFile = strtolower($GLOBALS['env'])."/v/{$list['liveid']}.mp4";

    $mdata = array(
        'taskid'=>$mergeret['persistentId'],
        'liveid'=>$liveid,
        'opt'=>WCS_OPT,
        'status'=>WCS_VIDEO_MERGING,
        'bucket'=>$list['bucket'],
        //'vname'=>"{$list['liveid']}.mp4"
        'vname'=>$saveAsFile
    );

    $vinstance->recordOpt($mdata);
	_my_log("$liveid complete!");
	sleep(SLEEP_INTERVAL);
	unset($client);
    continue;
}
