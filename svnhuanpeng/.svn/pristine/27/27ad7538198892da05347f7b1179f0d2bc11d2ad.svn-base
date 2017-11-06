<?php
// +----------------------------------------------------------------------
// IP安全策略
// 对IP的安全防护
// +----------------------------------------------------------------------

namespace HP\Secure;

class Ipsafety extends \HP\Cache\Proxy
{
    const CACHE_TIME=3600;

    //***********基础数据
    static public function flushByIp($ips)
    {
        return self::flushCache(\HP\Cache\CacheKey::SECURE_IPSAFETY,$ips);
    }
    static public function checkByIp($ips)
    {
        return self::QueryCache(\HP\Cache\CacheKey::SECURE_IPSAFETY,self::CACHE_TIME,$ips,__CLASS__.'::checkByIpFromDb');
    }
    static public function checkByIpFromDb($ips)
    {
        $data = D('blacklist')->where(['ip'=>['in',$ips],'status'=>['in','0,10']])->getField('ip,1');
        return $data;
    }
    
    //***********内网数据
    static public function flushintraByIp($ips)
    {
        return S(\HP\Cache\CacheKey::SECURE_IPSAFETY_INTRA,$ips,['expire'=>3666]);
    }
    static public function checkintraByIp($ip)
    {
        return strpos(S(\HP\Cache\CacheKey::SECURE_IPSAFETY_INTRA),$ip)!==false;
    }
}