<?php
/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017年9月11日
 * Time: 下午5:09:09
 * Desc: 支付页面 活动参数获取
 */
namespace api\activity;
include '../../../include/init.php';
//ini_set("display_errors", 1); 
use service\common\ApiCommon;
use service\activity\RechargeService; 
use service\activity\ShareActivityConfig; 

class payActivity extends ApiCommon
{ 
    //首充奖励 db 活动id 
    const RETURN_CODE_01 = 1001; //活动未发布
    const RETURN_CODE_02 = 1002; //活动获取失败 
    const RETURN_CODE_03 = 1003; //未到活动期
    const RETURN_CODE_04 = 1004; //活动期已过期
    static public $codeDesc = [
        self::RETURN_CODE_01=>'活动还未发布',
        self::RETURN_CODE_02=>'活动获取失败',
        self::RETURN_CODE_03=>'未到活动期',
        self::RETURN_CODE_04=>'活动期已过期',
    ];
    
    public $uid;
    public $encpass;
    private $rechargeService = null;

    public function __construct() {
        if(is_null($this->rechargeService))
            $this->rechargeService = new RechargeService;
        $this->uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
        $this->encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
    }
    public function returnData($code){
        render_json(self::$codeDesc[$code],$code);
    }
    public function display(){
        //返回活动
        $data = $this->rechargeService->onceDayActivity(ShareActivityConfig::PAY_ACTIVITY_ID);
        if($data['ispublish'] == 0)
            $this->returnData (self::RETURN_CODE_01);
        //校验是否登录
        if($data['is_login'] == 1)
            $this->checkIsLogin(true);
        //客户端请求渠道  1 是安卓；0是iOS
        $channer_type = !isset($_POST['channel_type']) || $_POST['channel_type'] == 1 ? 1 : intval($_POST['channel_type']);
        $now = date("Y-m-d H:i:s");
        $is_has_activity = 1;
        if($now < $data['stime']){
            $is_has_activity = 0;
            $this->returnData (self::RETURN_CODE_03);
        } 
        if($now > $data['etime']){
            $is_has_activity = 0;
            $this->returnData (self::RETURN_CODE_04);
        }
        $data = [
            'pay_activity_id' => ShareActivityConfig::PAY_ACTIVITY_ID,
            'share_thumb_img' => $data['thumbnail'],
            'activity_img'    => RechargeService::getHttpHost().$data['poster'],
            'activity_url'    => $data['url'],
            'is_has_activity' => $is_has_activity //1 活动运营中；0活动结束
        ];
        $arrs['activity'] = $data;
        //返回充值金额  安卓价格规范  以后如果iOS 新增，则 可根据channer_type进行判断
        if($channer_type == 1){
            $arrs['recharge_money'] = [
                ['gold_coin'=>10,'money'=>1,'type'=>1],
                ['gold_coin'=>100,'money'=>10,'type'=>0],
                ['gold_coin'=>200,'money'=>20,'type'=>0],
                ['gold_coin'=>500,'money'=>50,'type'=>0],
                ['gold_coin'=> 1000,'money'=>100,'type'=>0],
                ['gold_coin'=> 2000,'money'=>200,'type'=>0],
                ['gold_coin'=> 5000,'money'=>500,'type'=>0],
                ['gold_coin'=> 10000,'money'=> 1000,'type'=>0],
            ];
        }
        render_json($arrs);
    }
}
$obj = new payActivity();
$obj->display();

