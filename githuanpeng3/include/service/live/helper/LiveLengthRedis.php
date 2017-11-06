<?php

namespace service\live\helper;

use system\RedisHelper;

class LiveLengthRedis
{

    //直播时长redis池子
    const LIVE_LENGTH_REDIS_CONF = 'huanpeng';
    //主播直播时长前缀
    const ANCHOR_LIVE_LENGTH = 'ANCHOR_LIVE_LENGTH_%s';
    //缓存存储时间
    const CACHE_TIME = 3600;

    public function setAnchorLiveLength(string $month, array $data,int $cacheTime = 0)
    {
        $cacheTime = !$cacheTime ? self::CACHE_TIME : $cacheTime;
        
        $key = $this->getAnchorLiveLengthCacheKey($month);
        
        $redisObj = $this->getRedis();

        $redisObj->hMset($key, $data);

        $redisObj->expire($key, $cacheTime);

        return true;
    }
    
    public function getAnchorLiveLength($month,$uids)
    {
        $uids = (array) $uids;
        $key = $this->getAnchorLiveLengthCacheKey($month);
        return $this->getRedis()->hMget($key,$uids);
    }

    public function getAnchorLiveLengthCacheKey($month)
    {
        return sprintf(self::ANCHOR_LIVE_LENGTH, $month);
    }

    public function getRedis()
    {
        return RedisHelper::getInstance(self::LIVE_LENGTH_REDIS_CONF);
    }

}
