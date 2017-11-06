<?php

/**
 * 客服处理申诉订单
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
class customerServiceHandleOrder extends ApiCommon {

    public $orderService = null;
    public $appealService = null;
    public $param = [
        'uid' => ['name' => 'uid', 'default' => '0'],
        'orderId' => ['name' => 'orderId', 'default' => '0', 'rule' => self::PARAM_RULE_02_GT_0],
        'type' => ['name' => 'type', 'default' => '0', 'rule' => self::PARAM_RULE_02_GT_0],
    ];

    public function initOrderService() {
        $this->orderService = new DueOrderService();
        $this->appealService = new DueAppealService();
    }

    public function initCheck() {
//        $this->checkIsLogin(true);
    }

    /**
     * 执行接口
     */
    public function action() {
        $data = self::getParam($this->param);
        if (!in_array($data['type'], [1, 2])) {
            error2(-2004);
        }
        if ($data['type'] == 1) {
            //退单
            $reply = self::getParam(['reply' => ['name' => 'reply', 'default' => ''],]);
            $res = $this->orderService->customerServiceAgreeOrderByOrderId($data['uid'], $data['orderId'], $reply['reply']);
            $state = \lib\due\DueAppeal::APPEAL_STATUS_01_AGREE;
        } else {
            //不退
            $reply = self::getParam(['reply' => ['name' => 'reply', 'default' => ''], 'rule' => self::PARAM_RULE_01_NOT_NULLF]);
            $res = $this->orderService->customerServiceDisagreeOrderByOrderId($data['uid'], $data['orderId'], $reply['reply']);
            $state = \lib\due\DueAppeal::APPEAL_STATUS_02_DISAGREE;
        }
        $this->appealService->updateAppealByOrderId($data['orderId'], $state, $reply['reply']);
        if ($res === TRUE) {
            $this->resultData['message'] = $data['type'] == 1 ? '客服退单操作成功' : '客服不退单操作成功';
        } else {
            $this->resultData['code'] = $res;
            $this->resultData['desc'] = DueOrderService::$error_info[$res];
        }
        render_json($this->resultData, isset($this->resultData['code']) ? $this->resultData['code'] : 0);
    }

}

$do = new customerServiceHandleOrder();
$do->action();
