<?php
namespace system;

/**
 *  锁管理工具
 */
class LockHelper
{

    private static $redis;

    /**
     * 设置redis实列
     * @param redis $driver
     */
    public static function setRedis($driver)
    {
        self::$redis = $driver;
    }

    /**
     * 获取锁
     * @param  string  $name 锁名称
     * @param  int $lockTime 锁定时间 单位秒
     * @return boolean 成功返回true
     */
    public static function getLock($name, $lockTime = 12)
    {
        $ct       = time();
        $lockTime = $ct + (int) $lockTime;
        $result   = self::$redis->setNx($name, $lockTime);
        if ($result === true )
        {
            self::$redis->expire($name, $lockTime);
            return true;
        }

        $et = (int) self::$redis->get($name);
        if($et === 0 || $et >= $ct )
        {
            return false;
        }

        $et = self::$redis->getSet($name,$lockTime);
        if(!$et || $et >= $ct)
        {
            return false;
        }

        return true;
    }

    /**
     * 释放锁
     * @param  $name 锁名称
     * @return boolean
     */
    public static function unLock($name)
    {
        return self::$redis->delete($name);
    }

}