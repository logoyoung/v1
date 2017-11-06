<?php

include '../../../include/init.php';

use service\room\LiveRoomService;

class getSocketServer
{

    public function getServer()
    {
        $liveRoomService = new LiveRoomService();
        $liveRoomService->setCaller('file:'.__FILE__);
        return $liveRoomService->getSocketServer();
    }

    public function display()
    {
        $serverList = $this->getServer();
        render_json($serverList);
    }
}

$server = new getSocketServer();
$server->display();