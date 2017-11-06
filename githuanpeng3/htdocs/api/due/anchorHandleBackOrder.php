<?php

/**
 * 主播处理退单订单
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
class anchorHandleBackOrder extends ApiCommon {

    public $orderService = null;
    public $param = [
        'orderId' => ['name' => 'orderId', 'default' => '0', 'rule' => self::PARAM_RULE_02_GT_0],
        'type' => ['name' => 'type', 'default' => '0', 'rule' => self::PARAM_RULE_02_GT_0],
        'reason' => ['name' => 'reason', 'default' => ''],
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
        $data = self::getParam($this->param);
        if (!in_array($data['type'], [1, 2])) {
            error2(-2004);
        }
          self::checkStringLength($data['reason'], 200, TRUE);
        if ($data['type'] == 1) {
            //退单
            $res = $this->orderService->anchoragreeBackOrderByOrderId($this->uid, $data['orderId'], $data['reason']);
        } else {
            //不退
            $res = $this->orderService->anchorDisagreeBackOrderByOrderId($this->uid, $data['orderId'],$data['reason']);
        }
        if ($res === TRUE) {
            $this->resultData['message'] = $data['type'] == 1 ? '主播退单操作成功' : '主播不同意退单操作成功';
        } else {
            $this->resultData['code'] = $res;
            $this->resultData['desc'] = DueOrderService::$error_info[$res];
        }
        render_json($this->resultData, isset($this->resultData['code']) ? $this->resultData['code'] : 0);
    }

}

$do = new anchorHandleBackOrder();
$do->action();
