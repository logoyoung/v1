<?php

/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/7/14
 * Time: 11:35
 */
include '../../../include/init.php';
use service\anchor\AnchorApplyService;

/**
 * 主播取消申请经济公司
 * Class anchorCancelApply
 */
class anchorCancelApply  extends \service\common\ApiCommon
{
    public $aid;
    //初始化
    private function _init()
    {
        $this->checkIsLogin(true);
        $this->aid   = isset($_POST['aid']) ? trim($_POST['aid']) : '';
        return true;
    }
    public function cancelAnchorApply()
    {
        $data ['aid']= $this->aid;
        $data['uid'] = $this->uid;
        $anchorApplyService = new AnchorApplyService();
        $anchorApplyService->setUid($this->uid);
        $res = $anchorApplyService->cancelAnchorApply($data);
        return $res;
    }
    public function display()
    {
        $this->_init();
        $list  = $this->cancelAnchorApply();
        if($list)
        {
            $list = ['message'=>'成功'];
        }
        render_json($list);
    }
}
$obj = new anchorCancelApply();
$obj->display();