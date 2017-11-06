<?php

include '../../../include/init.php';
/**
 * 获取录像列表
 * @author guanlong
 * @copyright 6.cn
 * @version 1.0.3
 * revise 2015-12-16 by yandong
 */
use service\live\VideoService;

class VideoList
{

    private $_uid;
    private $_gametid;
    private $_gameid;
    private $_size;
    private $_page;
    const ERROR_CODE_UID = -13001;
    public static $errorMsg = [
        self::ERROR_CODE_UID => '无效的参数，luid不能为空',
    ];

    private function _init()
    {
        $this->_gametid = isset($_POST['gameTypeID']) ? (int) ($_POST['gameTypeID']) : '';
        $this->_gameid  = isset($_POST['gameID'])     ? (int) ($_POST['gameID']) : '';
        $this->_uid     = isset($_POST['luid'])       ? (int) ($_POST['luid']) : '';
        if(!$this->_uid)
        {
            $code = self::ERROR_CODE_UID;
            $msg  = self::$errorMsg[$code];
            render_error_json($msg,$code);
        }
        $this->_size    = isset($_POST['size'])       ? (int) ($_POST['size']) : 9;
        $this->_page    = isset($_POST['page'])       ? (int) ($_POST['page']) : 1;

    }

    public function display()
    {
        $this->_init();
        $video = new VideoService();
        $video->setCaller('api:'.__FILE__);
        $video->setUid($this->_uid);
        $video->setSize($this->_size);
        $video->setPage($this->_page);
        $list = $video->getVideoList();
        if(!$list)
        {
            $list = ['list' => '', 'total' => '0'];
        } else {
            $list = ['list' => $list,'total' => $video->getVideoListTotalNum()];
        }

        render_json($list);
    }

}


$obj = new VideoList();
$obj->display();