<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/6/15
 * Time: 14:49
 */
include '../../../include/init.php';
use service\due\DueCertService;
class getAnchorSkillList extends  \service\common\ApiCommon
{
    public $luid;
    //初始化
    private function _init()
    {
        //$this->checkIsLogin(true);
        $this->luid   = isset($_POST['luid']) ? trim($_POST['luid']) : '';
        //$this->skillId   = isset($_POST['skillId']) ? trim($_POST['skillId']) : '';
        return true;
    }
    //获取资质列表内容
    public function getAnchorCertList()
    {
        $data = [];
        $res  = [];
        $pic_urls = [];
        $CertService = new DueCertService();
        $CertService->setUid($this->luid);
        $imgDomain = $CertService->getImageDomain();
        $luid['luid'] = $this->luid ;
        $arr =  $CertService->getAllCertListByStatusPass($luid);
        //获取列表数量
        $total = count($arr);
        if($total>0)
        {
            //列表项
            foreach($arr as $key=>$value)
            {
                $data[$key]['certId']= $value['certId'];
                $data[$key]['skillId']= $value['skillId'];
                $data[$key]['gameId'] = $value['game_id'];
                $data[$key]['gameName'] = $value['gameName'];
                $data[$key]['info'] = $value['info'];
                //取第一张图片
                if( isset($value['pic_urls']))
                {
                    $pic_urls = explode(',',$value['pic_urls']);
                }
                $data[$key]['picUrl'] = $imgDomain.$pic_urls[0];
                $data[$key]['price'] = isset($value['price']) ? $value['price']:'100';
                $data[$key]['unit'] = isset($value['unit']) ? DueCertService::getUnitName($value['unit']):'局';
                $data[$key]['star']= isset($value['star']) ? $value['star']:'';
                $data[$key]['orderTotal'] = isset($value['orderTotal']) ? $value['orderTotal']:'0';
            }
            $res['list'] = $data;

        }else{
            $res['list'] = [];
        }

        $res['total'] = $total;
        return $res;
    }
    public function display()
    {
        $this->_init();
        //获取资质列表
        $list = $this->getAnchorCertList();
        render_json($list);
    }
}

$obj = new getAnchorSkillList();
$obj->display();
//$obj->testData();