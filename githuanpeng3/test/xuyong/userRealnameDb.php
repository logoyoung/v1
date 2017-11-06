<?php
require __DIR__.'/../../include/init.php';
use lib\user\UserRealName;
class test
{
    public function testAdd($uid,$name,$papersid,$status,$reason,$ctime,$paperstype = 0,$adminid=888)
    {
        $db = new UserRealName;
        var_dump($db->add($uid,$name,$papersid,$status,$reason,$ctime,$paperstype,$adminid));
    }

    public function getDataByPapersid($id,$s,$f)
    {
        $db = new UserRealName;
        print_r($db->getDataByPapersid($id,$s,$f));
    }
}

$obj = new test;
//$obj->testAdd($uid='44441',$name='cat',$papersid='150401199901014031',$status='101',$reason='芝麻认证',$ctime = date('Y-m-d H:i:s'),$paperstype = 0,$adminid=888);
//
$obj->getDataByPapersid([620522199108153511,2109211982090608531],101,['uid']);