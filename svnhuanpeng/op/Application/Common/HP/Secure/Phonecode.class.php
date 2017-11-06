<?php
// +----------------------------------------------------------------------
// 安全策略
// 对敏感性功能做安全防止
// +----------------------------------------------------------------------

namespace HP\Secure;

class Phonecode
{
    const KEY_PREFIX='phonecode.';

    /**
     * 获取一个token
     * @param type  手机号
     * @param type 存放Cache的key
     * @return token md5
     */
    static public function get($key,$phone)
    {
        $key = self::KEY_PREFIX.session_id().$key;
        $token = mt_rand(100000,999999);
        S($key,$phone.':'.$token,['expire'=>600]);
        \HP\Log\Log::sms(get_uid().'##'.$key.'##'.$phone);
        return $token;
    }
    /**
     * 验证一个token
     * @param type  手机号
     * @param type  值
     * @param type  存放Cache的key
     * @return 通过返回true 不通过返回false
     */
    static public function check($phone,$value,$key=null,$clear=false)
    {
        if(empty($key)){
            $key = strtolower(parse_url($_SERVER['HTTP_REFERER'])['path']);
        }
        $key = self::KEY_PREFIX.session_id().$key;
        if(empty($value) || $phone.':'.$value!=S($key)){
            self::error(__CLASS__.':'.$key.':'.S($key).':'.$value);
            return false;
        }else{
            $clear and S($key,null);
            return true;
        }
    }
    /**
     * 记录一个token错误的操作
     */
    static public function error($str)
    {
        return \HP\Log\Log::token($str);
    }
}
