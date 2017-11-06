<?php

include '../../../include/init.php';

use service\live\LiveService;

class homePageGameList
{

    //获取游戏直播列表信息失败
    const ERROR_GAME_LIVE_LIST = 770001;

    public static $errorMsg = [
        self::ERROR_GAME_LIVE_LIST => '获取游戏直播列表信息失败',
    ];
    private $_type;
    private $_gameID;
    private $_page;
    private $_size;

    public function __construct()
    {
        $this->_init();
    }

    private function _init()
    {

        $type = isset($_POST['type']) ? (int) $_POST['type'] : 0;
        $this->_gameID = isset($_POST['gameID']) ? (int) $_POST['gameID'] : 0;
        $this->_size = isset($_POST['size']) ? (int) $_POST['size'] : 0;
        if(isset($_POST['lastId']))
        {
            $page = (int) $_POST['lastId'];
            $this->_page = ++$page; //兼容旧版本从0开始

        } elseif (isset ($_POST['page']))
        {
            $this->_page = $_POST['page'];
        } else {
            $this->_page = 1;
        }
        
        $this->_type = ++$type;
    }

    public function getHomePageGameList()
    {
        $service = new LiveService();
        $service->setLiveType($this->_type);
        $service->setPage($this->_page);
        $service->setSize($this->_size);
        if (!$this->_gameID)
        {
            $res = $service->getLiveListByType();
            $total = $service->getLiveTotal();
            return ['list' => $res,'total' => $total,'ref'=>'全部直播'];
        } else
        {
            $service->setGameId($this->_gameID);
            return $service->getLiveListByLiveTypeAndGameId();
        }
    }

    public function display()
    {
        $list = $this->getHomePageGameList();

        if (!$list)
        {
            $code = self::ERROR_GAME_LIVE_LIST;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            render_json([]);
        }
        render_json($list);
    }

}

$obj = new homePageGameList();
$obj->display();
