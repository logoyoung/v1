<?php
require __DIR__.'/../../include/init.php';
use service\anchor\AnchorGetDataService;
use service\anchor\helper\AnchorRedis;

class test
{
    public function getData($uid = [1870])
    {
        $service = new AnchorGetDataService();
        $service->setUid($uid);
        $r = $service->getAnchorData();
        print_r($r);
        die;
        $service->setFromDb(true);
        $r = $service->getAnchorData();
        print_r($r);
    }

    public function isExist($uid=129267)
    {
        $service = new AnchorGetDataService();
        $service->setUid($uid);
        var_dump($service->isExist());
        $service->setFromDb(true);
        var_dump($service->isExist());
    }

    public function getCertStatus($uid=129267)
    {
        $service = new AnchorGetDataService();
        $service->setUid($uid);
        var_dump($service->getCertStatus());
        $service->setFromDb(true);
        var_dump($service->getCertStatus());
    }

    public function getCertStatusRedis($uid=16595)
    {
        $redis = new AnchorRedis;
        var_dump($redis->getCertStatus($uid));
    }
}

$obj = new test;
//$obj->getData([129267,129751,129748,1860,1870]);

// $obj->getData(129267);
// $obj->isExist($uid=1870);
$obj->getCertStatus($uid=16595);
$obj->getCertStatusRedis($uid=16595);