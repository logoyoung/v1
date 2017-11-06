<?php

include '../../../include/init.php';

use service\room\RoomInitService;

/**
 * App端直播间信息
 * date 2016-05-09 11:04
 * anthor yandong@6rooms.com
 * update xuyong 2017-5-17
 */

class getLiveRoomInfo
{

    const ERROR_CODE_LUID_EMPTY = -4013;

    private $_luid;
    private $_roomid;
    private $_roomService;
    private $_uid;
    private $_enc;

    public static $errorMsg = [
        self::ERROR_CODE_LUID_EMPTY => 'luid不能为空',
    ];

    public function display()
    {

        $this->_luid = isset($_POST['luid'])    ? (int) $_POST['luid']     : '';
        $this->_uid  = isset($_POST['uid'])     ? (int) $_POST['uid']      : 0;
        $this->_enc  = isset($_POST['encpass']) ? trim($_POST['encpass'])  : '';

        if(!$this->_luid)
        {
            $code = self::ERROR_CODE_LUID_EMPTY;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code);
        }

        $this->_roomService = new RoomInitService();
        $this->_roomService->setPlatform(RoomInitService::PLATFOMR_MOB);
        $this->_roomService->setLuid($this->_luid);
        $this->_roomService->setUuid($this->_uid);
        $this->_roomService->setEnc($this->_enc);
        $this->_roomService->init();
        $data = $this->_roomService->getMobRoomData();
        render_json($data);
    }
}

$obj = new getLiveRoomInfo();
$obj->display();