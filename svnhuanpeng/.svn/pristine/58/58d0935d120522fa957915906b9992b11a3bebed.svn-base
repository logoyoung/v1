<?php
// +----------------------------------------------------------------------
// 帐户安全
// 检测IP访问
// +----------------------------------------------------------------------

namespace HP\Secure;
use HP\Cache\CacheKey;
class Account
{
    static public function loginerror($uid){
        $info = S(CacheKey::SECURE_ACCOUNT.$uid);
        $info or $info=['c'=>0];
        $info['c']++;
        
        if($info['c']>4){
            $info['l'] = time()+3600;
            $info['m'] = '一小时';
            $info['c'] = 0;
            Iprule::inc(Iprule::TYPE_LOGIN,1);
        }
        S(CacheKey::SECURE_ACCOUNT.$uid,$info,['expire'=>86400]);
        $info['remark'] = $info['c']>0?'还可输入'.strval(5-$info['c']).'次':'您的帐户已被锁定'.$info['m'];
        return $info;
    }
    static public function checknum($uid){
        $info = S(CacheKey::SECURE_ACCOUNT.$uid);
        return 5-$info['c'];
    }
    static public function checklock($uid){
        $info = S(CacheKey::SECURE_ACCOUNT.$uid);
        if($info['l'] and $info['l']>time()){
            return $info;
        }
    }
    static public function unlock($uid){
        S(CacheKey::SECURE_ACCOUNT.$uid,null);
    }
}
