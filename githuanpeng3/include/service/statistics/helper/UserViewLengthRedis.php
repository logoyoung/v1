<?php
namespace service\statistics\helper;
use system\RedisHelper;
use system\Crc;
use Exception;

class UserViewLengthRedis
{

    //用户redis池子 string
    const USER_REDIS_CONF  = 'huanpeng';

    //用户观看时长记录观看用户uid的KEY
    const USER_LIVE_VIEW_LENGTH_UIDS    = 'HP_USER_LIVE_VIEW_LENGTH_UIDS_%s_%s';
    //用户观看时长信息KEY
    const USER_LIVE_VIEW_LENGTH_DATA    = 'HP_USER_LIVE_VIEW_LENGTH_DATA_%s';      
    //缓存时间
    const CACHE_TIME = 600;
    //队列数
    const QUENE_NUM = 3;

    public function setUserLiveViewLengthUids($date,$uid)
    {
        $mod = $this->getMod($uid);
        $key = $this->getUserLiveViewLengthUidsKey($date,$mod);
        try
        {
            $this->getRedis()->zAdd($key,$uid,$uid);
            $this->getRedis()->expire($key,self::CACHE_TIME);
            return true;    
        } catch (Exception $exc)
        {
            return false;
        }

    }
    
    public function getUserLiveViewLengthUids($date,int $mod = 0,int $page = 1,int $size = 0)
    {
        $start = ($page - 1) * $size;
        $end = $start + $size - 1;
        $key = $this->getUserLiveViewLengthUidsKey($date,$mod);
        return $this->getRedis()->zRange($key,$start,$end);
    }
    
    public function getUserLiveViewLengthUidsCount($date, int $mod = 0)
    {
        $key = $this->getUserLiveViewLengthUidsKey($date, $mod);
        return $this->getRedis()->zCard($key);
    }

    public function rmUserLiveViewLengthUid($date,int $mod = 0,int $uid = 0)
    {
        if(!$uid)
        {
            return false;
        }
        $key = $this->getUserLiveViewLengthUidsKey($date, $mod);
        return $this->getRedis()->zRem($key,$uid);
    }

    public function setUserLiveViewDataRedis($date,int $uid,array $data)
    {
        if(!$uid)
        {
            return false;
        }
        
        $key = $this->getUserLiveViewLengthDataKey($date);
        try
        {
            $this->getRedis()->hSet($key,$uid, hp_json_encode(array_values_to_string($data)));
            $this->getRedis()->expire($key, self::CACHE_TIME);
            return true;
        } catch (Exception $exc)
        {
            return false;
        }

    }
    
    public function getUserLiveViewData($date,int $uid)
    {
        if(!$uid)
        {
            return false;
        }
        
        $key = $this->getUserLiveViewLengthDataKey($date);

        return $this->getRedis()->hGet($key,$uid);
    }
    
    public function getUserLiveViewLengthUidsKey($date,$mod)
    {
        return sprintf(self::USER_LIVE_VIEW_LENGTH_UIDS,$date,$mod);
    }

    public function getUserLiveViewLengthDataKey($date)
    {
        return sprintf(self::USER_LIVE_VIEW_LENGTH_DATA,$date);
    }
    
    public function getMod($str)
    {
        $crcInt = Crc::x_crc32($str);
        return $crcInt % self::QUENE_NUM;
    }

    public function getRedis()
    {
        return RedisHelper::getInstance(self::USER_REDIS_CONF);
    }

}