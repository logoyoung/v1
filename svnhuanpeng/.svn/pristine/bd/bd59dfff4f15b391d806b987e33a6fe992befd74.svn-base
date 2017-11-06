<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/4/25
 * Time: 12:14
 */

/**
 *
 * 网宿文件清理
 *
 */
include( __DIR__ . '/../../include/init.php' );
use lib\Video;

//function getTask(){}


function completeClear()
{
}

/*************************main*********************/

$db = new DBHelperi_huanpeng();
//获取任务
$taskID = Video::getClearTask( $db );
if( !$taskID )
{
	exit;
}
$flvs = Video::getFlvs($taskID,$db);
$vfile = $taskID.'.mp4';

//加入删除文件队列
//add delete_queue
/*
$ret = $Video->deleteFiles($flvs);
if(!isset($ret['persistentId']))
{
	//error todo
	exit;
}
//录像是否发布
//已发布不删除
$videoInfo = Video::getVideoByLiveID($taskID,$db);
if($videoInfo['publish'])
{
	//更新删除记录
	Video::completeClear($taskID,$db);
	exit;
}
//是否做了备份
if(!Video::downloadStatus())
{
	//更新删除记录
	Video::completeClear($taskID,$db);
	exit;
}
//删除录像
$ret = $Video->deleteFiles($videoInfo['vfile']);
if(!isset($ret['persistentId']))
{
	//error todo
	exit;
}*/
//更新记录
Video::completeClear($taskID,$db);



