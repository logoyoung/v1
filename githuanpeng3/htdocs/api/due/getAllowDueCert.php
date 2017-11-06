<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/6/16
 * Time: 11:24
 */
include '../../../include/init.php';
use service\due\DueCertService;
class getAllowDueCert extends \service\common\ApiCommon
{
    const ERROR_DUE_ANCHOR_CERT = -80000;
    public static $errorMsg =[
        self::ERROR_DUE_ANCHOR_CERT => '未签约主播无法发布陪玩，请联系客服签约',
    ];
    //初始化
    private function _init()
    {
        //必须有uid
        $this->checkIsLogin(true);
        return true;
    }
    public function getAllowAnchor()
    {
        $CertService = new DueCertService();
        $CertService->setUid($this->uid);
        $res =  $CertService->getIsAllowDue();
        return $res;

    }
    public function display()
    {
        $this->_init();
        $list = $this->getAllowAnchor();
        if($list)
        {
            //仅仅显示而已
            $code = self::ERROR_DUE_ANCHOR_CERT;
            $msg =  self::$errorMsg[$code];
            $list = ['message'=>$msg];
            render_json($list);
        }else
        {
            $code = self::ERROR_DUE_ANCHOR_CERT;
            $msg =  self::$errorMsg[$code];
            //此处端显示 type为2
            render_json($msg, $code,2);
        }
    }
}

$obj = new getAllowDueCert();
$obj->display();