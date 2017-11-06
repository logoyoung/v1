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
}

$obj = new test;
$obj->getAnchorData();