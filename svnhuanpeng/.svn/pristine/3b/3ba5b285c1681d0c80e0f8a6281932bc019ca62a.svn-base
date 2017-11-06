<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/5/20
 * Time: 13:22
 */

include( __DIR__ . '/../../include/init.php' );

//最大直播
$maxLive = 0;
//缓存
$mergeCache = 0;



echo "================安装直播相关==================\n";
if(!$maxLive)
{
	$db   = new DBHelperi_huanpeng();
	$live = $db->order('liveid desc')->limit(1)->select( 'live' );
	$maxLive = (int)$live[0]['liveid'];
}
$redis = new RedisHelp();
$redis->set('MAX_LIVE_INDEX',$maxLive);
$liveid = $redis->get('MAX_LIVE_INDEX');
echo "已安装完最大直播索引$liveid\n";
//安装录像合并缓存
/*$redis->set('VIDEO_MERGE_CACHE',$mergeCache);
$cache = $redis->get('VIDEO_MERGE_CACHE');
if($cache)
{
	echo "录像缓存服务已经安装\n";
}
else{
	echo "录像缓存服务未安装\n";
}*/
//释放超时脚本锁
$redis->set('LiveTimeOut',0);

