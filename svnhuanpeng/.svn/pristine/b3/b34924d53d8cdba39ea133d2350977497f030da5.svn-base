<?php

/**
 * 分享优惠券
 * @encoding utf-8
 * @author liupeng@6.cn
 * @since
 * @version 1.0
 * @Description
 */
include '../../../include/init.php';

ini_set("display_errors", 1);

use service\common\ApiCommon;
use service\due\DueActivityService;
use Exception;

/**
 * 创建订单
 */
class shareCoupon extends ApiCommon {

    public $service = null;
    public $param = [
        'activityId' => ['name' => 'activityId', 'default' => '', 'rule' => self::PARAM_RULE_01_NOT_NULL],
        'sourceId' => ['name' => 'sourceId', 'default' => '', 'rule' => self::PARAM_RULE_01_NOT_NULL],
    ];

    public function initService() {
        $this->service = new DueActivityService();
    }

    public function initCheck() {
        $this->checkIsLogin(true);
    }

    /**
     * 执行接口
     */
    public function action() {


        try {
            $param = self::getParam($this->param, TRUE);
            $result = $this->service->shareCoupon($this->uid, $param['sourceId'], $param['activityId'], $this->isAnchor);
            $this->resultData['shareUuid'] = $result['share_uuid'];
            $this->resultData['activityId'] = $result['aid'];
//            $this->resultData['title'] = $result['name'];
            $this->resultData['title'] = '欢朋直播千万约玩券，等你来玩！';
//            $this->resultData['describe'] = $result['content'];
            $this->resultData['describe'] = "只要敢下单，男神女神由你摆布。点击领取约玩券>>";
            $conf = $GLOBALS['env-def'][$GLOBALS['env']];
            $this->resultData['pic'] =DOMAIN_PROTOCOL . $conf['domain-img'] .  $result['pic'];
            $this->resultData['shareNumber'] = intval($result['share_number']);
            $this->resultData['typeId'] = $result['type'];
            $this->resultData['shareUuid'] = $result['share_uuid'];
            
            $domain = DOMAIN_PROTOCOL . $conf['domain'] . '/';
            $linkPrefix = $domain . "mobile/coupon/index.html?receiveUuid=" . $result['share_uuid'];
            $linkPrefix .= "&activityId=" . $result['aid'];
            $this->resultData['shareLink'] = $linkPrefix;
        } catch (Exception $exc) {
            $this->resultData = [];
            render_json($exc->getMessage(), $exc->getCode(), 2);
        }


        render_json($this->resultData);
    }

}

$do = new shareCoupon();
$do->action();
