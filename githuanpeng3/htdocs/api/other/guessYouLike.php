<?php

include '../../../include/init.php';

use service\home\IndexService;

class guessYouLike
{
    //获取猜你喜欢列表失败
    const ERROR_GUESS_YOU_LIKE_LIST = 720001;
    
    public static $errorMsg = [
        self::ERROR_GUESS_YOU_LIKE_LIST => '获取猜你喜欢列表失败',
    ];


    private $_uid;
    private $_size;

    public function __construct()
    {
        $this->_init();
    }

    private function _init()
    {

        $this->_uid = isset($_POST['uid']) ? (int) $_POST['uid'] : 0;
        $this->_size = isset($_POST['size']) ? (int) $_POST['size'] : 0;
    }

    public function getGuessYouLikeList()
    {
        $service = new IndexService();
        $service->setUid($this->_uid);
        $service->setSize($this->_size);
        
        return $service->getGuessYouLike();
    }

    public function display()
    {
        $list = $this->getGuessYouLikeList();
        if (isset($list['list']))
        {
            render_json($list);
        } else
        {
            $code   = self::ERROR_GUESS_YOU_LIKE_LIST;
            $msg    = self::$errorMsg[$code];
            $log    = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            render_json([]);            
        } 

    }

}

$obj = new guessYouLike();
$obj->display();
