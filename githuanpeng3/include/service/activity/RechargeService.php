<?php

/**
 * Created by NetBeans.
 * User: yalongSun <yalong_2017@6.cn> 
 * Desc: 充值活动
 */

namespace service\activity;

use lib\activity\RechargeLib;

class RechargeService {
    private $librecharge = null;
    public function __construct() {
        if(is_null($this->librecharge))
            $this->librecharge = new RechargeLib;
    }
    //首充活动信息拉去
    public function onceDayActivity(int $activity_id){
        $data = $this->librecharge->_onceDayActivity($activity_id);
        return !empty($data) ? $data : false; 
    }
    static public function getHttpHost(){
        return DOMAIN_PROTOCOL.$GLOBALS['env-def'][$GLOBALS['env']]['domain-img']; //后端上传的图片域名;
    }
}
