<?php
require __DIR__.'/../../include/init.php';
use service\user\UserCertService;

class test
{

    public function cert($uid,$name,$idNo,$transactionId,$s)
    {
        $obj = new UserCertService;
        $obj->setUid($uid);
        $obj->setCertName($name);
        $obj->setCertno($idNo);
        $obj->setTransactionId($transactionId);
        $obj->setZhimaStatus($s);
        $s = $obj->zhimaCertSuccss();
        var_dump($s);
    }
}

$obj = new test;
$obj->cert(1999998,'cat','1504770786','1d70ea36603a708273a73ad38a3f046c',8);
