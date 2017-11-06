<?php
require __DIR__.'/../../include/init.php';
use service\room\helper\RoomidRedis;

class test
{
    public function getRedis()
    {
        return new RoomidRedis();
    }

    public function testSetRoomidToUid($roomid,$uid)
    {
        $redis = $this->getRedis();
        $r = $redis->setRoomidToUid($roomid,$uid);
        var_dump($r);
    }

    public function testGetRoomidToUid($roomid)
    {
        $redis = $this->getRedis();
        $r = $redis->getRoomidByUid($roomid);
        var_dump($r);
    }

    public function setUidToRoomid($uid,$roomid)
    {
        $redis = $this->getRedis();
        $r = $redis->setUidToRoomid($uid,$roomid);
        var_dump($r);
    }

    public function getUidToRoomid($uid)
    {
        $redis = $this->getRedis();
        $r     = $redis->getUidToRoomid($uid);
        var_dump($r);
    }
}

$obj = new test;
$roomid = 10005;
$uid = 1870;
$obj->testSetRoomidToUid($roomid,$uid);
$obj->testGetRoomidToUid($roomid);

$obj->setUidToRoomid($uid,$roomid);
$obj->getUidToRoomid($uid);