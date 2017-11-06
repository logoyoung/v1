<?php

/**
 * 活动配置手动更新脚本
 * @encoding utf-8
 * @author liupeng@6.cn
 * @since
 * @version 1.0
 * @Description
 */
include '../../../include/init.php';

use service\user\UserDataService;
use service\common\ApiCommon;
use service\due\DueActivityService;
use lib\User;
use Exception;

/**
 * 创建订单
 */
class getActivity extends ApiCommon {

    public $service = null;
    public $param = [
        'activityId' => ['name' => 'activityId', 'default' => '', 'rule' => self::PARAM_RULE_01_NOT_NULL],
        'shareUuid'  => ['name' => 'receiveUuid', 'default' => ''],
        'phone'      => ['name' => 'phone', 'default' => ''],
    ];

    public function initService() {
        $this->service = new DueActivityService();
    }

    public function initCheck() {
//        $this->checkIsLogin(true);
    }

    /**
     * 执行接口
     */
    public function action() {
        try {
            $param = self::getParam($this->param, TRUE, FALSE);
            $adata = $this->service->getActivityDataById($param['activityId']);

            if ($adata[0]['type'] == DueActivityService::ACTIVITY_TYPE_05_PROMOTION &&  TRUE === checkMobile($param['phone'])) {
                $userModel = new User();
                $uid = $userModel->getUidByPhoneNumber($param['phone']);
                if($uid>0){
                      throw new Exception(errorDesc(-8033), -8033);
                }
            }

            if ($adata[0]['type'] == DueActivityService::ACTIVITY_TYPE_01_LOGIN) {
                $this->checkIsLogin(true);
                $res = $this->UserDataService->setUserInfoDetail(UserDataService::USER_DATA_ALL)->getUserInfo();
                $this->checkIsAnchor();
                $phone = $res['phone'];
//                $checkRes = checkMobile($res['phone']);
//                if ($checkRes !== TRUE) {
//                    throw new Exception(errorDesc(-33), -33);
//                }
            } else {
                $phone = $param['phone'];
                $checkRes = checkMobile($phone);
                if ($checkRes !== TRUE) {
                    throw new Exception(errorDesc(-4058), -4058);
                }
                $userModel = new User();
                $uid = $userModel->getUidByPhoneNumber($phone);
                if ($uid > 0) {
                    $this->uid = $uid;
                    $this->UserDataService->setUid($this->uid);
                    $this->checkIsAnchor();
                } else {
                    $this->uid = '';
                    $this->encpass = '';
                    //新用户
                    //新用户靠事件去同步。
                }
            }
            $this->service->phone = $phone;
            if (empty($this->uid) && empty($param['phone'])) {
                throw new Exception(errorDesc(-4013), -4013);
            }
            $result = $this->service->getActivityCoupon2($this->uid, $this->isAnchor, $param['activityId'], $param['shareUuid'], $phone);

            if (!empty($result)) {
                $data['receiveResult'] = TRUE;
                $data['ctimeStamp'] = strtotime($result['ctime']);
                $data['stimeStamp'] = strtotime($result['stime']);
                $data['etimeStamp'] = strtotime($result['etime']);
                $data['activityId'] = $result['activity_id'];
                $data['shareUuid'] = $result['share_uuid'];
                $data['couponId'] = $result['coupon_id'];
                $data['couponCode'] = $result['code'];
                $data['typeId'] = $result['type'];
                $data['uid'] = $result['uid'];
                $data['phone'] = strval($result['phone']);
                $data['price'] = intval($result['price']);
            }


            $this->resultData = $data;
        } catch (Exception $exc) {
            $this->resultData = [];
            render_json($exc->getMessage(), $exc->getCode(), 2);
        }
        render_json($this->resultData, isset($this->resultData['code']) ? $this->resultData['code'] : 0);
    }

}

$do = new getActivity();
$do->action();
