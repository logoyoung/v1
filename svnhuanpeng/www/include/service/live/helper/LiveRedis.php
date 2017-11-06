<?php

namespace service\live\helper;

use system\RedisHelper;

class LiveRedis
{

    //直播redis池子
    const Live_REDIS_CONF = 'huanpeng';
    //按照观众数排序所有直播列表KEY
    const ALL_LIVE_LIST_BY_VIEW_COUNT = 'HP_ALL_LIVE_LIST_BY_VIEW_COUNT';
    //按照开播时间排序所有直播列表KEY
    const ALL_LIVE_LIST_BY_CTIME = 'HP_ALL_LIVE_LIST_BY_CTIME';
    //按照关注数排序所有直播列表KEY
    const ALL_LIVE_LIST_BY_FOLLOW_COUNT = 'HP_ALL_LIVE_LIST_BY_FOLLOW_COUNT';
    //主播id对应的最近一条直播id的key
    const UID_LIVEID_KEY = 'HP_UID_TO_LIVEID_%s';
    const LIVE_STATUS = 'HP_LIVE_STATUS';
    const LIVE_INFO = 'HP_LIVE_INFO';
    
    //首页推荐视频key
    const RECOMMEND_LIVE_LIST = 'HP_RECOMMEND_LIVE_LIST';
    //首页资讯key
    const INFORMATION_LIST = 'HP_INFORMATION_LIST';
    
    //缓存存储时间
    const CACHE_TIME = 600;
    
    const LIVE_LIST_BY_VIEW_COUNT = 1;
    const LIVE_LIST_BY_CTIME = 2;
    const LIVE_LIST_BY_FOLLOW_COUNT = 3;
    
    public static $sortType = [
        self::LIVE_LIST_BY_VIEW_COUNT,
        self::LIVE_LIST_BY_CTIME,
        self::LIVE_LIST_BY_FOLLOW_COUNT,
    ];
    
    public function setLiveList($type, $score, $mem)
    {
        $key = $this->getCacheKeyByType($type);
        $redisObj = $this->getRedis();

        $redisObj->zAdd($key, $score, $mem);
            
        $this->getRedis()->expire($key, self::CACHE_TIME);
        
        return true;
            
    }

    public function remLiveList()
    {
        foreach (self::$sortType as $type)
        {
            $key = $this->getCacheKeyByType($type);
            $this->getRedis()->zRemRangeByRank($key,0,-1);
        }
        return;
    }

    public function getLiveList($type, int $page = 1, int $size = 0, $withsocres = false)
    {
        $start = ($page - 1) * $size;
        $end = $start + $size - 1;
        $key = $this->getCacheKeyByType($type);
        return $this->getRedis()->zRevRange($key, $start, $end,$withsocres);
    }

    public function getLiveCount($type = 1)
    {
        $key = $this->getCacheKeyByType($type);
        return $this->getRedis()->zCard($key);
    }

    public function removeUid($mem, $types = [])
    {
        $types = $types ? (array) $types : self::$sortType;
        foreach ($types as $type)
        {
            $key = $this->getCacheKeyByType($type);
            $this->getRedis()->zRem($key,$mem);
        }
    }
    
    public function getCacheKeyByType($type = 1)
    {
        switch ($type)
        {
            case self::LIVE_LIST_BY_VIEW_COUNT:
                $key = $this->getAllLiveListByViewCountCacheKey();
                break;
            case self::LIVE_LIST_BY_CTIME:
                $key = $this->getAllLiveListByCtimeCacheKey();
                break;
            case self::LIVE_LIST_BY_FOLLOW_COUNT:
                $key = $this->getAllLiveListByFollowCountCacheKey();
                break;
        }
        return $key;
    }

    public function setLiveStatus(array $datas)
    {
        $key = $this->getLiveStatusCacheKey();
        $liveStatus = [];
        foreach ($datas as $v)
        {
            $liveStatus[$v['liveid']] = $v['status'];
        }
        $try = 2;

        do
        {
            $status = $this->getRedis()->hMset($key, $liveStatus);
            if ($status)
            {
                $this->getRedis()->expire($key, self::CACHE_TIME);
                return true;
            }
            usleep(1);
        } while ($try-- > 0);
        return false;
    }

    public function getLiveStatus($liveids)
    {
        $liveids = (array) $liveids;
        $key = $this->getLiveStatusCacheKey();
        return $this->getRedis()->hMget($key,$liveids);
    }
    
    public function getAnchorLiveStatus($uids)
    {
        $uids = (array) $uids;
        $key = $this->getLiveStatusCacheKey();
        $liveids = [];
        foreach ($uids as $uid)
        {
            $liveids[] = $this->getUidToLiveId($uid);
        }
        return $this->getRedis()->hMget($key, $liveids);
    }
    
    public function setUidToLiveid($uid,$liveid)
    {
        $key = $this->getUidToLiveIdCacheKey($uid);
        $try = 2;
        do
        {
            $status = $this->getRedis()->set($key,$liveid);
            if($status)
            {
                $this->getRedis()->expire($key, self::CACHE_TIME);
                return true;
            }
            usleep(1);
        } while ($try-- > 0);
        return false;
    }

    public function getUidToLiveId($uid)
    {
        $key = $this->getUidToLiveIdCacheKey($uid);
        return $this->getRedis()->get($key);
    }

    public function setLiveInfo(array $datas)
    {
        $key = $this->getLiveInfoCacheKey();
        $liveInfo = [];
        foreach ($datas as $v)
        {
            $liveInfo[$v['liveid']] = hp_json_encode($v);
        }
        $try = 2;
        do
        {
            $status = $this->getRedis()->hMset($key, $liveInfo);
            if ($status)
            {
                $this->getRedis()->expire($key, self::CACHE_TIME);
                return true;
            }
            usleep(1);
        } while ($try-- > 0);
        return false;
    }

    public function getLiveInfoByUid($uids)
    {
        $uids = (array) $uids;
        $key = $this->getLiveInfoCacheKey();

        $liveids = [];
        foreach ($uids as $uid)
        {
            $liveids[] = $this->getUidToLiveId($uid);
        }
        return $this->getRedis()->hMget($key, $liveids);
    }
    
    public function getLiveInfo($liveids)
    {
        $liveids = (array) $liveids;
        $key = $this->getLiveInfoCacheKey();

        return $this->getRedis()->hMget($key, $liveids);
    }
    
    public function setRecommendLiveList(array $datas)
    {
        $key = $this->getRecommendLiveListCacheKey();
        $try = 2;
        
        do
        {
            $status = $this->getRedis()->set($key, hp_json_encode($datas));
            if($status)
            {
                $this->getRedis()->expire($key,self::CACHE_TIME);
                return true;
            }
            usleep(1);
        } while ($try-- > 0);
        
        return false;
    }
    
    public function getRecommendLiveList()
    {
        $key = $this->getRecommendLiveListCacheKey();
        return $this->getRedis()->get($key);
    }
    
    public function setInformationList($type,$client,array $datas)
    {
        $key = $this->getInformationListCacheKey($type,$client);
        $try = 2;
        
        do{
            $status = $this->getRedis()->set($key, hp_json_encode($datas));
            if($status)
            {
                $this->getRedis()->expire($key,self::CACHE_TIME);
                return true;
            }
            usleep(1);
        } while ($try-- > 0);
        return false;
    }
    
    public function getInformationList($type,$client)
    {
        $key = $this->getInformationListCacheKey($type,$client);
        return $this->getRedis()->get($key);
    }

    public function getAllLiveListByViewCountCacheKey()
    {
        return self::ALL_LIVE_LIST_BY_VIEW_COUNT;
    }

    public function getAllLiveListByCtimeCacheKey()
    {
        return self::ALL_LIVE_LIST_BY_CTIME;
    }

    public function getAllLiveListByFollowCountCacheKey()
    {
        return self::ALL_LIVE_LIST_BY_FOLLOW_COUNT;
    }

    public function getLiveStatusCacheKey()
    {
        return self::LIVE_STATUS;
    }
    
    public function getUidToLiveIdCacheKey($uid)
    {
        return sprintf(self::UID_LIVEID_KEY,$uid);
    }

    public function getLiveInfoCacheKey()
    {
        return self::LIVE_INFO;
    }
    
    public function getRecommendLiveListCacheKey()
    {
        return self::RECOMMEND_LIVE_LIST;
    }
    
    public function getInformationListCacheKey($type,$client)
    {
        return self::INFORMATION_LIST . ':' . $type . ':' . $client;
    }

    public function getRedis()
    {
        return RedisHelper::getInstance(self::Live_REDIS_CONF);
    }

}
