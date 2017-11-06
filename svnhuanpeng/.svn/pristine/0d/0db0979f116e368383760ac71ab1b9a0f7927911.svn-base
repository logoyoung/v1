<?php
include '../../../include/init.php';
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/13
 * Time: 上午11:29
 * @update xuyong 2017-5-9
 */


use service\user\UserDataService;
use service\room\RoomManagerService;
use service\user\UserAuthService;

class setSilenced
{
    //操作用户uid
    private $_uid;
    //主播ID
    private $_luid;
    //操作用户enc
    private $_enc;
    //初禁言用户
    private $_targetUid;
    //房间号
    private $_roomID;
    //禁言时间
    private $_timeLength;

    private $_roomManagerService;

    const ERROR_CODE_PARAM    = -4013;
    const ERROR_CODE_USER_ENC = -4067;
    const ERROR_TUID_INVALID  = -8521;
    const ERROR_ACUID_INVALID = -8523;
    //禁言用户不存在
    const ERROR_TUID_NOTEXIST = -8524;

    public static $erroMsg = [
        self::ERROR_CODE_PARAM    => '缺少参数或者参数类型错误',
        self::ERROR_CODE_USER_ENC => '请重新登陆',
        self::ERROR_TUID_INVALID  => '无效的targetUID',
        self::ERROR_ACUID_INVALID => '无权操作',
        self::ERROR_TUID_NOTEXIST => '被禁言用户不存在',
    ];

    public function display()
    {
        $this->_uid  = isset($_POST['uid'])     ? (int) $_POST['uid']  : 0;
        $this->_enc  = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
        $this->_luid = isset($_POST['luid'])    ? (int) $_POST['luid'] : 0;
        $this->_targetUid  = isset($_POST['targetUID']) ? (int) $_POST['targetUID'] : 0;
        $this->_timeLength = isset($_POST['timeLength']) ? (int) $_POST['timeLength'] : 0;
        if(!$this->_uid || !$this->_enc || !$this->_luid || !$this->_targetUid )
        {
            $code = self::ERROR_CODE_PARAM;
            $msg  = self::$erroMsg[$code];
            render_error_json($msg,$code,2);
        }

        if($this->_targetUid == $this->_uid || $this->_targetUid == $this->_luid)
        {
            $code = self::ERROR_TUID_INVALID;
            $msg  = self::$erroMsg[$code];
            render_error_json($msg,$code,2);
        }

        $auth = new UserAuthService();
        $auth->setCaller('api:'.__FILE__.';line:'.__LINE__);
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
            $code = self::ERROR_CODE_USER_ENC;
            $msg  = self::$erroMsg[$code];
            render_error_json($msg,$code,2);
        }

        $this->_roomManagerService = new RoomManagerService();
        $this->_roomManagerService->setCaller('api:'.__FILE__.';line:'.__LINE__);
        //主播uid
        $this->_roomManagerService->setUid($this->_luid);
        //操作者uid
        $this->_roomManagerService->setAcUid($this->_uid);
        //禁方用户uid
        $this->_roomManagerService->setTargetUid($this->_targetUid);
        //禁言时间
        $this->_roomManagerService->setTimeLength($this->_timeLength);

        //校验操作者是否是房管
        if(!$this->_roomManagerService->isRoomManager() && $this->_uid != $this->_luid)
        {
            $code = self::ERROR_ACUID_INVALID;
            $msg  = self::$erroMsg[$code];
            render_error_json($msg,$code,2);
        }

        $userDataService = new UserDataService();
        $userDataService->setCaller('api:'.__FILE__.';line:'.__LINE__);
        $userDataService->setUid($this->_targetUid);
        //targetUid用户是否存在
        if(!$userDataService->isExist())
        {
            $code = self::ERROR_TUID_NOTEXIST;
            $msg  = self::$erroMsg[$code];
            render_error_json($msg,$code,2);
        }

        try
        {
            $this->_roomManagerService->setSilence();
            render_json(['code' => 0]);
        } catch (Exception $e)
        {
            render_error_json('系统繁忙，请稍后再试！',$e->getCode(),2);
        }

    }
}

$obj = new setSilenced();
$obj->display();