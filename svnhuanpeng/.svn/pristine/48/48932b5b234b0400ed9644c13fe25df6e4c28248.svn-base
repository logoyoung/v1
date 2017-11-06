<?php
namespace service\user;
use service\common\AbstractService;
use lib\user\UserDisableStatus;
use Exception;
use service\event\EventManager;
use service\user\helper\UserRedis;
use service\common\LogDbService;

/**
 *  封禁管理 （登陆，发言，开播）
 *
 */
class UserDisableStatusService extends AbstractService
{

    //封禁(禁登陆)
    const USER_DISABLE_TYPE_LOGIN    = 10;

    //禁言
    const USER_DISABLE_TYPE_SEND_MSG = 20;

    //禁播
    const USER_DISABLE_TYPE_LIVE     = 30;

    //取消
    const USER_DISABLE_STATUS_OFF    = 1;
    //开启
    const USER_DISABLE_STATUS_ON     = 2;

    //封禁范围（全局）
    const USER_DISABLE_SCOPE_ALL     = 1;

    //永久封禁时间
    const USER_DISABLE_MAX_TIME      = 2114478671;

    public static $allType = [
        self::USER_DISABLE_TYPE_LOGIN,
        self::USER_DISABLE_TYPE_SEND_MSG,
        self::USER_DISABLE_TYPE_LIVE,
    ];

    public static $allStatus = [
        self::USER_DISABLE_STATUS_OFF,
        self::USER_DISABLE_STATUS_ON,
    ];

    //无效的uid
    const ERROR_INVALID_UID       = -2001;
    //无效type类型
    const ERROR_INVALID_TYPE      = -2003;
    //无效的status
    const ERROR_INVALID_STATUS    = -2004;

    public static $errorMsg = [
        self::ERROR_INVALID_UID         => '无效的uid',
        self::ERROR_INVALID_TYPE        => '无效type类型',
        self::ERROR_INVALID_STATUS      => '无效的status',
    ];

    private $_logName = 'user_disable_access';

    private $_uid;
    private $_type;
    private $_status;
    private $_scope;
    private $_etime;
    private $_userDisableStatusDao;
    private $_userRedis;
    private $_fromDb       = false;
    private $_fromDbMaster = false;
    private $_acUid;
    private $_platform;
    private $_desc;

    public function setUid($uid)
    {
        $this->_uid       = (int) $uid;
        $this->_userRedis = null;
        $this->_userDisableStatusDao = null;
        $this->_fromDb       = false;
        $this->_fromDbMaster = false;
        $this->_etime        = 0;
        $this->_acUid        = 0;
        $this->_platform     = 0;
        $this->_desc         = '';
        return $this;
    }

    public function getUid()
    {
        if(!$this->_uid)
        {
            $code = self::ERROR_INVALID_UID;
            $msg  = self::$errorMsg[$code];
            throw new Exception($msg, $code);
        }

        return $this->_uid;
    }

    public function setType($type)
    {
        $this->_type = (int) $type;
        return $this;
    }

    public function getType()
    {
        if(!$this->_type || !in_array($this->_type, self::$allType))
        {
            $code = self::ERROR_INVALID_TYPE;
            $msg  = self::$errorMsg[$code];
            throw new Exception($msg, $code);
        }

        return $this->_type;
    }

    public function setStatus($status)
    {
        $this->_status = (int) $status;
        return $this;
    }

    public function getStatus()
    {
        if(!$this->_status || !in_array($this->_status, self::$allStatus))
        {
            $code = self::ERROR_INVALID_STATUS;
            $msg  = self::$errorMsg[$code];
            throw new Exception($msg, $code);
        }

        return $this->_status;
    }

    public function setScope($scope)
    {
        $this->_scope = (int) $scope;
        return $this;
    }

    public function getScope()
    {
        if(!is_numeric($this->_scope) || !$this->_scope)
        {
            $this->_scope = self::USER_DISABLE_SCOPE_ALL;
        }

        return $this->_scope;
    }

    public function setEtime($etime)
    {
        $this->_etime = (int) $etime;

        if( $this->_etime <= 0)
        {
            $this->_etime = self::USER_DISABLE_MAX_TIME;
        }

        if($this->_etime < self::USER_DISABLE_MAX_TIME)
        {
            $this->_etime += time();
        }

        return $this;
    }

    public function getEtime()
    {
        return $this->_etime;
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

    public function setAcUid($uid)
    {
        $this->_acUid = (int) $uid;
        return $this;
    }

    public function getAcUid()
    {
        return $this->_acUid;
    }

    public function setPlatform($platform)
    {
        $this->_platform = $platform;
        return $this;
    }

    public function getPlatform()
    {
        return $this->_platform;
    }

    public function setDesc($desc)
    {
        $this->_desc = $desc;
        return $this;
    }

    public function getDesc()
    {
        return $this->_desc;
    }

    public function addDisable()
    {
        try {

            $uid    = $this->getUid();
            $type   = $this->getType();
            $scope  = $this->getScope();
            $etime  = $this->getEtime();
            $dbLog  = [
                'platform' => $this->getPlatform(),
                'uid'      => $uid,
                'type'     => $type,
                'scope'    => $scope,
                'etime'    => $etime,
                'status'   => 2,
                'info'     => '',
                'desc'     => $this->getDesc(),
            ];
            $logMsg = hp_json_encode($dbLog)."|class:".__CLASS__.";func:". __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            $dao    = $this->getUserDisableStatusDao();
            $result = $dao->addUserDisableStatus($uid, $type, $scope, $etime);

            if($result !== false)
            {

                $event = new EventManager();

                switch ($type)
                {
                    //封禁用户
                    case self::USER_DISABLE_TYPE_LOGIN:
                        $event->trigger(EventManager::ACTION_USER_UPATE_DISABLE_LOGIN,['uid' => $uid]);
                        $this->log("success|加禁成功;{$logMsg}");
                        $dbLog['info'] = '封禁用户';
                        LogDbService::log($uid, $dbLog, LogDbService::LOG_DISABLE_LOGIN, $this->getAcUid());
                        break;

                    //添加直播间禁言
                    case self::USER_DISABLE_TYPE_SEND_MSG:
                        $event->trigger(
                            EventManager::ACTION_USER_ADD_SILENCE,
                            ['uid' => $uid, 'disableType' => $type, 'disableScope' => $scope, 'disableEtime' => $etime]
                        );
                        $this->log("success|直播间禁言成功;{$logMsg}");
                        $dbLog['info'] = '加禁言';
                        LogDbService::log($uid, $dbLog, LogDbService::LOG_TYPE_SEILENCED, $this->getAcUid());
                        break;

                    //禁播
                    case self::USER_DISABLE_TYPE_LIVE:
                        $event->trigger(
                            EventManager::ACTION_ADD_DISABLE_LIVE,
                            ['uid' => $uid, 'disableType' => $type, 'disableScope' => $scope, 'disableEtime' => $etime]
                        );
                        $this->log("success|添加禁播成功;{$logMsg}");
                        break;
                }

                $event = null;
                return true;
             }

             $this->log("error|失败;{$logMsg}");
             return false;

        } catch (Exception $e)
        {
            $this->log("error|加禁失败;error_code:{$e->getCode()};error_msg{$e->getMessage()}".$this->getCaller());
            throw $e;
        }

    }

    public function deleteDisable()
    {
        try {

            $uid    = $this->getUid();
            $type   = $this->getType();
            $scope  = $this->getScope();
            $dbLog  = [
                'platform' => $this->getPlatform(),
                'uid'      => $uid,
                'type'     => $type,
                'scope'    => $scope,
                'status'   => 1,
                'info'     => '',
                'desc'     => $this->getDesc(),
            ];
            $logMsg = hp_json_encode($dbLog)."|class:".__CLASS__.";func:". __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            $dao    = $this->getUserDisableStatusDao();
            $event  = new EventManager();

            switch ($type)
            {
                //封禁用户
                case self::USER_DISABLE_TYPE_LOGIN:

                    if($this->getDisableLoginStatus() === true)
                    {
                        $this->log("notice|用户没有封禁;{$logMsg}");
                        return true;
                    }

                    $result = $dao->deleteUserDisableStatus($uid, $type, $scope);
                    if($result)
                    {
                        $this->log("success|解禁成功;{$logMsg}");
                        $event->trigger(EventManager::ACTION_USER_UPATE_DISABLE_LOGIN,['uid' => $uid]);
                        $event = null;
                        $dbLog['info'] = '解禁用户';
                        LogDbService::log($uid, $dbLog, LogDbService::LOG_DISABLE_LOGIN, $this->getAcUid());
                        return true;
                    }
                    break;

                //删除直播间禁言
                case self::USER_DISABLE_TYPE_SEND_MSG:

                    if($this->getSliencedStatus() === true)
                    {
                        $this->log("notice|用户没有直播间禁言;{$logMsg}");
                        return true;
                    }

                    $result = $dao->deleteUserDisableStatus($uid, $type, $scope);
                    if($result)
                    {
                        $this->log("success|直播间禁言解禁成功;{$logMsg}");
                        $event->trigger(
                            EventManager::ACTION_USER_REMOVE_SILENCE,
                            ['uid' => $uid, 'disableType' => $type, 'disableScope' => $scope ]
                        );
                        $event = null;
                        $dbLog['info'] = '删除禁言';
                        LogDbService::log($uid, $dbLog, LogDbService::LOG_TYPE_SEILENCED, $this->getAcUid());
                        return true;
                    }

                    break;

                //解除禁播
                case self::USER_DISABLE_TYPE_LIVE:

                    if($this->getAnchorLiveStatus() === true)
                    {
                        $this->log("notice|主播没有被禁播;{$logMsg}");
                        return true;
                    }

                    $result = $dao->deleteUserDisableStatus($uid, $type, $scope);
                    if($result)
                    {
                        $event->trigger(
                            EventManager::ACTION_DEL_DISABLE_LIVE,
                            ['uid' => $uid, 'disableType' => $type, 'disableScope' => $scope]
                        );

                        $this->log("success|解除禁播成功;{$logMsg}");
                        return true;
                    }

                    break;
            }

            $this->log("error|解禁失败;{$logMsg}");
            return false;

        } catch (Exception $e) {
            $this->log("error|解禁失败;error_code:{$e->getCode()};error_msg{$e->getMessage()}".$this->getCaller());
            throw $e;
        }

    }

    public function getDisableLoginStatus()
    {
        try {

            $uid   = $this->getUid();
            $redis = $this->getUserRedis();

            if($this->getFromDb() || !$redis->getRedis()->ping())
            {
                $userDisableStatusDao = $this->getUserDisableStatusDao();
                $userDisableStatusDao->setMaster($this->getFromDbMaster());
                $type   = UserDisableStatusService::USER_DISABLE_TYPE_LOGIN;
                $scope  = UserDisableStatusService::USER_DISABLE_SCOPE_ALL;
                $disableData = $userDisableStatusDao->getDisableStatusByUidTypeScope($uid,$type,$scope);
                if($disableData === false)
                {
                    $this->log("error|从数据库获取用户封禁数据异常;uid:{$uid}".$this->getCaller());
                    return false;
                }

                if($disableData)
                {
                    $disableData = $disableData[0];
                }

                return $disableData ? $disableData : true;
            }

            $redis->setUid($uid);
            $disableData = $redis->getUserDisableLoginStatusData();

            if($disableData === true)
            {
                return true;
            }

            if($disableData === false)
            {
                $this->log("error|从redis获取用户封禁数据异常;uid:{$uid}".$this->getCaller());
            }

            return $disableData;

        } catch (Exception $e) {
            $this->log("error|获取用户封禁异常;error_code:{$e->getCode()};error_msg{$e->getMessage()}".$this->getCaller());
            throw $e;
        }
    }

    public function getSliencedStatus()
    {
        try {

            $uid    = $this->getUid();
            $type   = self::USER_DISABLE_TYPE_SEND_MSG;
            $scope  = $this->getScope();
            $redis  = $this->getUserRedis();

            if($this->getFromDb() || !$redis->getRedis()->ping())
            {
                $userDisableStatusDao = $this->getUserDisableStatusDao();
                $userDisableStatusDao->setMaster($this->getFromDbMaster());
                $disableData = $userDisableStatusDao->getDisableStatusByUidTypeScope($uid,$type,$scope);
                if($disableData === false)
                {
                    $this->log("error|从数据库获取 直播间禁言 数据异常;uid:{$uid};type:{$type};scope:{$scope}".$this->getCaller());
                    return false;
                }

                if($disableData)
                {
                    $disableData = $disableData[0];
                }

                return $disableData ? $disableData : true;
            }

            $redis->setUid($uid);
            $disableData = $redis->getSilencedByAnchorUid($scope);

            if($disableData === true)
            {
                return true;
            }

            if($disableData === false)
            {
                $this->log("error|从redis获取直播间禁言封禁数据异常;uid:{$uid};type:{$type};scope:{$scope}".$this->getCaller());
            }

            return $disableData;

        } catch (Exception $e) {
            $this->log("error|获取直播间禁言异常;error_code:{$e->getCode()};error_msg{$e->getMessage()}".$this->getCaller());
            throw $e;
        }
    }

    public function getAnchorLiveStatus()
    {
        try {

            $uid    = $this->getUid();
            $type   = self::USER_DISABLE_TYPE_LIVE;
            $scope  = $this->getScope();
            $redis  = $this->getUserRedis();

            if($this->getFromDb() || !$redis->getRedis()->ping())
            {
                $userDisableStatusDao = $this->getUserDisableStatusDao();
                $userDisableStatusDao->setMaster($this->getFromDbMaster());
                $disableData = $userDisableStatusDao->getDisableStatusByUidTypeScope($uid,$type,$scope);
                if($disableData === false)
                {
                    $this->log("error|从数据库获取 禁播数据异常;uid:{$uid};type:{$type};scope:{$scope}".$this->getCaller());
                    return false;
                }

                if($disableData)
                {
                    $disableData = $disableData[0];
                }

                return $disableData ? $disableData : true;
            }

            $redis->setUid($uid);
            $disableLive = $redis->getDisableLiveStatus();
            if($disableLive  === true)
            {
                return true;
            }

            if($disableLive  === false)
            {
                $this->log("error|从redis获取禁播数据据异常;uid:{$uid};type:{$type};scope:{$scope}".$this->getCaller());
            }

            return $disableLive;
        } catch (Exception $e) {
            $this->log("error|获取主播是否能发直播权限;error_code:{$e->getCode()};error_msg{$e->getMessage()}".$this->getCaller());
            throw $e;
        }
    }

    public function getUserDisableStatusDao()
    {
        if(!$this->_userDisableStatusDao)
        {
            $this->_userDisableStatusDao = new UserDisableStatus();
        }

        return $this->_userDisableStatusDao;
    }

    public function getUserRedis()
    {
        if(!$this->_userRedis)
        {
            $this->_userRedis = new UserRedis();
        }

        return $this->_userRedis;
    }

    public function log($msg)
    {
        write_log($msg,$this->_logName);
    }

}