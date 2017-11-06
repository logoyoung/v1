<?php

include '../include/init.php';

use service\common\PcCommon;
use service\live\LiveService;

/**
 * 直播大厅
 * @author longgang@6.cn
 * @date 2017-04-14 11:21:46
 * @copyright (c) 2017, 6.cn
 * @version 1.0.1
 */

class LiveHall extends PcCommon
{
    
    private $_param;

    public function __construct()
    {
        parent::__construct();
        $this->_init();
    }

    private function _init() 
    {
        //默认页数
        $this->_param['page'] = LiveService::DEFAULT_PAGE;
        //默认每页数量
        $this->_param['size'] = LiveService::DEFAULT_PC_NUM;
    }

    private function _getData()
    {
        $service = $this->getService();
        $service->setPage($this->_param['page']);
        $service->setSize($this->_param['size']);
        
        return $service->getLiveList();
    }

    public function display() {
        
        $data = $this->_getData();
        if (!$data)
        {
            //TODO log...
        }
        
        $result = ['status' => 0,'content' => $data];
        
        $this->smarty->assign('isLogin','1');
        $this->smarty->assign('content', xss_clean($result));
        $this->smarty->assign('headSign','LiveHall');

        $this->smarty->display('LiveHall.tpl');
    }
    
    public function getService() 
    {
        return new LiveService();
    }
}

$liveHall = new LiveHall();
$liveHall ->display();