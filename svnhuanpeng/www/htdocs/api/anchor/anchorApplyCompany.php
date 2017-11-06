<?php

/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/7/14
 * Time: 11:34
 */
include '../../../include/init.php';
use service\anchor\AnchorApplyService;

/**
 *主播申请经济公司
 * Class anchorApplyCompany
 */
class anchorApplyCompany  extends \service\common\ApiCommon
{
    public $cid;
    public $videoId;
    //初始化
    private function _init()
    {
        $this->checkIsLogin(true);
        $this->cid   = isset($_POST['cid']) ? trim($_POST['cid']) : '';
        $this->videoId   = isset($_POST['videoId']) ? trim($_POST['videoId']) : '';
        return true;
    }
    public function addAnchorApply()
    {
        $data ['cid']= $this->cid;
        $data['videoid'] = $this->videoId;
        $data['uid'] = $this->uid;
        $anchorApplyService = new AnchorApplyService();
        $anchorApplyService->setUid($this->uid);
        $res = $anchorApplyService->addAnchorApplyCompany($data);
        return $res;
    }
    public function display()
    {
        $list = [];
        $this->_init();
        $aid  = $this->addAnchorApply();
        if($aid)
        {
            $list = [
                'aid'=>$aid,
                'message'=>'成功'];
        }
        render_json($list);
    }
}
$obj = new anchorApplyCompany();
$obj->display();