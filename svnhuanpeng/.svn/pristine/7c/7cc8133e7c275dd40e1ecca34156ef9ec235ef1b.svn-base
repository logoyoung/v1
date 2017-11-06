<?php
namespace service\gift;
use service\common\AbstractService;
use lib\Gift;

/**
 * 礼物类服务
 */

class GiftService extends AbstractService
{

    //从数据库获取积分信息异常
    const ERROR_GIFTINFO  = 43001;

    public $errorMsg = [
        self::ERROR_GIFTINFO => '从数据库获取积分信息异常',
    ];

    public function getGiftList()
    {
        $giftInfo = Gift::getGiftList($this->getGiftDb());
        if($giftInfo === false)
        {
            $code = self::ERROR_GIFTINFO;
            $msg  = self::$errorMsg[$code];
            $log  = "error |error_code:{$code};msg:{$msg}|class:".__CLASS__.';func'.__FUNCTION__.';line:'.__LINE__.$this->getCaller();
            write_log($log);

            return false;
        }

        return $giftInfo;
    }


    public function getGiftDb()
    {
        return Gift::getDB();
    }

}