<?php

include '../include/init.php';

use service\common\PcCommon;
use service\home\IndexService;
use service\cookie\CookieService;
/**
 * 首页
 * @author longgang@6.cn
 * @date 2017-04-14 11:10:23
 * @copyright (c) 2017, 6.cn
 * @version 1.0.1
 */
class index extends PcCommon
{

    private $_param;

    //推荐游戏ID

    public function __construct()
    {
        parent::__construct();
        $this->_init();
    }

    private function _init()
    {
        $this->_param['uid'] = CookieService::getUid() ? (int) CookieService::getUid() : 0;
    }

    private function _getData()
    {
        $service = $this->getService();
        $service->setUid($this->_param['uid']);

        return $service->getAll();
    }

    public function getParam()
    {
        return $this->_param;
    }

    public function display()
    {
        $data = $this->_getData();
        if (!$data)
        {
            // do log
        }

        $result = ['status' => 0, 'content' => $data];
        
        $this->smarty->assign('isLogin', '1');
        $this->smarty->assign('content', xss_clean($result));
        $this->smarty->assign('headSign', 'index');

        $this->smarty->display('index.tpl');
    }

    public function getService()
    {
        return new IndexService();
    }

}

$index = new index();
$index->display();
