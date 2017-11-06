<?php
namespace service\anchor\helper;
use service\anchor\helper\AnchorRedis;
use lib\anchor\Anchor;
use service\event\EventManager;

class RepairAnchorRedisData
{

    private $_uid;
    private $_anchorDb;
    private $_anchorRedis;
    private $_anchorDbData;
    private $_anchorRedisData;
    private $_anchorDbExist;
    private $_anchorRedisExist;
    private $_dbCertStatus;
    private $_redisCertStatus;
    private $_status;
    private $_log    = 'cron_check_anchor';

    public function setUid($uid)
    {

        $this->_uid               = $uid;
        $this->_anchorDb          = null;
        $this->_anchorRedis       = null;
        $this->_anchorDbData      = null;
        $this->_anchorRedisData   = null;
        $this->_anchorDbExist     = null;
        $this->_anchorRedisExist  = null;
        $this->_dbCertStatus      = null;
        $this->_redisCertStatus   = null;
        $this->_status            = null;
        return $this;
    }

    public function getUid()
    {
        return $this->_uid;
    }

    public function setAnchorDbExist($exist)
    {
        $this->_anchorDbExist = $exist;
        return $this;
    }

    public function getAnchorDbExist()
    {
        return $this->_anchorDbExist;
    }

    public function setAnchorRedisExist($exist)
    {
        $this->_anchorRedisExist = $exist;
        return $this;
    }

    public function getAnchorRedisExist()
    {
        return $this->_anchorRedisExist;
    }

    public function setCertDbStatus($status)
    {
        $this->_dbCertStatus = (int) $status;
        return $this;
    }

    public function getCertDbStatus()
    {
        return $this->_dbCertStatus;
    }

    public function setCertRedisStatus($status)
    {
        $this->_redisCertStatus = (int) $status;
        return $this;
    }

    public function getCertRedisStatus()
    {
        return $this->_redisCertStatus;
    }

    public function setAnchorDbData($data)
    {
        if(is_array($data) && $data)
        {
            ksort($data);
        }

        $this->_anchorDbData = $data;
        return $this;
    }

    public function getAnchorDbData()
    {
        return $this->_anchorDbData;
    }

    public function setAnchorRedisData($data)
    {
        if(is_array($data) && $data)
        {
            ksort($data);
        }

        $this->_anchorRedisData = $data;
        return $this;
    }

    public function getAnchorRedisData()
    {
        return $this->_anchorRedisData;
    }

    private function _initDbData()
    {
        $uid      = $this->getUid();
        $anchorDb = $this->getAnchorDb();

        if($this->getAnchorDbData() === null)
        {
            $anchorData = $anchorDb->getAnchorDataByUid($uid);
            if($anchorData === false)
            {
                $this->log("error|初始化主播db信息异常;uid:{$uid}");
                return false;
            }

            $anchorData = $anchorData ? $anchorData[$uid] : [];
            $this->setAnchorDbExist(($anchorData ? 1 : 0));
            $this->setAnchorDbData($anchorData);

            if(!$anchorData)
            {
                $this->log("info|数据库没有主播数据;uid:{$uid}");
                $this->setCertDbStatus(0);
            } else
            {
                $this->setCertDbStatus($anchorData['cert_status']);
            }

        }

        return true;
    }

    private function _initRedisData()
    {
        $uid         = $this->getUid();
        $anchorRedis = $this->getAnchorRedis();
        $isExist     = $anchorRedis->isExist($uid);
        $this->setAnchorRedisExist($isExist === 1 ? 1 : 0);
        if(!$isExist)
        {
            return true;
        }

        $certStatus  = $anchorRedis->getCertStatus($uid);
        $redisData   = $anchorRedis->getAnchorData($uid);
        $this->setCertRedisStatus($certStatus);
        $this->setAnchorRedisData($redisData);

        return true;
    }

    public function checkDataStatus()
    {
        if(!$this->getUid())
        {
            return false;
        }

        $uid = $this->getUid();
        if($this->_status !== null)
        {
            return $this->_status;
        }

        if(!$this->_initDbData() || !$this->_initRedisData())
        {
            $this->_status = false;
            return false;
        }

        if(hp_json_encode(array_values_to_string($this->getAnchorDbExist())) != hp_json_encode(array_values_to_string($this->getAnchorRedisExist())))
        {
            $this->log("info|存在状态 数据库与redis 数据不一致;uid:{$uid}");
            $this->_status = false;
            return false;
        }

        if(hp_json_encode(array_values_to_string($this->getCertDbStatus())) != hp_json_encode(array_values_to_string($this->getCertRedisStatus())))
        {
            $this->log("info|认证状态 数据库与redis 数据状态不一致;uid:{$uid}");
            $this->_status = false;
            return false;
        }

        if(hp_json_encode(array_values_to_string($this->getAnchorDbData())) != hp_json_encode(array_values_to_string($this->getAnchorRedisData())))
        {
            $this->log("info|认证状态 数据库与redis 数据状态不一致;uid:{$uid}");
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
            return false;
        }

        if($this->checkDataStatus())
        {
            $this->log("info|anchor 数据库与redis一致;无需修复; uid:{$this->getUid()}");
            return true;
        }

        $event   = new EventManager();
        $event->trigger($event::ACTION_ANCHOR_DATA_UPDATE,['uid' => $this->getUid() ]);
        $event   = null;
        $this->_status = null;
        $this->log("info|anchor 修复redis成功; uid:{$this->getUid()}");
        return true;
    }

    public function getAnchorDb()
    {
        if(!$this->_anchorDb)
        {
            $this->_anchorDb = new Anchor;
        }

        return $this->_anchorDb;
    }

    public function getAnchorRedis()
    {
        if(!$this->_anchorRedis)
        {
            $this->_anchorRedis = new AnchorRedis;
        }

        return $this->_anchorRedis;
    }

    public function log($msg)
    {
        write_log($msg,$this->_log);
    }

}