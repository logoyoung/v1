<?php
namespace service\room\helper;

class RoomidEventParam
{
    private $_param = [];

    public function setUid($uid)
    {
        $this->_param['uid'] = $uid;
        return $this;
    }

    public function getUid()
    {
         return isset($this->_param['uid']) ? $this->_param['uid'] : false;
    }

    public function setRoomid($roomid)
    {
        $this->_param['roomid'] = $roomid;
        return $this;
    }

    public function getRoomid()
    {
        return isset($this->_param['roomid']) ? $this->_param['roomid'] : false;
    }

    public function getParam()
    {
        return $this->_param;
    }
}