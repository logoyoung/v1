<?php

/**
 * 下单聊天页面头部
 * @encoding utf-8
 * @author liupeng@6.cn
 * @since
 * @version 1.0
 * @Description
 */
include '../../../include/init.php';

use service\common\ApiCommon;
use service\common\UploadImagesCommon;
use service\due\DueOrderService;
use service\due\DueCertService;
use service\user\UserDataService;

/**
 * 创建订单
 */
class orderTopDetail extends ApiCommon {

    public $orderService = null;
    public $param = [
        'otherUid' => ['name' => 'otherUid', 'default' => '0', 'rule' => self::PARAM_RULE_02_GT_0],
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
        ###[ order detail ]##
        $orderInfo = $this->orderService->getOrderForTwoPersons($this->uid, $data['otherUid'], 1);
        if (!empty($orderInfo)) {
            $reCode = $this->orderService->getOrderRongCloudStatusMessage($orderInfo['status']);
            if ($reCode['code'] == 0) {
                //非正常订单,默认订单不存在
                $orderInfo = NULL;
            }
        }
        if (empty($orderInfo)) {
            //两人之间没有订单
            ###[ game info ]###
            /**
             * @todo 角色无法区分,角色决定了有哪些操作:业务逻辑错误
             */
            //默认当前访问用户是下单用户

            $this->resultData['roleType'] = '0';
            $this->resultData['orderId'] = '';
            $this->resultData['num'] = '0';
            $this->resultData['playTime'] = '';
            $this->resultData['memo'] = '';
            $this->resultData['canConmment'] = $this->orderService->getCanComment($this->uid, []);

            $certObj = new DueCertService();
            $certUid = $data['otherUid'];
            $certObj->setUid($data['otherUid']);

            $roleType = \lib\due\DueOrder::ORDER_USER_ROLE_TYPE_01_USER;
            $skillInfo = null;
            $skillInfos = $certObj->getSkillByUid();
            foreach ($skillInfos as $value) {
                if($value['switch'] != DueCertService::SKILL_SWITCH_OFF){
                    $skillInfo = $value;
                    break;
                }
            }
            $this->resultData['roleType'] = $roleType;
            if (empty($skillInfo) || $skillInfo['switch'] == DueCertService::SKILL_SWITCH_OFF) {
                $this->resultData = [];
                $this->resultData['code'] = -8007;
                $this->resultData['desc'] = errorDesc($this->resultData['code']);

                render_json($this->resultData, isset($this->resultData['code']) ? $this->resultData['code'] : 0);
            }
            $certUserInfoObj = new UserDataService();
            $certUserInfoObj->setUid($skillInfo['uid']);

            $this->resultData['skillId'] = $skillInfo['skillId'];
            $this->resultData['gameId'] = $skillInfo['game_id'];
            $this->resultData['playGameName'] = $certObj->getGameNameByGameId($skillInfo['game_id']);
            $this->resultData['price'] = $skillInfo['price'];
            $this->resultData['unit'] = DueCertService::getUnitName($skillInfo['unit']);
            $this->resultData['orderTotal'] = $certObj->getOrderTotalBySkillID($skillInfo['skillId'])[0]['order_total'];
            $certId = $skillInfo['cert_id'];
            $this->resultData['certId'] = $certId;
            $certInfo = $certObj->getCertInfoByCertIds($certId);
            if (!empty($certInfo[$certId])) {
                $pic = explode(',', $certInfo[$certId]['pic_urls']);
            } else {
                $pic = [];
            }
            $this->resultData['gamePic'] = isset($pic[0]) ? UploadImagesCommon::getImageDomainUrl() . $pic[0] : '';

            $this->resultData['avgScore'] = intval($skillInfo['avg_score']) / 2;
            ##[ order status ]###
            $status = $this->orderService->getOrderRongCloudStatusMessage(0);
            $this->resultData['statusCode'] = $status['code'];
            $this->resultData['statusDesc'] = $status['desc'];

            if ($status['code'] == 0) {

//                $this->resultData['detailStatusCode'] = 0;
                $this->resultData['statusDesc'] = $this->orderService->getOrderReturnStatusMessage(0);
                $this->resultData['reason'] = $orderInfo['reason'];
            } else {
                $this->resultData['reason'] = '';
            }
            ## [ order action  ]
            $this->resultData['userAction'] = $this->orderService->getUserOrderAction($roleType, 0);
            render_json($this->resultData, isset($this->resultData['code']) ? $this->resultData['code'] : 0);
        }
        ## order  is not null
        $this->resultData['orderId'] = $data['orderId'] = $orderInfo['order_id'];
        $orderDetailInfo = $this->orderService->getOrderDetailByOrderId($data['orderId']);
        $this->resultData['unit'] = DueCertService::getUnitName($orderDetailInfo['unit']);
        $this->resultData['num'] = $orderDetailInfo['number'];
        $this->resultData['playTime'] = self::getFormatDayTime($orderDetailInfo['start_time']);
        $this->resultData['playTimeStamp'] = $orderDetailInfo['start_time'];
        $this->resultData['memo'] = $orderInfo['memo'];
        $this->resultData['canConmment'] = $this->orderService->getCanComment($this->uid, $orderInfo);
        ### [ init ]
        $this->resultData['roleType'] = 0;
        
        if ($orderInfo['uid'] == $this->uid) {
            $roleType = \lib\due\DueOrder::ORDER_USER_ROLE_TYPE_01_USER;
        } elseif ($orderInfo['cert_uid'] == $this->uid) {
            $roleType = \lib\due\DueOrder::ORDER_USER_ROLE_TYPE_02_ANCHOR;
        } else {
            $this->resultData['code'] = DueOrderService::ERROR_CODE_14;
            $this->resultData['desc'] = DueOrderService::$error_info[DueOrderService::ERROR_CODE_14];
            render_json($this->resultData, isset($this->resultData['code']) ? $this->resultData['code'] : 0);
        }

        $this->resultData['roleType'] = $roleType;
        ###[ game info ]###
        $certObj = new DueCertService();
        $skillInfo = $certObj->getSkillBySkillId(['skillId' => $orderDetailInfo['skill_id']]);
        $certObj->setUid($skillInfo[0]['uid']);

        $certUserInfoObj = new UserDataService();
        $certUserInfoObj->setUid($skillInfo[0]['uid']);
        $this->resultData['skillId'] = $skillInfo['skillId'];
        $this->resultData['gameId'] = $skillInfo[0]['game_id'];
        $this->resultData['playGameName'] = $certObj->getGameNameByGameId($skillInfo[0]['game_id']);
        $this->resultData['price'] = $skillInfo[0]['price'];
        $this->resultData['orderTotal'] = $certObj->getOrderTotalBySkillID($orderDetailInfo['skill_id'])[0]['order_total'];

        $certId = $skillInfo[0]['cert_id'];
        $this->resultData['certId'] = $orderInfo['cert_id'];
        $certInfo = $certObj->getCertInfoByCertIds($certId);
        if (!empty($certInfo[$certId])) {
            $pic = explode(',', $certInfo[$certId]['pic_urls']);
        } else {
            $pic = [];
        }
        $this->resultData['gamePic'] = isset($pic[0]) ? UploadImagesCommon::getImageDomainUrl() . $pic[0] : '';

        $this->resultData['avgScore'] = '0';
        ##[ order status ]###
        $status = $this->orderService->getOrderRongCloudStatusMessage($orderInfo['status']);
        $this->resultData['statusCode'] = $status['code'];
        $this->resultData['statusDesc'] = $status['desc'];

        if ($status['code'] == 0) {
//            $this->resultData['detailStatusCode'] = $orderInfo['status'];
            $this->resultData['statusDesc'] = $this->orderService->getOrderReturnStatusMessage($orderInfo['status']);
            $this->resultData['reason'] = $orderInfo['reason'];
        } else {
            $this->resultData['reason'] = '';
        }
        ## [ order action  ]
        $this->resultData['userAction'] = $this->orderService->getUserOrderAction($roleType, $orderInfo['status']);

        render_json($this->resultData, isset($this->resultData['code']) ? $this->resultData['code'] : 0);
    }

}

$do = new orderTopDetail();
$do->action();
