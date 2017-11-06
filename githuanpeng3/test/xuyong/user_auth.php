<?php
require __DIR__.'/../../include/init.php';

use service\user\UserAuthService;

class test
{
    public function checkUserDisableLogin($uid)
    {
        try {
             $service = new UserAuthService();
             $service->setUid($uid);
             $s = $service->checkDisableLoginStatus();
             $r = $service->getResult();
             var_dump($s);
             var_dump($r);
        } catch (Exception $e)
        {
            echo $e->getCode(),"\n",$e->getMessage(),"\n";
        }
    }

    public function checkLoginStatus($uid='',$enc='')
    {
        try {
            $uid = $uid?:47420;
            $enc = $enc?:'63ee451939ed580ef3c4b6f0109d1fd0';
            $service = new UserAuthService();
            $service->setUid($uid);
            $service->setEnc($enc);
            $s = $service->checkLoginStatus();
            $r = $service->getResult();
            var_dump($s);
            var_dump($r);
        } catch (Exception $e)
        {
             echo $e->getCode(),"\n",$e->getMessage(),"\n";
        }

    }

    public function checkSilenced($uid = 47420,$anchorUid = 1860)
    {
        $auth = new UserAuthService();
        $auth->setUid($uid);
        $auth->setAnchorUid($anchorUid);
        $r = $auth->checkSilencedStatus();
        var_dump($r);
        var_dump($auth->getResult());
    }

    public function checkIsDueAnchor($uid = 1860)
    {
        $auth = new UserAuthService();
        $auth->setUid($uid);
        $r    = $auth->checkIsDueAnchor();
        var_dump($r);
    }

    public function checkAnchorCertStatus($uid = 1860)
    {
        $auth = new UserAuthService();
        $auth->setUid($uid);
        $r    = $auth->checkAnchorCertStatus();
        var_dump($r);
    }

    public function checkIsAnchor($uid = 1860)
    {
        $auth = new UserAuthService();
        $auth->setUid($uid);
        $r    = $auth->checkIsAnchor();
        var_dump($r);
    }
}

$obj = new test;
// $obj->checkAnchorCertStatus($uid = 129691);
// $obj->checkIsAnchor($uid = 129691);
var_dump($obj->checkIsDueAnchor(1870));