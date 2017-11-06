<?php
require __DIR__.'/../../include/init.php';
use service\anchor\AnchorEvent;
use service\event\EventManager;

class test
{
    public function testEvent()
    {
        $param   = ['uid' => '1870'];
        $action  = EventManager::ACTION_ANCHOR_CHECK_SUCC;
        //$event   = new AnchorEvent;

        $event   = new EventManager;
        $event->trigger($action,$param);
    }

    public function t()
    {
        //主播数据变化事件
        $event   = new \service\event\EventManager();
        $event->trigger($event::ACTION_ANCHOR_RESET_CACHE,['uid' => 1870]);
        $event   = null;
    }
}

$obj = new test;
$obj->t();