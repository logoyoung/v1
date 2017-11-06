<?php

include '../../../include/init.php';
use service\rank\LiveRoomRankService;

/**
 * 直播间排行榜
 * author yandong@6rooms.com
 * date 2016-02-02 09:33
 * copyright@6.cn version 0.0
 * update xuyong 2017-04-25
 */

class LiveRoomRanking
{
    private $_timeType;
    private $_luid;
    private $_size;
    //参数错误，luid不能为空
    const LIVE_ROOM_RANK_UID = -15211;
    public static $errorMsg = [
        self::LIVE_ROOM_RANK_UID => '参数错误，luid不能为空',
    ];

    private function _init()
    {

        $this->_timeType = isset($_POST['timeType']) ? (int) ($_POST['timeType']) : 1;
        $this->_luid     = isset($_POST['luid'])     ? (int) ($_POST['luid']) : '';
        $this->_size     = isset($_POST['size'])     ? (int) ($_POST['size']) : 10;

        if(!$this->_luid)
        {
            $code = self::LIVE_ROOM_RANK_UID;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code);
        }

    }

    public function display()
    {
        $this->_init();

        $roomRank = new LiveRoomRankService();
        $roomRank->setCaller('api:'.__FILE__);
        $roomRank->setLuid($this->_luid);
        $roomRank->setSize($this->_size);
        $roomRank->setTimeType($this->_timeType);
        $list   = $roomRank->getRankList();

        if(!$list)
        {
            $list = [];
        }

        render_json([ 'list' => $list]);

    }
}

$obj = new LiveRoomRanking();
$obj->display();