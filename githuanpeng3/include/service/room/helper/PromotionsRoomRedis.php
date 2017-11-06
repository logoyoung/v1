<?php
namespace service\room\helper;
use system\RedisHelper;

/**
 * 直播间活动信息
 * 暂时只做单key
 */
class PromotionsRoomRedis
{
    const ROOM_REDIS_CONF      = 'huanpeng';
    const HP_ROOM_PROMOTION_AD = 'HP_ROOM_PROMOTION_AD';

    public function getRedis()
    {
        return RedisHelper::getInstance(self::ROOM_REDIS_CONF);
    }

    public function setPromotionAd(array $data)
    {

        $key = $this->getPromotionAdKey();
        $try = 2;

        do {

            $status = $this->getRedis()->set($key,hp_json_encode($data));
            if($status)
            {
                return true;
            }

            usleep(1);
        } while ($try-- > 0);

        return false;
    }

    public function getPromotionAd()
    {
        $key = $this->getPromotionAdKey();
        if(!$this->getRedis()->exists($key))
        {
            return -1;
        }

        $result = $this->getRedis()->get($key);
        if ($result !== false)
        {
            return json_decode($result, true);
        }

        return false;
    }

    public function deletePromotionAd()
    {
        $key = $this->getPromotionAdKey();
        return $this->getRedis()->delete($key);
    }

    public function getPromotionAdKey()
    {
        return self::HP_ROOM_PROMOTION_AD;
    }

}