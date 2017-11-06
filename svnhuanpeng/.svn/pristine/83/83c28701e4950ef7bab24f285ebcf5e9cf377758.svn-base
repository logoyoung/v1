<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/6/15
 * Time: 10:48
 */
include '../../../include/init.php';
use service\due\DueCertService;
class getSkillPriceList
{
    public $unit;
    //初始化
    private function _init()
    {
        $this->unit   = isset($_POST['unit']) ? trim($_POST['unit']) : '';
        return true;
    }
    //先手动配置
    public $priceList =[
        ['price'=>'60','priceName'=>'60 欢朋币/局'],
        [ 'price'=>'80','priceName'=>'80 欢朋币/局'],
        ['price'=>'100','priceName'=>'100 欢朋币/局'],
        ['price'=>'120','priceName'=>'120 欢朋币/局'],
        ['price'=>'160','priceName'=>'160 欢朋币/局'],
        ['price'=>'180','priceName'=>'180 欢朋币/局'],
    ];
    public function getPriceList()
    {
        $postdata['unit'] = $this->unit;
        $CertService = new DueCertService();
        $res = $CertService->getSkillPriceList($postdata);
        return $res;
    }
    public function display()
    {
        $this->_init();
        //后台配置好开启
        // $list = $this->getPriceList();
        $list['list'] = $this->priceList;
        render_json($list);
    }

}
$obj = new getSkillPriceList();
$obj->display();
//$obj->testData();