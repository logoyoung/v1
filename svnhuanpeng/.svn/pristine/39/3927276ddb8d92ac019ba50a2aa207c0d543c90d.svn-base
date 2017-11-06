<?php
namespace service\user\helper;
use lib\user\UserStatic;
use lib\user\UserActive;
use service\event\EventManager;
use service\common\AbstractService;
use service\user\helper\UserRedis;
use lib\user\UserDisableStatus;
use service\user\UserDisableStatusService;

/**
 * 校验或修复用户redis与数据库是否一致
 */
class RepairUserRedisData extends AbstractService
{

    private $_uid;
    private $_userstaticDbData;
    private $_useractiveDbData;
    private $_userDisableLoginDbData;
    private $_passwordDb;
    private $_passwordRedis;
    private $_encpassDb;
    private $_encpassRedis;
    private $_logName;
    private $_status;
    private $_userRedis;
    private $_userStaticRedisData;
    private $_userActiveRedisData;
    private $_userDisableLoginRedisData;
    private $_silencedDbData;
    private $_silencedRedisData;
    private $_disableLiveDbData;
    private $_disableLiveRedisData;
    private $_defaultLog = 'repair_user_redis_data';

    public function setUid($uid)
    {

        $this->_uid        = $uid;
        $this->_userRedis  = new UserRedis();
        $this->_status     = null;
        $this->_logName    = $this->_defaultLog;
        $this->_userstaticDbData  = null;
        $this->_useractiveDbData  = null;
        $this->_passwordDb        = null;
        $this->_encpassDb         = null;
        $this->_userDisableLoginDbData = null;
        $this->_userStaticRedisData       = null;
        $this->_userActiveRedisData       = null;
        $this->_passwordRedis             = null;
        $this->_encpassRedis              = null;
        $this->_userDisableLoginRedisData = null;
        $this->_silencedDbData       = null;
        $this->_silencedRedisData    = null;
        $this->_disableLiveDbData    = null;
        $this->_disableLiveRedisData = null;
        return $this;
    }

    public function getUid()
    {
        return $this->_uid;
    }

    public function setUserStaticDbData($userstatic)
    {
        if(is_array($userstatic))
        {
            $this->_passwordDb = isset($userstatic['password']) ? $userstatic['password'] : '';
            $this->_encpassDb  = isset($userstatic['encpass'])  ? $userstatic['encpass']  : '';
            unset($userstatic['password'],$userstatic['encpass']);
            ksort($userstatic);
        }

        $this->_userstaticDbData = $userstatic;
        return $this;
    }

    public function getUserStaticDbData()
    {
        return $this->_userstaticDbData;
    }

    public function setUserStaticRedisData($userStaticRedisData)
    {
        if(is_array($userStaticRedisData))
        {
            ksort($userStaticRedisData);
        }

        $this->_userStaticRedisData = $userStaticRedisData;
        return $this;
    }

    public function getUserStaticRedisData()
    {
        return $this->_userStaticRedisData;
    }

    public function setUserActiveDbData($useractive)
    {
        if(is_array($useractive))
        {
            ksort($useractive);
        }
        $this->_useractiveDbData = $useractive;
        return $this;
    }

    public function getUserActiveDbData()
    {
        return $this->_useractiveDbData;
    }

    public function setUserActiveRedisData($userActiveRedisData)
    {
        if(is_array($userActiveRedisData))
        {
            ksort($userActiveRedisData);
        }

        $this->_userActiveRedisData = $userActiveRedisData;
        return $this;
    }

    public function getUserActiveRedisData()
    {
        return $this->_userActiveRedisData;
    }

    public function setDisableLoginDbData($diableLoginData)
    {
        if(is_array($diableLoginData))
        {
            ksort($diableLoginData);
        }
        $this->_userDisableLoginDbData = $diableLoginData;
        return $this;
    }

    public function getDisableLoginDbData()
    {
        return $this->_userDisableLoginDbData;
    }

    public function setDisableLoginRedisData($disableLoginRedisData)
    {
        if(is_array($disableLoginRedisData))
        {
            ksort($disableLoginRedisData);
        }
        $this->_userDisableLoginRedisData = $disableLoginRedisData;
        return $this;
    }

    public function getDisableLoginRedisData()
    {
        return $this->_userDisableLoginRedisData;
    }

    public function setSilencedDbData($silencedDbData)
    {
        if(is_array($silencedDbData) && $silencedDbData)
        {
            ksort($silencedDbData);
        }
        $this->_silencedDbData = $silencedDbData;
        return $this;
    }

    public function getSilencedDbData()
    {
        return $this->_silencedDbData;
    }

    public function setSilencedRedisData($silencedRedisData)
    {
        if(is_array($silencedRedisData) && $silencedRedisData)
        {
            ksort($silencedRedisData);
        }

        $this->_silencedRedisData = $silencedRedisData;
        return $this;
    }

    public function getSilencedRedisData()
    {
        return $this->_silencedRedisData;
    }

    public function setDisableLiveDbData($data)
    {
        if(is_array($data) && $data)
        {
            ksort($data);
        }

        $this->_disableLiveDbData = $data;
        return $this;
    }

    public function getDisableLiveDbData()
    {
        return $this->_disableLiveDbData;
    }

    public function setDisableLiveRedisData($data)
    {
        if(is_array($data) && $data)
        {
            ksort($data);
        }

        $this->_disableLiveRedisData = $data;
        return $this;
    }

    public function getDisableLiveDedisData()
    {
        return $this->_disableLiveRedisData;
    }

    public function setLogName($logName)
    {
        $this->_logName = $logName;
        return $this;
    }

    public function getLogName()
    {
        return $this->_logName?:$this->_defaultLog;
    }

    private function _initDbData()
    {
        if($this->_status !== null)
        {
            return $this->_status;
        }

        if(!$this->getUserStaticDbData())
        {
            $staticDao = $this->_getUserStaticDao();
            $userstatic = $staticDao->getUserStaticData($this->getUid());
            if(!$userstatic)
            {
                $this->log("error|从数据库没有获到 userstatic 信息，uid:{$this->getUid()}");
                return false;

            }

            $userstatic = $userstatic[$this->getUid()];
            $this->setUserStaticDbData($userstatic);
            unset($userstatic);
        }

        if(!$this->getUserActiveDbData())
        {
            $activeDao  = $this->_getUserActiveDao();
            $activeData = $activeDao->getUserActiveData($this->getUid());
            if(!$activeData)
            {
                $this->log("error|从数据库没有获取到 useractive，uid:{$this->getUid()}");
                return false;
            }

            $activeData = $activeData[$this->getUid()];
            $this->setUserActiveDbData($activeData);
            unset($activeData);
        }

        if($this->getDisableLoginDbData() === null)
        {
            try {

                $disableService   = new UserDisableStatusService();
                $disableService->setUid($this->getUid());
                $disableService->setFromDb(true);
                $disableLoginData = $disableService->getDisableLoginStatus();

                if($disableLoginData === false)
                {
                    $this->log("error|从数据库获取用户封禁异常;uid:{$this->getUid()}; error_code:{$e->getCode()};error_msg{$e->getMessage()}");
                    return false;
                }

                $this->setDisableLoginDbData($disableLoginData === true ? [] : (array) $disableLoginData);
                unset($disableLoginData);

            } catch (Exception $e) {
                $this->log("error|从数据库获取用户封禁异常;uid:{$this->getUid()}; error_code:{$e->getCode()};error_msg{$e->getMessage()}");
                return false;
            }

        }

        if($this->getSilencedDbData() === null)
        {

            $disableDao     = $this->_getUserDisableStatusDao();
            $silencedDbData = $disableDao->getSilencedStatusByUidType($this->getUid(),UserDisableStatusService::USER_DISABLE_TYPE_SEND_MSG);

            if($silencedDbData === false)
            {
                $this->log("error|从数据库获取用户禁言异常;uid:{$this->getUid()}");
                return false;
            }

            if($silencedDbData){
                array_walk($silencedDbData, function (&$v,$k) {
                    unset($v['utime'],$v['sid']);
                });
            }

            $this->setSilencedDbData((array) $silencedDbData);
            unset($silencedDbData);
        }

        if($this->getDisableLiveDbData() === null)
        {
            $disableDao        = $this->_getUserDisableStatusDao();
            $disableLiveDbData = $disableDao->getDisableStatusByUidTypeScope($this->getUid(),UserDisableStatusService::USER_DISABLE_TYPE_LIVE,UserDisableStatusService::USER_DISABLE_SCOPE_ALL);
            if($disableLiveDbData === false)
            {
                $this->log("error|从数据库获取用户禁言异常;uid:{$this->getUid()}");
                return false;
            }
            $disableLiveDbData = isset($disableLiveDbData[0]) ? $disableLiveDbData[0] : [];
            if($disableLiveDbData)
            {
                unset($disableLiveDbData['utime'],$disableLiveDbData['sid']);
            }

            $this->setDisableLiveDbData((array) $disableLiveDbData);
            unset($disableLiveDbData);
        }

        return true;
    }

    private function _initRedisData()
    {
        $uid = $this->getUid();
        $this->_userRedis->setUid($uid);
        if($this->_userRedis->isExist() !== true)
        {
            $this->log("notice|uid:{$uid}; redis数据不存在，需要重新构建");
            return false;
        }

        $passwordArr      = $this->_userRedis->getPassword();
        $encpassArr       = $this->_userRedis->getEncpass();
        $this->_passwordRedis = isset($passwordArr['password']) ? $passwordArr['password'] : '';
        $this->_encpassRedis  = isset($encpassArr['encpass'])   ? $encpassArr['encpass']   : '';
        $staticActiveData = $this->_userRedis->setGetUserStatic(true)->setGetUserActive(true)->getUserData();
        if(!$staticActiveData[UserRedis::USER_STATIC_NAME])
        {
            $this->log("warning|读取 redis userstatic 异常;uid:{$uid}");
            return false;
        }

        if(!$staticActiveData[UserRedis::USER_ACTIVE_NAME])
        {
            $this->log("warning|读取 redis active 异常;uid:{$uid}");
            return false;
        }

        $this->setUserActiveRedisData($staticActiveData[UserRedis::USER_ACTIVE_NAME]);

        $userStaticRedisData = $staticActiveData[UserRedis::USER_STATIC_NAME];
        if(isset($userStaticRedisData['phone']) && $userStaticRedisData['phone'])
        {
             $rdsPhone   = $userStaticRedisData['phone'];
             $this->_userRedis->setPhone($rdsPhone);
             $phoneToUid = $this->_userRedis->getUidByPhone();
             if($phoneToUid != $uid)
             {
                $this->log("warning|出现两个相同的手机号，redis phone 转换的uid 与实际uid 不相等;phone:{$rdsPhone};db_uid:{$uid}; phoneToUid:{$phoneToUid};uid:{$uid}");
                return false;
             }
        }

        if(isset($userStaticRedisData['nick']) && $userStaticRedisData['nick'])
        {
            $rdsNick    = $userStaticRedisData['nick'];
            $nickToUid  = $this->_userRedis->getUidByNick($rdsNick);
            if($nickToUid != $uid)
            {
                $this->log("warning|出现两个相同昵称，redis nick 转uid 异常,nick:{$rdsNick} db_uid:{$uid};nickToUid:{$nickToUid};uid:{$uid}");
                return false;
            }
        }

        $this->setUserStaticRedisData($userStaticRedisData);

        $disableService   = new UserDisableStatusService();
        $disableService->setUid($uid);
        $disableService->setFromDb(false);
        $disableLoginData = $disableService->getDisableLoginStatus();
        $this->setDisableLoginRedisData($disableLoginData === true ? []: (array) $disableLoginData);

        $silencedRedisData = $this->_userRedis->getSilencedByAnchorUid(null);
        $this->setSilencedRedisData(($silencedRedisData === true ? [] : (array) $silencedRedisData));

        $disableLiveRedisData = $this->_userRedis->getDisableLiveStatus();
        $this->setDisableLiveRedisData(($disableLiveRedisData === true ? [] : (array) $disableLiveRedisData));
        return true;
    }

    /**
     *   校验redis与db数据是否一致
     * @return true |false
     */
    public function checkDataStatus()
    {
        if($this->_status !== null)
        {
            return $this->_status;
        }

        //初始化db与redis数据
        if($this->_initDbData() === false || $this->_initRedisData() === false)
        {
            $this->_status = false;
            return $this->_status;
        }

        //比较password
        if($this->_passwordDb != $this->_passwordRedis)
        {
            $this->log("error|uid:{$this->getUid()};数据库与redis password不相等");
            $this->_status = false;
            return $this->_status;
        }

        //比较encpass
        if($this->_encpassDb != $this->_encpassRedis)
        {
            $this->log("error|uid:{$this->getUid()};数据库与redis encpass不相等");
            $this->_status = false;
            return $this->_status;
        }

        //比较用户封禁状态数据
        if(hp_json_encode(array_values_to_string(array_values_to_string($this->_userDisableLoginDbData))) != hp_json_encode(array_values_to_string($this->_userDisableLoginRedisData)))
        {
            $this->log("error|uid:{$this->getUid()}; 数据库与redis用户封禁状态数据不一致");
            $this->_status = false;
            return $this->_status;
        }

        //比较userstatic 表里信息
        if(hp_json_encode(array_values_to_string($this->_userstaticDbData)) != hp_json_encode(array_values_to_string($this->_userStaticRedisData)))
        {
            $this->log("error|uid:{$this->getUid()};数据库与redis userstatic 表里信息不相等");
            $this->_status = false;
            return $this->_status;
        }

        //比较useractive表里的数据
        if(hp_json_encode(array_values_to_string($this->_useractiveDbData)) != hp_json_encode(array_values_to_string($this->_userActiveRedisData)))
        {
            $this->log("error|uid:{$this->getUid()};数据库与redis useractive 表里信息不相等");
            $this->_status = false;
            return $this->_status;
        }

        //校验禁言数据
        if(hp_json_encode(array_values_to_string($this->_silencedDbData)) != hp_json_encode(array_values_to_string($this->_silencedRedisData)))
        {
            $this->_status = false;
            $this->log("error|uid:{$this->getUid()};禁言数据库 user_disable_status 与redis信息不相等");
            return $this->_status;
        }

        //校验主播是否被禁播
        if(hp_json_encode(array_values_to_string($this->_disableLiveDbData)) != hp_json_encode(array_values_to_string($this->_disableLiveRedisData)))
        {
            $this->_status = false;
            $this->log("error|uid:{$this->getUid()};主播禁播数据库 user_disable_status 与redis信息不相等");
            return $this->_status;
        }

        $this->_status = true;

        return $this->_status;
    }

    /**
     * 修复用户redis数据
     * @return boolean
     */
    public function rebuild()
    {
        if($this->checkDataStatus() !== false)
        {
            //$this->log("notice|用户数据库与redis数据一致无需构造，uid:{$this->getUid()}");
            return true;
        }
        $event  = new EventManager;
        $status = $event->trigger(EventManager::ACTION_USER_RESET_CACHE,['uid' => $this->getUid()]);
        if($status)
        {
            $this->log("success|用户构造redis数据成功，uid:{$this->getUid()}");
        } else
        {
            $this->log("error|用户构造redis数据异常，uid:{$this->getUid()}");
        }

        return $status;
    }

    public function log($msg)
    {
        write_log($msg,$this->getLogName());
    }

    private function _getUserStaticDao()
    {
        $userStaticDao  = new UserStatic();
        return $userStaticDao;
    }

    private function _getUserActiveDao()
    {
        $userActiveDao = new UserActive();
        return $userActiveDao;
    }

    private function _getUserDisableStatusDao()
    {
        $userDisabeStatusDao = new UserDisableStatus();
        return $userDisabeStatusDao;
    }
}