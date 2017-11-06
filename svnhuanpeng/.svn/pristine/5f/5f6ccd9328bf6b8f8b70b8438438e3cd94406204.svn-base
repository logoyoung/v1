<?php

/**
 * 创建订单
 * @encoding utf-8
 * @author liupeng@6.cn
 * @since
 * @version 1.0
 * @Description
 */
include '../../../include/init.php';
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
use service\common\ApiCommon;
use service\due\DueOrderService;
use service\due\DueCertService;
use service\user\UserDataService;
 
/**
 * 创建订单
 */
class userCreateOrder extends ApiCommon {

    public $orderService = null;
    public $certService = null;
    public $param = [
        'skillId' => ['name' => 'skillId', 'default' => '0', 'rule' => self::PARAM_RULE_02_GT_0],
        'num' => ['name' => 'num', 'default' => '0', 'rule' => self::PARAM_RULE_02_GT_0],
        'startPlayTime' => ['name' => 'startPlayTime', 'default' => '', 'rule' => self::PARAM_RULE_01_NOT_NULL],
        'memo' => ['name' => 'memo', 'default' => ''],
        'couponId'=> ['name' => 'couponId', 'default' => '0']
    ];

    public function initOrderService() {
        $this->orderService = new DueOrderService();
    }

    public function initCertService() {
        $this->certService = new DueCertService();
    }

    public function initCheck() {
        $this->checkIsLogin(true);
    }

    /**
     * 执行接口
     */
    public function action() {
        $data = self::getParam($this->param, TRUE); 
        //校验用户使用优惠券时  是否已经绑定手机号
        if($data['couponId']!=0){
            $userinfo = $this->UserDataService->setUserInfoDetail(UserDataService::USER_DATA_ALL)->getUserInfo();
            $phone = $userinfo['phone'];
            $checkRes = checkMobile($phone);
            if ($checkRes !== TRUE) {
                $this->resultData['code'] = DueOrderService::ERROR_CODE_31;
                $type = 1;
                render_json(DueOrderService::$error_info[$this->resultData['code']], isset($this->resultData['code']) ? $this->resultData['code'] : 0, $type);
            }
        }
        
        self::checkStringLength($data['memo'], 200, TRUE);
        self::textFilter($this->uid, $data['memo']);
        
//        $time = strtotime($data['startPlayTime']);
        $time = $data['startPlayTime'];
        $skill = $this->certService->getSkillBySkillId(['skillId' => $data['skillId']])[0];
        $cert = $this->certService->getCertInfoByCertIds($skill['cert_id']);
        if ($cert[$skill['cert_id']]['uid'] == $this->uid) {
            $this->resultData['code'] = -100;
            $this->resultData['desc'] = "不能对自己下单哦!";
            render_json($this->resultData, isset($this->resultData['code']) ? $this->resultData['code'] : 0, $this->resultData['code'] < 0 ? 2 : 1);
        }
        $res = $this->orderService->createOrder($this->uid, $data['skillId'], $data['num'], $time ,$data['couponId'], 0, $data['memo']);
        if ($res < 0) {
            $this->resultData['code'] = $res;
            $this->resultData['desc'] = DueOrderService::$error_info[$res];
        } else {
            $this->resultData['orderId'] = $res;
        }
        $type = isset($this->resultData['code']) && $this->resultData['code'] < 0 ? 2 : 1;
        if(in_array($this->resultData['code'], [-121,-128])) $type = 1;
        render_json($this->resultData, isset($this->resultData['code']) ? $this->resultData['code'] : 0, $type);
    }

}

$do = new userCreateOrder();
$do->action();
