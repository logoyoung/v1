<?php

include '../../../include/init.php';

use service\home\IndexForAppService;

class getCarousel
{

    //获取轮播信息列表异常
    const ERROR_CAROUSEL_LIST = -720001;

    public static $errorMsg = [
        self::ERROR_CAROUSEL_LIST => '获取轮播信息列表异常',
    ];
    private $_client;

    private function _init()
    {

        $this->_client = isset($_POST['client']) ? (int) $_POST['client'] : 1;
    }

    public function getCarouselList()
    {
        $this->_init();
        $service = new IndexForAppService();
        $service->setCaller('api:' . __FILE__);
        $service->setClient($this->_client);
        return $service->getCarouselList();
    }

    public function display()
    {

        $list = $this->getCarouselList();
        if ($list)
        {
            render_json(['list' => $list]);
        } else
        {
            $code = self::ERROR_CAROUSEL_LIST;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            render_json(['list' => []]);
        }
    }

}

$obj = new getCarousel();
$obj->display();
