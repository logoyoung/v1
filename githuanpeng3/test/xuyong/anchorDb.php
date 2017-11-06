<?php
require __DIR__.'/../../include/init.php';
use lib\anchor\Anchor;

class test
{
    public function getDb()
    {
        return new Anchor();
    }

    public function getAnchorData($anchor = [1870,199])
    {
        $db = $this->getDb();
        $r = $db->getAnchorDataByUid($anchor);
        print_r($r);
    }

    public function add($uid,$level,$certStatus,$rate,$utime)
    {
        $db = $this->getDb();
        $s = $db->add($uid,$level,$certStatus,$rate,$utime);
        var_dump($s);
    }
}

$obj = new test;
$uid=111;
$obj->add($uid,$level=1,$certStatus=101,$rate=12,$utime=date('Y-m-d H:i:s'));
$obj->getAnchorData($uid);