<?php
/**
 * Created by NetBeans.
 * User: yalongSun <yalong_2017@6.cn> 
 * Desc: 邀请奖励活动  点击统计接口（预生成领奖记录）
 */

namespace api\activity;
//ini_set("display_errors", 1); 
include '../../../include/init.php'; 
use service\common\ApiCommon;
use service\activity\InviteActivityService;

class preCreateInviteReceive extends ApiCommon{
    const RETURN_CODE_01 = 1000;
    const RETURN_CODE_02 = 1001;
    const RETURN_CODE_03 = 1002;
    public $returnDesc = [
        self::RETURN_CODE_01 => '操作成功',
        self::RETURN_CODE_03 => '邀请码不合法'
    ];

    public $param = [
        'code' => ['name' => 'code', 'default' => '0'],
    ];
    public function display(){
        //获取 邀请码code参数
        $code = self::getParam($this->param, TRUE); 
        $inviteActivity = new InviteActivityService();
        //校验 code 是否合法
        $res = $inviteActivity->checkInviteCode($code['code']); 
        if($res[0]['nums'] == 0){
            render_json (['desc'=>$this->returnDesc[self::RETURN_CODE_03],'code'=>self::RETURN_CODE_03]);
        } 
        $res = $inviteActivity->preCreateInviteReceive($code['code']);
        if($res) render_json (['desc'=>$this->returnDesc[self::RETURN_CODE_01],'code'=>self::RETURN_CODE_01]);
        else render_json (['desc'=>$this->returnDesc[self::RETURN_CODE_01],'code'=>self::RETURN_CODE_02]);
    }
}
$obj = new preCreateInviteReceive();
$obj->display();