<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



/**
 * 用户订单
 * @encoding utf-8
 * @author liupeng@6.cn
 * @since
 * @version 1.0
 * @Description
 */
include '../../../include/init.php';

use service\common\ApiCommon;
use service\due\DueOrderService;
use service\due\DueAppealService;

/**
 * 创建订单
 */
class userAppealOrder extends ApiCommon {

    public $orderService = null;
    public $appealService = null;
    public $param = [
        'uid' => ['name' => 'uid', 'default' => '0'],
        'orderId' => ['name' => 'orderId', 'default' => '0','rule'=>self::PARAM_RULE_02_GT_0],
        'reason' => ['name' => 'reason', 'default' => '','rule'=>self::PARAM_RULE_01_NOT_NULL],
        'pic' => ['name' => 'pic', 'default' => '','rule'=>self::PARAM_RULE_01_NOT_NULL],
    ];

    public function initOrderService() {
        $this->orderService = new DueOrderService();
        $this->appealService = new DueAppealService();
    }

    public function initCheck() {
        $this->checkIsLogin(true);
    }

    /**
     * 执行接口
     */
    public function action() {
        $data = self::getParam($this->param,TRUE);
        $res = $this->orderService->userAppealOrderByOrderId($this->uid, $data['orderId'], $data['reason']);
        if ($res === TRUE) {
            $this->appealService->insertAppeal($this->uid, $data['orderId'], $data['reason'], $data['pic']);
            $this->resultData['message'] = '用户申诉客服成功';
        } else {
            $this->resultData['code'] = $res;
            $this->resultData['desc'] = DueOrderService::$error_info[$res];
        }
        render_json($this->resultData, isset($this->resultData['code']) ? $this->resultData['code'] : 0);
    }

}

$do = new userAppealOrder();
$do->action();
