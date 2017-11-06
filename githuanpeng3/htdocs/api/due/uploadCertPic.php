<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/6/12
 * Time: 9:29
 */
include '../../../include/init.php';
use service\due\DueCertService;
class uploadCertPic extends \service\common\ApiCommon
{

    //初始化
    private function _init()
    {
        $this->checkIsLogin(true);
        return true;
    }
    public  function upload()
    {
        $CertService = new DueCertService();
        $CertService->setUid($this->uid);
        $res = $CertService->uploadCertImage();
        return $res;
    }
    public function display()
    {
        $this->_init();
        //获取资质列表
        $list = $this->upload();
        render_json($list);
    }
}
$obj = new uploadCertPic();
$obj->display();