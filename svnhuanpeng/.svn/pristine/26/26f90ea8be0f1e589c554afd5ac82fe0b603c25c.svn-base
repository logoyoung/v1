<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/1/17
 * Time: 10:05
 */

include '../../../include/init.php';
use lib\Video;
use lib\Live;
use lib\live\LiveLog;
//获取回调数据
//mylog('截图回调',LOG_DIR.'Live.error.log');
$callBackBody = @file_get_contents('php://input');

if( !$callBackBody )
{
	echo 0;
	exit;
}
$callBackBody = json_decode( base64_decode( $callBackBody ), true );
//录制开始截图
//获取对应任务id
$taskID = $callBackBody['id'];
$db     = new DBHelperi_huanpeng();
$flvRecord = Video::getMergeRecordByTaskId($taskID,$db);
$flvRecord = $flvRecord[0];
//mylog("直播{$flvRecord['liveid']}完成截图",LOG_DIR.'Live.error.log');
LiveLog::wslog("record:录像{$flvRecord['liveid']}完成截图");
$vr = Video::updateMergeRecord($taskID,0,$db);
$lr = Live::videoPosterCallBack($flvRecord['liveid'],$db);
Live::completeLive($flvRecord['liveid'],$db);
//error todo
//mylog("直播{$flvRecord['liveid']}活动完成",LOG_DIR.'Live.error.log');
LiveLog::wslog("record:录像{$flvRecord['liveid']}活动完成");
exit;