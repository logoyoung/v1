<?php
require __DIR__.'/../../include/init.php';
use service\follow\FollowManagerService;
use service\follow\FollowDataService;

class test
{

    public static function isFollow($uid=47420,$objUid=1870)
    {
        $followManagerService = new FollowManagerService;
        $s = $followManagerService->setUid($uid)->setObjectUid($objUid)->isFollow();
        var_dump($s);
    }


    public static function getFansTotalNum($uid=47420)
    {
        $followDataService = new FollowDataService;
        $s = $followDataService->setUid($uid)->getFansTotalNum();
        var_dump($s);
    }
}

test::isFollow($uid=47420,$objUid=1870);
test::getFansTotalNum($uid=47420);