<?php
require __DIR__.'/../../include/init.php';
use service\room\helper\ManagerRedis;

class test
{
    public function getRedis()
    {
        return new ManagerRedis;
    }

    public function add($anchorUid,$managerUid)
    {
        $redis = $this->getRedis();
        var_dump($redis->add($anchorUid, $managerUid));
    }

    public function isExsits($anchorUid)
    {

    }

    public function getListByAnchorUid($anchorUid)
    {
        $redis = $this->getRedis();
        var_dump($redis->getListByAnchorUid($anchorUid));
    }

    public function deleteByManagerUid($anchorUid,$managerUid)
    {
        $redis = $this->getRedis();
        var_dump($redis->deleteByAnchorUidManagerUid($anchorUid, $managerUid));
    }

    public function isExistsByAnchorUidManagerUid($anchorUid, $managerUid)
    {
        $redis = $this->getRedis();
        var_dump($redis->isExistsByAnchorUidManagerUid($anchorUid, $managerUid));
    }

    public function getTotalNum($anchorUid)
    {
        $redis = $this->getRedis();
        var_dump($redis->getTotalNum($anchorUid));
    }
}

$t = new test;
$anchorUid = 888;
$managerUid = 111;
$t->add($anchorUid,$managerUid);
$managerUid = 222;
$t->add($anchorUid,$managerUid);
$t->getListByAnchorUid($anchorUid);
// $t->deleteByManagerUid($anchorUid,111);
// $t->getListByAnchorUid($anchorUid);
$t->isExistsByAnchorUidManagerUid($anchorUid, 222);
$t->getTotalNum($anchorUid);