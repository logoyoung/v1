<?php

/**
 * 创建订单页面显示内容
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
use service\due\DueCouponService;

/**
 * 创建订单
 */
class preUserCreateOrder extends ApiCommon {

    public $orderService = null;
    public $param = [
        'uid' => ['name' => 'uid', 'default' => '0'],
        'certUid' => ['name' => 'certUid', 'default' => '0', 'rule' => self::PARAM_RULE_02_GT_0],
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
        ##[ anchor info ]##
        $certUserInfoObj = new UserDataService();
        $certUserInfoObj->setUid($data['certUid']);
        $certUserData = $certUserInfoObj->getUserInfo();
        $this->resultData['certUid'] = $data['certUid'];
        $this->resultData['certNick'] = $certUserData['nick'];
        $this->resultData['certPic'] = $certUserData['pic'];
        $couponObj = new DueCouponService();
        $couponObj->setUid($this->uid);
        $datas = $couponObj->getUnusedCouponList();
        $this->resultData['hasCoupon'] = !empty($datas) ? 1 : 0;
        ##[order limit]
        $time = time() + 600;
        $i = intval(date('i', $time) / 10);
        $playTime =  date("Y-m-d H:{$i}0:00", $time);
        $this->resultData['playTime'] = $playTime;
        $this->resultData['playTimeStamp'] = strtotime($playTime);
        $this->resultData['max'] = DueOrderService::PALY_MAX_NUMBER;
        ##[ cert info ]##
        $certObj = new DueCertService();
        $certObj->setUid($data['certUid']);
        $certInfo = $certObj->getCertByUid();
        $certInfoKeyUid = [];
        foreach ($certInfo as $rows) {
            $certInfoKeyUid[$rows['certId']] = $rows;
        }
        ##[ skill ]
        $skills = $certObj->getSkillByUid();
        $this->resultData['skillList']= [];
        foreach ($skills as $value) {
            ##[ check ]
            if(!isset($certInfoKeyUid[$value['cert_id']]) || DueCertService::setStatus($certInfoKeyUid[$value['cert_id']]['status']) != DueCertService::CERT_STATUS_PASS  || $value['switch'] ==DueCertService::SKILL_SWITCH_OFF ){
                continue;
            }
            ##[cert]
            $tmp['skillId'] = $value['skillId'];
            $tmp['gameId'] = $value['game_id'];
            $tmp['playGameName'] = $certObj->getGameNameByGameId($value['game_id']);
            $pic = explode(',', $certInfoKeyUid[$value['cert_id']]['pic_urls']);
            $tmp['gamePic'] = isset($pic[0]) ? UploadImagesCommon::getImageDomainUrl() . $pic[0] : '';
            ##[skills]
            $tmp['price'] = $value['price'];
            $tmp['unit'] = DueCertService::getUnitName($value['unit']);
            $this->resultData['skillList'][] = $tmp;
        }
        render_json($this->resultData, isset($this->resultData['code']) ? $this->resultData['code'] : 0);
    }

}

$do = new preUserCreateOrder();
$do->action();
