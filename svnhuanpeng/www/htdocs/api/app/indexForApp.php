<?php

include '../../../include/init.php';

use service\home\IndexForAppService;

class indexForApp
{
    //获取直播信息列表异常
    const ERROR_LIVE_LIST = -710001;
    
    public static $errorMsg = [
        self::ERROR_LIVE_LIST => '获取直播信息列表异常',
    ];

    private $_uid;
    private $_enc;


    private function _init()
    {
        $this->_uid = isset($_POST['uid']) ? (int) $_POST['uid'] : 0;
        $this->_enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
        $this->_a   = isset($_POST['a']) ? (int) $_POST['a'] : 0;
        $this->_black = isset($_POST['black']) ? trim($_POST['black']) : '';
    }

    public function getLiveList()
    {
        $this->_init();
        $service = new IndexForAppService();
        $service->setCaller('api:' . __FILE__);
        $service->setA($this->_a);
        $service->setBlack($this->_black);
        return $service->getLiveList();
    }
    
    public function display()
    {
        
        $list = $this->getLiveList();
        if ($list)
        {
            render_json(['list' => $list]);
        } else
        {
            $code   = self::ERROR_LIVE_LIST;
            $msg    = self::$errorMsg[$code];
            $log    = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            render_json(['list' => []]);            
        } 

    }

}

$obj = new indexForApp();
$obj->display();
