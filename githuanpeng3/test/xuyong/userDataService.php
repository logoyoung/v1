<?php
require __DIR__.'/../../include/init.php';
use service\user\UserDataService;

class test {

    public function getUserInfo($uid=47420)
    {

    }
    public function existByPhone($phone,$uid=47420) {
        $userDataService = new \service\user\UserDataService;
        $userDataService ->setPhone($phone);
        var_dump($userDataService ->isExist());

        $userDataService->setUid($uid);
        var_dump($userDataService->isExist());
    }
}

$obj = new UserDataService;
$obj->setUid($uid=47420);
$obj->setUserInfoDetail($obj::USER_STATIC_ACTICE_BASE);
$data = $obj->getUserInfo();
print_r($data);

die;
$obj->setPapersid('410224198912044618');
print_r($obj->getUserCertByPapersid());
die;
$phone = 13269691568;
$phone = 11111111;
$phone = 13211111111;
$obj = new test;
$obj->existByPhone($phone);