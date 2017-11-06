<?php

require __DIR__.'/../../include/init.php';
use service\anchor\helper\RepairAnchorRedisData;

class test
{

    public function testCheck($uid=1870)
    {
        $anchor = new RepairAnchorRedisData;
        $anchor->setUid($uid);
        $anchor->checkDataStatus();
        var_dump($anchor->rebuild());
    }
}

$obj = new test;
$obj->testCheck();