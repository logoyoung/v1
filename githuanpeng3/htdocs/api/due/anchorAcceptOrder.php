<?php

/**
 * 主播拒绝订单
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
class anchorAcceptOrder extends ApiCommon {

    public $orderService = null;
    public $param = [
        'orderId' => ['name' => 'orderId', 'default' => '0','rule'=>self::PARAM_RULE_02_GT_0],
    ];

    public function initOrderService() {
        $this->orderService = new DueOrderService();
    }

    public function initCheck() {
        $this->checkIsLogin(true);
    }

    /**
     * 执行接口
     */
    public function action() {
        $data = self::getParam($this->param,TRUE);
        $res = $this->orderService->anchorAcceptOrderByOrderId($this->uid, $data['orderId'], '');
        if ($res === TRUE) {
            $this->resultData['message'] = '接单成功';
        } else {
            $this->resultData['code'] = $res;
            $this->resultData['desc'] = DueOrderService::$error_info[$res];
        }
        render_json($this->resultData, isset($this->resultData['code']) ? $this->resultData['code'] : 0);
    }

}

$do = new anchorAcceptOrder();
$do->action();
