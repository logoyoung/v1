<?php
require __DIR__.'/../../include/init.php';
use service\room\RoomManagerService;

class test
{
    public function getRoomidByUids($uid = [1870])
    {
        $service = new RoomManagerService();
        $service->setUid($uid);
        $r = $service->getRoomidByUid();
        print_r($r);
    }

    public function getUidByRoomid($roomid = [100298,100342])
    {
        $service = new RoomManagerService();
        $service->setRoomid($roomid);
        $r = $service->getUidByRoomid();
        print_r($r);
    }

    public function getUidToRoomId($uid = 1870)
    {
        $service = new RoomManagerService();
        $service->setUid($uid);
        $r = $service->getUidToRoomId();
        var_dump($r);
    }
}

$obj = new test;
$obj->getUidToRoomId($uid = 1870);
die;
$obj->getRoomidByUids([12350,12170]);
$obj->getUidByRoomid([100402,100401]);