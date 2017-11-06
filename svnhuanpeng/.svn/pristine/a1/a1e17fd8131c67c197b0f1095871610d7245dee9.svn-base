<?php

include '../../../include/init.php';
/*
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/29
 * Time: 上午10:34
 * 2017-4-24 xuyong  update
 */

use service\task\TaskService;
use service\user\UserDataService;
use service\user\UserAuthService;

class MyTask
{

    const ERROR_CODE_UID    = -13001;
    const ERROR_CODE_ENC    = -13002;
    const ERROR_CODE_USER   = -13004;
    const ERROR_CODE_JOB    = -13005;
    //手机尚未认证哦～
    const ERROR_CODE_PHONE  = -5026;

    public static $errorMsg = [
        self::ERROR_CODE_UID    => '缺少参数或者参数类型错误',
        self::ERROR_CODE_ENC    => '缺少参数或者参数类型错误',
        self::ERROR_CODE_USER   => '请重新登录',
        self::ERROR_CODE_JOB    => '系统异常，请稍后再试',
        self::ERROR_CODE_PHONE  => '手机尚未认证哦～',
    ];

    public function display()
    {
        $this->uid   = isset($_POST['uid'])     ? (int) $_POST['uid']     : '';
        $this->enc   = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';

        if(!$this->uid)
        {
            $code = self::ERROR_CODE_UID;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code);
        }

        if(!$this->enc)
        {
            $code = self::ERROR_CODE_ENC;
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
            $code = self::ERROR_CODE_USER;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code);
        }

        $userDataService = new UserDataService();
        $userDataService->setCaller('api:'.__FILE__);
        $userDataService->setUid($this->uid);
        $userDataService->setEnc($this->enc);
        $userData = $userDataService->getUserInfo();
        //手机验证
        if(!isset($userData['phone']) || !$userData['phone'])
        {
            //手机尚未认证哦～
            $code = self::ERROR_CODE_PHONE;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code,2);
        }

        $taskService = new TaskService();
        $taskService->setCaller('api:'.__FILE__);
        $taskService->setUid($this->uid);
        $list = $taskService->getUserTaskList();
        render_json(['list' => $list]);

    }

}

$obj = new MyTask();
$obj->display();