<?php
require __DIR__.'/../../include/init.php';
use service\room\RoomEvent;
use service\gift\helper\GiftRedis;
use service\gift\GiftService;

class test
{
    public function updateRedis()
    {
        $event   = new RoomEvent;
        $event->trigger($event::ACTION_GITF_UPDATE);
        $event   = null;
    }

    public function getDataRedis()
    {
        $redis = new GiftRedis;
        print_r($redis->getGiftData());
    }

    public function getData()
    {
        $giftService = new \service\gift\GiftService();
        $giftData    = $giftService->getGiftList();
        print_r($giftData);
    }
}


$t = new test;
//$t->updateRedis();

$t->getData();