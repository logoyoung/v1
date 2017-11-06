<?php

include '../../../include/init.php';

use service\user\UserDataService;
use service\user\HistoryService;
use service\user\UserAuthService;

class historyList
{
    //用户登录状态有误
    const ERROR_USER_LOGIN = 710001;
    //获取用户历史记录列表失败
    const ERROR_HISTORY_LIST = 710002;

    public static $errorMsg = [
        self::ERROR_USER_LOGIN => '用户登录状态有误',
        self::ERROR_HISTORY_LIST => '获取用户历史记录列表失败',
    ];

    private $_enc;
    private $_uid;
    private $_page;
    private $_size;

    public function __construct()
    {
        $this->_init();
    }

    private function _init()
    {

        $this->_enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
        $this->_uid = isset($_POST['uid']) ? (int) $_POST['uid'] : 0;
        $this->_page = isset($_POST['page']) ? (int) $_POST['page'] : 1;
        $this->_size = isset($_POST['size']) ? (int) $_POST['size'] : 5;
        if(!$this->_uid || !$this->_enc)
        {
            render_error_json (['LoginStatus' => 0]);
            exit;
        }
    }

    public function getHistoryList()
    {
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
            render_error_json (['LoginStatus' => 0]);
        }

        $historyService = new HistoryService();
        $historyService->setCaller('api:' . __FILE__);
        $historyService->setUid($this->_uid);
        $historyService->setPage($this->_page);
        $historyService->setSize($this->_size);

        return $historyService->getHistoryList();
    }

    public function display()
    {
        $data = $this->getHistoryList();
        if (!isset($data['total']) || $data['total'] <= 0)
        {
            $code   = self::ERROR_HISTORY_LIST;
            $msg    = self::$errorMsg[$code];
            $log    = "Notice | error_code:{$code};msg:{$msg};uid:{$this->_uid}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
        }
        render_json($data);
    }

}

$obj = new historyList();
$obj->display();
