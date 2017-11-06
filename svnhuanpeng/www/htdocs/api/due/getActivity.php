<?php

/**
 * 获取活动
 * @encoding utf-8
 * @author liupeng@6.cn
 * @since
 * @version 1.0
 * @Description
 */
include '../../../include/init.php';
//ini_set("display_errors", 1);
use service\common\ApiCommon;
use service\due\DueActivityService;
use service\user\UserDataService;
use Exception;

/**
 * 创建订单
 */
class getActivity extends ApiCommon {

    public $service = null;
    public $param = [
        'typeId' => ['name' => 'typeId', 'default' => '', 'rule' => self::PARAM_RULE_01_NOT_NULL],
    ];

    public function initService() {
        $this->service = new DueActivityService();
    }

    public function initCheck() {
      
    }

    /**
     * 执行接口
     */
    public function action() {

        try {
            $param = self::getParam($this->param, TRUE);
            if($this->checkIsLogin()){
                $res = $this->UserDataService->setUserInfoDetail(UserDataService::USER_DATA_ALL)->getUserInfo();
                $phone = $res['phone'];
            }else{
                $phone = '';
            }
            
            $this->service->phone = $phone;
            $result = $this->service->checkActivity($param['typeId'], $this->uid, $this->isAnchor);
            $this->resultData['activityList'] = [];
            foreach ($result as $key => $row) {
                $this->resultData['activityList'][$key]['activityId'] = $row['aid'];
                $this->resultData['activityList'][$key]['title'] = $row['name'];
                $this->resultData['activityList'][$key]['describe'] = $row['content'];
                $this->resultData['activityList'][$key]['pic'] = $row['pic'];
                $this->resultData['activityList'][$key]['shareNumber'] = $row['share_number'];
                $this->resultData['activityList'][$key]['typeId'] = $row['type'];
                $this->resultData['activityList'][$key]['ctimeStamp'] = strtotime($row['ctime']);
                $this->resultData['activityList'][$key]['stimeStamp'] = strtotime($row['stime']);
                $this->resultData['activityList'][$key]['etimeStamp'] = strtotime($row['etime']);
                /**
                 * @todo add img '  copon number
                 */
            }
        } catch (Exception $exc) {
            $this->resultData = [];
            $this->resultData['code'] = $exc->getCode();
            $this->resultData['desc'] = $exc->getMessage();
            render_json($this->resultData, $this->resultData['code'], 2);
        }
        render_json($this->resultData);
    }

}

$do = new getActivity();
$do->action();
