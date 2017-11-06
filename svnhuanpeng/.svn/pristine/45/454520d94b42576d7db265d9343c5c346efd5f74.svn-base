<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/6/3
 * Time: 上午11:49
 */

include '../../../include/init.php';

use service\task\TaskService;
use service\user\UserDataService;
use service\user\UserAuthService;

class BeanTask
{

    const ERROR_CODE_UID    = -13001;
    const ERROR_CODE_ENC    = -13002;
    const ERROR_CODE_TASKID = -13003;
    const ERROR_CODE_USER   = -13004;
    const ERROR_CODE_JOB    = -13005;
    //该任务尚未完成，不能领取
    const ERROR_CODE_TASK   = -5018;

    public $uid;
    public $enc;
    public $taskId;

    public static $errorMsg = [
        self::ERROR_CODE_UID    => '缺少参数或者参数类型错误',
        self::ERROR_CODE_ENC    => '缺少参数或者参数类型错误',
        self::ERROR_CODE_TASKID => '缺少参数或者参数类型错误',
        self::ERROR_CODE_USER   => '请重新登录',
        self::ERROR_CODE_JOB    => '系统异常，请稍后再试',
        self::ERROR_CODE_TASK   => '该任务尚未完成，不能领取',
    ];

    public function display()
    {
        $this->uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
        if(!$this->uid)
        {
            $code = self::ERROR_CODE_UID;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code);
        }

        $this->enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
        if(!$this->enc)
        {
            $code = self::ERROR_CODE_ENC;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code);
        }

        $this->taskId = isset($_POST['taskID']) ? (int) $_POST['taskID'] : '';
        if(!$this->taskId)
        {
            $code = self::ERROR_CODE_TASKID;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code,2);
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
            render_error_json($msg,$code,2);
        }

        $taskService = new TaskService();
        $taskService->setCaller('api:'.__FILE__);
        $taskService->setUid($this->uid);
        $taskService->setTaskId($this->taskId);

        //领取欢豆数量
        $count = $taskService->getBeanByTask();
        if($count === false)
        {
            $code = self::ERROR_CODE_JOB;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code,2);
        }

        //没有需要领取的任务
        if($count === true)
        {
            $code = self::ERROR_CODE_TASK;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code,2);
        }

        $userDataService = new UserDataService();
        $userDataService->setCaller('api:'.__FILE__);
        $userDataService->setUid($this->uid);
        $userDataService->setEnc($this->enc);
        $userDataService->setUserInfoDetail(UserDataService::USER_ACTICE_BASE);
        $userDataService->setFromDb(true)->setFromDbMaster(true);
        $property = $userDataService->getUserInfo();
        $result = [
                'count'  => $count,
                'hpbean' => isset($property['hpbean']) ? $property['hpbean'] : '0',
                'hpcoin' => isset($property['hpcoin']) ? $property['hpcoin'] : '0',
        ];

        render_json($result);
    }

}

$obj = new BeanTask();
$obj->display();
