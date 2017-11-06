<?php
namespace service\user\helper;

class UserEventParam
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

    public function setExist($status = 1)
    {
        $this->_param['exist'] = (int) $status;
        return $this;
    }

    public function getExsit()
    {
        return isset($this->_param['exist']) ? $this->_param['exist'] : false;
    }

    public function setPhone($phone)
    {
        $this->_param['phone'] = $phone;
        return $this;
    }

    public function getPhone()
    {
        return isset($this->_param['phone']) ? $this->_param['phone'] : false;
    }

    public function setNick($nick)
    {
        $this->_param['nick'] = $nick;
        return $this;
    }

    public function getNick()
    {
        return isset($this->_param['nick']) ? $this->_param['nick'] : false;
    }

    public function setEncpass($encpass)
    {
        $this->_param['encpass'] = $encpass;
        return $this;
    }

    public function getEncpass()
    {
        return isset($this->_param['encpass']) ? $this->_param['encpass'] : false;
    }

    public function setPassword($password)
    {
        $this->_param['password'] = $password;
        return $this;
    }

    public function getPassword()
    {
        return isset($this->_param['password']) ? $this->_param['password'] : false;
    }

    public function setUserStaticData(array $userStaticData)
    {
        $this->_param['userstatic'] = $userStaticData;
        return $this;
    }

    public function getUserStaticData()
    {
        return isset($this->_param['userstatic']) ? $this->_param['userstatic'] : false;
    }

    public function setUserActiveData(array $userActiveData)
    {
        $this->_param['useractive'] = $userActiveData;
        return $this;
    }

    public function getUserActiveData()
    {
        return isset($this->_param['useractive']) ? $this->_param['useractive'] : false;
    }

    public function setUserDisableLoginStatusData(array $status)
    {
        $this->_param['disableLoginStatus'] = $status;
        return $this;
    }

    public function getUserDisableLoginStatusData()
    {
        return isset($this->_param['disableLoginStatus']) ? $this->_param['disableLoginStatus'] : false;
    }

    public function setDisableType($type)
    {
        $this->_param['disableType'] = $type;
        return $this;
    }

    public function getDisableType()
    {
        return isset($this->_param['disableType']) ? $this->_param['disableType'] : false;
    }

    public function setDisableScope($scope)
    {
        $this->_param['disableScope'] = $scope;
        return $this;
    }

    public function getDisableScope()
    {
        return isset($this->_param['disableScope']) ? $this->_param['disableScope'] : false;
    }

    public function setDisableEtime($etime)
    {
        $this->_param['disableEtime'] = $etime;
        return $this;
    }

    public function getDisableEtime()
    {
        return isset($this->_param['disableEtime']) ? $this->_param['disableEtime'] : false;
    }

    public function getParam()
    {
        return $this->_param;
    }

}