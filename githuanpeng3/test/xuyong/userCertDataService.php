<?php
require __DIR__.'/../../include/init.php';
use service\user\UserCertDataService;

class test
{

    public function getRealNameCertStatus($uid)
    {
        $service = new UserCertDataService;
        $service->setUid($uid);
        $s = $service->getRealNameCertStatus();
        var_dump($s);
    }

    public function getRealNameData($uid)
    {
        $service = new UserCertDataService;
        $service->setUid($uid);
        $s = $service->getRealNameData();
        var_dump($s);
    }
}

$obj = new test;
$uid = 47420;
//$uid = 1870;
$obj->getRealNameCertStatus($uid);
$obj->getRealNameData($uid);