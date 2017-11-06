<?php

include '../../../include/init.php';
use lib\Video;
use lib\Live;
use lib\live\LiveLog;

//获取回调数据
//mylog( '合并回调', LOG_DIR . 'Live.error.log' );
$callBackBody = @file_get_contents( 'php://input' );
if( !$callBackBody )
{
	echo 0;
	exit;
}
$callBackBody = json_decode( base64_decode( $callBackBody ), true );
//录制开始回调
//获取对应直播
$db = new DBHelperi_huanpeng();

$records = Video::getMergeRecordByTaskId( $callBackBody['id'], $db );
foreach ($records as $record)
{
	//mylog("直播{$record['liveid']}转mp4成功",LOG_DIR.'Live.error.log');
	LiveLog::wslog("record:直播{$record['liveid']}转mp4成功");
	if( !$record )
	{
		//error todo
		//mylog("直播转mp4任务{$callBackBody['id']}完成,但未获取到对应直播信息",LOG_DIR.'Live.error.log');
		LiveLog::wslog("error:直播转mp4任务{$callBackBody['id']}完成,但未获取到对应直播信息");
	}
	$duration = (int)$callBackBody['items'][0]['duration'];
	$offset   = (int)( $duration / 2 );
	$live = Live::getLiveByLiveID($record['liveid'],$db);
	if($live&&(int)$live['orientation']==0)
	{
		$param = "o/$offset/w/720/h/1280";
	}
	else
	{
		$param = "o/$offset/w/1280/h/720";
	}

	$recordStatus = Video::updateMergeRecord( $callBackBody['id'], $duration, $db );
	$liveStatus   = Live::flvToVideoCallBack( $record['liveid'], $db );

//录像截图

	$videoURI  = $record['vname'];
	$posterURI = $record['liveid'] . '.jpg';

	$Video = new Video( $db );
	$ret   = $Video->cutOutVideoPicture( $videoURI, $posterURI, $param );
	LiveLog::wslog("$videoURI-$posterURI-$param");
	$ret = json_decode( $ret, true );
	if( !isset( $ret['persistentId'] ) )
	{
		//error todo
		LiveLog::wslog("error:{$record['liveid']}-".json_encode($ret));
	}
//记录截图
	$dir       = array(
		'DEV' => array( 'v' => 'dev/i/' ),
		'PRE' => array( 'v' => 'pre/i/' ),
		'PRO' => array( 'v' => 'pro/i/' )
	);
	$optRecord = array(
		'taskid' => $ret['persistentId'],
		'liveid' => $record['liveid'],
		'opt'    => Video::OPT_POSTER,
		'bucket' => $record['bucket'],
		'vname'  => $dir[$GLOBALS['env']]['v'] . $record['liveid'] . '.jpg'
	);
	$mret      = $Video->addOptRecord( $optRecord );
//error todo
}
