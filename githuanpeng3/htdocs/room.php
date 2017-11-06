<?php
include '../include/init.php';

use service\room\RoomInitService;
use service\common\PcCommon;

class Room extends PcCommon
{

    private $_roomService;
    private $_luid;
    private $_roomid;


    /**
     * 初始房间信息
     * @return boolean
     */
    private function _init()
    {
        $this->_luid   = isset($_GET['luid'])   ? (int) $_GET['luid']   : 0;
        $this->_roomid = isset($_GET['roomid']) ? (int) $_GET['roomid'] : 0;


        if(!$this->_luid && !$this->_roomid)
        {

            return false;
        }
        $this->_roomService = new RoomInitService();
        $this->_roomService->setCaller('file:'.__FILE__.';line:'.__LINE__);
        $this->_roomService->setPlatform(RoomInitService::PLATFORM_PC);
        if($this->_luid)
        {
            $this->_roomService->setLuid($this->_luid);

        } else
        {
            $this->_roomService->setRoomid($this->_roomid);
        }

        return $this->_roomService->init();

    }

    public function display()
    {


        if(!$this->_init())
        {

            header("Location: /404.php");
            die;
        }

        $pcUserInfo = xss_clean($this->_roomService->getPcUserInfo());
        $pcRoomInfo = xss_clean($this->_roomService->getPcRoomInfo());
        $this->smarty->assign('pageUser', json_encode($pcUserInfo));
        $this->smarty->assign('room', json_encode($pcRoomInfo));
        $this->smarty->assign('headSign','room');
        $this->smarty->display('room.tpl');
    }
}

$room = new Room();
$room->display();