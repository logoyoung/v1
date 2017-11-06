<?php
require __DIR__.'/../../include/init.php';
use lib\room\Roomid;

class test
{


    public function getDb()
    {
        return new Roomid();
    }

    public function getTotalNum()
    {
        $db = $this->getDb();
        var_dump($db->getTotalNum());
    }

    public function getUidByRoomid($roomid = '100005')
    {
        $db = $this->getDb();
        $uid = $db->getUidByRoomid($roomid);
        var_dump($uid);
        return $uid;
    }

    public function getRoomidByUid($uid)
    {
        $db = $this->getDb();
        $roomid = $db->getRoomidByUid($uid);
        var_dump($roomid);
        return $roomid;
    }
}

$obj    = new test;
$obj->getTotalNum();
echo "\n";
echo $roomid = '100005';
echo "\n";
$uid    = $obj->getUidByRoomid($roomid);
echo "\n";
if($roomid == $obj->getRoomidByUid($uid))
{
    echo "success \n";
} else
{
    echo "error \n";
}
