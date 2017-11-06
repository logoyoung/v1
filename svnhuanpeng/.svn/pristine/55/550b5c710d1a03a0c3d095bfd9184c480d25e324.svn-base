<?php

/**
 * 用户添加评论
 * @encoding utf-8
 * @author liupeng@6.cn
 * @since
 * @version 1.0
 * @Description
 */
include '../../../include/init.php';

use service\common\ApiCommon;
use service\due\DueCommentService; //addComment
use service\due\DueCertService; //addComment
use service\due\DueOrderService; //addComment

/**
 * 创建订单
 */

class userAddComment extends ApiCommon {

    public $DueCommentService = null;
    public $DueCertService = null;
    public $DueOrderService = null;
    public $param = [
        'orderId' => ['name' => 'orderId', 'default' => '0', 'rule' => self::PARAM_RULE_02_GT_0],
        'comment' => ['name' => 'comment', 'default' => '', 'rule' => self::PARAM_RULE_01_NOT_NULL],
        'star' => ['name' => 'star', 'default' => '0', 'rule' => self::PARAM_RULE_02_GT_0],
        'tagIds' => ['name' => 'tagIds', 'default' => ''],
    ];

    public function initCommentService() {
        $this->DueCommentService = new DueCommentService();
    }

    public function initOrderService() {
        $this->DueOrderService = new DueOrderService();
    }

    public function initCertService() {
        $this->DueCertService = new DueCertService();
    }

    public function initCheck() {
        $this->checkIsLogin(true);
    }

    /**
     * 执行接口
     */
    public function action() {
        $data = self::getParam($this->param, TRUE);
        ## 内容过滤
        self::textFilter($this->uid, $data['comment']);
//        $rowOrder = $this->DueCommentService->getCommentByOrderIdAndUid($this->uid, $data['orderId']);
        $OrderInfo = $this->DueOrderService->getOrderByOrderId($data['orderId']);
        if ($OrderInfo['uid'] != $this->uid) {
            $this->resultData['code'] = -8006;
            $this->resultData['desc'] = errorDesc($this->resultData['code']);
            render_json($this->resultData, isset($this->resultData['code']) ? $this->resultData['code'] : 0, 2);
        } else if (empty($OrderInfo)) {
            $this->resultData['code'] = -8008;
            $this->resultData['desc'] = errorDesc($this->resultData['code']);
            render_json($this->resultData, isset($this->resultData['code']) ? $this->resultData['code'] : 0, 2);
        } else if ($OrderInfo['comment'] == lib\due\DueOrder::ORDER_COMMENT_STATUS_01_HAVE_COMMENT) {
            $this->resultData['code'] = -8009;
            $this->resultData['desc'] = errorDesc($this->resultData['code']);
            render_json($this->resultData, isset($this->resultData['code']) ? $this->resultData['code'] : 0, 2);
        }
        $OrderDetailInfo = $this->DueOrderService->getOrderDetailByOrderId($data['orderId']);

        self::checkStringLength($data['tagIds'], 200, TRUE);

        self::checkStringLength($data['comment'], 200, TRUE);
        $res = $this->DueCommentService->addComment($data['orderId'], $this->uid, $OrderInfo['cert_uid'], $OrderDetailInfo['skill_id'], $data['star'] * 2, $data['tagIds'], $data['comment']);
        if ($res == TRUE) {
            $this->resultData['message'] = '评论成功';
            $type = 2;
        } else {
            $this->resultData['code'] = $res;
            $this->resultData['desc'] = DueOrderService::$error_info[$res];
            $type = 1;
        }
        render_json($this->resultData, isset($this->resultData['code']) ? $this->resultData['code'] : 0, $type);
    }

}

$do = new userAddComment();
$do->action();
