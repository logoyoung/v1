<?php
namespace service\anchor;

use service\common\AbstractService;
use service\anchor\helper\AnchorRedis;
use lib\anchor\Anchor;
use service\event\EventManager;

class AnchorGetDataService extends AbstractService
{

    private $_uid;
    private $_anchorData;
    private $_anchorDb;
    private $_anchorRedis;
    private $_fromDb       = false;
    private $_fromDbMaster = false;
    private $_infoLog      = 'anchor_data_access';
    private $_errorLog     = 'anchor_data_error';

    public function setUid($uid)
    {
        $this->_uid = is_array($uid) ? array_values((array_unique($uid))) : $uid;
        $this->_anchorData   = [];
        $this->_anchorDb     = false;
        $this->_anchorRedis  = false;
        $this->_fromDb       = false;
        $this->_fromDbMaster = false;
        return $this;
    }

    public function getUid()
    {
        return $this->_uid;
    }

    public function setFromDb($fromDb = true)
    {
        $this->_fromDb = $fromDb;
        return $this;
    }

    public function getFromDb()
    {
        return $this->_fromDb;
    }

    public function setFromDbMaster($master = true)
    {
        $this->_fromDbMaster = $master;
        return $this;
    }

    public function getFromDbMaster()
    {
        return $this->_fromDbMaster;
    }

    public function isExist()
    {
        $uid = $this->getUid();
        if(!$uid)
        {
            return false;
        }

        $anchorRedis = $this->getAnchorRedis();

        if(!$this->getFromDb() && $anchorRedis->getRedis()->ping())
        {
            $isExist     = $anchorRedis->isExist($uid);

            if($isExist === 1)
            {
                return true;
            }

            if($isExist === 0)
            {
                return false;
            }

            if($isExist === false)
            {
                $this->logError("redis 服务异常;uid:{$uid};fun:".__FUNCTION__.';line:'.__LINE__);
            }

            $anchorDb = $this->getAnchorDb();
            $anchorDb->setMaster($this->getFromDbMaster());
            $data     = $anchorDb->getAnchorDataByUid($uid,['uid']);
            if($data === false)
            {
                $this->logError("mysql 服务异常;uid:{$uid};fun:".__FUNCTION__.';line:'.__LINE__);
                return false;
            }

            $event   = new EventManager;
            $action  = EventManager::ACTION_ANCHOR_RESET_CACHE;
            $event->trigger($action,['uid' => $uid]);
            $event   = null;

        } else
        {
            $anchorDb = $this->getAnchorDb();
            $anchorDb->setMaster($this->getFromDbMaster());
            $data     = $anchorDb->getAnchorDataByUid($uid,['uid']);
            if($data === false)
            {
                $this->logError("mysql 服务异常;uid:{$uid};fun:".__FUNCTION__.';line:'.__LINE__);
                return false;
            }
        }

        return $data ? true : false;
    }

    public function getCertStatus()
    {
        if(!$this->isExist())
        {
            return 0;
        }

        $uid         = $this->getUid();
        $anchorRedis = $this->getAnchorRedis();

        if(!$this->getFromDb() && $anchorRedis->getRedis()->ping())
        {
            $status      = $anchorRedis->getCertStatus($uid);
            if($status !== false)
            {
                return (int) $status;
            }
        }

        $anchorDb = $this->getAnchorDb();
        $anchorDb->setMaster($this->getFromDbMaster());
        $data     = $anchorDb->getAnchorDataByUid($uid,['cert_status']);
        if($data === false)
        {
            $this->logError("mysql 服务异常;uid:{$uid};fun:".__FUNCTION__.';line:'.__LINE__);
            return 0;
        }

        return isset($data[$uid]['cert_status']) ? $data[$uid]['cert_status'] : 0;
    }

    public function getAnchorData()
    {
        $uid = $this->getUid();
        if(!$uid)
        {
            return false;
        }

        $uid    = (array) $uid;
        $dbUid  = [];
        $result = [];
        $anchorRedis = $this->getAnchorRedis();

        if(!$this->getFromDb() && $anchorRedis->getRedis()->ping())
        {
            foreach ($uid as $u)
            {

                $isExist = $anchorRedis->isExist($u);

                if($isExist === false)
                {
                    //redis error
                    $dbUid[] = $u;
                    $this->logError("redis 服务异常;uid:{$u};fun:".__FUNCTION__.';line:'.__LINE__);
                    continue;
                }

                if($isExist === -1)
                {
                    $dbUid[] = $u;
                    continue;
                }

                if($isExist === 0)
                {
                    continue;
                }

                $data = $anchorRedis->getAnchorData($u);
                if($data === false)
                {
                    //redis error
                    $dbUid[] = $u;
                    $this->logError("redis 服务异常;uid:{$u};fun:".__FUNCTION__.';line:'.__LINE__);
                    continue;
                }

                $result[$u] = $data;
                $data       = [];
            }

            if($dbUid)
            {
                $anchorDb = $this->getAnchorDb();
                $anchorDb->setMaster($this->getFromDbMaster());
                $data     = $anchorDb->getAnchorDataByUid($dbUid);
                if($data !== false)
                {
                    $result  = $result ? ((array) $result + (array) $data) : $data;
                    $event   = new EventManager;
                    $action  = EventManager::ACTION_ANCHOR_RESET_CACHE;

                    foreach ($dbUid as $_u)
                    {
                        $event->trigger($action,['uid' => $_u]);
                    }

                    $event   = null;

                } else
                {
                     //db error
                     $this->logError("mysql 服务异常;uid:".implode(',', $dbUid).";fun:".__FUNCTION__.';line:'.__LINE__);
                }
            }

        } else
        {
            $anchorDb = $this->getAnchorDb();
            $anchorDb->setMaster($this->getFromDbMaster());
            $result   = $anchorDb->getAnchorDataByUid($uid);
            if($result === false)
            {
                //db error
                $this->logError("mysql 服务异常;uid:".implode(',', $uid).";fun:".__FUNCTION__.';line:'.__LINE__);
                return false;
            }
        }

        foreach ($result as &$v)
        {
            if(isset($v['level']))
            {
                $v['level_to_integral'] = get_anchor_integral_by_level($v['level']);
            }
        }

        return !is_array($this->getUid()) ? (isset($result[$this->getUid()]) ? $result[$this->getUid()] : $result ) : $result;
    }

    /**
     *  获取所有等级列表
     * @return array
     */
    public function getAnchorLevelList()
    {
        return get_anchor_integral_by_level();
    }

    public function getAnchorDb()
    {
        if(!$this->_anchorDb)
        {
            $this->_anchorDb = new Anchor();
        }

        return $this->_anchorDb;
    }

    public function getAnchorRedis()
    {
        if(!$this->_anchorRedis)
        {
            $this->_anchorRedis = new AnchorRedis();
        }

        return $this->_anchorRedis;
    }

    public function logInfo($msg)
    {
        write_log("info|{$msg};class:".__CLASS__.";caller:.{$this->getCaller()}",$this->_infoLog);
    }

    public function logError($msg)
    {
        write_log("error|{$msg};class:".__CLASS__.";caller:.{$this->getCaller()}",$this->_errorLog);
    }
}