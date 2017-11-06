<?php
namespace service\anchor\helper;

class AnchorEventParam
{

    private $_param;

    public function setUid($uid)
    {
        $this->_param['uid'] = $uid;
        return $this;
    }

    public function getUid()
    {
        return isset($this->_param['uid']) ? $this->_param['uid'] : false;
    }

    public function getParam()
    {
        return $this->_param;
    }
}