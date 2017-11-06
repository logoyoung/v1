<?php
namespace service\gift\helper;
use system\RedisHelper;

class GiftRedis
{
    const ROOM_REDIS_CONF  = 'huanpeng';
    const ROOM_GITF_LIST   = 'HP_GITF_LIST_%s';
    const CACHE_NUM        = 5;

    public function getRedis()
    {
        return RedisHelper::getInstance(self::ROOM_REDIS_CONF);
    }

    public function setGiftData(array $data)
    {
        $redis  = $this->getRedis();
        $status = [];
        $data   = hp_json_encode(array_values_to_string($data));
        for($i = 1 ; $i <= self::CACHE_NUM; $i++)
        {
            $status[] = $redis->set($this->getGiftKey($i),$data);
        }

        return array_search(false, $status, true) ? false : true;
    }

    public function getGiftData()
    {
        $key    = $this->getGiftKey(mt_rand(1, self::CACHE_NUM));
        $result = $this->getRedis()->get($key);
        return $result ? json_decode($result, true) : $result;
    }

    public function getGiftKey($n)
    {
        return sprintf(self::ROOM_GITF_LIST, $n);
    }
}