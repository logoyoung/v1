<?php

namespace service\user;

use service\common\AbstractService;
use lib\User;
use service\anchor\AnchorDataService;
use lib\user\UserStatic;
use lib\user\UserActive;
use service\user\helper\UserRedis;
use service\event\EventManager;

/**
 * 用户服务
 */
class UserDataService extends AbstractService
{

    //主播uid等于访客uid
    const UID_LUID_EQ_GPID = 5;
    //房间管理员
    const UID_ROOM_ADMIN_GPID = 4;
    //默认分组
    const UID_DEFAULT_GPID = 1;
    //enc参数为空
    const ERROR_ENC_N1003 = -1003;
    //从数据没有获取到enc
    const ERROR_ENC_N1014 = -1014;
    //无效的enc
    const ERROR_ENC_N1013 = -1013;
    //从数据库获取用户个人资产异常
    const ERROR_USER_PROPERTY = -7001;
    //从数据库获取用户认证信息异常
    const ERROR_USER_CERTIFY = -7002;
    //从数据库获取所有用户的所有等级异常
    const ERROR_USER_LEVEL_LIST = -7003;
    //从数据获取用户个人等级异常
    const ERROR_USER_LEVEL = -7004;
    //用户不存在
    const ERROR_USER_NOT_EXISTS = -7005;
    //底层服务没有获取到相对应的用户信息
    const ERROR_USER_INFO = -7006;
    //底层服务没有获取到相对应的用户信息(批量获取)
    const ERROR_USER_BATCH_INFO = -7007;
    //从底层获取历史记录失败
    const ERROR_HISTORY_LIST = 7008;
    //从底层没有获取到主播LUID
    const ERROR_LUIDS = -7009;
    //从底层没有获取到主播信息失败
    const ERROR_ANCHOR_INFO = -7010;
    //从底层获取主播房间ID失败(批量获取)
    const ERROR_ROOM_IDS = -7011;
    //获取直播信息失败
    const ERROR_LIVE_INFO = -7012;

    //底层数据库异常
    const ERROR_DB_STATIC = -7013;

    const USER_INFO_BASE   = 0; //用户基础信息
    const USER_INFO_DETAIL = 1; //用户详细信息
    const USER_INFO_ALL    = 2;//用户所有信息

    const USER_STATIC_BASE   = 10;
    const USER_STATIC_DETAIL = 11;
    const USER_ACTICE_BASE   = 20;
    const USER_ACTICE_DETAIL = 21;
    const USER_STATIC_ACTICE_BASE = 30;
    const USER_DATA_ALL      = 99;

    public static $errorMsg = [
        self::ERROR_ENC_N1003 => 'enc参数为空',
        self::ERROR_ENC_N1014 => '从数据没有获取到enc',
        self::ERROR_ENC_N1013 => '无效的enc',
        self::ERROR_USER_PROPERTY => '获取用户个人资产异常',
        self::ERROR_USER_CERTIFY => '获取用户认证信息异常',
        self::ERROR_USER_LEVEL_LIST => '从数据库获取所有用户的所有等级异常',
        self::ERROR_USER_LEVEL => '从数据获取用户个人等级异常',
        self::ERROR_USER_NOT_EXISTS => '用户不存在',
        self::ERROR_USER_INFO => '底层服务没有获取到相对应的用户信息',
        self::ERROR_USER_BATCH_INFO => '底层服务没有获取到相对应的用户信息(批量获取)',
        self::ERROR_HISTORY_LIST => '从底层获取历史记录失败',
        self::ERROR_LUIDS => '从底层没有获取到主播LUID',
        self::ERROR_ANCHOR_INFO => '从底层没有获取到主播信息失败',
        self::ERROR_ROOM_IDS => '从底层获取主播房间ID失败(批量获取)',
        self::ERROR_LIVE_INFO => '获取直播信息失败',
        self::ERROR_DB_STATIC  => '底层数据库异常',
    ];

    private $_uid;
    private $_luid;
    private $_userDao;
    private $_userDb;
    private $_userStaticDao;
    private $_userActiveDao;
    private $_detail = self::USER_STATIC_BASE;
    private $_fromDb = false;
    private $_enc;
    private $_fromDbMaster = false;
    private $_userRedis;
    private $_getDataErrorLog      = 'user_get_data_error';
    private $_checkEncpassErrorLog = 'user_get_encpass_error';

    public static function getUserStaticBaseFields()
    {
        return ['uid','username','nick','pic','sex','phone','rtime'];
    }

    public static function getUserStaticDetailFields()
    {
        $fields = UserStatic::$fields;
        $pwdIdx = array_search('password',$fields);
        $encIdx = array_search('encpass',$fields);
        unset($fields[$pwdIdx],$fields[$encIdx]);
        return $fields;
    }

    public static function getUserActiveBaseFields()
    {
        return ['level','hpbean','hpcoin','integral','readsign','isnotice','province','city','address'];
    }

    public static function getUserActiveDetailFields()
    {
        $fields = UserActive::$fields;
        return $fields;
    }

    public function setUid($uid)
    {
        $this->_uid     = is_array($uid) ? array_values((array_unique($uid))) : $uid;
        $this->_userDao = false;
        $this->_detail  = self::USER_STATIC_BASE;
        $this->_fromDb  = false;
        $this->_enc     = '';
        $this->_luid        = '';
        $this->_userActiveDao = false;
        $this->_userStaticDao = false;
        $this->_fromDbMaster  = false;
        $this->_userRedis     = false;
        return $this;
    }

    public function getUid()
    {
        return $this->_uid;
    }

    public function setLuid($luid)
    {
        $this->_luid = $luid;
        return $this;
    }

    public function getLuid()
    {
        return $this->_luid;
    }

    public function setUserInfoDetail($getDetail = self::USER_STATIC_BASE)
    {
        $this->_detail = $getDetail;
        return $this;
    }

    public function getUserInfoDetail()
    {
        return $this->_detail;
    }

    public function setEnc($enc)
    {
        $this->_enc = $enc;
        return $this;
    }

    public function getEnc()
    {
        return $this->_enc;
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
     * 获取用户信息
     * @return array |false
     */
    public function getUserInfo()
    {
        if(!$this->getUid())
        {
            return false;
        }

        $uids     = (array) $this->getUid();
        $userData = [];
        $redis    = $this->getUserRedis();

        if($this->getFromDb() || !$redis->getRedis()->ping())
        {
            $userData = $this->_getUserInfoFromDb($uids);

        } else
        {
            $dbUids  = [];
            $rdsUids = [];

            foreach ($uids as $uid)
            {
                $redis->setUid($uid);
                $exist = $redis->isExist();
                if($exist === true)
                {
                    $rdsUids[] = $uid;
                    continue;
                }

                if($exist === -1)
                {
                    $dbUids[] = $uid;
                }

            }


            $redisUserData = false;
            $dbUserData    = false;

            if($rdsUids)
            {
                $redisUserData = $this->_getUserInfoFromRedis($rdsUids);
                if($redisUserData)
                {
                    $userData  = (array) $redisUserData;
                }
                unset($redisUserData);
            }

            if($dbUids)
            {
                $dbUserData = $this->_getUserInfoFromDb($dbUids);
                if($dbUserData)
                {
                    $dbUserData = array_filter($dbUserData);
                    $userData   = $userData ? ($userData + $dbUserData) : $dbUserData;
                    $event      = new EventManager();
                    foreach ($dbUids as $_resetCacheUid)
                    {
                         $event->trigger(EventManager::ACTION_USER_RESET_CACHE,['uid' => $_resetCacheUid]);
                    }
                    $event   = null;
                }
                unset($dbUserData);
            }

        }

        if(!$userData)
        {
            return $userData;
        }

        foreach ($userData as &$v)
        {
            if(isset($v['pic']))
            {
                $v['pic'] = !$v['pic'] ? DEFAULT_PIC : DOMAIN_PROTOCOL .$GLOBALS['env-def'][$GLOBALS['env']]['domain-img'] . '/' . ltrim($v['pic'],'/');
            }

            if(isset($v['level']))
            {
                $v['level_to_integral'] = get_user_integral_by_level($v['level']);
            }

        }

        return !is_array($this->getUid()) ? (isset($userData[$this->getUid()]) ? $userData[$this->getUid()] : $userData) : $userData;
    }

    private function _getUserInfoFromRedis(array $uids)
    {
        $result = false;
        $log    = '';
        $redis  = $this->getUserRedis();
        switch ($this->getUserInfoDetail())
        {

            //用户active表里的基本信息 ('level','integral','readsign','isnotice')
            case self::USER_ACTICE_BASE:
            //用户active表里的所有信息
            case self::USER_ACTICE_DETAIL:

                foreach ($uids as $uid)
                {
                    $activeData   = $redis->setUid($uid)->setGetUserActive(true)->getUserData();
                    if(isset($activeData[UserRedis::USER_ACTIVE_NAME]) && $activeData[UserRedis::USER_ACTIVE_NAME])
                    {
                        $result[$uid] = $activeData[UserRedis::USER_ACTIVE_NAME];
                    }
                    unset($activeData);
                }

                break;

             //用户所有信息 (userstatic,useractive)
            case self::USER_DATA_ALL:
            case self::USER_STATIC_ACTICE_BASE:
            case self::USER_INFO_ALL:

                foreach ($uids as $uid)
                {
                    $allData    = $redis->setUid($uid)->setGetUserActive(true)->setGetUserStatic(true)->getUserData();
                    $staticData = $allData[UserRedis::USER_STATIC_NAME];
                    $activeData = $allData[UserRedis::USER_ACTIVE_NAME];
                    if($staticData || $activeData)
                    {
                        $result[$uid] = array_merge((array) $staticData, (array) $activeData);
                    }
                    unset($allData,$staticData,$activeData);
                }

                break;

            //用户static 基础信息 (uid,nick,pic)
            case self::USER_INFO_BASE:
            case self::USER_STATIC_BASE:
            //用户static除敏感字段的所有信息
            case self::USER_STATIC_DETAIL:
            default:

                foreach ($uids as $uid)
                {
                    $staticData   = $redis->setUid($uid)->setGetUserStatic(true)->getUserData();
                    if(isset($staticData[UserRedis::USER_STATIC_NAME]) && $staticData[UserRedis::USER_STATIC_NAME])
                    {
                        $result[$uid] = $staticData[UserRedis::USER_STATIC_NAME];
                    }
                    unset($staticData);
                }

                break;
        }

        if($result === false)
        {
            $code = self::ERROR_USER_INFO;
            $uids = implode(',', $uids);
            $log  = "error |redis异常;error_code:{$code};msg:从redis获取 userstatic 异常;uid:{$uids}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log,$this->_getDataErrorLog);
        }

        return $result;
    }

    private function _getUserInfoFromDb(array $uids)
    {
        $userStaticDao = $this->getUserStaticDao();
        $userActiceDao = $this->getUserActiveDao();

        $result = false;
        $log    = '';

        switch ($this->getUserInfoDetail())
        {

            //用户static除敏感字段的所有信息
            case self::USER_STATIC_DETAIL:
                $userStaticDao->setMaster($this->getFromDbMaster());
                $result = $userStaticDao->getUserStaticData($uids,self::getUserStaticDetailFields());
                if($result === false)
                {
                    $code = self::ERROR_USER_INFO;
                    $uids = implode(',', $uids);
                    $log  = "error |数据库异常;error_code:{$code};msg:从数据库获取:userstatic表信息异常;uid:{$uids}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
                }
                break;

            //用户static  基础信息 (uid,nick,pic), active表里的基本信息 'level','hpbean','hpcoin','integral','readsign','isnotice'
            case self::USER_STATIC_ACTICE_BASE:
            case self::USER_INFO_ALL:

                $userStaticDao->setMaster($this->getFromDbMaster());
                $staticBaseData = $userStaticDao->getUserStaticData($uids,self::getUserStaticBaseFields());
                $log = '';
                if($staticBaseData === false)
                {
                    $log  .= "从数据库获取:userstatic表信息异常;";
                }

                $userActiceDao->setMaster($this->getFromDbMaster());
                $activeBaseData = $userActiceDao->getUserActiveData($uids,self::getUserActiveBaseFields());
                if($activeBaseData === false)
                {
                    $log  .= "从数据库获取:useractive表信息异常;";
                }

                if($log)
                {
                    $uids = implode(',', $uids);
                    $log = "error|数据库异常;{$log};uid:{$uids}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();;
                }

                if($staticBaseData === false && $activeBaseData === false)
                {
                    $result = false;
                } else
                {
                    $result         = [];
                    $staticBaseData = (array) $staticBaseData;
                    $activeBaseData = (array) $activeBaseData;
                    foreach ($uids as $uid)
                    {
                        $result[$uid] = [];
                        if(isset($staticBaseData[$uid]))
                        {
                            $result[$uid] = array_merge($result[$uid],$staticBaseData[$uid]);
                        }
                        if(isset($activeBaseData[$uid]))
                        {
                            $result[$uid] = array_merge($result[$uid],$activeBaseData[$uid]);
                        }
                    }
                    unset($staticBaseData,$activeBaseData);
                }

                break;

            //用户active表里的基本信息 'level','hpbean','hpcoin','integral','readsign','isnotice'
            case self::USER_ACTICE_BASE:
                $userActiceDao->setMaster($this->getFromDbMaster());
                $result = $userActiceDao->getUserActiveData($uids,self::getUserActiveBaseFields());
                if($result === false)
                {
                    $code = self::ERROR_USER_INFO;
                    $uids = implode(',', $uids);
                    $log  = "error |数据库异常;error_code:{$code};msg:从数据库获取:useractive表信息异常;uid:{$uids}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
                }
                break;

            //用户active表里的所有信息
            case self::USER_ACTICE_DETAIL:
                $userActiceDao->setMaster($this->getFromDbMaster());
                $result = $userActiceDao->getUserActiveData($uids,self::getUserActiveDetailFields());
                if($result === false)
                {
                    $code = self::ERROR_USER_INFO;
                    $uids = implode(',', $uids);
                    $log  = "error |数据库异常;error_code:{$code};msg:从数据库获取:useractive表信息异常;uid:{$uids}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
                }
                break;

            //用户所有信息 (userstatic,useractive)
            case self::USER_DATA_ALL:
                $userStaticDao->setMaster($this->getFromDbMaster());
                $userStatic = $userStaticDao->getUserStaticData($uids,self::getUserStaticDetailFields());
                $userActiceDao->setMaster($this->getFromDbMaster());
                $userActive = $userActiceDao->getUserActiveData($uids,self::getUserActiveDetailFields());

                $log = '';
                if($userStatic === false)
                {
                    $log .= '从数据库获取:userstatic表信息异常;';
                }

                if($userActive === false)
                {
                    $log .= '从数据库获取:useractive表信息异常;';
                }

                if($log)
                {
                    $code = self::ERROR_USER_INFO;
                    $uids = implode(',', $uids);
                    $log  = "error|数据库异常;error_code:{$code};msg:{$log};uid:{$uids}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
                }

                $result = ($userStatic !== false || $userActive !== false) ? array_merge((array) $userStatic, (array) $userActive) : false;
                unset($userActive,$userStatic);
                break;

            //用户static 基础信息 (uid,nick,pic)
            case self::USER_INFO_BASE:
            case self::USER_STATIC_BASE:
            default:
                $userStaticDao->setMaster($this->getFromDbMaster());
                $result = $userStaticDao->getUserStaticData($uids,self::getUserStaticBaseFields());
                if($result === false)
                {
                    $code = self::ERROR_USER_INFO;
                    $uids = implode(',', $uids);
                    $log  = "error |数据库异常;error_code:{$code};msg:从数据库获取:userstatic表信息异常;uid:{$uids}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
                }
                break;
        }

        if ($log)
        {
            write_log($log,$this->_getDataErrorLog);
        }

        return $result;
    }

    /**
     * 批量获取用户信息
     * @return array
     */
    public function batchGetUserInfo()
    {
        return $this->getUserInfo();
    }

    /**
     *  校验用户是否存在
     * @return boolean
     */
    public function isExist()
    {

        //当uid 大于常量时 LIVEROOM_ANONYMOUS 需要修改此处逻辑
        //目前访客uid就LIVEROOM_ANONYMOUS(30亿加随机数)
        if ($this->getUid() == 0 || $this->getUid() >= LIVEROOM_ANONYMOUS)
        {
            return false;
        }
        $redis   = $this->getUserRedis();
        if(!$this->getFromDb() && $redis->getRedis()->ping())
        {
            $redis->setUid($this->getUid());
            $isExist = $redis->isExist();

            if($isExist === false || $isExist === true)
            {
                return $isExist;
            }

        }

        $userStaticDao = $this->getUserStaticDao();
        $userStaticDao->setMaster($this->getFromDbMaster());
        $userStatic    = $userStaticDao->getUserStaticData($this->getUid(),['uid']);

        if($userStatic === false)
        {
            $code = self::ERROR_DB_STATIC;
            $msg  = self::$errorMsg[$code];
            $log  = "error |error_code:{$code};msg:{$msg};uid:{$this->getUid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log,$this->_getDataErrorLog);

            return false;
        }

        if(!$this->getFromDb())
        {
            $event   = new EventManager();
            $event->trigger(EventManager::ACTION_USER_RESET_CACHE,['uid' => $this->getUid()]);
        }

        return $userStatic ? 1 : false;
    }

    /**
     * 获取encpass
     * @return boolean
     */
    public function getEncpass()
    {

        $redis   = $this->getUserRedis();
        if(!$this->getFromDb() && $redis->getRedis()->ping())
        {
            $redis->setUid($this->getUid());
            $result  = $redis->getEncpass();

            if(!isset($result[UserRedis::USER_ENC_NAME]) || !$result[UserRedis::USER_ENC_NAME])
            {
                $log = "error|uid:{$this->getUid()};从redis获取encpass异常;请检查redis服务|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
                write_log($log,$this->_checkEncpassErrorLog);
                return false;

            }

            return $result[UserRedis::USER_ENC_NAME];
        }

        $userStaticDao = $this->getUserStaticDao();
        $userStaticDao->setMaster($this->getFromDbMaster());
        $userStatic    = $userStaticDao->getUserStaticData($this->getUid(),['encpass']);
        if($userStatic === false)
        {
            $log = "error|uid:{$this->getUid()}; 从db获取encpass异常;请检查db服务|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log,$this->_checkEncpassErrorLog);

            return false;
        }

        return $userStatic[$this->getUid()]['encpass'];
    }

    /**
     *  是否是主播
     * @return boolean [description]
     */
    public function isAnchor()
    {
        $anchorDataService = new AnchorDataService();
        $anchorDataService->setUid($this->getUid());
        return $anchorDataService->isAnchor();
    }

    public function getUserDao()
    {
        if (!$this->_userDao)
        {
            $this->_userDao = new User($this->getUid());
        }

        return $this->_userDao;
    }

    //是否关注这个主播
    public function isFollowAnchor()
    {
        $userDao = $this->getUserDao();
        return $userDao->isFollow($this->getLuid());
    }

    //获取所有的用户等级信息
    public function getUserLevelInfoList()
    {
        return get_user_integral_by_level();
    }

    /**
     * 是否房间管理员
     * @return boolean [description]
     */
    public function isRoomAdmin()
    {
        $userDao = $this->getUserDao();
        return $userDao->isRoomManager($this->getUid());
    }

    /**
     * 获取分组角色id
     * @return int
     */
    public function getGroupId()
    {
        //主播uid等于访客uid
        if ($this->getLuid() == $this->getUid())
        {
            return self::UID_LUID_EQ_GPID;
        }

        //房间管理员
        if ($this->isRoomAdmin())
        {
            return self::UID_ROOM_ADMIN_GPID;
        }

        //默认分组
        return self::UID_DEFAULT_GPID;
    }

    public function getUserActiveDao()
    {

        if(!$this->_userActiveDao)
        {
            $this->_userActiveDao = new UserActive();
        }

        return $this->_userActiveDao;
    }

    public function getUserStaticDao()
    {
        if(!$this->_userStaticDao)
        {
            $this->_userStaticDao = new UserStatic();
        }

        return $this->_userStaticDao;
    }

    public function getUserRedis()
    {
        if(!$this->_userRedis)
        {
            $this->_userRedis = new UserRedis();
        }

        return $this->_userRedis;
    }

    public function getUserDb()
    {
        if (!$this->_userDb)
        {

            $this->_userDb = User::getDB();
        }

        return $this->_userDb;
    }

}
