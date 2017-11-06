<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/6/7
 * Time: 11:58
 */
include '../../../include/init.php';
use service\due\DueCertService;
/**
 * 展示修改认证(资质认证数据)
 * Class getSetCertData
 */
class getSetCertData extends \service\common\ApiCommon
{
    public $certId;
    //初始化
    private function _init()
    {
       $this->checkIsLogin(true);
        $this->certId   = isset($_POST['certId']) ? trim($_POST['certId']) : '';
        return true;
    }

    /**
     *获取主播资质信息
     * @return array|bool|PDOStatement
     */
    public function getCertData()
    {
        $res = [];
        $postData = [];
        $postData['certId'] = $this->certId;
        $CertService = new DueCertService();
        $CertService->setUid($this->uid);
        $imgDomain = $CertService->getImageDomain();
        $data =  $CertService->getCertByCertId($postData);
        //格式化输出
        $res['certId']  = $data[0]['certId'];
        $res['gameId']  = $data[0]['game_id'];
        $res['gameName']  = $data[0]['gameName'];
        $res['picUrls'] = $data[0]['pic_urls'];
        $res['info']    = $data[0]['info'];
        $res['status']  = $data[0]['status'];
        $res['imgDomain']   = $imgDomain;
        return $res;
    }
    public function display()
    {
        $this->_init();
        //获取资质列表
        $list = $this->getCertData();
        render_json($list);
    }
}
$obj = new getSetCertData();
$obj->display();