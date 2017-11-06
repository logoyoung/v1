<?php

ini_set('memory_limit', '1024M');
require_once __DIR__ . '/../../include/init.php';

use lib\live\Live;
use lib\game\Game;
use system\Timer;
use service\game\helper\GameRedis;
use service\user\FollowService;
use service\room\LiveRoomService;

/**
 *  将游戏直播列表顺序存入redis(最新、最热、最多关注)
 * @date 2017-07-17 14:17:06
 * @author longgang <longgang@6.cn>
 * @copyright (c) 2017, longgang
 * @version 1.0.0
 */
class cronGameLiveListRedis
{

    const GAME_LIVE_LIST_BY_VIEW_COUNT = 1;
    const GAME_LIVE_LIST_BY_CTIME = 2;
    const GAME_LIVE_LIST_BY_FOLLOW_COUNT = 3;

    private $_logName = 'cron_game_live_list_redis';
    public static $sortType = [
        self::GAME_LIVE_LIST_BY_VIEW_COUNT,
        self::GAME_LIVE_LIST_BY_CTIME,
        self::GAME_LIVE_LIST_BY_FOLLOW_COUNT,
    ];

    public function run()
    {
        $this->log('start|游戏直播列表种入缓存开始');
        $timer = new Timer();
        $timer->start();
        
        
        $gameDao = new Game();
        $liveDao = new Live();

        $gameData = $gameDao->getGameList();

        $gameIds = array_unique(array_column($gameData, 'gameid'));

        if(!$gameIds)
        {
            $this->log('error| 获取gameids异常,脚本停止!');
            return false;
        }
        $gameLiveCount = $liveDao->getGameLiveCount();

        $gameRedis = new GameRedis();

        foreach ($gameIds as $gameId)
        {
            $count = isset($gameLiveCount[$gameId]) ? $gameLiveCount[$gameId] : 0;
            $gameRedis->setGameLiveCount($count, $gameId);
            //清空缓存
            $gameRedis->remGameLiveList($gameId);
        }
        
        $this->log('notice|gamelivecount存入缓存');
        
        $totalNum = $liveDao->getLiveTotalNum();
        if ($totalNum == 0)
        {
            $this->log('error|从数据库获取直播总数异常');
            die(-1);
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
            if ($type == self::GAME_LIVE_LIST_BY_VIEW_COUNT)
            {
                foreach ($liveData as $v)
                {
                    $score = isset($viewCount[$v['uid']]) ? $viewCount[$v['uid']] : 0;
                    $gameRedis->setGameLiveList($type,$v['gameid'], $score, $v['uid']);
                }
            } elseif ($type == self::GAME_LIVE_LIST_BY_CTIME)
            {
                foreach ($liveData as $v)
                {
                    $v['ctime'] = strtotime($v['ctime']);
                    $gameRedis->setGameLiveList($type,$v['gameid'], $v['ctime'], $v['uid']);
                }
            } elseif ($type == self::GAME_LIVE_LIST_BY_FOLLOW_COUNT)
            {
                foreach ($liveData as $v)
                {
                    $score = isset($fansCount[$v['uid']]) ? $fansCount[$v['uid']] : 0;
                    $gameRedis->setGameLiveList($type,$v['gameid'], $fansCount[$v['uid']], $v['uid']);
                }
            }
        }

        unset($liveData, $fansCount, $viewCount);
        
        $this->log("notice | 直播存入缓存 直播总数:{$totalNum} 条;");
        
        $timer->end();
        $t = $timer->getTime();
        $this->log("end |success| 脚本执行完成; 耗时:{$t}s;");

        return true;
    }

    public function log($msg)
    {
        return write_log($msg, $this->_logName);
    }

}

$obj = new cronGameLiveListRedis();

$obj->run();
