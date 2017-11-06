<?php

/**
 * 用户评论详情
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
        'orderId' => ['name' => 'orderId', 'default' => '0','rule'=>self::PARAM_RULE_02_GT_0],
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
        $data = self::getParam($this->param,TRUE);
        $res = $this->DueCommentService->getCommentByOrderIdAndUid($this->uid, $data['orderId']);
       if ($res) {
            $this->resultData = $res[0];
        } else {
            $this->resultData['code'] = $res;
            $this->resultData['desc'] = DueOrderService::$error_info[$res];
        }
        render_json($this->resultData, isset($this->resultData['code']) ? $this->resultData['code'] : 0);
    }

}

$do = new userAddComment();
$do->action();
