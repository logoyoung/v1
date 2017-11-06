<?php
namespace service\room\helper;
use service\room\helper\RoomidRedis;
use lib\room\Roomid;
use service\event\EventManager;

/**
 * 校验 修复 roomid redis与mysql数据不一致 脚本
 */
class RepairRoomidRedisData
{

    private $_uid;
    private $_roomidDb;
    private $_roomidRedis;
    private $_roomidDbData;
    private $_roomidRedisData;
    private $_status;
    private $_redisUid;
    private $_log    = 'cron_check_anchor';

    public function setUid($uid)
    {
        $this->_uid             = $uid;
        $this->_roomidDb        = null;
        $this->_roomidRedis     = null;
        $this->_roomidDbData    = null;
        $this->_roomidRedisData = null;
        $this->_status          = null;
        $this->_redisUid        = null;
        return $this;
    }

    public function getUid()
    {
        return $this->_uid;
    }

    public function setRedisUid($uid)
    {
        $this->_redisUid = $uid;
        return $this;
    }

    public function getRedisUid()
    {
        return $this->_redisUid;
    }

    public function setRoomidDbData($data)
    {
        if(is_array($data) && $data)
        {
            ksort($data);
        }
        $this->_roomidDbData = $data;
        return $this;
    }

    public function getRoomidDbData()
    {
        return $this->_roomidDbData;
    }

    public function setRoomidRedisData($data)
    {
        if(is_array($data) && $data)
        {
            ksort($data);
        }
        $this->_roomidRedisData = $data;
        return $this;
    }

    public function getRoomidRedisData()
    {
        return $this->_roomidRedisData;
    }

    public function checkDataStatus()
    {
        $uid = $this->getUid();
        if(!$uid)
        {
            $this->_status = false;
            return false;
        }

        if($this->_status !== null)
        {
            return $this->_status;
        }

        if(!$this->_initDbData() || !$this->_initRedisData())
        {
            $this->_status = false;
            return false;
        }

        if(hp_json_encode(array_values_to_string($this->getRoomidDbData())) != hp_json_encode(array_values_to_string($this->getRoomidRedisData())))
        {
            $this->_status = false;
            $this->log("error|roomid数据库异常roomid 与redis roomid 不一致; db_roomid;{$this->getRoomidDbData()};redis_roomid:{$this->getRoomidRedisData()};uid:{$uid};line:".__LINE__);
            return false;
        }

        if((int) $uid != (int) $this->getRedisUid())
        {
            $this->log("error|roomid redis uid  与数据库uid不一致 ;uid:{$uid};line:".__LINE__);
            $this->_status = false;
            return false;
        }

        $this->_status = true;
        return true;
    }

    public function rebuild()
    {
        if(!$this->getUid())
        {
            $this->log("error|uid不能为空;line:".__LINE__);
            return false;
        }

        if($this->checkDataStatus())
        {
            $this->log("info|roomid 数据库与redis一致;无需修复; uid:{$this->getUid()}");
            return true;
        }

        $event   = new EventManager();
        $event->trigger($event::ACTION_ROOMID_DATA_UPDATE,['uid' => $this->getUid() ]);
        $event   = null;
        $this->_status = null;
        $this->log("info|roomid 修复redis成功; uid:{$this->getUid()}");

        return true;
    }

    private function _initDbData()
    {
        $uid      = $this->getUid();
        $roomidDb = $this->getRoomidDb();
        $roomid   = $roomidDb->getRoomidByUid($uid);
        if($roomid === false)
        {
            $this->log("error|roomid数据库异常;uid:{$uid};line:".__LINE__);
            return false;
        }
        $this->setRoomidDbData($roomid);
        return true;
    }

    private function _initRedisData()
    {
        $uid         = $this->getUid();
        $roomidRedis = $this->getRoomidRedis();
        $roomid      = $roomidRedis->getRoomidByUid($uid);
        if($roomid === false)
        {
            $this->log("error|roomid redis异常;uid:{$uid};line:".__LINE__);
            return false;
        }

        $this->setRoomidRedisData($roomid);
        $redisUid = $roomidRedis->getUidByRoomid($roomid);
        $this->setRedisUid($redisUid);
        return true;
    }

    public function getRoomidDb()
    {
        if(!$this->_roomidDb)
        {
            $this->_roomidDb = new Roomid;
        }

        return $this->_roomidDb;
    }

    public function getRoomidRedis()
    {
        if(!$this->_roomidRedis)
        {
            $this->_roomidRedis = new RoomidRedis;
        }

        return $this->_roomidRedis;
    }

    public function log($msg)
    {
        write_log($msg,$this->_log);
    }
}