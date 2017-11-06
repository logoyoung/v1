<?php

ini_set('memory_limit', '1024M');
require_once __DIR__ . '/../../include/init.php';

use lib\live\Live;
use system\Timer;
use service\live\helper\LiveRedis;
use service\user\FollowService;
use service\room\LiveRoomService;

/**
 *  将直播列表顺序存入redis(最新、最热、最多关注)
 * @date 2017-07-10 17:29:54
 * @author longgang <longgang@6.cn>
 * @copyright (c) 2017, longgang
 * @version 1.0.0
 */
class cronLiveListRedis
{

    const LIVE_LIST_BY_VIEW_COUNT = 1;
    const LIVE_LIST_BY_CTIME = 2;
    const LIVE_LIST_BY_FOLLOW_COUNT = 3;

    private $_logName = 'cron_live_list_redis';
    public static $sortType = [
        self::LIVE_LIST_BY_VIEW_COUNT,
        self::LIVE_LIST_BY_CTIME,
        self::LIVE_LIST_BY_FOLLOW_COUNT,
    ];

    public function run()
    {
        $this->log('start|直播列表种入缓存开始');
        $timer = new Timer();
        $timer->start();
        $liveDao = new Live();
        $liveRedis = new LiveRedis();

        $totalNum = $liveDao->getLiveTotalNum();
        
        //清空缓存
        $liveRedis->remLiveList();
        
        if ($totalNum == 0)
        {
            $this->log('error|从数据库获取直播总数异常');
            return false;
        }

        $this->log("notice|共计:{$totalNum} 个直播");


        $liveData = $liveDao->getLiveList();

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

        $liveRoomService = new LiveRoomService();
        $liveRoomService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
        $liveRoomService->setLuid($luids);
        $viewCount = $liveRoomService->batchGetLiveUserCountFictitious();

        foreach (self::$sortType as $type)
        {
            if ($type == self::LIVE_LIST_BY_VIEW_COUNT)
            {
                foreach ($liveData as $v)
                {
                    $score = isset($viewCount[$v['uid']]) ? $viewCount[$v['uid']] : 0;
                    $liveRedis->setLiveList($type, $score, $v['uid']);
                }
            } elseif ($type == self::LIVE_LIST_BY_CTIME)
            {
                foreach ($liveData as $v)
                {
                    $v['ctime'] = strtotime($v['ctime']);
                    $liveRedis->setLiveList($type, $v['ctime'], $v['uid']);
                }
            } elseif ($type == self::LIVE_LIST_BY_FOLLOW_COUNT)
            {
                foreach ($liveData as $v)
                {
                    $score = isset($fansCount[$v['uid']]) ? $fansCount[$v['uid']] : 0;
                    $liveRedis->setLiveList($type, $fansCount[$v['uid']], $v['uid']);
                }
            }
        }

        unset($liveData, $fansCount, $viewCount);

        $timer->end();
        $t = $timer->getTime();
        $this->log("end |success| 脚本执行完成; 耗时:{$t}s; 直播总数:{$totalNum} 条;");

        return true;
    }

    public function log($msg)
    {
        return write_log($msg, $this->_logName);
    }

}

$obj = new cronLiveListRedis();

$obj->run();
