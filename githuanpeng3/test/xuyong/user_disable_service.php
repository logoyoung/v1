<?php
require __DIR__.'/../../include/init.php';
use service\user\UserDisableStatusService;
use service\user\helper\UserRedis;

class test
{
    public function addDisable($uid, $type,$scope, $etime)
    {
        try {
            $service = new UserDisableStatusService();
            $service->setUid($uid);
            $service->setType($type);
            $service->setScope($scope);
            $service->setEtime($etime);
            $r = $service->addDisable();
            var_dump($r);

        } catch (Exception $e)
        {
            echo $e->getCode(),"\n",$e->getMessage();
        }

    }

    public function deleteDisable($uid, $type,$scope)
    {
        try {
            $service = new UserDisableStatusService();
            $service->setUid($uid);
            $service->setType($type);
            $service->setScope($scope);
            $r = $service->deleteDisable();
            var_dump($r);

        } catch (Exception $e)
        {
            echo $e->getCode(),"\n",$e->getMessage();
        }

    }

    public function getDisableLoginStatusData($uid,$db=false)
    {
        try {
            $service = new UserDisableStatusService();
            $service->setUid($uid);
            $service->setFromDb($db);
            $r = $service->getLoginDisableStatus();
            var_dump($r);

        } catch (Exception $e)
        {
            echo $e->getCode(),"\n",$e->getMessage();
        }
    }

    public function addSliencedRedis($uid='',array $data=[])
    {
        $uid   = $uid ?:47420;
        $data  = $data? $data:['uid' => $uid,'scope' => 222,'etime' => 11111];
        $redis = new UserRedis();
        $redis->setUid($uid);
        $r = $redis->addSilenced($data);
        var_dump($r);


        $r = $redis->getSilencedByAnchorUid(222);
        var_dump($r);
    }

    public function seliencedDeleteRedis($uid)
    {
        $uid   = $uid ?:47420;
        $redis = new UserRedis();
        $redis->setUid($uid);
        $r = $redis->getSilencedByAnchorUid(null);
         var_dump($r);
        $r = $redis->deleteSilencedByAnchorUid(222);
        var_dump($r);

        $r = $redis->getSilencedByAnchorUid(null);
         var_dump($r);
    }

    public function addSlienced($uid='')
    {
        $uid   = $uid ?:47420;
        $data  = isset($data) ? $data : ['uid' => $uid,'scope' => 1870,'type' => 20,'etime' => 11111];
        try {
            $service = new UserDisableStatusService();
            $service->setUid($data['uid']);
            $service->setType($data['type']);
            $service->setScope($data['scope']);
            $service->setEtime($data['etime']);
            $r = $service->addDisable();
            var_dump($r);

        } catch (Exception $e)
        {
            echo $e->getCode(),"\n",$e->getMessage();
        }
        $redis = new UserRedis();
        $redis->setUid($uid);
        $r = $redis->getSilencedByAnchorUid(null);
        var_dump($r);
    }

    public function deleteSilenced($uid = '')
    {
        $uid   = $uid ?:47420;
        $data  = isset($data) ? $data : ['uid' => $uid,'scope' => 1870,'type' => 20,'etime' => 11111];
        try {
            $service = new UserDisableStatusService();
            $service->setUid($data['uid']);
            $service->setType($data['type']);
            $service->setScope($data['scope']);
            $r = $service->deleteDisable();
            var_dump($r);

        } catch (Exception $e)
        {
            echo $e->getCode(),"\n",$e->getMessage();
        }

        $redis = new UserRedis();
        $redis->setUid($uid);
        $r = $redis->getSilencedByAnchorUid(null);
        var_dump($r);
    }

    public function getSilencedStatus($uid='')
    {
        $uid   = $uid ?:47420;
        $data  = isset($data) ? $data : ['uid' => $uid,'scope' => 1870,'type' => 20,'etime' => 11111];
        try {
            $service = new UserDisableStatusService();
            $service->setUid($data['uid']);
            $service->setType($data['type']);
            $service->setScope($data['scope']);
           // $service->setFromDb(true);
            $r = $service->getSliencedStatus();
            var_dump($r);
            $redis = new UserRedis();
            $redis->setUid($uid);
            $r = $redis->getSilencedByAnchorUid(null);
            var_dump($r);

        } catch (Exception $e)
        {
            echo $e->getCode(),"\n",$e->getMessage();
        }
    }
}

$obj   = new test;
$uid   = 47420;
//$uid = 133;
$type  = 10;
$scope = 1;
$etime = 100;
//$obj->addDisable($uid, $type,$scope, $etime);
//$obj->deleteDisable($uid, $type,$scope);
// $obj->getDisableLoginStatusData($uid);
// $obj->getDisableLoginStatusData($uid,true);
//
// $obj->addSliencedRedis();
//$obj->seliencedDeleteRedis();
//
 //$obj->addSlienced();

//$obj->deleteSilenced();
//
$obj->getSilencedStatus();