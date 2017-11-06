<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/6/2
 * Time: 17:04
 */
include '../../../include/init.php';
use service\due\DueCertService;
use service\due\DueActivityService;

/**
 * 资质列表接口
 * Class certList
 */
class certList extends \service\common\ApiCommon
{

    //暂时主播列表限制申请3资质
    const DUE_CERTLIST_LIMIT = 3;
    const ADD_CERT_MEMO = '目前仅支持平台签约主播';
    //初始化
    private function _init()
    {
       $this->checkIsLogin(true);
        //$this->certId   = isset($_POST['certId']) ? trim($_POST['certId']) : '';
        //$this->skillId   = isset($_POST['skillId']) ? trim($_POST['skillId']) : '';
        return true;
    }
    //获取资质列表内容
    public function getCertList()
    {
        $data = [];
        $res  = [];
        $pic_urls = [];
        $CertService = new DueCertService();
        $CertService->setUid($this->uid);
        $imgDomain = $CertService->getImageDomain();
        $arr =  $CertService->getAdminCertList();
        //获取列表数量
        $total = count($arr);
        if($total>0)
        {
            //列表项
            foreach($arr as $key=>$value)
            {
                $data[$key]['certId']= $value['certId'];
                $data[$key]['gameId'] = $value['game_id'];
                $data[$key]['gameName'] = isset($value['gameName']) ? $value['gameName']:'';
                $data[$key]['skillId'] = isset($value['skillId']) ? $value['skillId']:'-1';
                $data[$key]['info'] = isset($value['info']) ? $value['info']:'';
                //取第一张图片
                if( isset($value['pic_urls']))
                {
                    $pic_urls = explode(',',$value['pic_urls']);
                }
                $data[$key]['picUrl'] = $imgDomain.$pic_urls[0];
                $data[$key]['price'] = isset($value['price']) ? $value['price']:'100';
                $data[$key]['unit'] = isset($value['unit']) ? $value['unit']:'1';
                $data[$key]['star']= isset($value['star']) ? $value['star']:'';
                $data[$key]['switch'] = isset($value['switch']) ? $value['switch']:'-1';
                $data[$key]['status'] = DueCertService::setStatus($value['status']);
                $data[$key]['orderTotal'] = isset($value['orderTotal']) ? $value['orderTotal']:'0';
            }
            $res['list'] = $data;

        }else{
            $res['list'] = [];
        }

        $res['total'] = $total;
        
         ### 分享信息
        $res['shareSourceId'] = 0;
        $res['shareActivityId'] = 0;
        $avtivity = new DueActivityService();
        foreach ($data as $value) {
             $isShare = $avtivity->publicCheckSourceId($this->uid, $value['certId'], DueActivityService::ACTIVITY_TYPE_03_CERT);
             if($isShare){
                 $res['shareSourceId'] = $isShare['sourceId'] ;
                 $res['shareActivityId'] = $isShare['activityId'] ;
                 break;
             }
        }
        
        //资质页允许添加资质数量
        $res['listLimit'] = self::DUE_CERTLIST_LIMIT;
        $res['addCertMemo'] = self::ADD_CERT_MEMO;
        return $res;
    }
    public function display()
    {
        $this->_init();
        //获取资质列表
        $list = $this->getCertList();
        render_json($list);
    }
}

$obj = new certList();
$obj->display();
//$obj->testData();