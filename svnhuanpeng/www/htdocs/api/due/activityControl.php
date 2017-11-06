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

ini_set("display_errors", 1);

use service\common\ApiCommon;
use service\due\DueActivityConfigService;

/**
 * 创建订单
 */
class activityControl extends ApiCommon {

    public $service = null;
    public $param = [
        'action' => ['name' => 'action', 'default' => '', 'rule' => self::PARAM_RULE_01_NOT_NULL],
        'id' => ['name' => 'id', 'default' => '1'],
        'cid'=> ['name' => 'cid', 'default' => '2'],
    ];

    
    
    public function initService() {
        $this->service = new DueActivityConfigService();
    }

    public function initCheck() {
        $this->checkIsLogin(true);
    }

    /**
     * 执行接口
     */
    public function action() {
        $data = self::getParam($this->param, TRUE);
        $action = $data['action'];
        $res  = 0;
        switch ($action) {
            case 'up':
                $res = $this->service->updateRule($data['id']);
                $res1 = $this->service->updateUseCouponRule($data['cid']);
                break;
            default:
                break;
        }
        if ($res) {
            $this->resultData['message'] = 'exec success';
        } else {
            $this->resultData['message'] = 'exec fail';
        }
        if (!$res1) {
            $this->resultData['message'] = $this->resultData['message'].'<-->优惠券配置失败';
        }
        render_json($this->resultData);
    }

}

$do = new activityControl();
$do->action();
