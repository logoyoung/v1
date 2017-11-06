<?php
namespace service\user\helper;
use system\RedisHelper;

class UserViewLengthRedis
{

    //用户redis池子 string
    const USER_REDIS_CONF  = 'huanpeng';

    //用户观看直播时长信息key
    const USER_LIVE_VIEW_LENGTH_DATA          = 'HP_UUSER_LIVE_VIEW_LENGTH_DATA';

    public function setUserLiveViewDataRedis($data)
    {
        $key = $this->getUserLiveViewLengthKey();
        $this->getRedis()->lPush($key, hp_json_encode(array_values_to_string($data)));
    }
    
    public function getUserLiveViewData(int $page =1 , int $size = 0)
    {
        $key = $this->getUserLiveViewLengthKey();
        
        $start = -($page) * $size + 1;
        $end   = $start + $size - 1;
        
        return $this->getRedis()->lrange($key,$page,$size);
    }
    
    public function getFirstUserLiveViewData()
    {
        $key = $this->getUserLiveViewLengthKey();
        return $this->getRedis()->lPop($key);
    }
    
    public function getLastUserLiveViewData()
    {
        $key = $this->getUserLiveViewLengthKey();
        return $this->getRedis()->rPop($key);
    }

    public function getUserLiveViewLengthKey()
    {
        return self::USER_LIVE_VIEW_LENGTH_DATA;
    }

    public function getRedis()
    {
        return RedisHelper::getInstance(self::USER_REDIS_CONF);
    }

}