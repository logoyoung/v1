<?php

namespace service\user;

use service\event\EventManager;
use service\common\AbstractService;
use service\rule\TextService;
use lib\Register;
use tool\ValidationTool;
use Exception;
use sendMobileMsg;
use service\user\UserNickService;

/**
 * 用户注册服务
 * <pre>    
 *      $reg = new service\user\UserRegisterService(); 
 *      $reg->setPhone($param['phone']);
 *      $reg->setVerificationCode($param['vCode']);
 *      $reg->setNick($param['nick']);
 *      $reg->setPassword($param['password']);
 *      $res = $reg->register();
 *      if (!empty($res)) {
 *          $this->resultData = $res;
 *      } else {
 *          $this->resultData['code'] = $reg->getErrorCode();
 *          $this->resultData['desc'] = $reg->getErrorMessage();
 *      }
 * </pre>
 * @author xuyong <[<email address>]>
 * @date 2017-5-3
 */
class UserRegisterService extends AbstractService {

    //注册来源
    const CLICK_WEB = 0;
    const CLICK_APP = 1;
    //无效的手机号
    const ERROR_PHONE_INVALID = -7301;
    //该手机号已被注册
    const ERROR_PHONE_USED = -7302;
    //昵称含特殊字符或者为空
    const ERROR_NICK_INVALID = -7303;
    //昵称长度范围3-10个字符
    const ERROR_NICK_LENGTH = -7304;
    //该昵称已存在
    const ERROR_NICK_UESD = -7305;
    //无效的验证码
    const ERROR_CODE_INVALID = -7306;
    //请输入验证码
    const ERROR_CODE_INPUT_EMPTY = -7307;

    public $logFileName = 'user_register';
    //用户名
    private $_userName;
    //用户昵称
    private $_nick;
    //密码
    private $_password;
    //手机号
    private $_phone;
    //验证码
    private $_verificationCode;
    //0 web端  1客户端  默认0
    private $_client = 0;
    //渠道号
    private $_cid;
    //渠道编号
    private $_channelID;
    private $_userDb;
    //注册信息
    private $_registerInfo;
    //用户信息
    private $_userInfo = [];
    //
    private $_defaultNick = '';
    private $_defaultNickAuditModel = '';
    //错误信息
    public $errorCode = 0;
    public $errorMessage = '';
    public static $errorMsg = [
        self::ERROR_PHONE_INVALID    => '请输入正确的手机号码!',
        self::ERROR_CODE_INVALID     => '无效的验证码!',
        self::ERROR_PHONE_USED       => '该手机号已被注册!',
        self::ERROR_NICK_INVALID     => '哈尼,昵称中含非法字符!',
        self::ERROR_NICK_LENGTH      => '昵称长度范围3-10个字符',
        self::ERROR_NICK_UESD        => '该昵称已存在',
        self::ERROR_CODE_INPUT_EMPTY => '请输入验证码',
    ];

    public function __construct() {
        
    }

    public function setPassword($password) {
        $this->_password = $password;
        return $this;
    }

    public function getPassword() {
        return $this->_password;
    }

    public function setNick($nick) {
        $this->_nick = xss_clean($nick);
        return $this;
    }

    public function getNick() {
        return $this->_nick;
    }

    public function setPhone($phone) {
        $this->_phone = $phone;
        return $this;
    }

    public function getPhone() {
        return $this->_phone;
    }

    public function setVerificationCode($phoneCode) {
        $this->_verificationCode = $phoneCode;
        return $this;
    }

    public function getVerificationCode() {
        return $this->_verificationCode;
    }

    public function setClient($client) {
        $this->_client = $client;
        return $this;
    }

    public function getClient() {
        return $this->_client;
    }

    public function setCid($cid) {
        $this->_cid = $cid;
        return $this;
    }

    public function getCid() {
        return $this->_cid;
    }

    public function setChannelID($phone) {
        $this->_channelID = $phone;
        return $this;
    }

    public function getChannelID() {
        return $this->_channelID;
    }

    protected $register = null;

    protected function getRegister(): Register {
        if (!$this->register) {
            $this->register = new Register();
        }
        return $this->register;
    }

    protected $userNickService = null;

    protected function getUserNickService(): UserNickService {
        if (!$this->userNickService) {
            $this->userNickService = new UserNickService();
        }
        return $this->userNickService;
    }

    /**
     * 注册
     */
    public function register() {
        try {
            ##1
            #2. 昵称
            $this->_checkNick();
            #3. 手机
            $this->_checkPhone();
            #4. 密码
            $this->_checkPassword();
            #5. 客户端
            $this->_checkClient(); //客户端注册活动。先关闭
            #6. 渠道号
//            $this->_checkCid();
            #7. 渠道编号
            $this->_checkChannelId();
            #1. 验证码,放到后面可以减小短信发送次数
            $this->_checkVerificationCode();

            ##2  昵称,密码,手机
            #1. 审核模式
//            $this->_checkNickModel();
            $defaultNick = $this->getUserNickService()->createNick($this->_nick);
            #2. 添加用户
            $userInfo = $this->getRegister()->addUserWhitFilter($this->_phone, $defaultNick, $this->_password, true);

            if (is_array($userInfo) && isset($userInfo['uid']) && $userInfo['uid'] > 0) {
                $this->_userInfo = $userInfo;
                #3. 送审
//                $this->_nickSendAudit($this->_userInfo['uid']);

                $this->getUserNickService()->setUid($this->_userInfo['uid']);
                $this->getUserNickService()->alterByRegister($this->_nick);
                #4. 清理缓存
                $this->refreshCache($this->_userInfo['uid']);
                #5. 绑定渠道
                $this->_bindChannelId($this->_userInfo['uid'], $this->_channelID);
                return $this->_userInfo;
            } else {
                return FALSE;
            }
        } catch (Exception $exc) {
            $this->errorMessage = $exc->getMessage();
            $this->errorCode = $exc->getCode();
            return FALSE;
        }
    }

    public function getErrorMessage() {
        return $this->errorMessage;
    }

    public function getErrorCode() {
        return $this->errorCode;
    }

    #=============================
    # 规则检验
    #=============================

    private function _checkVerificationCode() {
        if ($this->_verificationCode) {
            require_once INCLUDE_DIR . 'mobileMessage.class.php';
            $checkmcode = sendMobileMsg::checkSuccess(sendMobileMsg::t_register, $this->_phone, $this->_verificationCode, $this->getUserDb());

            if ($checkmcode) {
                sendMobileMsg::sendMsgCallBack(sendMobileMsg::$codeid, $this->getUserDb());
            } else {
                write_log("error|验证码错误;nick:{$this->_nick},phone:{$this->_phone};client:{$this->_client}", $this->logFileName);

                throw new Exception(errorDesc(-4031), -4031);
            }
        } else {
            throw new Exception(errorDesc(-4013), -4013);
        }
    }

    /**
     * 昵称检测
     * @return boolean
     * @throws Exception
     */
    private function _checkNick() {
        $validation = $this->getUserNickService();
        $res = $validation->isValidNick($this->_nick, $errno, $desc);
        if (!$res) {
            throw new Exception($desc, $errno);
        }
        $res = $validation->isNickWasOccupied($this->_nick);
        if ($res) {
            throw new Exception(errorDesc(-4035), -4035);
        }

        return TRUE;
        $this->_nick = ValidationTool::filterWords($this->_nick);
        if ($this->_nick) {
            ### 反垃圾
            $this->_textService();
            ### 昵称分析
            $checkNickValidRes = $this->checkNickValid($this->_nick);
            if (!$checkNickValidRes) {
                write_log("error|nick含有敏感字符;nick:{$this->_nick},phone:{$this->_phone};client:{$this->_client}", $this->logFileName);
                throw new Exception(errorDesc(-4091), -4091);
            }
            ### 昵称长度判断
            $checkNickLenthRes = $this->checkNickLenth($this->_nick);
            if (!$checkNickLenthRes) {
                write_log("error|nick长度不合法;nick:{$this->_nick},phone:{$this->_phone};client:{$this->_client}", $this->logFileName);
                throw new Exception(errorDesc(-4010), -4010);
            }
            ### 数据库昵称检测是否已存在
            $checkNickUseInfoRes = $this->checkNickUseInfo($this->_nick);
            if ($checkNickUseInfoRes) {
                write_log("error|nick已存在;nick:{$this->_nick},phone:{$this->_phone};client:{$this->_client}", $this->logFileName);
                throw new Exception(errorDesc(-4035), -4035);
            }
            return TRUE;
        } else {
            throw new Exception(errorDesc(-4064), -4064);
        }
    }

    /**
     * 手机检测
     * @return boolean
     * @throws Exception
     */
    private function _checkPhone() {
        #手机格式
        $checkPhoneValidRes = $this->checkPhoneValid($this->_phone);
        if (!$checkPhoneValidRes) {
            write_log("error|手机格式不合法;nick:{$this->_nick},phone:{$this->_phone};client:{$this->_client}", $this->logFileName);
            throw new Exception(errorDesc(-4058), -4058);
        }
        #手机是否已经被使用
        $checkPhoneUsedRes = $this->checkPhoneUsed($this->_phone);
        if ($checkPhoneUsedRes) {
            write_log("error|手机号已经注册过;nick:{$this->_nick},phone:{$this->_phone};client:{$this->_client}", $this->logFileName);
            throw new Exception(errorDesc(-4060), -4060);
        }
        return TRUE;
    }

    /**
     * 密码过滤
     * @throws Exception
     */
    private function _checkPassword() {
        # 密码简单过滤就可以了
        if (empty($this->_password)) {
            throw new Exception(errorDesc(-4013), -4013);
        }
        $this->_password = ValidationTool::filterWords($this->_password);
        if (!$this->_password) {
            throw new Exception(errorDesc(-4013), -4013);
        }
        $checkPasswordLengRes = ValidationTool::checkPasswordLeng($this->_password);
        if (!$checkPasswordLengRes) {
            throw new Exception(errorDesc(-1003), -1003);
        }
        return TRUE;
    }

    /**
     * 检测注册来源
     * @throws Exception
     */
    private function _checkClient() {

        if (!in_array($this->_client, array(self::CLICK_WEB, self::CLICK_APP))) {
            throw new Exception(errorDesc(-4017), -4017);
        }


        return TRUE;
    }

    /**
     * 检测注册渠道来源
     * @throws Exception
     */
    private function _checkChannelId() {
        //不强制要求
        return TRUE;
    }

    /**
     * 检测注册来源
     * @throws Exception
     */
    private function _bindChannelId($uid, $channel) {
        //绑定注册渠道,注册已完成,不直接报错
        if (!bindUserChannel($uid, $channel, $this->getUserDb())) {
            write_log("error|添加注册渠道异常;nick:{$this->_nick},phone:{$this->_phone};client:{$this->_client}", $this->logFileName);
        }
        return TRUE;
    }

    /**
     * 检测
     * @throws Exception
     */
    private function _checkCid() {
        return TRUE;
    }

    private function _textService($type = TextService::CHANNEL_NICKNAME) {
        $textService = new TextService();
        $textService->setCaller('api:' . __FILE__ . ';line:' . __LINE__);
        //关闭后如果接请求反垃圾接口网络服务异常都会返回true,默认通过
        //$textService->setCallLevel(true);
        $textService->addText($this->_nick, time(), $type)->setIp(fetch_real_ip($port));
        //反垃圾过滤
        if (!$textService->checkStatus()) {
//            write_log("error|nick含有发敏感字符(反垃圾);nick:{$this->_nick},phone:{$this->_phone};client:{$this->_client}", 'user_register');
            throw new Exception(errorDesc(-4091), -4091);
        }
        return TRUE;
    }

    /**
     * 
     */
    private function _checkNickModel() {
        $checkNickMode = checkMode(CHECK_NICK, $this->getUserDb()); //检测昵称审核模式
        if (!$checkNickMode) {
//            $defaultNick = "用户" . $this->_phone; //先审后发状态下默认昵称
            $defaultNick = $this->getUserNickService()->createNick($this->_nick);
            $status = USER_NICK_WAIT;
        } else {
            $status = USER_NICK_AUTO_PASS;
            $defaultNick = $this->_nick;
        }
        $this->_defaultNickAuditModel = $status;
        $this->_defaultNick = $defaultNick;
    }

    /**
     * 送审
     * @param type $uid
     * @return type
     */
    private function _nickSendAudit($uid) {
        return setNickToAdmin($uid, $this->_nick, $this->getUserDb(), $this->_defaultNickAuditModel); //同步到admin_user_nick表中
    }

    /**
     * 刷新缓存
     * @param type $uid
     * @return type
     */
    public function refreshCache($uid) {
        $event = new EventManager();
        $event->trigger(EventManager::ACTION_USER_REGISTER, ['uid' => $uid]);
        return TRUE;
    }

    /**
     * 校验手机是否合法
     * @return boolean
     */
    public function checkPhoneValid($phone) {
        return ValidationTool::checkPhoneValid($phone);
    }

    /**
     * 校验手机是否可用
     * @param type $phone
     * @return bool  已使用true  ｜ 未使用 false
     */
    public function checkPhoneUsed($phone) {
        $res = $this->getRegister()->checkMobileIsUsed($phone, $this->getUserDb());

        return $res;
    }

    /**
     * 校验昵称是否合法
     * @return bool true 合法 | false非法
     */
    public function checkNickValid($nick) {
        $res = ValidationTool::checkEmoji($nick);
        return $res;
    }

    /**
     * 校验昵称长度是否合法
     * @return 符合true ｜ 不符合false
     */
    public function checkNickLenth($nick) {
        $res = ValidationTool::checkNickLength($nick);
        return $res;
    }

    /**
     * 校验昵称是否被使用
     *
     * return bool  已使用true  ｜ 未使用 false
     */
    public function checkNickUseInfo($nick) {
        $res = $this->getRegister()->checkNickIsUsed($nick, $this->getUserDb());
        return $res;
    }

    public function getUserDb() {
        if (!$this->_userDb) {
            $this->_userDb = Register::getDB();
        }
        return $this->_userDb;
    }

}
