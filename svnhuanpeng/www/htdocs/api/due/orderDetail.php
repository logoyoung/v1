<?php

/**
 * 订单详情
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
use service\due\DueTagsService;
use service\user\UserDataService;
use service\common\UploadImagesCommon;
use service\due\DueActivityService;

/**
 * 创建订单
 */
class orderDetail extends ApiCommon {

    public $DueCommentService = null;
    public $DueCertService = null;
    public $DueOrderService = null;
    public $DueTagsService = null;
    public $param = [
        'orderId' => ['name' => 'orderId', 'default' => '0', 'rule' => self::PARAM_RULE_02_GT_0],
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

    public function initTagsService() {
        $this->DueTagsService = new DueTagsService();
    }

    public function initCheck() {
        $this->checkIsLogin(true);
    }

    /**
     * 执行接口
     */
    public function action() {
        $data = self::getParam($this->param, TRUE);
        $orderInfo = $this->DueOrderService->getOrderByOrderId($data['orderId']);
        if (empty($orderInfo)) {
            $this->resultData = [];
            $this->resultData['code'] = DueOrderService::ERROR_CODE_13;
            $this->resultData['desc'] = DueOrderService::$error_info[$this->resultData['code']];
            render_json($this->resultData, isset($this->resultData['code']) ? $this->resultData['code'] : 0);
        }
        if (!in_array($this->uid, [$orderInfo['uid'], $orderInfo['cert_uid']])) {
            error2(-8006);
        }
        $orderDetailInfo = $this->DueOrderService->getOrderDetailByOrderId($data['orderId']);
        $roleType = $orderInfo['uid'] == $this->uid ? lib\due\DueOrder::ORDER_USER_ROLE_TYPE_01_USER : lib\due\DueOrder::ORDER_USER_ROLE_TYPE_02_ANCHOR;
        if ($roleType == 2) {
            $log = $this->DueOrderService->getOrderLog($data['orderId'], \lib\due\DueOrder::ORDER_STATUS_050_ANCHOR_ACCEPTING_ORDER);
            $orderTime = $log['ctime'];
        }
        $skills = $this->DueCertService->getSkillBySkillId(['skillId' => $orderDetailInfo['skill_id']]);
        if (!empty($skills) && isset($skills[0])) {
            $skillInfo = $skills[0];
        } else {
            $this->resultData = [];
            $this->resultData['code'] = -1;
            $this->resultData['desc'] = '主播技能不存在';
            render_json($this->resultData, isset($this->resultData['code']) ? $this->resultData['code'] : 0);
        }
        $user = new UserDataService();
        if ($roleType == lib\due\DueOrder::ORDER_USER_ROLE_TYPE_01_USER) {
            $user->setUid($orderInfo['cert_uid']);
        } else {
            $user->setUid($orderInfo['uid']);
        }
        $userInfo = $user->getUserInfo();
        $orderStatusMessage = DueOrderService::getOrderStatusMessage($orderInfo['status'], $roleType, $orderInfo['reason']);
        $certInfo = $this->DueCertService->getCertInfoByCertIds($skillInfo['cert_id']);
        $pic = explode(',', $certInfo[$skillInfo['cert_id']]['pic_urls']);
        $gamePic = isset($pic[0]) ? UploadImagesCommon::getImageDomainUrl() . $pic[0] : '';
        $orderTime = isset($orderTime) ? $orderTime : $orderInfo['ctime'];
        $statusDis = $this->DueOrderService->getOrderRongCloudStatusMessage($orderInfo['status']);
        $res = [
            ### [ 状态 ]
            'uid' => $orderInfo['uid'],
            'certUid' => $orderInfo['cert_uid'],
            'certId' => $orderInfo['cert_id'],
            'status' => $orderInfo['status'],
            'statusDes' => $orderStatusMessage['order'],
            'reason' => $orderStatusMessage['reason'],
            ### [主播 及 订单内容信息]
            'userNick' => $userInfo['nick'],
            'userPic' => $userInfo['pic'],
            'skillId' => $skillInfo['skillId'],
            'gameId' => $skillInfo['game_id'],
            'gameName' => $this->DueCertService->getGameNameByGameId($skillInfo['game_id']),
            'gamePic' => $gamePic,
            'skillPrice' => $skillInfo['price'],
            'unit' => DueCertService::getUnitName($skillInfo['unit']),
            'playTime' => self::getFormatDayTime($orderDetailInfo['start_time']),
            'playTimeStamp' => $orderDetailInfo['start_time'],
            'num' => $orderDetailInfo['number'],
            'memo' => $orderInfo['memo'],
            'roleType' => $roleType,
            ### [订单支付信息]
            'orderId' => $data['orderId'],
            'orderTime' => $orderTime,
            'orderTimeStamp' => strtotime($orderTime),
            'orderAmount' => $orderInfo['amount'],
            'orderRealAmount' => $orderInfo['real_amount'],
            'orderDiscount' => $orderInfo['discount'],
            'orderIncome' => $this->DueOrderService->myIncome($orderInfo),
            'statusCode' => $statusDis['code'],
            'payStatus' => $orderStatusMessage['pay'],
            ### [isHaveComment]
            'canComment' => $this->DueOrderService->getCanComment($this->uid, $orderInfo),
            'commentStar' => '',
            'commentContent' => '',
            'tagIds' => [],
            ### 分享信息
            'shareSourceId' => '0',
            'shareActivityId' => '0',
        ];
        ### 分享信息
        if ($roleType == lib\due\DueOrder::ORDER_USER_ROLE_TYPE_01_USER) {
            $avtivity = new DueActivityService();
            $isShare = $avtivity->publicCheckSourceId($this->uid, $data['orderId'], DueActivityService::ACTIVITY_TYPE_02_ORDER);
            if ($isShare) {
                $res['shareSourceId'] = $isShare['sourceId'];
                $res['shareActivityId'] = $isShare['activityId'];
            }
        }
        ### [评论]

        if ($orderInfo['comment'] == \lib\due\DueOrder::ORDER_COMMENT_STATUS_01_HAVE_COMMENT
//                && in_array($orderInfo['status'], \lib\due\DueOrder::$do_map[\lib\due\DueOrder::ORDER_STATUS_101_ORDER_CANCEL_AND_REFUND])
        ) {
            $comment = $this->DueCommentService->getCommentByOrderIdAndUid($orderInfo['uid'], $data['orderId'])[0];
            $res['commentStar'] = $comment['star'] / 2;
            $res['commentContent'] = $comment['comment'];
            $res['tagIds'] = $this->DueTagsService->getTagsByids($comment['tag_ids']);
        }
        if ($res) {
            $this->resultData = $res;
        } else {
            $this->resultData['code'] = $res;
            $this->resultData['desc'] = DueOrderService::$error_info[$res];
        }
        render_json($this->resultData, isset($this->resultData['code']) ? $this->resultData['code'] : 0);
    }

}

$do = new orderDetail();
$do->action();
