<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/6/7
 * Time: 11:59
 */
include '../../../include/init.php';
use service\due\DueCertService;
/**
 * 展示修改技能数据
 * Class getSetSkillData
 */
class getSetSkillData extends \service\common\ApiCommon
{
    public $certId;
    public $skillId;
    public $gameId;
    //初始化
    private function _init()
    {
        $this->checkIsLogin();
        $this->skillId   = isset($_POST['skillId']) ? trim($_POST['skillId']) : '';
        return true;
    }


    public function getSkillData()
    {
        $postData = [];
        $postData['skillId'] = $this->skillId;
        $CertService = new DueCertService();
        $CertService->setUid($this->uid);
        $data =  $CertService->getSkillBySkillId($postData);
        //格式化输出
        $res['certId']      = $data[0]['cert_id'];
        $res['gameId']      = $data[0]['game_id'];
        $res['skillId']     = $this->skillId;
        $res['price']       = $data[0]['price'];
        $res['unit']        = $data[0]['unit'];
        $res['switch']      = $data[0]['switch'];
        return $res;
    }
    public function display()
    {
        $this->_init();
        //获取资质列表
        $list = $this->getSkillData();
        render_json($list);
    }
}
$obj = new getSetSkillData();
$obj->display();