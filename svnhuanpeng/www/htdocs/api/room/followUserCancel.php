<?php

include '../../../include/init.php';
use service\user\UserDataService;
use service\user\FollowService;
use service\anchor\AnchorDataService;
use service\user\UserAuthService;

/**
 *  取消主播接口
 * @author xuyong
 * @date 2017-4-23
 * @version 1.01
 *
 */

class followUserCancel
{

    //uid不能为空
    const ERROR_USER_UID = -12761;
    //encpass不能为空
    const ERROR_USER_ENC = -12762;
    //无效的关注对象
    const ERROR_LUID     = -1027;
    //无效的目标uid
    const ERROR_USER_NOT_EXISTS = -1025;
    //请重新登陆
    const ERROR_USER_AUTH = -4067;
    //系统异常，请稍后再试!
    const ERROR_FOLLOW    = -12763;

    public $uid;
    public $enc;
    public $luid;

    public static $errorMsg = [
        self::ERROR_USER_UID => 'uid不能为空',
        self::ERROR_USER_ENC => 'encpass不能为空',
        self::ERROR_LUID     => '无效的关注对象luids',
        self::ERROR_USER_NOT_EXISTS => '目标用户不存在',
        self::ERROR_USER_AUTH => '请重新登录!',
        self::ERROR_FOLLOW    => '系统异常，请稍后再试!',
    ];

    private function _init()
    {
        $this->uid   = isset($_POST['uid'])     ? (int) $_POST['uid']     : '';
        $this->enc   = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
        $this->luid  = isset($_POST['luids'])   ? (int) $_POST['luids']   : '';
        if(!$this->uid)
        {
            $code = self::ERROR_USER_UID;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code);
        }

        if(!$this->enc)
        {
            $code = self::ERROR_USER_ENC;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code);
        }

        if(!$this->luid)
        {
            $code = self::ERROR_LUID;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code);
        }

        $auth = new UserAuthService();
        $auth->setUid($this->uid);
        $auth->setEnc($this->enc);
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
            write_log("notice|uid:{$this->uid};error_code:{$errorCode};error_msg:{$errorMsg};解禁时间:{$etime}|api:".__FILE__,'auth_access');
            $code = self::ERROR_USER_AUTH;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code,2);
        }

        $userDataService = new UserDataService();
        $userDataService->setCaller('api:'.__FILE__);
        $userDataService->setUid($this->luid);
        //校验目标用户是存在
        if(!$userDataService->isExist())
        {
            $code = self::ERROR_USER_NOT_EXISTS;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code);
        }

        return true;
    }

    public function display()
    {
        $this->_init();
        $service = new FollowService();
        $service->setCaller('api:'.__FILE__);
        $service->setUid($this->uid);
        $service->setLuid($this->luid);
        if(!$service->unFollowAnchor())
        {
            $code = self::ERROR_FOLLOW;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code);
        }

        //获取主播粉丝数
        $anchorDataService = new AnchorDataService();
        $anchorDataService->setCaller('api:'.__FILE__);
        $anchorDataService->setUid($this->luid);
        $fansNum = $anchorDataService->getFollowNumber();
        $result = [
            'type'      => '1',
            'luids'     => $this->luid,
            'fansCount' => $fansNum,
        ];

        render_json($result);

    }

}

$obj = new followUserCancel();
$obj->display();