<?php
namespace service\follow;
use Exception;
use service\common\AbstractService;
use service\follow\helper\UserFollowDb;

class FollowManagerService extends AbstractService
{

    private $_uid;
    private $_objectUid;
    private $_followDb;

    public function setUid($uid)
    {
        $this->_uid = (int) $uid;
        return $this;
    }

    public function getUid()
    {
        return $this->_uid;
    }

    public function setObjectUid($objectUid)
    {
        $this->_objectUid = (int) $objectUid;
        return $this;
    }

    public function getObjectUid()
    {
        return $this->_objectUid;
    }

    public function isFollow()
    {
        $followDb = $this->getFollowDb();
        return $followDb->getFollowStatusByUidObjectUid($this->getUid(),$this->getObjectUid()) ? 1 : 0;
    }

    public function follow()
    {

    }

    public function unFollow()
    {

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