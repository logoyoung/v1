<?php
require __DIR__.'/../../include/init.php';
use service\zhima\CertService;

class test {


    public function h5()
    {
        $obj = new CertService;
        $obj->initH5CertUrl();
    }

    public function init($uid,$name,$certNo)
    {
        $obj = new CertService;
        $obj->setUid($uid);
        $obj->setCertName($name);
        $obj->setCertNo($certNo);
        $s = $obj->getZhimaInitBizno();
        print_r($s);
    }

    public function success($uid,$transactionId)
    {
        $obj = new CertService;
        $obj->setUid($uid);
        $obj->setTransactionId($transactionId);
        $s = $obj->zhimaCertSuccss();
        var_dump($s);
    }

    public function error($uid,$transactionId) {
        $obj = new CertService;
        $obj->setUid($uid);
        $obj->setTransactionId($transactionId);
        $obj->setErrorMsg(-9000);
        $s = $obj->zhimaCertError();
        var_dump($s);
    }


    public function query($biz_no)
    {
        $obj = new CertService;
        $obj->setBizNo($biz_no);
        $s = $obj->getAliZhimaCertStatus();
        print_r($s);
    }
}

$t    = new test;
//$t->h5();
//die;

 $t->query('ZM201709113000000494900753535882');
 $t->query('ZM201709113000000040400753560449');
 die;
$uid  = 47420;
$name = '胥勇';
$no   = '511525198807020435';

$t->init($uid,$name,$no);
die;
$transactionId = '1d70ea36603a708273a73ad38a3f046c';

//$t->success($uid,$transactionId);
$t->error($uid,$transactionId);