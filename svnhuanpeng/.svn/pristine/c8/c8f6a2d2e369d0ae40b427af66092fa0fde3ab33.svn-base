<?php

namespace service\video\helper;

use system\RedisHelper;

class VideoRedis
{

    //直播redis池子
    const Live_REDIS_CONF = 'huanpeng';
    //首页最热视频KEY
    const INDEX_HOT_VIDEO_LIST = 'HP_INDEX_HOT_VIDEO_LIST';
    //首页最新视频KEY
    const INDEX_NEW_VIDEO_LIST = 'HP_INDEX_NEW_VIDEO_LIST';
        
    //缓存存储时间
    const CACHE_TIME = 600;
        
    public function setIndexHotVideo(array $datas)
    {
        $key = $this->getIndexHotVideoCacheKey();
        $redisObj = $this->getRedis();
        $try = 2;

        do
        {
            $status = $redisObj->set($key, hp_json_encode($datas));
            if ($status)
            {
                $this->getRedis()->expire($key, self::CACHE_TIME);
                return true;
            }
            usleep(1);
        } while ($try-- > 0);
        return false;
    }

    public function getIndexHotVideo()
    {
        $key = $this->getIndexHotVideoCacheKey();
        return $this->getRedis()->get($key);
    }
    
    public function setIndexNewVideo(array $datas)
    {
        $key = $this->getIndexNewVideoCacheKey();
        $redisObj = $this->getRedis();
        $try = 2;

        do
        {
            $status = $redisObj->set($key, hp_json_encode($datas));
            if ($status)
            {
                $this->getRedis()->expire($key, self::CACHE_TIME);
                return true;
            }
            usleep(1);
        } while ($try-- > 0);
        return false;
    }

    public function getIndexNewVideo()
    {
        $key = $this->getIndexNewVideoCacheKey();
        return $this->getRedis()->get($key);
    }

    public function getIndexNewVideoCacheKey()
    {
        return self::INDEX_NEW_VIDEO_LIST;
    }

    public function getIndexHotVideoCacheKey()
    {
        return self::INDEX_HOT_VIDEO_LIST;
    }

    public function getRedis()
    {
        return RedisHelper::getInstance(self::Live_REDIS_CONF);
    }

}
