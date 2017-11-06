<?php
require __DIR__.'/../../include/init.php';

use service\user\helper\UserRedis;

class test
{

    public function addDisableLogin($uid,$data)
    {
        $redis = new UserRedis();
        $redis->setUid($uid);
        $r = $redis->addUserDisableLogin($data);
        var_dump($r);
    }

    public function getDisableLoginStatusData($uid)
    {
        $redis = new UserRedis();
        $redis->setUid($uid);
        $r = $redis->getUserDisableLoginStatusData();
        var_dump($r);
    }

    public function deleteDisableLogin($uid)
    {
        $redis = new UserRedis();
        $redis->setUid($uid);
        $r = $redis->deleteUserDisableLogin();
        var_dump($r);
    }
}

$obj = new test;
$uid = 666;
//$obj->addDisableLogin($uid,['uid'=>666,'scope'=>1]);
$obj->getDisableLoginStatusData($uid);
$obj->deleteDisableLogin($uid);
$obj->getDisableLoginStatusData($uid);