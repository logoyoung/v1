<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/1/16
 * Time: 16:52
 */

include '/usr/local/huanpeng/include/init.php';
include (INCLUDE_DIR.'wcSDK/vendor/autoload.php');
include_once (INCLUDE_DIR.'wcSDK/src/Wcs/PersistentFops/Fops_huanpeng.class.php');
include (INCLUDE_DIR.'videoHelper.class.php');


use \Wcs\PersistentFops\Fops_huanpeng;
use \Wcs\Config;
use \Wcs\MgrAuth;


define('SLEEP_INTERVAL', 1);
define('NOTIFY_URL',WEB_ROOT_URL.'api/server/posterCallBack.php');

echo $GLOBALS['env'];
if($GLOBALS['env']=='DEV')
	define('LOG_FILE', '/data/logs/saverecord.log');
else
	define('LOG_FILE', '/data/logs/saverecord.log');

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

/********************** MAIN ***********************/
$vinstance = videoHelper::getInstance();
$ak = Config::WCS_ACCESS_KEY;
$sk = Config::WCS_SECRET_KEY;
$auth = new MgrAuth($ak, $sk);
while (true)
{
	$video = $vinstance->getMergedVideo();
	if($video==0){
		if (DEBUG) _my_log('got id: 0');
		//echo "get id: 0\n";
		sleep(SLEEP_INTERVAL);
		continue;
	}
	//echo "got id:{$video['liveid']}\n";
	if (DEBUG) _my_log("got id:{$video['liveid']}");

	//get poster
	$data = array(
		'liveid'=>$video['liveid'],
		'bucket'=>$video['bucket'],
		'keys'=>array($video['vname']),
		'duration'=>$video['length']
	);
	$client = new Fops_huanpeng($auth, $data['bucket']);
	$r = $client->poster($data,NOTIFY_URL);
	$r = json_decode($r,'true');
	if(!isset($r['persistentId'])){
		$errStr = 'get video poster failed:'.$r['message'];
		_my_log($errStr);
		$vinstance->report(array('liveid'=>$data['liveid'],'type'=>5));//获取截图失败
		//echo "------视频截图失败----\n";
		sleep(SLEEP_INTERVAL);
		continue;
	}
	//echo "------视频截图成功----\n";
	$mdata = array(
		'id'=>$video['id'],
		'posterid'=>$r['persistentId']
	);
	$vinstance->updatePosterOpt($mdata);
	if (DEBUG) _my_log("{$video['id']}完成");
	unset($client);
	sleep(SLEEP_INTERVAL);
	continue;
}