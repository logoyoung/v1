<?php
// +----------------------------------------------------------------------
// | 项目启动时检测
// +----------------------------------------------------------------------

namespace Common\Behavior;

class HPinitBehavior extends \Think\Behavior
{
    public function run(&$params)
    {
        if(IS_CLI)return;
        
        //内网检测
        if(C('INTRA_IP') || \HP\Secure\Ipsafety::checkintraByIp((get_client_ip()))){
            C('INTRA_IP',1);
        }else{
            C('INTRA_IP',0);
            //黑名单
            if(\HP\Secure\Ipsafety::checkByIp(get_client_ip())){
                require (APP_PATH.'Www/View/Base/error.html');die;
            }
        }
    }
}
