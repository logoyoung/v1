<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/6/7
 * Time: 11:56
 */
include '../../../include/init.php';
use service\due\DueCertService;
/**
 * 资质详情接口
 * Class certDetail
 */
class certDetail extends  \service\common\ApiCommon
{
    public $certId;
    public $skillId;
    //初始化
    private function _init()
    {
       // $this->checkIsLogin(true);
        $this->certId   = isset($_POST['certId']) ? trim($_POST['certId']) : '';
        $this->skillId   = isset($_POST['skillId']) ? trim($_POST['skillId']) : '';
        return true;
    }
    //获取资质列表内容
    public function getCertDetail()
    {
        $res  = [];
        $CertService = new DueCertService();
        $CertService->setUid($this->uid);
        $imgDomain = $CertService->getImageDomain();
        $data['certId'] = $this->certId;
        $data['skillId'] = $this->skillId;
        $arr =  $CertService->getCertDetail($data);
        //列表项
        foreach($arr as $value)
        {
            $data['uid'] = isset($value['uid']) ? $value['uid']:'';
            $data['nick'] = isset($value['nick']) ? $value['nick']:'';
            $data['pic'] = isset($value['pic']) ? $value['pic']:'';
            $data['isLiving'] = isset($value['isLiving']) ? $value['isLiving']:'0';
            //Android端拼写错误 临时加上
            $data['isLiveing'] = isset($value['isLiving']) ? $value['isLiving']:'0';
            $data['switch'] = isset($value['switch']) ? $value['switch']:'-1';
            $data['tags']= isset($value['tags']) ? $value['tags']:[];
            $data['certId']= isset($value['certId']) ? $value['certId']:'';
            $data['gameId'] = isset($value['game_id']) ? $value['game_id']:'';
            $data['gameName'] = isset($value['gameName']) ? $value['gameName']:'';
            $data['picUrls'] = $value['pic_urls'];
            $data['imgDomain']   = $imgDomain;
            $data['price'] = isset($value['price']) ? $value['price']:'100';
            $data['unit'] = isset($value['unit']) ? $value['unit']:'1';
            $data['star']= isset($value['star']) ? $value['star']:'';
            $data['info']= isset($value['info']) ? $value['info']:'';
            $data['orderTotal'] = isset($value['orderTotal']) ? $value['orderTotal']:'0';
            $data['comment'] =  isset($value['comment']) ? $value['comment']:[];
            $data['lvid'] = isset($value['lvid']) ? $value['lvid']:'';
            $data['poster'] = isset($value['poster']) ? $value['poster']:'';
            $data['videoUrl'] = isset($value['videoUrl']) ? $value['videoUrl']:'';
            $data['vtype'] = isset($value['vtype']) ? $value['vtype']:'';
            $data['orientation'] = isset($value['orientation']) ? $value['orientation']:'';
        }
        $res = $data;
        return $res;
    }

    public function display()
    {
        $this->_init();
        //获取资质列表
        $list = $this->getCertDetail();
        render_json($list);
    }

}
$obj = new certDetail();
$obj->display();