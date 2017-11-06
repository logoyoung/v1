<?php
/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017年9月6日
 * Time: 下午5:07:40
 * Desc: 获取奖励
 */
namespace api\activity;
include '../../../include/init.php';
// ini_set("display_errors", 1); 
use service\common\ApiCommon;
use service\activity\InviteActivityService;

class getReward extends ApiCommon
{
    const RETURN_CODE_01 = 1001; //手机号格式有误
    const RETURN_CODE_02 = 1002; //邀请链接已失效
    const RETURN_CODE_03 = 1003; //该手机号已注册
    const RETURN_CODE_04 = 1004; //领取失败
    const RETURN_CODE_05 = 1005; //邀请奖励已被领完
    const RETURN_CODE_06 = 1006; //该手机号已领取
    const RETURN_CODE_08 = 1008; //领取成功马上注册吧
    
    static public $returnMsg = [
        self::RETURN_CODE_01 => '请输入正确的手机号码',
        self::RETURN_CODE_02 => '邀请链接已失效',
        self::RETURN_CODE_03 => '该手机号已注册',
        self::RETURN_CODE_05 => '邀请奖励已被领完',
        self::RETURN_CODE_06 => '该手机号已领取',
        self::RETURN_CODE_08 => '领取成功马上注册吧',
    ];
    
    public $param = [
        'code' => ['name' => 'code', 'default' => '0'],
        'phone'=> ['name' => 'phone', 'default' => '0'],
    ]; 
    private $inviteService = null;
    public function __construct(){
        if(is_null($this->inviteService))
            $this->inviteService = new InviteActivityService();
    }
    //检查 邀请码是否合法 
    private function checkInviteCode($code){
        return $this->inviteService->checkInviteCode($code);
    }
    //检查 邀请码和手机号  
    private function checkIsReceive($phone){
        $data = $this->inviteService->checkIsReceive($phone);
        if(isset($data[0]['nums']) && $data[0]['nums'] > 0){
            $this->renderResult(self::RETURN_CODE_06);
        }
    }
    //查 邀请可领剩余
    private function isReceiveMore($code){
        return $this->inviteService->isReceiveMore($code);
    }
    //查手机号是否已经注册
    private function isRegMobile($phone){
        return $this->inviteService->isRegMobile($phone);
    }
    //检索 该手机号是否已经 领取
    public function display(){
            $params = self::getParam($this->param, TRUE);
            //安检 邀请链接携带邀请码是否合法
            $data = $this->checkInviteCode($params['code']);
            if(!isset($data[0]['nums']) || $data[0]['nums']==0){
                $this->renderResult(self::RETURN_CODE_02);
            }
            //检查 此邀请链接是否还有可领取剩余
            $data = $this->isReceiveMore($params['code']);
            if(!isset($data[0]['nums']) || $data[0]['nums']==0){
                $this->renderResult(self::RETURN_CODE_05);
            }
            //安检 手机号格式
            if(checkMobile($params['phone'])!==true){
                $this->renderResult(self::RETURN_CODE_01);
            } 
            //安检 手机号是否已经注册过  
            if(false != $this->isRegMobile($params['phone'])){
                $this->renderResult(self::RETURN_CODE_03);
            }
            //检索 该手机是否已经领取过此邀请
            $this->checkIsReceive($params['phone']);
            //开始领取
            $data = $this->inviteService->getReward($params['phone'], $params['code']);
            if(empty($data)){
                $this->renderResult(self::RETURN_CODE_04);
                write_log(__CLASS__." 第".__LINE__."行 获取邀请奖励失败 code：".$params['code']." 手机号：".$params['phone'],'inviteActivity');
            }
            render_json(self::$returnMsg[self::RETURN_CODE_08]);
    }
    //结果集返回
    private function renderResult($returnCode){
        render_json(self::$returnMsg[$returnCode],$returnCode);
    }
}
$obj = new getReward();
$obj->display();
