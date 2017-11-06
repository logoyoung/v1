<?php

/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/3/22
 * Time: 10:14
 */
//echo 1;
//exit;

include( '../../../include/init.php' );

use lib\Live;
use DBHelperi_huanpeng;
use lib\live\LiveLog;
use service\event\EventManager;
use service\user\UserDataService;
use service\room\RoomManagerService;
use service\anchor\AnchorDataService;
use service\room\LiveRoomService;
use service\live\LiveService;

//mylog('直播截图回调',LOG_DIR.'Live.error.log');
//截图类型
$posterType = array(
    'big' => array(
        '0' => 'big-shu',
        '1' => 'big-heng',
        '4' => 'big-heng'
    ),
    'small' => array(
        '0' => 'small-shu',
        '1' => 'small-heng',
        '4' => 'small-heng'
    )
);
//mylog("截图回调", LOG_DIR . 'livePosterCallBack.php.log');
$db = new DBHelperi_huanpeng(true);

//获取截图回调
$posterList = @file_get_contents('php://input');
if (!$posterList)
{
    echo 0;
    exit;
}
$posterList = base64_decode($posterList);
$posterList = json_decode($posterList, true);
//mylog(json_encode($posterList),LOG_DIR.'Live.error.log');
if (!isset($posterList['items']))
{
    echo 0;
    exit;
}
//
foreach ($posterList['items'] as $k => $item)
{
    //直播流名
    //mylog("直播{$live['liveid']}截图：$posterUrl",LOG_DIR.'livePosterCallBack.php.log');
    if(!isset($item['streamname']))
	{
		continue;
	}
	$streamName = explode('.', $item['streamname']);
    $streamName = isset($streamName[0])?$streamName[0]:'';
	if(!$streamName)
	{
		continue;
	}
    $streamName = explode('-', $streamName);
    $streamName = "{$streamName[1]}-{$streamName[2]}-{$streamName[3]}";
    //角度
    $live = Live::getLiveByStreamName($streamName, $db);
    //$uid = Live::getUidByLiveStream($streamName,$db);var_dump($uid);exit;
    if (!$live)
    {
        continue;
    }
    $orientation = (string) $live['orientation'];
    if (!strstr($item['keys'][0], $posterType['big'][$orientation]))
    {
        continue;
    }
    $posterUrl = basename($item['urls'][0]);
    $posterUrl = $posterUrl . '?' . time();
    //mylog("直播{$live['liveid']}截图：$posterUrl", LOG_DIR . 'livePosterCallBack.php.log');
    LiveLog::wslog("record:直播{$live['liveid']}截图：$posterUrl");

    /*********更新liveinfo缓存*****start*****/

    $userService = new UserDataService();
    $userService->setCaller('api:' . __FILE__);
    $userService->setUid($live['uid']);
    $userInfo = $userService->getUserInfo();

    $roomManagerService = new RoomManagerService();
    $roomManagerService->setCaller('api:' . __FILE__);
    $roomManagerService->setUid($live['uid']);

    $anchorDataService = new AnchorDataService();
    $anchorDataService->setCaller('api:' . __FILE__);
    $anchorDataService->setUid($live['uid']);

    $liveRoomService = new LiveRoomService();
    $liveRoomService->setCaller('api:' . __FILE__);
    $liveRoomService->setLuid($live['uid']);
    
    $gameId = $live['gameid'];

    $liveInfo = [];
    $liveInfo['liveid'] = $live['liveid'];
    $liveInfo['uid'] = $live['uid'];
    $liveInfo['head'] = isset($userInfo['pic']) ? $userInfo['pic'] : '';
    $liveInfo['nick'] = isset($userInfo['nick']) ? $userInfo['nick'] : '';
    $liveInfo['roomID'] = $roomManagerService->getRoomidByUid();
    $liveInfo['poster'] = LiveService::getPosterUrl($posterUrl);
    $liveInfo['ispic'] = '1';
    $liveInfo['title'] = $live['title'];
    $liveInfo['gamename'] = $live['gamename'];
    $liveInfo['gameName'] = $live['gamename']; //兼容不同端大小写
    $liveInfo['gameid'] = $gameId;
    $liveInfo['stime'] = $live['ctime'];
    $liveInfo['ctime'] = $live['ctime'];
    $liveInfo['orientation'] = $live['orientation'];
    $liveInfo['viewCount'] = $liveRoomService->getLiveUserCountFictitious();
    $liveInfo['fansCount'] = $anchorDataService->getFollowNumber();
    $liveInfo['status'] = $live['status'];

    $params['liveinfo'] = [$liveInfo];
    $event = new EventManager();

    $event->trigger(EventManager::ACTION_UPDATE_LIVE_INFO, $params);

    $event = null;
    
    /*********更新liveinfo缓存*****end*******/

    $r = Live::livePosterCallBack($posterUrl, $live['liveid'], $db);
}
echo 1;
exit;
