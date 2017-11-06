<?php
require __DIR__.'/../../include/init.php';
use service\room\RoomEvent;

class test
{

    public function testAnchorCheckSucc()
    {
        $param   = ['uid' => '1870'];
        $action  = roomEvent::ACTION_ANCHOR_CHECK_SUCC;
        $event   = new RoomEvent;
        //$event->trigger($action,$param);
        //$param   = ['roomid' => '100005'];
        $event->trigger($action,$param);
        $event   = null;

    }
}

$obj = new test;
$obj->testAnchorCheckSucc();