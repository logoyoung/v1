<?php
include '../include/init.php';

use service\rank\RankService;
use service\common\PcCommon;

class Rank extends PcCommon
{

    /**
     * 获取所有排行数据
     * @return array
     */
    public function getData()
    {
        $service = new RankService();
        return $service->getAll();
    }

    /**
     * 数据渲染输出
     * @return void
     */
    public function display()
    {
        $list = $this->getData();
        $list = xss_clean($list);
        //主播收入排行数据
        $this->smarty->assign("anchorEarn", $list['anchorEarn']);
        //主播收人气排行数据
        $this->smarty->assign("anchorPop", $list['anchorPop']);
        //主播等级排行榜数据
        $this->smarty->assign("anchorLevel", $list['anchorLevel']);
        //观众贡献榜数据
        $this->smarty->assign("userDevote", $list['userDevote']);
        $this->smarty->assign('headSign','rank');
        $this->smarty->display('rank_refactoring.tpl');
    }

}

$rank = new Rank();
$rank->display();