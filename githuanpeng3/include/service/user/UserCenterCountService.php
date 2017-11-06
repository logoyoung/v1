<?php

namespace service\user;

use Exception;
use system\RedisHelper;

/**
 * @author liupeng@6.cn
 * 用户中心,tag计数
 */
class UserCenterCountService {

    const USER_CENTER_HASH_CACHE_KEY = 'usercenterservice_cache_key_0915_';

    ## 字段表 ##
    /**
     * 优惠券计数
     */
    const HASH_TABLE_FIELD_COUPON_NUM = 'center_tag_coupon_num';

    /**
     * 背包计数
     */
    const HASH_TABLE_FIELD_BACKPACK_NUM = 'center_tag_backpack_num';

    private static $redis = null;

    public static function getRedis(): \system\RedisConnection {
        if (is_null(self::$redis)) {
            self::$redis = RedisHelper::getInstance('huanpeng');
        }
        return self::$redis;
    }

    public static $dataTemplate = [
        self::HASH_TABLE_FIELD_COUPON_NUM   => '0',
        self::HASH_TABLE_FIELD_BACKPACK_NUM => '0',
    ];

    public static function getHashKey($uid) {
        $key = self::USER_CENTER_HASH_CACHE_KEY . $uid;
        self::_init($key);
        return $key;
    }

    public function setExpire($key, $exprie = 86400) {
        return self::getRedis()->expire($key, $exprie);
    }

    public static function setValue($uid, $field, $value = 0) {
        $key = self::getHashKey($uid);
        return self::getRedis()->hSet($key, $field, $value);
    }

    public static function getValue($uid, $field) {
        $key = self::getHashKey($uid);
        $res = self::getRedis()->hGet($key, $field);
        if ($res === FALSE) {
            return self::setValue($uid, self::$dataTemplate[$field]);
        }
        return $res;
    }

    public static function addValue($uid, $field, $num = 1) {
        $key = self::getHashKey($uid);
        $value = self::getRedis()->hIncrBy($key, $field, $num);
        if ($value < 0) {
            self::setValue($uid, $field);
        }
        return $value;
    }

    private static function _init($key) {
        $isExists = self::getRedis()->exists($key);
        if (!$isExists) {
            return self::getRedis()->hMSet($key, self::$dataTemplate);
        } else {
            return TRUE;
        }
    }

}
