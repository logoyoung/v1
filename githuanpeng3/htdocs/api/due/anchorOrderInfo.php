<?php

/**
 * 用户订单详情
 * @encoding utf-8
 * @author liupeng@6.cn
 * @since
 * @version 1.0
 * @Description
 */
include '../../../include/init.php';

use service\common\ApiCommon;
use service\due\DueOrderService; 

/**
 * 创建订单
 */

class userAddComment extends ApiCommon {

    public $DueOrderService = null;
    public $param = [
        'orderId' => ['name' => 'orderId', 'default' => '0','rule'=>self::PARAM_RULE_02_GT_0],
    ];

    public function initOrderService() {
        $this->DueOrderService = new DueOrderService();
    }


    public function initCheck() {
        $this->checkIsLogin(true);
    }

    /**
     * 执行接口
     */
    public function action() {
        $data = self::getParam($this->param,TRUE);
        $res = $this->DueOrderService->getOrderByOrderId($data['orderId']);
        if ($res['cert_uid'] != $this->uid) {
            error2(-8006);
        }
        if ($res) {
            $this->resultData = $res;
        }
        render_json($this->resultData, isset($this->resultData['code']) ? $this->resultData['code'] : 0);
    }

}

$do = new userAddComment();
$do->action();
