<?php

include '../../../include/init.php';

use service\home\IndexService;

class getInformation
{

    //获取资讯列表失败
    const ERROR_INFORMATION_LIST = 730001;
    
    public static $errorMsg = [
        self::ERROR_INFORMATION_LIST => '获取资讯列表失败', 
    ];

    private $_type;
    private $_client;

    public function __construct()
    {
        $this->_init();
    }

    private function _init()
    {
        $this->_client = isset($_POST['client']) ? (int) $_POST['client'] : 0;
        $this->_type = isset($_POST['type']) ? (int) $_POST['type'] : 0;
    }

    public function getInformationList()
    {
        $service = new IndexService();
        $service->setInformationType($this->_type);
        $service->setClient($this->_client);
        return $service->getInformationList();
    }

    public function display()
    {
        $list = $this->getInformationList();
        if(!$list)
        {
            $code   = self::ERROR_INFORMATION_LIST;
            $msg    = self::$errorMsg[$code];
            $log    = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            render_json([]); 
        }
        render_json($list);
    }

}

$obj = new getInformation();
$obj->display();
