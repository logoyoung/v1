<?php

namespace service\common;

use service\home\HeaderService;
use service\cookie\CookieService;

/**
 * 网站头部
 * @author longgang@6.cn
 * @date 2017-04-13 17:19:25
 * @copyright (c) 2017, 6.cn
 * @version 1.0.1
 */
class PcHeader
{

    //获取头部信息失败
    const ERROR_HEADER_DATA = 710001;

    private $_uid;
    private $_enc;
    public static $errorMsg = [
        self::ERROR_HEADER_DATA => '获取头部信息失败',
    ];

    public function __construct()
    {
        $this->_init();
    }

    private function _init()
    {
        $this->_uid = CookieService::getUid() ? (int) CookieService::getUid() : 0;
        $this->_enc = CookieService::getEnc() ? trim(CookieService::getEnc()) : '';
    }

    private function _getData()
    {
        $service = $this->getService();
        $service->setUid($this->_uid);
        $service->setEnc($this->_enc);

        return $service->getAll();
    }

    public function getHeaderData()
    {
        $data = $this->_getData(); 
    
        if (!$data)
        {
            $code   = self::ERROR_HEADER_DATA;
            $msg    = self::$errorMsg[$code];
            $log    = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            return [];
        }

        $result = ['status' => 0, 'content' => ['list' => $data]];

        return json_encode($result);
    }

    public function getService()
    {
        return new HeaderService();
    }

}
