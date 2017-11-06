<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/4/25
 * Time: 12:14
 */

/**
 *
 * 录像备份
 *
 */

/**************************main****************************/

//获取备份任务

//备份

//写备份记录




/***************************test*******************************/

include( __DIR__ . '/../../include/init.php' );
use lib\Video;

$video =  new Video();
$r = $video->mergeFiles(['liverecord-Y-1380311-8264732--20170815080754.flv'
	,''
	],'mergetesterdsdsqs.mp4');   //'liverecord-Y-1075047-1308428--20170724024454.flv'
//$r = $video->transcodeFile('liverecord-Y-1351803-7446492--20170813014015.flv','transcodetest20.mp4');
//$r = $video->cutOutVideoPicture('/dev/v/656189.mp4','2017072522.jpg');
var_dump($r);
