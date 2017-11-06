<?php

include '../../../include/init.php';
use service\rank\RankService;

class HomeRanking
{

    private $_param;
    private $_rankService;
    public  $result;

    public function __construct()
    {
        $this->_init();
    }

    private function _init()
    {
        //默认按主播
        $this->_param['userType']  = isset($_POST['userType']) ? (int) ($_POST['userType']) : RankService::getDefaultUserType();
        //默认按日周月
        $this->_param['timeType']  = isset($_POST['timeType']) ? (int) ($_POST['timeType']) : RankService::getDefaultTimeType();
        //默认按收入
        $this->_param['orderType'] = isset($_POST['orderType']) ? (int) ($_POST['orderType']) : RankService::getDefaultOrderType();
        //默认pc端输出数据10条
        $this->_param['size']      = isset($_POST['size']) ? (int) ($_POST['size']) : RankService::DEFAULT_PC_NUM;
        $this->result['userType']  = $this->_param['userType'];
    }

    private function _getData()
    {
        $service = $this->getService();
        $service->setUserType($this->_param['userType']);
        $service->setTimeType($this->_param['timeType']);
        $service->setOrderType($this->_param['orderType']);
        $service->setSize($this->_param['size']);

        return $service->getList();
    }

    public function getParam()
    {
        return $this->_param;
    }

    public function display()
    {
        $data = $this->_getData();
        if(!$data) {
            // do log
        }

        render_json([ 'list' => $data, 'userType' => $this->_param['userType'] ]);
    }

    public function getService()
    {
        return new RankService();
    }

}

$rank = new HomeRanking();
$rank->display();

