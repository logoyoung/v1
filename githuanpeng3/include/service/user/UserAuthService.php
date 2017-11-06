<?php
namespace service\user;
use Exception;
use service\common\AbstractService;
use service\user\UserDataService;
use service\user\UserDisableStatusService;
use service\event\EventManager;
use service\due\DueCertService;
use service\anchor\AnchorGetDataService;
use service\user\UserCertDataService;

class UserAuthService extends AbstractService
{

    //uid不能为空
    const ERROR_EMPTY_UID         = -30001;

    //encpass不能为空
    const ERROR_EMPTY_ENCPASS     = -30003;

    //password不能为空
    const ERROR_EMPTY_PASSWORD    = -30004;

    //phone 手机号不能为空
    const ERROR_EMPTY_PHONE       = -30005;

    //请重新登陆
    const ERROR_INVALID_ENCPASS   = -30006;

    //无效的密码
    const ERROR_INVALID_PASSWORD  = -30007;

    //此号已被永久封禁
    const ERROR_DISABLE_LOGIN_F   = -30008;

    //此号已被封禁
    const ERROR_DISABLE_LOGIN_I   = -30009;

    //anchorUid 不能为空
    const ERROR_EMPTY_ANCHOR_UID  = -30010;

    //此号已被全局禁言
    const ERROR_SILENCED_ALL      = -30011;

    //此号已被禁言
    const ERROR_SILENCED_ROOM     = -30012;

    //此号已被禁播
    const ERROR_DISABLE_LIVE      = -30013;

    //从层底没有获取用户的encpass,一般是底层数据问题
    const ERROR_GET_ENC_DATA      = -30014;

    //用户不存在
    const ERROR_USER_NOT_EXIST    = -30015;

    public static $errorMsg = [
        self::ERROR_EMPTY_UID         => 'uid不能为空',
        self::ERROR_EMPTY_ENCPASS     => 'encpass不能为空',
        self::ERROR_EMPTY_PASSWORD    => 'password不能为空',
        self::ERROR_EMPTY_PHONE       => 'phone 手机号不能为空',
        self::ERROR_INVALID_ENCPASS   => '请重新登陆',
        self::ERROR_INVALID_PASSWORD  => '无效的密码',
        self::ERROR_DISABLE_LOGIN_F   => '此号已被永久封禁',
        self::ERROR_DISABLE_LOGIN_I   => '此号已被封禁',
        self::ERROR_EMPTY_ANCHOR_UID  => 'anchorUid 不能为空',
        self::ERROR_SILENCED_ALL      => '此号已被全局禁言',
        self::ERROR_SILENCED_ROOM     => '此号已被禁言',
        self::ERROR_DISABLE_LIVE      => '此号已被禁播',
        self::ERROR_GET_ENC_DATA      => '系统异常，请稍后再试',
        self::ERROR_USER_NOT_EXIST    => '无效的密码',
    ];

    private $_uid;
    private $_password;
    private $_encpass;
    private $_phone;
    private $_anchorUid;
    private $_fromDb       = false;
    private $_fromDbMaster = false;
    private $_userDataService;
    private $_userDisableService;
    private $_accessLog    = 'user_auth_access';
    private $_errorLog     = 'user_auth_error';
    private $_result       = [];

    public function setUid($uid)
    {
        $this->_uid      = $uid;
        $this->_password = false;
        $this->_encpass  = false;
        $this->_fromDb   = false;
        $this->_fromDbMaster       = false;
        $this->_userDataService    = false;
        $this->_userDisableService = false;
        $this->_anchorUid = false;
        $this->_result    = [];

        return $this;
    }

    public function getUid()
    {
        if(!$this->_uid)
        {
            $code = self::ERROR_EMPTY_UID;
            $msg  = self::$errorMsg[$code];
            throw new Exception($msg, $code);
        }

        return $this->_uid;
    }

    public function setPhone($phone)
    {
        $this->_phone = $phone;
        return $this;
    }

    public function getPhone()
    {
        if(!$this->_phone)
        {
            $code = self::ERROR_EMPTY_PHONE;
            $msg  = self::$errorMsg[$code];
            throw new Exception($msg, $code);
        }

        return $this->_phone;
    }

    public function setPassword($password)
    {

        $this->_password = $password;
        return $this;
    }

    public function getPassword()
    {
        if(!$this->_password)
        {
            $code = self::ERROR_EMPTY_PASSWORD;
            $msg  = self::$errorMsg[$code];
            throw new Exception($msg, $code);
        }

        return $this->_password;
    }

    public function setEnc($encpass)
    {
        $this->_encpass = $encpass;
        return $this;
    }

    public function getEnc()
    {
        if(!$this->_encpass)
        {
            $code = self::ERROR_EMPTY_ENCPASS;
            $msg  = self::$errorMsg[$code];
            throw new Exception($msg, $code);
        }

        return $this->_encpass;
    }

    public function setAnchorUid($anchorUid)
    {
        $this->_anchorUid = $anchorUid;
        return $this;
    }

    public function getAnchorUid()
    {
        if(!$this->_anchorUid)
        {
            $code = self::ERROR_EMPTY_ANCHOR_UID;
            $msg  = self::$errorMsg[$code];
            throw new Exception($msg, $code);
        }

        return $this->_anchorUid;
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

    //用户登陆
    public function login()
    {

    }

    /**
     * 校验登陆状态 uid, enc
     * @return boolean 成功返回 true,失败返回 false
     */
    public function checkLoginStatus()
    {
        try {

            $uid = $this->getUid();
            $enc = $this->getEnc();
            $userDataService = $this->_getUserDataService();
            $userDataService->setUid($uid);
            $userDataService->setFromDb($this->getFromDb());
            $userDataService->setFromDbMaster($this->getFromDbMaster());
            $isExist = $userDataService->isExist();

            //校验用户是否存在
            if($isExist === false)
            {
                $code = self::ERROR_USER_NOT_EXIST;
                $msg  = self::$errorMsg[$code];
                $this->_setResult($code);
                $this->accesslog("notice|用户不存在;uid:{$uid}|class:".__CLASS__.";func:".__FUNCTION__.$this->getCaller());
                return false;
            }

            //redis没有数据，但mysql有改为强制从mysql调取
            if($isExist === 1 && $this->getFromDb() !== true)
            {
                $userDataService->setFromDb(true);
            }

            $userEnc = $userDataService->getEncpass();
            if($userEnc === false)
            {
                $code = self::ERROR_GET_ENC_DATA;
                $msg  = self::$errorMsg[$code];
                $this->_setResult($code);
                $this->errorLog("error|error_code:{$code}; uid:{$uid};底层数据异常，没有获到encpass;|class:".__CLASS__.";func:".__FUNCTION__.$this->getCaller());

                return false;
            }

            //校验encpass
            if(strcasecmp($enc,$userEnc) !== 0)
            {
                $code = self::ERROR_INVALID_ENCPASS;
                $msg  = self::$errorMsg[$code];
                $this->_setResult($code);
                $this->accesslog("notice|无效的encpass;uid:{$uid},encpass:{$enc},error_code:{$code},error_msg:{$msg}|class:".__CLASS__.";func:".__FUNCTION__.$this->getCaller());
                return false;
            }

            //校验封禁状态
            if(!$this->checkDisableLoginStatus())
            {
                return false;
            }

            return true;

        } catch (Exception $e) {
            $this->_result['error_code'] = $e->getCode();
            $this->_result['error_msg']  = $e->getMessage();
            $this->errorLog("error|校验用户登陆异常;error_code:{$e->getCode()};error_msg{$e->getMessage()}|class:".__CLASS__.";func:".__FUNCTION__.$this->getCaller());

            return false;
        }

    }

    /**
     * 校验用户是否被封禁 只需uid
     * @return boolean 成功返回 true,失败返回 false
     */
    public function checkDisableLoginStatus()
    {
        try {

            $uid            = $this->getUid();
            $disableService = $this->_getUserDisableStatusService();
            $disableService->setUid($uid);
            $disableService->setFromDb($this->getFromDb());
            $disableService->setFromDbMaster($this->getFromDbMaster());
            $disableStatusData = $disableService->getDisableLoginStatus();

            if($disableStatusData === true)
            {
                return true;
            }

            if($disableStatusData === false)
            {
                //这里调的级别为低，封禁管理服务异常直接通过，以后可以调为false,这样更安全
                //封禁管理服务异常（可能是db,也有可能是redis,具体的可以查看封禁管理服务日志）
                $this->errorLog("notice|目前通过,uid:{$uid};封禁管理服务异常;可能是db,也有可能是redis,具体的可以查看封禁管理服务日志");
                return true;
            }

            //校验过期时间
            $etime   = (int) $disableStatusData['etime'];
            $nowTime = time();
            if($etime == 0)
            {
                $code = self::ERROR_DISABLE_LOGIN_F;
                $msg  = self::$errorMsg[$code];
                $this->_setResult($code);
                $this->_result['login_disable_etime'] = $etime;
                $this->accesslog("notice|uid:{$uid};过期时间为0,error_code:{$code};error_msg:{$msg}");
                return false;
            }

            if($etime == UserDisableStatusService::USER_DISABLE_MAX_TIME)
            {
                $code = self::ERROR_DISABLE_LOGIN_F;
                $msg  = self::$errorMsg[$code];
                $this->_setResult($code);
                $this->_result['login_disable_etime'] = $etime;
                $this->accesslog("notice|uid:{$uid};error_code:{$code};error_msg:{$msg}");
                return false;
            }

            if($etime >= $nowTime)
            {
                $code = self::ERROR_DISABLE_LOGIN_I;
                $msg  = self::$errorMsg[$code];
                $et   = date('Y-m-d H:i:s',$etime);
                $this->_setResult($code);
                $this->_result['login_disable_etime'] = $etime;
                $this->accesslog("notice|uid:{$uid};此帐号处于封禁期,禁止登陆,error_code:{$code},error_msg:{$msg},解禁时间:{$et}");
                return false;
            }

            //过期删除记录
            $disableService->setType(UserDisableStatusService::USER_DISABLE_TYPE_LOGIN);
            $disableService->setScope(UserDisableStatusService::USER_DISABLE_SCOPE_ALL);
            $disableService->setDesc('系统操作，过期记录自动删除');
            $disableService->deleteDisable();

            return true;

        } catch (Exception $e) {
            $this->_result['error_code'] = $e->getCode();
            $this->_result['error_msg']  = $e->getMessage();
            $this->errorLog("error|error_code:{$e->getCode()};error_msg{$e->getMessage()}|class:".__CLASS__.";func:".__FUNCTION__.$this->getCaller());

            return false;
        }
    }

    /**
     * 获取直播间禁言状态
     * @return true |false (true 用户状态正常，false 用户被禁言)
     */
    public function checkSilencedStatus()
    {
        try {

            $uid       = $this->getUid();
            $anchorUid = $this->getAnchorUid();
            $disableService = $this->_getUserDisableStatusService();
            $disableService->setUid($uid);
            $disableService->setFromDb($this->getFromDb());
            $disableService->setFromDbMaster($this->getFromDbMaster());
            $disableService->setType(UserDisableStatusService::USER_DISABLE_TYPE_SEND_MSG);
            $disableService->setScope(UserDisableStatusService::USER_DISABLE_SCOPE_ALL);
            $scopeAll  = $disableService->getSliencedStatus();
            $nowTime   = time();

            //先校验是否有全局禁言
            if($scopeAll && $scopeAll !== true)
            {
                $etime = (int) $scopeAll['etime'];
                if($etime >= $nowTime)
                {
                    $code = self::ERROR_SILENCED_ALL;
                    $msg  = self::$errorMsg[$code];
                    $this->_setResult($code);
                    $this->_result['silenced_etime'] = $etime;
                    $this->accesslog("notice|uid:{$uid};此帐号全局禁言,error_code:{$code},error_msg:{$msg}");
                    return false;
                }

                //过期删除记录
                $disableService->setType(UserDisableStatusService::USER_DISABLE_TYPE_SEND_MSG);
                $disableService->setScope(UserDisableStatusService::USER_DISABLE_SCOPE_ALL);
                $disableService->deleteDisable();
            }

            $disableService->setScope($anchorUid);
            $roomSilenced = $disableService->getSliencedStatus();
            if($roomSilenced === true)
            {
                return true;
            }

            if($roomSilenced)
            {
                $etime   = (int) $roomSilenced['etime'];
                if($etime >= $nowTime)
                {

                    $code = self::ERROR_SILENCED_ROOM;
                    $msg  = self::$errorMsg[$code];
                    $this->_setResult($code);
                    $this->_result['silenced_etime'] = $etime;
                    $et   = date('Y-m-d H:i:s',$etime);
                    $this->accesslog("notice|uid:{$uid};此帐号禁言,error_code:{$code},error_msg:{$msg},解禁时间:{$et}");
                    return false;
                }

                //过期删除记录
                $disableService->setType(UserDisableStatusService::USER_DISABLE_TYPE_SEND_MSG);
                $disableService->setScope($anchorUid);
                $disableService->setDesc('系统操作，过期记录自动删除');
                $disableService->deleteDisable();
            }

            return true;

        } catch (Exception $e) {
            $this->_result['error_code'] = $e->getCode();
            $this->_result['error_msg']  = $e->getMessage();
            $this->errorLog("error|error_code:{$e->getCode()};error_msg{$e->getMessage()}|class:".__CLASS__.";func:".__FUNCTION__.$this->getCaller());
            return false;
        }
    }

    /**
     * 获取是否被禁播 状态
     * @return boolean
     */
    public function checkAnchorLiveStatus()
    {
        try {

            $uid = $this->getUid();
            $disableService  = $this->_getUserDisableStatusService();
            $disableService->setUid($uid);
            $disableService->setFromDb($this->getFromDb());
            $disableService->setFromDbMaster($this->getFromDbMaster());
            $disableLiveData = $disableService->getAnchorLiveStatus();
            if($disableLiveData === true)
            {
                return true;
            }

            if($disableLiveData)
            {
                $nowTime = time();
                $etime   = (int) $disableLiveData['etime'];
                if($etime >= $nowTime)
                {

                    $code = self::ERROR_DISABLE_LIVE;
                    $msg  = self::$errorMsg[$code];
                    $this->_setResult($code);
                    $this->_result['live_etime'] = $etime;
                    $et   = date('Y-m-d H:i:s',$etime);
                    $this->accesslog("notice|uid:{$uid};此帐号禁播,error_code:{$code},error_msg:{$msg},解禁时间:{$et}");
                    return false;
                }

                //过期删除记录
                $disableService->setType(UserDisableStatusService::USER_DISABLE_TYPE_LIVE);
                $disableService->setDesc('系统操作，过期记录自动删除');
                $disableService->deleteDisable();
            }

            return true;

        } catch (Exception $e) {

            $this->_result['error_code'] = $e->getCode();
            $this->_result['error_msg']  = $e->getMessage();
            $this->errorLog("error|error_code:{$e->getCode()};error_msg{$e->getMessage()}|class:".__CLASS__.";func:".__FUNCTION__.$this->getCaller());
            return false;
        }
    }

    /**
     * 是否是主播 (包含未实名认证的)
     * @return boolean
     */
    public function checkIsAnchor()
    {
        try {

            $uid  = $this->getUid();
            $anchorService = new AnchorGetDataService();
            $anchorService->setUid($uid);
            $anchorService->setFromDb($this->getFromDb());
            $anchorService->setFromDbMaster($this->getFromDbMaster());
            return $anchorService->isExist() ? true : false;

        } catch (Exception $e) {
            $this->_result['error_code'] = $e->getCode();
            $this->_result['error_msg']  = $e->getMessage();
            $this->errorLog("error|error_code:{$e->getCode()};error_msg{$e->getMessage()}|class:".__CLASS__.";func:".__FUNCTION__.$this->getCaller());
            return false;
        }
    }

    /**
     *  校验主播 实名认证状态
     * @return boolean
     */
    public function checkAnchorCertStatus()
    {
        try {

            $uid  = $this->getUid();
            $anchorService = new AnchorGetDataService();
            $anchorService->setUid($uid);
            $anchorService->setFromDb($this->getFromDb());
            $anchorService->setFromDbMaster($this->getFromDbMaster());
            return $anchorService->getCertStatus() ? true : false;

        } catch (Exception $e) {
            $this->_result['error_code'] = $e->getCode();
            $this->_result['error_msg']  = $e->getMessage();
            $this->errorLog("error|error_code:{$e->getCode()};error_msg{$e->getMessage()}|class:".__CLASS__.";func:".__FUNCTION__.$this->getCaller());
            return false;
        }

    }

    /**
     *  判断用户是否开起陪玩
     * @return boolean
     */
    public function checkIsDueAnchor()
    {
        try {

            $uid     = $this->getUid();
            //底层由xingwei提供
            $dueCert = new DueCertService();
            $dueCert->setUid($uid);
            $status  = $dueCert->isAnchorSkillOn();
            return $status ? true :false;

        } catch (Exception $e) {
            $this->_result['error_code'] = $e->getCode();
            $this->_result['error_msg']  = $e->getMessage();
            $this->errorLog("error|error_code:{$e->getCode()};error_msg{$e->getMessage()}|class:".__CLASS__.";func:".__FUNCTION__.$this->getCaller());
            return false;
        }
    }

    /**
     * 校校用户是否实名认证过
     * @return true |false
     */
    public function checkUserCertStatus()
    {
        try {

            $uid  = $this->getUid();
            $certService = new UserCertDataService();
            $certService->setUid($uid);
            $certService->setFromDb($this->getFromDb());
            $certService->setFromDbMaster($this->getFromDbMaster());
            return $certService->getRealNameCertStatus() ? true : false;

        } catch (Exception $e) {
            $this->_result['error_code'] = $e->getCode();
            $this->_result['error_msg']  = $e->getMessage();
            $this->errorLog("error|error_code:{$e->getCode()};error_msg{$e->getMessage()}|class:".__CLASS__.";func:".__FUNCTION__.$this->getCaller());
            return false;
        }
    }

    /**
     * 获取封禁结果
     * @return array
     */
    public function getResult()
    {
        return $this->_result;
    }

    private function _getUserDisableStatusService()
    {
        if(!$this->_userDisableService)
        {
            $this->_userDisableService = new UserDisableStatusService();
        }

        return $this->_userDisableService;
    }

    private function _getUserDataService()
    {
        if(!$this->_userDataService)
        {
            $this->_userDataService = new UserDataService();
        }

        return $this->_userDataService;
    }

    private function _setResult($code)
    {
        $this->_result['error_code'] = $code;
        $this->_result['error_msg']  = self::$errorMsg[$code];
    }

    public function accesslog($msg)
    {
        write_log($msg,$this->_accessLog);
    }

    public function errorLog($msg)
    {
        write_log($msg,$this->_errorLog);
    }
}