<?php

/**
 * 用户订单详情
 * @encoding utf-8
 * @author liupeng@6.cn
 * @since
 * @version 1.0
 * @Description   废弃了 2017-06-30 13:24:02
 */
include '../../../include/init.php';

use service\common\ApiCommon;
use service\due\DueOrderService; 

/**
 * 创建订单
 */

class userOrderInfo extends ApiCommon {

    public $DueOrderService = null;
    public $param = [
        'orderId' => ['name' => 'orderId', 'default' => '0'],
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
        $data = self::getParam($this->param);
        if (empty($data['orderId'])) {
            error2(-2004);
        }
        $res = $this->DueOrderService->getOrderByOrderId($data['orderId']);
        
        
        
        if ($res['uid'] != $this->uid) {
            error2(-8006);
        }
        if ($res) {
            $this->resultData = $res;
        }
        render_json($this->resultData, isset($this->resultData['code']) ? $this->resultData['code'] : 0);
    }

}

$do = new userOrderInfo();
$do->action();
