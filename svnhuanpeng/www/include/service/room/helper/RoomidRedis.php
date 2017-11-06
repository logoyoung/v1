<?php
namespace service\room\helper;
use system\RedisHelper;

class RoomidRedis
{
    //用户redis池子 string
    const ROOM_REDIS_CONF  = 'huanpeng';

    //roomid与uid绑定关系
    const HP_ROOMID_TO_UID = 'HP_ROOMID_TO_UID_%s';

    //uid与roomid绑定关系
    const HP_UID_TO_ROOMID = 'HP_UID_TO_ROOMID_%s';

    public function getRedis()
    {
        return RedisHelper::getInstance(self::ROOM_REDIS_CONF);
    }

    private  function _getRoomidToUidKey($roomid)
    {
        return sprintf(self::HP_ROOMID_TO_UID, $roomid);
    }

    private  function _getUidToRoomidKey($uid)
    {
        return sprintf(self::HP_UID_TO_ROOMID, $uid);
    }

    public function setRoomidToUid($roomid,$uid = 0)
    {
        if(!$roomid)
        {
            return false;
        }

        $key = $this->_getRoomidToUidKey($roomid);
        $try = 2;

        do {

            $status = $this->getRedis()->set($key,$uid);
            if($status)
            {
                return true;
            }
            usleep(1);
        } while ($try-- > 0);

        return false;
    }

    public function getUidByRoomid($roomid)
    {
        if(!$roomid)
        {
            return false;
        }

        $key = $this->_getRoomidToUidKey($roomid);
        if(!$this->getRedis()->exists($key))
        {
            return -1;
        }

        $result = $this->getRedis()->get($key);
        return $result !== false ?  (int) $result : false;
    }

    public function setUidToRoomid($uid,$roomid = 0)
    {
        if(!$uid)
        {
            return false;
        }
        $key = $this->_getUidToRoomidKey($uid);
        $try = 2;

        do {

            $status = $this->getRedis()->set($key,$roomid);
            if($status)
            {
                return true;
            }
            usleep(1);
        } while ($try-- > 0);

        return false;

    }

    public function getRoomidByUid($uid)
    {
        if(!$uid)
        {
            return false;
        }

        $key = $this->_getUidToRoomidKey($uid);
        if(!$this->getRedis()->exists($key))
        {
            return -1;
        }

        $result = $this->getRedis()->get($key);
        return $result !== false ?  (int) $result : false;
    }
}