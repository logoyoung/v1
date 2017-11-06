<?php
// +----------------------------------------------------------------------
// IP安全策略
// 检测IP访问
// +----------------------------------------------------------------------

namespace HP\Secure;

class Iprule
{
    const TYPE_REGISTER = 1;
    const TYPE_LOGIN = 2;
    const TYPE_SMSCODE = 3;
    const TYPE_IMGCODE = 5;
    const TYPE_INVALIDURL = 4;
    
    static protected function getPeriod(){
        return date('Ym');
    }

    static public function inc($type,$mark,$ip=null,$period=null){
        return self::dosql($type,$mark,$ip,$period);
    }
    static public function dec($type,$mark,$ip=null,$period=null){
        return self::dosql($type,-$mark,$ip,$period);
    }
    static protected function dosql($type,$mark,$ip,$period){
        if(!is_numeric($type)||$type<1){
            return;
        }
        if(!is_numeric($mark)){
            return;
        }
        is_numeric($ip) or $ip=get_client_ip(1);
        is_numeric($period) or $period=self::getPeriod();
        $update_at = get_date();
        
        $dao = D('ipRule');
        $dao->execute("update `".$dao->getTableName()."` set mark=mark+{$mark},update_at='{$update_at}' where ip='{$ip}' and period='{$period}' and type='{$type}' limit 1")
        or $dao->add(compact('ip','period','type','mark','update_at'));
    }
}
