<?php
require __DIR__.'/../../include/init.php';
use lib\user\ZhimaCert;

class test {

    public function add($uid,$type,$certName,$certNo,$transactionId,$bizCode,$bizNo,$status,$bizEtime,$utime) {
        $db = new ZhimaCert;
        $s = $db->add($uid,$type,$certName,$certNo,$transactionId,$bizCode,$bizNo,$status,$bizEtime,$utime);
        var_dump($s);
    }

    public function update($transactionId,$uid,$status)
    {
         $db = new ZhimaCert;
         $s  = $db->updateStatusByTidUid($transactionId,$uid,$status);
         var_dump($s);
    }

    public function getZhimaCertByYidUid($transactionId, $uid, array $fields = [])
    {
        $db = new ZhimaCert;
        $s  = $db->getZhimaCertByYidUid($transactionId, $uid,$fields);
        print_r($s);
    }
}


function add()
{
    $obj = new test;
    $uid=111;
    $type=1;
    $certName='cat';
    $certNo=time();
    $transactionId=md5(time());
    $bizCode=1;
    $bizNo=md5(122);
    $status=1;
    $bizEtime=date('Y-m-d H:i:s');
    $utime=date('Y-m-d H:i:s');
    $obj->add($uid,$type,$certName,$certNo,$transactionId,$bizCode,$bizNo,$status,$bizEtime,$utime);
}

function update()
{
    $obj = new test;
    $uid=111;
    $transactionId='1d70ea36603a708273a73ad38a3f046c';
    $status=3;
    $obj->update($transactionId,$uid,$status);
}

function getData()
{
    $obj = new test;
    $uid=1999998;
    $transactionId='1d70ea36603a708273a73ad38a3f046c';

    $obj->getZhimaCertByYidUid($transactionId, $uid);
}

getData();