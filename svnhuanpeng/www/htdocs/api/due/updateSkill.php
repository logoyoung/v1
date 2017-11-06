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
 * 设置提交
 * Class updateSkill
 */
class updateSkill extends \service\common\ApiCommon
{
    public $certId;
    public $skillId;
    public $gameId;
    public $price;
    public $unit;
    public $switch;

    private function _init()
    {
        $this->checkIsLogin(true);
        $this->certId   = isset($_POST['certId']) ? trim($_POST['certId']): '';
        $this->skillId   = isset($_POST['skillId']) ? trim($_POST['skillId']) : '';
        $this->gameId   = isset($_POST['gameId']) ? trim($_POST['gameId']) : '';
        $this->price   = isset($_POST['price']) ? trim($_POST['price']) : '';
        $this->unit   = isset($_POST['unit']) ? trim($_POST['unit']) : '';
        $this->switch     = isset($_POST['switch']) ? trim($_POST['switch']) : '';
        return true;
    }
    public function updateSkillBySkillID()
    {
        $data = [];
        $data['certId'] = $this->certId;
        $data['skillId'] = $this->skillId;
        $data['gameId'] = $this->gameId;
        $data['price'] = $this->price;
        $data['unit'] = $this->unit;
        $data['switch'] = $this->switch;
        $CertService = new DueCertService();
        $CertService->setUid($this->uid);
        $res = $CertService->updateSkillBySkillId($data);
        return $res;
    }
    public function display()
    {
        $this->_init();
        //获取资质列表
        $list  = $this->updateSkillBySkillID();
        if($list)
        {
            $list = ['message'=>'成功'];
        }
        render_json($list);
    }
}
$obj = new updateSkill();
$obj->display();