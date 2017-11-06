<?php
namespace service\gift;
use service\common\AbstractService;
use service\gift\helper\GiftRedis;
use lib\room\Gift;
use service\event\EventManager;
/**
 * 礼物类服务
 */

class GiftService extends AbstractService
{


    private $_giftDb;
    private $_giftRedis;

    public function getGiftList()
    {
        $giftRedis   = $this->getGiftRedis();
        $redisStatus = $giftRedis->getRedis()->ping() ? true : false;
        if($redisStatus)
        {
            if($giftData = $giftRedis->getGiftData())
            {
                return $giftData;
            }

            $this->log("notice|获取礼品信息,redis没有礼品数据，穿透db获取");
        }

        $giftDb = $this->getGiftDb();
        $data   = $giftDb->getAllData();
        if($data === false)
        {
            $this->log("error|获取礼品信息，mysql异常，line:".__LINE__);
            return [];
        }

        if(!$data)
        {
            $this->log("error|获取礼品信息;mysql为空; line:".__LINE__);
            return [];
        }

        if($redisStatus)
        {
            $event   = new EventManager;
            $event->trigger($event::ACTION_GITF_UPDATE,[]);
            $event   = null;
        }

        return $data;
    }


    public function getGiftDb()
    {
        if(!$this->_giftDb)
        {
            $this->_giftDb = new Gift;
        }

        return $this->_giftDb;
    }

    public function getGiftRedis()
    {
        if(!$this->_giftRedis)
        {
            $this->_giftRedis = new GiftRedis;
        }

        return $this->_giftRedis;
    }

    public function log($msg)
    {
        write_log($msg,'gift_service_access');
    }
}