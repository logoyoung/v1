<?php
namespace service\follow;
use Exception;
use service\common\AbstractService;
use service\follow\helper\UserFollowDb;

class FollowDataService extends AbstractService
{

    private $_uid;
    private $_followDb;

    public function setUid($uid)
    {
        $this->_uid = $uid;
        return $this;
    }

    public function getUid()
    {
        return $this->_uid;
    }

    public function getFansTotalNum()
    {
        $followDb = $this->getFollowDb();
        $totalNum = $followDb->getFansTotalNumByUid($this->getUid());
        return (int) $totalNum;
    }

    public function getFollowDb()
    {
        if(!$this->_followDb)
        {
            $this->_followDb = new UserFollowDb;
        }

        return $this->_followDb;
    }
}