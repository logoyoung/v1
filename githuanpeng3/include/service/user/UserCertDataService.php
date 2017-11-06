<?php
namespace service\user;
use Exception;
use service\common\AbstractService;
use system\DbHelper;
use lib\anchor\Anchor;
use lib\room\Roomid;
use lib\user\UserRealName;
use lib\user\ZhimaCert;
use service\anchor\AnchorGetDataService;
use service\user\helper\UserRedis;
use service\user\UserCertCreateService;
use service\event\EventManager;

class UserCertDataService extends AbstractService
{
    private $_userRealNameDb;
    private $_userRedis;
    private $_fromDb;
    private $_fromDbMaster;
    private $_zhimaCertDb;
    private $_uid;
    private $_log = 'user_cert_data_service';

    public function setUid($uid)
    {
        $this->_uid            = $uid;
        $this->_userRedis      = false;
        $this->_userRealNameDb = false;
        $this->_zhimaCertDb    = false;
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

    /**
     * 获取用户实名认证状态
     * @return int |false
     */
    public function getRealNameCertStatus()
    {
        if(!$this->getUid())
        {
            return false;
        }

        $redis       = $this->getUserRedis();
        $redisStatus = $redis->getRedis()->ping() ? true : false;
        $status      = false;

        if(!$this->getFromDb() && $redisStatus )
        {
            $status = (int) $redis->setUid($this->getUid())->getCertStatus();
            if($status !== -1)
            {
                return $status == UserCertCreateService::USER_STATUS_SUCC ? true : false;
            }

        }

        $userRealNameDb = $this->getUserRealNameDb();
        $userRealNameDb->setMaster($this->getFromDbMaster());
        $realNameData   = $userRealNameDb->getDataByUid($this->getUid(),['status']);
        if($realNameData === false)
        {
            $this->log("error|获取用户实名认证状态，mysql异常;uid:{$this->getUid()}");
            return false;
        }

        if(!$this->getFromDb() && $redisStatus)
        {
            $event = new EventManager;
            $event->trigger($event::ACTION_USER_CERT_UPDATE, ['uid' => $this->getUid()]);
            $event = null;
        }

        if(isset($realNameData[$this->getUid()]['status']) && (int) $realNameData[$this->getUid()]['status'] == UserCertCreateService::USER_STATUS_SUCC)
        {
            return true;
        }

        return false;
    }

    /**
     * 获取用户实名认证数据
     * @return array
     */
    public function getRealNameData()
    {
        if(!$this->getUid())
        {
            return false;
        }

        $redis       = $this->getUserRedis();
        $redisStatus = $redis->getRedis()->ping() ? true : false;
        if(!$this->getFromDb() && $redisStatus )
        {
            $certData = $redis->setUid($this->getUid())->getCertData();
            if($certData !== -1)
            {
                return $certData;
            }
        }

        $userRealNameDb = $this->getUserRealNameDb();
        $userRealNameDb->setMaster($this->getFromDbMaster());
        $userRealNameDb = $this->getUserRealNameDb();
        $userRealNameDb->setMaster($this->getFromDbMaster());
        $realNameData   = $userRealNameDb->getDataByUid($this->getUid());
        if($realNameData === false)
        {
            $this->log("error|获取用户实名认证信息，mysql异常;uid:{$this->getUid()}");
            return false;
        }

        if(!$this->getFromDb() && $redisStatus)
        {
            $event = new EventManager;
            $event->trigger($event::ACTION_USER_CERT_UPDATE, ['uid' => $this->getUid()]);
            $event = null;
        }

        return isset($realNameData[$this->getUid()]) ? $realNameData[$this->getUid()] : [];
    }

    /**
     * 获取用户芝麻认证状态
     * @return boolean
     */
    public function getZhimaCertStatus()
    {

    }

    public function getUserRedis()
    {
        if(!$this->_userRedis)
        {
            $this->_userRedis = new UserRedis;
        }

        return $this->_userRedis;
    }

    public function getUserRealNameDb()
    {
        if(!$this->_userRealNameDb)
        {
            $this->_userRealNameDb = new UserRealName;
        }

        return $this->_userRealNameDb;
    }

    public function getZhimaCertDb()
    {
        if(!$this->_zhimaCertDb)
        {
            $this->_zhimaCertDb  = new ZhimaCert;
        }

        return $this->_zhimaCertDb;
    }

    public function log($msg)
    {
        write_log($msg.';class:'.__CLASS__.$this->getCaller(),$this->_log);
    }
}