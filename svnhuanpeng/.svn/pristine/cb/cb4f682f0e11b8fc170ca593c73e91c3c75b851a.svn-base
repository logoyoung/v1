<?php

include '../../../../include/init.php';

use service\home\HeaderService;

class getUserDetail
{
    
    //用户登录状态有误
    const ERROR_USER_LOGIN = 710001;
    //获取用户信息失败
    const ERROR_USER_INFO = 710002;
    
    public static $errorMsg = [
        self::ERROR_USER_LOGIN => '用户登录状态有误',
        self::ERROR_USER_INFO => '获取用户信息失败',
    ];
    
    private $_enc;
    private $_uid;
    
    public function __construct()
    {
        $this->_init();
    }
    
    private function _init()
    {
        
        $this->_enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
        $this->_uid = isset($_POST['uid']) ? (int) $_POST['uid'] : 0;
    }
    
    private function _getData()
    {
        $service  = $this->getService();
        $service->setUid($this->_uid);
        $service->setEnc($this->_enc);
        
        if(!$service->checkLogin())
        {
            $code   = self::ERROR_USER_LOGIN;
            $msg    = self::$errorMsg[$code];
            $log    = "error_code:{$code};msg:{$msg};uid:{$this->_uid}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            succ (['LoginStatus' => 0]);
            exit;
        }
        return $service->getUserInfo();
    }

    public function display()
    {
        $userInfo = $this->_getData();
        if (!$userInfo)
        {
            $code   = self::ERROR_USER_INFO;
            $msg    = self::$errorMsg[$code];
            $log    = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            succ([]);
            exit;
        }
        
        succ($userInfo);
    }
    
    public function getService()
    {
        return new HeaderService();
    }
}

$obj = new getUserDetail();
$obj->display();