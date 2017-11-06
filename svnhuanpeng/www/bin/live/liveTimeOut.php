<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/4/14
 * Time: 17:08
 */
include( __DIR__ . '/../../include/init.php' );
use lib\Live;
use lib\Video;
use lib\live\LiveLog;
//use \RedisHelp;

//define( "LIVE_INDEX", 79896 );

//mylog('超时脚本',LOG_DIR.'Live.error.log');
/**直播状态**/
/*//直播创建
define( 'LIVE_CREATE', 0 );
//直播超时
define( 'LIVE_TIMEOUT', 10 );
//直播开始
define( 'LIVE', 100 );
//直播停止
define( 'LIVE_STOP', 110 );
//直播flv片段生成
define( 'LIVE_TO_FLV', 120 );
//录像合并完成
define( 'FLV_TO_VIDEO', 130 );
//录像截图完成
define( 'VIDEO_TO_POSTER', 140 );
//直播活动完成
define( 'LIVE_COMPLETE', 200 );
//合并录像失败
define( 'VIDEO_MERGE_FAILED', 210 );
//录像截图失败
define( 'VIDEO_TO_POSTER_FAILED', 220 );*/

/*****直播流状态*****/
/*//直播流创建
define( 'STREAM_CREATE', 0 );
//直播流开始
define( 'STREAM_START', 100 );
//直播流中断
define( 'STREAM_DISCONNECT', 200 );
//直播流超时
define( 'STREAM_TIMEOUT', 210 );
//直播流被覆盖
define( 'STREAM_COVER', 220 );
//直播流被管理员停止
define( 'STREAM_ADMIN_STOP', 230 );
//直播流被用户停止
define( 'STREAM_USER_STOP', 240 );*/

/***超时时间***/
//直播流开始回调超时
define( 'STREAM_START_TIMEOUT', 180 );//3s
//直播流中断超时
define( 'STREAM_DISCONNECT_TIMEOUT', 180 );//3s
//直播生成FLV超时
define( 'LIVE_TO_FLV_TIMEOUT', 3600 );//test 1分钟 原3600
//FLV合并录像超时
define( 'FLV_TO_VIDEO_TIMEOUT', 3600 * 6 );//3600*6
//录像截图超时
define( 'VIDEO_TO_POSTER_TIMEOUT', 600 );//600
//延迟时间
define( 'DEFER_TIME', 60 );
define('SLEEP_INTERVAL',1);
$Redis = new RedisHelp();
//获取分界直播id
//$maxLiveIndex = $Redis->get('MAX_LIVE_INDEX');
//$maxLiveIndex = $maxLiveIndex?$maxLiveIndex:80000;
$maxLiveIndex = 450000;
//任务锁处理
/*$crontab = $Redis->get('LiveTimeOut');
//$Redis->set('LiveTimeOut',0);
if( $crontab==1 ){
	echo date('Y-m-d H:i:s')."资源被占据，结束任务\n";
	exit;
}else{
	echo date('Y-m-d H:i:s')."开始执行任务\n";
	$Redis->set('LiveTimeOut',1);
}*/
$db = new DBHelperi_huanpeng();


//获取直播状态小于等于100并且不等于10的所有直播
$liveList = array();
$liveList = Live::getTimeOutLive( $maxLiveIndex, $db );
if( !$liveList )
{
	//mylog( "直播检测：未检测到超时直播\n", LOG_DIR . 'Live.error.log' );
	LiveLog::processlog("record:直播检测：未检测到超时直播");
	//$Redis->set('LiveTimeOut',0);
	exit;
}
//循环所得直播
foreach ( $liveList as $k => $live )
{
	echo "检测直播{$live['liveid']}\n";
	$stream = LIVE::getStreamInfoByStreamName( $live['stream'], $db );
	$dtime  = time() - strtotime( $stream['utime'] );
	if( $live['status'] == LIVE_CREATE && $dtime > DEFER_TIME )
	{
		//L0,S0 => L230,S210
		if( $stream['status'] == STREAM_CREATE && $dtime > STREAM_START_TIMEOUT )
		{
			//直播流回调超时处理
			//mylog( "整场直播未推上流或流回调超时异常：{$live['liveid']}", LOG_DIR . 'Live.error.log' );
			LiveLog::processlog("record:整场直播未推上流或流回调超时异常：{$live['liveid']}");
			//$lr = Live::setLiveStatusByLiveID( $live['liveid'], LIVE_TIMEOUT, $db );
			//$sr = Live::setStreamInfoByStreamName( $live['stream'], STREAM_TIMEOUT, $db );
			//todo
			$Live = new Live( $live['uid'], $db );
			$Live->exceptionStop(LIVE_TIMEOUT,STREAM_TIMEOUT,$live['liveid']);
			echo "整场直播未推上流或流回调超时异常{$live['liveid']}\n";
		}
		//L0,S100 => L100,S100
		elseif( $stream['status'] == STREAM_START )
		{
			//mylog( "流连上状态但直播但直播状态为创建状态：{$live['liveid']}", LOG_DIR . 'Live.error.log' );
			LiveLog::processlog("record:流连上状态但直播但直播状态为创建状态：{$live['liveid']}");
			$liveStatus = Live::getLiveStatusByLiveID( $live['liveid'], $db );
			if( $liveStatus == LIVE_CREATE )
			{
				$lr = Live::setLiveStatusByLiveID( $live['liveid'], LIVE, $db );
				//todo
			}
		}
		//L0,S>=200 => L230,S>=200
		elseif( $stream['status'] >= STREAM_DISCONNECT )
		{
			//mylog( "提前收到断流回调停止直播：{$live['liveid']}", LOG_DIR . 'Live.error.log' );
			LiveLog::processlog("record:提前收到断流回调停止直播：{$live['liveid']}");
			//$r = Live::setLiveStatusByLiveID( $live['liveid'], LIVE_TIMEOUT, $db );
			//todo
			$Live = new Live( $live['uid'], $db );
			$Live->exceptionStop(LIVE_TIMEOUT,null,$live['liveid']);
			echo "提前收到断流回调停止直播{$live['liveid']}\n";
		}
		else
		{
			continue;
		}
		//sleep(SLEEP_INTERVAL);
	}
	elseif( $live['status'] == LIVE && $dtime > DEFER_TIME )
	{
		//L100，S>=200 => L110,S>=200
		/*$stream = LIVE::getStreamInfoByStreamName( $live['stream'], $db );
		$dtime  = time() - strtotime( $stream['utime'] );*/
		if( $stream['status'] >= STREAM_DISCONNECT && $dtime > STREAM_DISCONNECT_TIMEOUT )
		{
			//mylog( "直播断开连接超时系统停止直播:{$live['liveid']}", LOG_DIR . 'Live.error.log' );
			LiveLog::processlog("record:直播断开连接超时系统停止直播:{$live['liveid']}");
			//直播中断超时处理
			//$lr = Live::setLiveStatusByLiveID( $live['liveid'], LIVE_STOP, $db );
			$Live = new Live( $live['uid'], $db );
			$Live->systemStopLive( time(),$live['liveid'] );
			echo "超时结束直播{$live['liveid']}\n";
			//todo
		}
		//L100,S0 => L110,S210
		elseif( $stream['status'] == STREAM_CREATE && $dtime > STREAM_DISCONNECT_TIMEOUT )
		{
			//mylog( "开始直播连接超时系统停止直播:{$live['liveid']}", LOG_DIR . 'Live.error.log' );
			LiveLog::processlog("record:开始直播连接超时系统停止直播:{$live['liveid']}");
			//直播流回调超时处理
			//$lr = Live::setLiveStatusByLiveID( $live['liveid'], LIVE_STOP, $db );
			//$sr = Live::setStreamInfoByStreamName( $live['stream'], STREAM_TIMEOUT, $db );
			$Live = new Live( $live['uid'], $db );
			//$Live->systemStopLive( time() - STREAM_DISCONNECT_TIMEOUT );
			$Live->exceptionStop( LIVE_STOP, STREAM_TIMEOUT,$live['liveid'] );
			echo "异常结束直播{$live['liveid']}\n";
			//todo
		}
		//L100 S100
		elseif( $stream['status'] == STREAM_START )
		{
			//todo continue
			//todo record
			continue;
		}
		else
		{
			continue;
		}
		//sleep(SLEEP_INTERVAL);
	}
	//L110 => L120
	elseif( $live['status'] == LIVE_STOP && ( time() - strtotime( $live['utime'] ) > LIVE_TO_FLV_TIMEOUT ) )
	{
		//flv生成回调超时
		$lr = Live::setLiveStatusByLiveID( $live['liveid'], LIVE_TO_FLV, $db );
		//todo merge
		//mylog( $live['liveid'], LOG_DIR . 'Live.error.log' );
		$flvs = Video::getFlvs( $live['liveid'], $db );
		//mylog( json_encode( $flvs ), LOG_DIR . 'Live.error.log' );
		LiveLog::processlog("record:flv回调超时" . json_encode( $flvs ));
		$Video = new Video( $db );

		if( !strstr( '6huanpeng-test001', $flvs[0] ) )
		{
			if( count( $flvs ) < 2 )
			{
				$ret = $Video->transcodeFile( $flvs[0], "{$live['liveid']}.mp4" );
				$opt = Video::OPT_TRANSCODE;
			}
			else
			{
				//
				$ret = $Video->mergeFiles( $flvs, "{$live['liveid']}.mp4" );
				$opt = Video::OPT_MERGE;
			}
		}
		//mylog( '超时调用合并－》' . $ret, LOG_DIR . 'Live.error.log' );
		LiveLog::processlog("record:超时合并转码 $ret");
		$ret = json_decode( $ret, true );
		//合并失败处理
		//todo
		$dir       = array(
			'DEV' => array( 'v' => 'dev/v/' ),
			'PRE' => array( 'v' => 'pre/v/' ),
			'PRO' => array( 'v' => 'pro/v/' )
		);
		$optRecord = array(
			'taskid' => $ret['persistentId'],
			'liveid' => $live['liveid'],
			'opt'    => $opt,
			'bucket' => Video::WCS_BUCKET_VIDEO,
			'vname'  => $dir[$GLOBALS['env']]['v'] . $live['liveid'] . '.mp4'
		);
		$mret      = $Video->addOptRecord( $optRecord );
		//sleep(SLEEP_INTERVAL);
	}
	//L120 => L210
	elseif( $live['status'] == LIVE_TO_FLV && ( time() - strtotime( $live['utime'] ) > FLV_TO_VIDEO_TIMEOUT ) )
	{
		//生成录像回调超时
		$lr = Live::setLiveStatusByLiveID( $live['liveid'], VIDEO_MERGE_FAILED, $db );
		//todo
		//sleep(SLEEP_INTERVAL);
	}
	//L130 => L140(X)
	//L130 => L220(√)
	elseif( $live['status'] == FLV_TO_VIDEO && ( time() - strtotime( $live['utime'] ) > VIDEO_TO_POSTER_TIMEOUT ) )
	{
		//截图生成回调超时
		$lr = Live::setLiveStatusByLiveID( $live['liveid'], VIDEO_TO_POSTER_FAILED, $db );
		//todo
		//sleep(SLEEP_INTERVAL);
	}
	else
	{
		//写错误日志
		//mylog("过滤直播{$live['liveid']}\n",LOG_DIR.'Live.error.log');
		LiveLog::processlog("record:过滤直播{$live['liveid']}");
		echo "过滤{$live['liveid']}:" . json_encode($live) . "\n";
		echo "过滤{$live['liveid']}:" . json_encode($stream) . "\n";
		//echo "{$live['liveid']}----{$live['status']}---no process!\n";
	}
	//exit;
	//sleep(SLEEP_INTERVAL);
}
//释放锁
//$Redis->set('LiveTimeOut',0);
exit;