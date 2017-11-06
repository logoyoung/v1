<?php
require __DIR__.'/../../include/init.php';
use service\room\helper\RepairRoomidRedisData;

class test
{

    public function checkRoomidByUid($uid=1870)
    {
        $service = new RepairRoomidRedisData;
        $service->setUid($uid);
        var_dump($service->checkDataStatus());
    }
}

$obj = new test;
$obj->checkRoomidByUid($uid=1870);