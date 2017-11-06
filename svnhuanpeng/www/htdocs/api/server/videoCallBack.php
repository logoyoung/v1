<?php
/**
 * 直播录制FLV文件生成回调
 * 2017-04-17
 * v.1.0
 * copyright 6.cn version 0.0
 *
 */
//暂时兼容线上代码，稳定后去除
/*
$lastLiveID = 10000;
$wscb = @file_get_contents( 'php://input' );
$wscb = base64_decode($wscb);
preg_match('/liverecord-Y-([0-9]*)-([0-9]*)\.flv/',$wscb,$match);

file_put_contents('/data/logs/ws/videoCallback.php.log',"flv回调".$match[1],FILE_APPEND);
exit;*/
include '../../../include/init.php';
use lib\Live;
use lib\Video;
use lib\live\LiveLog;

//mylog( '录制回调', LOG_DIR . 'Live.error.log' );
//获取回调flv信息
$callBackBody = @file_get_contents( 'php://input' );
if( !$callBackBody )
{
	echo 0;
	exit;
}
$callBackBody = json_decode( base64_decode( $callBackBody ), true );
//录制开始回调
if( !isset( $callBackBody['items'][0] ) )
{
	//mylog( '录制开始回调', LOG_DIR . 'Live.error.log' );
	//LiveLog::wslog();
	echo 1;
	exit;
}
//mylog( '录制结束回调', LOG_DIR . 'Live.error.log' );
//录制结束回调
$data              = $callBackBody['items'][0];
$stream            = explode( '.', $data['streamname'] );
$stream            = str_replace( 'liverecord-', '', $stream[0] );
$flvInfo['stream'] = $stream;

$flvInfo['bucket'] = $data['bucket'];
$key               = explode( ':', $data['keys'][0] );
$key               = isset( $key[1] ) ? $key[1] : $key[0];
$flvInfo['keys']   = $key;
$flvInfo['urls']   = '';
$flvInfo['length'] = (int)$data['detail'][0]['duration'];

//添加flv文件记录
$db = new DBHelperi_huanpeng();
//获取直播id
$liveid = Live::getLiveIDByStreamName( $flvInfo['stream'], $db );
if( !$liveid )
{
	//写日志报警
}
$flvInfo['liveid'] = $liveid;
//获取flv片段
$flvRecords = Video::getStreamFlvs( $liveid, $db );
$r          = Video::addFlvRecord( $flvInfo, $db );
if( !$r )
{
	//写日志、报警
}
$flvRecords[] = array( 'stream' => $flvInfo['stream'], 'keys' => $flvInfo['keys'] );
/*$flvs         = array_map( function ( $flvRecord )
{
	return $flvRecord['keys'];
}, $flvRecords );
$streams      = array_map( function ( $flvRecord )
{
	return $flvRecord['stream'];
}, $flvRecords );*/
$flvs    = array();
$streams = array();
foreach ( $flvRecords as $k => $flvRecord )
{
	$flvs[]    = $flvRecord['keys'];
	$streams[] = $flvRecord['stream'];
}
//如果已经结束直播并且全部回调则调用合并命令
if( !Live::checkLiveByLiveID( $liveid, $db ) && Live::checkAllFlvCallBack( $streams, $liveid, $db ) )
{
	Live::liveToFlvCallBack( $liveid, $db );
	$Video = new Video( $db );
	if( count( $flvs ) < 2 )
	{
		$ret = $Video->transcodeFile( $flvs[0], "$liveid.mp4" );
		$opt = Video::OPT_TRANSCODE;
	}
	else
	{
		$ret = $Video->mergeFiles( $flvs, "$liveid.mp4" );
		$opt = Video::OPT_MERGE;
	}
	//$ret = $Video->mergeFiles( $flvs,"$liveid.mp4" );
	$ret = json_decode( $ret, true );
	//合并失败处理
	//mylog(json_encode($ret),LOG_DIR.'Live.error.log');
	if( !isset( $ret['persistentId'] ) )
	{
		LiveLog::wslog("error:{$record['liveid']}-".json_encode($ret));
		//error todo
		/*try{
			Video::pushErrtask($flvs);
			exit;
		}catch (\Exception $e){
			LiveLog::wslog("error:{$record['liveid']}-".json_encode($ret));
		}*/
	}
	LiveLog::wslog("record:转码或拼接结果" . json_encode($ret));
	//todo
	$dir       = array(
		'DEV' => array( 'v' => 'dev/v/' ),
		'PRE' => array( 'v' => 'pre/v/' ),
		'PRO' => array( 'v' => 'pro/v/' )
	);
	$optRecord = array(
		'taskid' => $ret['persistentId'],
		'liveid' => $liveid,
		'opt'    => $opt,
		'bucket' => $flvInfo['bucket'],
		'vname'  => $dir[$GLOBALS['env']]['v'] . $liveid . '.mp4'
	);
	//mylog(json_encode($optRecord),LOG_DIR.'Live.error.log');
	$mret      = $Video->addOptRecord( $optRecord );
	//error todo
}
//mylog("录制任务ID:{$data['persistentId']}－－流名:$stream－－直播：$liveid",LOG_DIR.'Live.error.log');
LiveLog::wslog("record:录制任务ID:{$data['persistentId']}－－流名:{$stream}－－直播：$liveid");
echo 1;
exit;


