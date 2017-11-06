<?php
require __DIR__.'/../../bootstrap/i.php';
use service\gift\GiftService;
use service\event\EventManager;
/**
 * 重设gift redis 缓存
 */
class RebuildGiftRedisCache
{

    public function check()
    {

        $giftService = new GiftService;
        $dbData      = $giftService->getGiftDb()->getAllData();
        $redisData   = $giftService->getGiftList();
        $dbData      = array_values_to_string($dbData);
        $redisData   = array_values_to_string($redisData);
        ksort($dbData);
        ksort($redisData);
        print_r($dbData);
        print_r($redisData);

        return $dbData == $redisData ? true : false;
    }

    public function run()
    {
        if($this->check())
        {
            die("db 与redis 数据一致 \n");
        }

        $event   = new EventManager;
        $event->trigger($event::ACTION_GITF_UPDATE,[]);
        $event   = null;

        if($this->check())
        {
            die("重设gift redis 缓存成功 \n");
        }

        die("重设gift redis 缓存失败 \n");
    }
}

$obj = new RebuildGiftRedisCache;
$obj->run();