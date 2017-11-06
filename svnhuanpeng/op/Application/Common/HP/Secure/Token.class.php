<?php
// +----------------------------------------------------------------------
// 安全策略
// 对敏感性功能做安全防止
// 改进 不放入session中了
// +----------------------------------------------------------------------

namespace HP\Secure;

class Token
{
    const KEY_PREFIX='token.';
    const RAND_LIMIT = 3;
    const CHECK_LENTH = 26;

    /**
     * 获取一个token
     * @param type 动作的key
     * @return token md5
     */
    static public function get($key)
    {
        $key = md5(get_uid().'#'.self::KEY_PREFIX.$key.'#'.date('Ymd',strtotime('-'.mt_rand(0, self::RAND_LIMIT-1).' day')));
        return substr($key,0,self::CHECK_LENTH).substr(\HP\Util\StringTool::getRandMd5(),0,32-self::CHECK_LENTH);
    }
    /**
     * 验证一个token
     * @param type  动作的key
     * @param type  值
     * @return 通过返回true 不通过返回false
     */
    static public function check($key,$value,$clear=false)
    {
        if(strlen($value)==32){
            for($i=self::RAND_LIMIT;$i>=0;$i--){
                if(strncmp($value, md5(get_uid().'#'.self::KEY_PREFIX.$key.'#'.date('Ymd',strtotime('-'.$i.' day'))), self::CHECK_LENTH)===0){
                    return true;
                }
            }
        }
        self::error($key.':'.$value);
        return false;
    }
    /**
     * 记录一个token错误的操作
     */
    static public function error($str)
    {
        return \HP\Log\Log::token($str);
    }
}
