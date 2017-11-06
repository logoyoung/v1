<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/6/7
 * Time: 11:26
 */
include '../../../include/init.php';
use service\due\DueCertService;
/**
 * 陪玩资质认证提交
 * Class addCert
 */
class addCert extends \service\common\ApiCommon
{
    public $certId;
    public $option;
    public $gameId;
    public $picUrl;
    public $info;
    //初始化
    private function _init()
    {
        $this->checkIsLogin(true);
        $this->option   = isset($_POST['option']) ? trim($_POST['option']) : '';
        $this->gameId   = isset($_POST['gameId']) ? trim($_POST['gameId']) : '';
        $this->picUrl   = isset($_POST['picUrls']) ? trim($_POST['picUrls']) : '';
        $this->info     = isset($_POST['info']) ? trim($_POST['info']) : '';
        $this->certId   = isset($_POST['certId']) ? trim($_POST['certId']) : '';
        return true;
    }
    public function addCertByCertId()
    {
        $data = [];
        $data['info'] = $this->info;
        self::checkStringLength($data['info'],200,true);
        $data['option'] = $this->option;
        $data['gameId'] = $this->gameId;
        $data['picUrls'] = $this->picUrl;
        $data['certId'] = $this->certId;
        $CertService = new DueCertService();
        //验证图片地址是否正确
        $CertService->checkPicUrls($data['picUrls']);
        $CertService->setUid($this->uid);
        //增加验证是否允许申请约玩
        $CertService->getIsAllowDue(true);
        $res = $CertService->addCert($data);
        return $res;
    }
    public function display()
    {
        $this->_init();
        //获取资质列表
        $list  = $this->addCertByCertId();
        if($list)
        {
            $list = ['message'=>'成功'];
        }
        render_json($list);
    }
}
$obj = new addCert();
$obj->display();