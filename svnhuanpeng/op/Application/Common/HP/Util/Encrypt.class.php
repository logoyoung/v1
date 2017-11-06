<?php

// +----------------------------------------------------------------------
// 对称加密解密函数
// +----------------------------------------------------------------------

namespace HP\Util;

class Encrypt {

    const KEY = 'HP!@#$%qzgr)(*&^ddx';
    
    /*
     * 加密一个id方法
     */
    public static function encodeId($id) {
        $rand = sha1($id);
        return substr($rand,11,3).strrev(dechex($id.$id%8)).substr($rand,21,2);
    }
    /*
     * 解密一个id方法
     */
    public static function decodeId($string) {
        return substr(hexdec(strrev(substr($string,3,-2))),0,-1);
    }

    /**
     * 加密方法
     * param: $string string //需要加密的字符串
     * param: $expiry int //解密过期时间，解密时间和加密字符串的时间超过$expiry之后，解密方法失效
     */
    public static function encode($string, $expiry = 86400) {
        return self::authcode($string, 'ENCODE', $expiry);
    }

    /**
     * 解密方法
     * param: $string string //需要解密的字符串
     */
    public static function decode($string) {
        return self::authcode($string, 'DECODE');
    }

    /**
     * 加密，解密字符串函数
     * param: $string string //需要加密或解密的字符串
     * param: $operation string //有ENCODE或DECODE两种模式，默认为DECODE即加密模式
     * param: $key string //加密的私钥
     * param: $expiry int //解密过期时间，解密时间和加密字符串的时间超过$expiry之后，解密方法失效
     */
    public static function authcode($string, $operation = 'DECODE', $expiry = 43200) {
        $ckey_length = 16;
        // 随机密钥长度 取值 0-32;
        // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
        // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
        // 当此值为 0 时，则不产生随机密钥


        $key = md5(self::KEY);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);

        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;

        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if ($operation == 'DECODE') {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc . str_replace('=', '', base64_encode($result));
        }
    }

}
