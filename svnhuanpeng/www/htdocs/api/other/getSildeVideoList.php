<?php

include '../../../include/init.php';

use service\user\UserAuthService;
use service\silde\SildeService;

class getSildeVideoList
{

    //获取视频列表异常
    const ERROR_VIDEO_LIST = -40001;

    public static $errorMsg = [
        self::ERROR_VIDEO_LIST => '获取视频列表异常',
    ];
    private $_params;

    private function _init()
    {
        $this->_params['videoId'] = isset($_POST['videoId']) ? (int) $_POST['videoId'] : 0;
        $this->_params['uid'] = isset($_POST['uid']) ? (int) $_POST['uid'] : 0;
        $this->_params['enc'] = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
        $this->_params['type'] = isset($_POST['type']) ? (int) $_POST['type'] : 0;
        $this->_params['gameId'] = isset($_POST['gameId']) ? (int) $_POST['gameId'] : 0;
        $this->_params['status'] = isset($_POST['status']) ? (int) $_POST['status'] : VIDEO;

        if(!$this->_params['videoId'])
        {
            render_error_json('The Param Error!');
            exit;
        }
        
        if ($this->_params['type'] == 4 || $this->_params['type'] == 5)
        {
            if (!$this->_params['uid'] || !$this->_params['enc'])
            {
                render_error_json(['LoginStatus' => 0]);
                exit;
            }

            $authService = new UserAuthService();
            $authService->setCaller('api:' . __FILE__);
            $authService->setUid($this->_params['uid']);
            $authService->setEnc($this->_params['enc']);
            //校验encpass、用户 登陆状态
            if ($authService->checkLoginStatus() !== true)
            {
                //获取校验结果
                $result = $authService->getResult();
                //错误码
                $errorCode = $result['error_code'];
                //错误消息
                $errorMsg = $result['error_msg'];
                //假如是封禁的，可以获取禁时间
                $etime = isset($result['login_disable_etime']) ? $result['login_disable_etime'] : 0;
                write_log("notice|uid:{$this->_params['uid']};error_code:{$errorCode};error_msg:{$errorMsg};解禁时间:{$etime}|class:" . __CLASS__, 'auth_access');
                render_error_json(['LoginStatus' => 0]);
                exit;
            }
        } elseif ($this->_params['type'] == 2 || $this->_params['type'] == 3)
        {
            if (!$this->_params['uid'])
            {
                render_error_json('The Param Error!');
                exit;
            }
        } elseif ($this->_params['type'] == 1)
        {
            if (!$this->_params['gameId'])
            {
                render_error_json('The Param Error!');
                exit;
            }
        }
    }

    public function getVideoList()
    {
        $this->_init();
        $SildeService = new SildeService();
        $SildeService->setCaller('api:' . __FILE__);
        $SildeService->setParams($this->_params);
        return $SildeService->getSildeVideoList();
    }

    public function display()
    {
        $videoList = $this->getVideoList();

        if (!$videoList)
        {
            $code = self::ERROR_VIDEO_LIST;
            $msg = self::$errorMsg[$code];
            $log = "Notice | error_code:{$code};msg:{$msg};|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            render_json(['list' => []]);
            exit;
        }
        render_json(['list' => $videoList]);
    }

}

$obj = new getSildeVideoList();
$obj->display();
