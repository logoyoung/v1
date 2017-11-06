<?php
require __DIR__.'/../../bootstrap/i.php';
use service\room\helper\PromotionsRoomRedis;
use service\room\RoomManagerService;
use service\event\EventManager;

/**
 * 重设直播间活动信息 redis缓存
 */
class RebuildRedisCache
{

    public function check()
    {
        $dbData    = RoomManagerService::getRoomPromotionsFromDb();
        $redisData = RoomManagerService::getRoomPromotions();
        unset($dbData['utime'],$redisData['utime']);
        ksort($dbData);
        ksort($redisData);
        $dbData    = array_values_to_string($dbData);
        $redisData = array_values_to_string($redisData);
        echo 'dbData:';
        print_r($dbData);
        echo 'redisData:';
        print_r($redisData);
        return ($dbData == $redisData) ? true : false;
    }

    public function run()
    {

        if($this->check())
        {
            die("db 与redis 数据一致\n");
        }

        echo "数据不一致,重设redis缓存 \n";
        $event  = new EventManager;
        $action = $event::ACTION_ROOM_PROMOTION_UPDATE;
        $event->trigger($action,[]);
        $event  = null;
        if($this->check())
        {
            die("数据一致,重设redis缓存成功 \n");
        }

        die("数据不一致,重设redis缓存失败 \n");
    }

}

$obj = new RebuildRedisCache;
$obj->run();