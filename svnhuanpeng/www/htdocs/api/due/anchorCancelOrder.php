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
class anchorCancelOrder extends ApiCommon {

    public $orderService = null;
    public $param = [
        'orderId' => ['name' => 'orderId', 'default' => '0', 'rule' => self::PARAM_RULE_02_GT_0],
        'reason' => ['name' => 'reason', 'default' => '', 'rule' => self::PARAM_RULE_01_NOT_NULL],
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
        $data = self::getParam($this->param, TRUE);
        self::checkStringLength($data['reason'], 200, TRUE);
        $res = $this->orderService->anchorCancleOrderByOrderId($this->uid, $data['orderId'], $data['reason']);
        if ($res === TRUE) {
            $this->resultData['message'] = '取消成功';
        } else {
            $this->resultData['code'] = $res;
            $this->resultData['desc'] = DueOrderService::$error_info[$res];
        }
        render_json($this->resultData, isset($this->resultData['code']) ? $this->resultData['code'] : 0);
    }

}

$do = new anchorCancelOrder();
$do->action();
