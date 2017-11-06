<?php

ini_set('memory_limit', '1024M');
require_once __DIR__ . '/../../include/init.php';

use lib\live\Live;
use system\Timer;
use service\live\helper\LiveRedis;
use service\user\FollowService;
use service\room\LiveRoomService;
use service\live\LiveService;
use service\user\UserDataService;
use service\room\RoomManagerService;

/**
 *  将直播信息种入redis
 * @date 2017-07-10 17:29:54
 * @author longgang <longgang@6.cn>
 * @copyright (c) 2017, longgang
 * @version 1.0.0
 */
class cronLiveInfoRedis
{

    private $_logName = 'cron_live_info_redis';

    public function run()
    {
        $this->log('start|直播信息种入缓存开始');
        $timer = new Timer();
        $timer->start();
        $liveDao = new Live();
        $totalNum = $liveDao->getLiveTotalNum();
        if ($totalNum == 0)
        {
            $this->log('error|从数据库获取直播总数异常');
            die(-1);
        }

        $this->log("notice|共计:{$totalNum} 个正在直播");

        $liveRedis = new LiveRedis();

        $liveData = $liveDao->getLiveList();

        $liveTotal = count($liveData);
        if (!$liveData)
        {
            $this->log("error|从live表里获取liveData异常,停止脚本");
            return false;
        }

        $luids = array_unique(array_column($liveData, 'uid'));
        if (!$luids)
        {
            $this->log("error|从live表里没有获取到uid,停止脚本");
            return false;
        }

        $followService = new FollowService();
        $followService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
        $fansCount = $followService->getUserCount($luids);

        $userDataService = new UserDataService();
        $userDataService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
        $userDataService->setUid($luids);
        $anchorInfo = $userDataService->batchGetUserInfo();

        $roomManagerService = new RoomManagerService();
        $roomManagerService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
        $roomManagerService->setUid($luids);
        $room = $roomManagerService->getRoomIdsByUids();

        $liveRoomService = new LiveRoomService();
        $liveRoomService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
        $liveRoomService->setLuid($luids);
        $viewCount = $liveRoomService->batchGetLiveUserCountFictitious();

        $lists = [];
        foreach ($liveData as $v)
        {
            $list = [];
            $list['liveid'] = $v['liveid']; 
            $list['uid'] = $v['uid'];
            $list['head'] = array_key_exists($v['uid'], $anchorInfo) ? $anchorInfo[$v['uid']]['pic'] : '';
            $list['roomID'] = array_key_exists($v['uid'], $room) ? $room[$v['uid']] : 0;
            $list['gameid'] = $v['gameid'];
            $list['gamename'] = $v['gamename'];
            $list['gameName'] = $v['gamename'];//兼容APP和pc取字段不一样
            $list['nick'] = array_key_exists($v['uid'], $anchorInfo) ? $anchorInfo[$v['uid']]['nick'] : '';
            $list['title'] = $v['title'];
            $list['stime'] = strtotime($v['ctime']);
            $list['ctime'] = strtotime($v['ctime']);
            $list['orientation'] = $v['orientation'];
            $list['status'] = $v['status'];
            if ($v['poster'])
            {
                $list['poster'] = LiveService::getPosterUrl($v['poster']);
                $list['ispic'] = '1';
            } else
            {
                $list['poster'] = CROSS;
                $list['ispic'] = '0';
            }
            $list['viewCount'] = isset($viewCount[$v['uid']]) ? $viewCount[$v['uid']] : 0;
            $list['fansCount'] = array_key_exists($v['uid'], $fansCount) ? $fansCount[$v['uid']] : 0;
            $lists[] = $list;
            
            $liveRedis->setUidToLiveid($v['uid'], $v['liveid']);
        }
//        $redis = $liveRedis->getRedis();
//        $redis->multi(1);
        $liveRedis->clearLiveStatus();
        if(!$liveRedis->setLiveStatus($liveData))
        {
            $this->log('error | 设置直播状态失败,停止脚本');
            return false;
        }
        $liveRedis->clearLiveInfo();
        if(!$liveRedis->setLiveInfo($lists))
        {
            $this->log('error | 设置直播信息失败,停止脚本');
            return false;
        } 
        
        unset($liveData, $fansCount, $viewCount);

        $timer->end();
        $t = $timer->getTime();
        $this->log("end |success| 脚本执行完成; 耗时:{$t}s; 直播信息总数:{$liveTotal} 条;");

        return true;
    }

    public function log($msg)
    {
        return write_log($msg, $this->_logName);
    }

}

$obj = new cronLiveInfoRedis();

$obj->run();
