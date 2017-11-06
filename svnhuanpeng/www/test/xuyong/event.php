<?php
require __DIR__.'/../../include/init.php';

use service\event\EventManager;

class test
{

    public function testUser($uid='')
    {
        $param  = [ 'uid' => $uid?:69456,];

        $action = EventManager::ACTION_USER_RESET_CACHE;
        $event  = new EventManager;
        $s = $event->trigger($action,$param);
        var_dump($s);
    }
}

$obj = new test;
$obj->testUser(47420);