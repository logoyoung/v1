<?php

include '../../../include/init.php';

use service\user\UserDataService;
use lib\RoomTimerGetBean;
use lib\BaseInfo;
use lib\User;
use service\event\EventManager;
use service\user\UserAuthService;

require_once INCLUDE_DIR . 'class.geetestlib.php';

/**
 * 到时获取欢豆
 * @author longgang <longgang@6.cn>
 * @date 2017-04-25 17:54:11
 * @copyright (c) 2017, 6.cn
 * @version 1.0.1
 */
class GetHuanBeans
{

    //用户ID
    private $_uid;
    //加密串
    private $_enc;
    //事件类型 enter:'进入房间获取当前领取信息’, pick:’领取欢豆'
    private $_event;
    //主播ID
    private $_luid;
    //请求来源 1为app端 2为web端
    private $_client;
    //当event=pick时传入表示第lvl次领取欢豆奖励
    private $_lvl;
    //当传入值"gt"表示极限检验
    private $_type;
    //event=pick时传入，极验验证所需参数
    private $_geetest_challenge;
    //event=pick时传入，极验验证所需参数
    private $_geetest_validate;
    //event=pick时传入，极验验证所需参数
    private $_geetest_seccode;
    //redis服务类
    private $_redis;

    //缺少参数或者参数类型错误
    const ERROR_USER_PARAM = -4013;
    //验证码错误或已过期
    const ERROR_VCODE = -4031;
    //用户登录验证失败
    const ERROR_USER_AUTH = -4067;
    //手机尚未认证
    const ERROR_MOBILE_AUTH = -5026;

    public static $errorMsg = [
        self::ERROR_USER_PARAM => '缺少参数或者参数类型错误',
        self::ERROR_VCODE => '验证码错误或已过期',
        self::ERROR_USER_AUTH => '用户登录验证失败',
        self::ERROR_MOBILE_AUTH => '手机尚未认证',
        RoomTimerGetBean::ERROR_ENTER_ROOM_RECORD => '数据错误',
        RoomTimerGetBean::ERROR_ALREADY_GET => '已经领取过',
        RoomTimerGetBean::ERROR_NOT_TIME => '还未到领取时间',
    ];

    private function _init()
    {
        $this->_uid = isset($_POST['uid']) ? (int) $_POST['uid'] : 0;
        $this->_enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
        $this->_event = isset($_POST['event']) ? trim($_POST['event']) : '';
        $this->_luid = isset($_POST['luid']) ? (int) $_POST['luid'] : 0;
        $this->_client = isset($_POST['client']) ? (int) $_POST['client'] : BaseInfo::INFORMATION_CLIENT_WEB;
        $this->_lvl = isset($_POST['lvl']) ? (int) $_POST['lvl'] : 0;
        $this->_type = isset($_POST['type']) ? (int) $_POST['type'] : 0;
        $this->_geetest_challenge = isset($_POST['geetest_challenge']) ? trim($_POST['geetest_challenge']) : '';
        $this->_geetest_validate = isset($_POST['geetest_validate']) ? trim($_POST['geetest_validate']) : '';
        $this->_geetest_seccode = isset($_POST['geetest_seccode']) ? trim($_POST['geetest_seccode']) : '';
        $this->_redis = new RedisHelp();

        if (!$this->_uid)
        {
            $this->rJson(self::ERROR_USER_PARAM);
        }

        $this->_auth();

        if ($this->_event == 'pick')
        {
            if ($this->_type != 'gt')
            {
                $this->rJson(self::ERROR_USER_PARAM);
            }

            if (!$this->_lvl || !$this->_geetest_challenge || !$this->_geetest_validate || !$this->_geetest_seccode)
            {
                $this->rJson(self::ERROR_USER_PARAM);
            }

            $this->_geetestAuth();
        }

        return true;
    }

    private function _auth()
    {

        if (!$this->_enc)
        {
            $this->rJson(self::ERROR_USER_PARAM);
        }

        $auth = new UserAuthService();
        $auth->setUid($this->_uid);
        $auth->setEnc($this->_enc);
        //校验encpass、用户 登陆状态
        if($auth->checkLoginStatus() !== true)
        {
            //获取校验结果
            $result    = $auth->getResult();
            //错误码
            $errorCode = $result['error_code'];
            //错误消息
            $errorMsg  = $result['error_msg'];
            //假如是封禁的，可以获取禁时间
            $etime     = isset($result['login_disable_etime']) ? $result['login_disable_etime'] : 0;
            write_log("notice|uid:{$this->_uid};error_code:{$errorCode};error_msg:{$errorMsg};解禁时间:{$etime}|api:".__FILE__,'auth_access');
            $this->rJson(self::ERROR_USER_AUTH);
        }

        return true;
    }

    private function _geetestAuth()
    {
        if (!($this->_checkGeetestCode($this->_geetest_challenge, $this->_geetest_validate, $this->_geetest_seccode, $this->_client, $this->_redis)))
        {
            $this->rJson(self::ERROR_VCODE);
        }
        return TRUE;
    }

    /**
     * 极限验证
     * @param string $challenge
     * @param string $validate
     * @param string $seccode
     * @param int $client
     * @param \RedisHelp $redis
     * @return type
     */
    private function _checkGeetestCode($challenge, $validate, $seccode, $client, $redis = null)
    {
        if (!$redis)
        {
            $redis = new RedisHelp();
        }

        $GtSdk = $client == '1' ? new GeetestLib(CAPTCHA_APP_ID, PRIVATE_APP_KEY) : new GeetestLib(CAPTCHA_ID, PRIVATE_KEY);
        if (!empty($_COOKIE['_geetest_client']) && (int) $redis->get($_COOKIE['_geetest_client'])
        )
        {
            return $GtSdk->success_validate($challenge, $validate, $seccode);
        } else
        {
            return $GtSdk->fail_validate($challenge, $validate, $seccode);
        }
    }

    public function display()
    {
        $this->_init();
        $db = new DBHelperi_huanpeng();
        $service = new RoomTimerGetBean($this->_uid, $db, $this->_redis);

        if ($this->_event == 'enter')
        {
            $result = $service->enterRoom($this->_luid, $errno);
            if ($errno)
            {
                $this->rJson($errno);
            }
        } elseif ($this->_event == 'pick')
        {

            $userService = new UserDataService();
            $userService->setUid($this->_uid);
            $userData    = $userService->getUserInfo();
            if (!isset($userData['phone']) || !$userData['phone'])
            {
                $this->rJson(self::ERROR_MOBILE_AUTH);
            }
            $user = new User($this->_uid);
            $result = $service->getBean($this->_luid, $this->_lvl, $user, $errno);

            if ($errno)
            {
                $msg = self::$errorMsg[$errno];
                $log = "error_code:{$errno};msg:{$msg};Uid: " . $this->_uid . '; Luid:' . $this->_luid . ';|class:' . __CLASS__ . ';func:' . __FUNCTION__;
                write_log($log);
                $this->rJson($errno);
            } else
            {
                $log = 'Uid: ' . $this->_uid . ' ;Luid:' . $this->_luid . ' get Bean Success!The result:' . json_encode($result) . ';|class:' . __CLASS__ . ';func:' . __FUNCTION__;
                write_log($log);
                $event   = new EventManager();
                $event->trigger(EventManager::ACTION_USER_MONEY_UPDATE,['uid' => $this->_uid]);
            }
        }

        render_json($result);
    }

    public function rJson($code = 0)
    {
        $msg = self::$errorMsg[$code];
        render_error_json($msg, $code);
    }

}

$obj = new GetHuanBeans();
$obj->display();
