<?php

namespace service\game\helper;

use system\RedisHelper;
use service\live\helper\LiveRedis;

class GameRedis
{

    //游戏redis池子
    const GAME_REDIS_CONF = 'huanpeng';
    //所有gameID的KEY
    const ALL_GAME_IDS = 'HP_ALL_GAME_IDS';
    //所有游戏列表信息的KEY
    const ALL_GAME_LIST = 'HP_ALL_GAME_LIST';
    //首页推荐游戏列表信息的KEY
    const RECOMMEND_GAME_LIST = 'HP_RECOMMEND_GAME_LIST';
    //最热游戏直播列表KEY
    const GAME_LIVE_LIST_BY_VIEW_COUNT = 'HP_GAME_LIVE_LIST_BY_VIEW_COUNT_%s';
    //最新游戏直播列表KEY
    const GAME_LIVE_LIST_BY_CTIME = 'HP_GAME_LIVE_LIST_BY_CTIME_%s';
    //最多关注游戏直播列表KEY
    const GAME_LIVE_LIST_BY_FOLLOW_COUNT = 'HP_GAME_LIVE_LIST_BY_FOLLOW_COUNT_%s';
    //游戏直播总数
    const GAME_LIVE_COUNT = 'HP_GAME_LIVE_COUNT';

    public static $sortType = [
        LiveRedis::LIVE_LIST_BY_VIEW_COUNT,
        LiveRedis::LIVE_LIST_BY_CTIME,
        LiveRedis::LIVE_LIST_BY_FOLLOW_COUNT,
    ];

    //缓存存储时间
    const CACHE_HOUR_TIME = 4200;
    const CACHE_TIME = 600;

    public function setAllGameIdsData(array $data)
    {
        $key = $this->getAllGameIdsCacheKey();
        $try = 2;

        do
        {
            $status = $this->getRedis()->set($key, hp_json_encode($data));
            if ($status)
            {
                $this->getRedis()->expire($key, self::CACHE_HOUR_TIME);
                return TRUE;
            }
            usleep(1);
        } while ($try-- > 0);
        return FALSE;
    }

    public function getAllGameIdsData()
    {
        $key = $this->getAllGameIdsCacheKey();
        return $this->getRedis()->get($key);
    }

    public function setAllGameListData(array $data)
    {
        $key = $this->getAllGameListCacheKey();
        $try = 2;

        do
        {
            $status = $this->getRedis()->hMset($key, $data);
            if ($status)
            {
                $this->getRedis()->expire($key, self::CACHE_HOUR_TIME);
                return TRUE;
            }
            usleep(1);
        } while ($try-- > 0);
        return FALSE;
    }

    public function getAllGameListData()
    {
        $key = $this->getAllGameListCacheKey();
        return $this->getRedis()->hGetAll($key);
    }

    public function getGameListDataByGameId($gameId)
    {
        if (!$gameId)
        {
            return FALSE;
        }

        $gameId = (array) $gameId;

        $key = $this->getAllGameListCacheKey();
        return $this->getRedis()->hMget($key, $gameId);
    }

    public function getGameCount()
    {
        $key = $this->getAllGameListCacheKey();
        return $this->getRedis()->hLen($key);
    }

    public function setRecommendGameList(string $gameids)
    {
        $key = $this->getRecommendGameListCacheKey();
        $try = 2;

        do
        {
            $status = $this->getRedis()->set($key, $gameids);
            if ($status)
            {
                $this->getRedis()->expire($key, self::CACHE_HOUR_TIME);
                return TRUE;
            }
            usleep(1);
        } while ($try-- > 0);
        return FALSE;
    }

    public function getRecommendGameList()
    {
        $key = $this->getRecommendGameListCacheKey();
        return $this->getRedis()->get($key);
    }

    public function setGameLiveList($type, $gameId, $score, $uid)
    {
        $key = $this->getCacheKeyByType($type, $gameId);
       
        $this->getRedis()->zAdd($key, $score, $uid);
   
        $this->getRedis()->expire($key, self::CACHE_TIME);

        return true;
            
    }

    public function getGameLiveList($type, $gameId, int $page = 1, int $size = 0, $withsocres = false)
    {
        $start = ($page - 1) * $size;
        $end = $start + $size - 1;
        $key = $this->getCacheKeyByType($type, $gameId);
        return $this->getRedis()->zRevRange($key, $start, $end, $withsocres);
    }
    
    public function remGameLiveList($gameId)
    {
        foreach (self::$sortType as $type)
        {
            $key = $this->getCacheKeyByType($type,$gameId);
            $this->getRedis()->zRemRangeByRank($key,0,-1);
        }
        return;
    }

    public function setGameLiveCount($score, $gameid)
    {
        $key = $this->getGameLiveCountCacheKey();

        $this->getRedis()->zAdd($key, $score, $gameid);

        $this->getRedis()->expire($key, self::CACHE_TIME);
        return true;
    }

    public function getGameLiveCount(int $page = 1, int $size = 0, $withsocres = true)
    {
        $start = ($page - 1) * $size;
        $end = $start + $size - 1;
        $key = $this->getGameLiveCountCacheKey();
        return $this->getRedis()->zRevRange($key, $start, $end, $withsocres);
    }

    public function updateGameLiveCount($gameid, $incr)
    {
        $key = $this->getGameLiveCountCacheKey();
        return $this->getRedis()->zIncrBy($key, $incr, $gameid);
    }

    public function removeUid($mem, $gameId, $types = [])
    {
        $types = $types ? (array) $types : self::$sortType;
        foreach ($types as $type)
        {
            $key = $this->getCacheKeyByType($type, $gameId);
            $this->getRedis()->zRem($key, $mem);
        }
    }

    public function getCacheKeyByType($type, $gameId)
    {
        switch ($type)
        {
            case LiveRedis::LIVE_LIST_BY_VIEW_COUNT:
                $key = $this->getGameLiveListByViewCountCacheKey($gameId);
                break;
            case LiveRedis::LIVE_LIST_BY_CTIME:
                $key = $this->getGameLiveListByCtimeCacheKey($gameId);
                break;
            case LiveRedis::LIVE_LIST_BY_FOLLOW_COUNT:
                $key = $this->getGameLiveListByFollowCountCacheKey($gameId);
                break;
        }
        return $key;
    }

    public function getAllGameIdsCacheKey()
    {
        return self::ALL_GAME_IDS;
    }

    public function getAllGameListCacheKey()
    {
        return self::ALL_GAME_LIST;
    }

    public function getRecommendGameListCacheKey()
    {
        return self::RECOMMEND_GAME_LIST;
    }

    public function getGameLiveListByViewCountCacheKey($gameId)
    {
        return sprintf(self::GAME_LIVE_LIST_BY_VIEW_COUNT, $gameId);
    }

    public function getGameLiveListByCtimeCacheKey($gameId)
    {
        return sprintf(self::GAME_LIVE_LIST_BY_CTIME, $gameId);
    }

    public function getGameLiveListByFollowCountCacheKey($gameId)
    {
        return sprintf(self::GAME_LIVE_LIST_BY_FOLLOW_COUNT, $gameId);
    }

    public function getGameLiveCountCacheKey()
    {
        return self::GAME_LIVE_COUNT;
    }

    public function getRedis()
    {
        return RedisHelper::getInstance(self::GAME_REDIS_CONF);
    }

}
