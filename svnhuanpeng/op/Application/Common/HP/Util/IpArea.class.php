<?php

// +----------------------------------------------------------------------
// coka,16-8-12 下午2:06,通过ip获得地区
// +----------------------------------------------------------------------

namespace HP\Util;

class IpArea{

    const REQUEST_URL = 'https://www.baidu.com/s?ie=utf-8&wd=';

    static public function getArea($ip = ''){
        if($ip){
            if(!self::isIp($ip)){
                return;
            }
            $url = self::REQUEST_URL . $ip;
            $header = array(
                'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.146 Safari/537.36'
            );
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            // 执行
            $content = curl_exec($ch);
            curl_close($ch);
            $content = self::str_substr('<div class="c-span21 c-span-last op-ip-detail">','</div>',$content);
            $content = self::str_substr('<table><tr><td>','</td></tr></table>',$content);
            $content = self::str_repalcestr('<span class="c-gap-right">','</span>',$content);
            return ['ip' => $ip,'area' => str_replace('&nbsp;','',$content)];
        }else{
            return;
        }
    }

    /**
     * 验证手机号是否正确
     */
    public static function isIp($ip){
        if(filter_var($ip,FILTER_VALIDATE_IP)){
            return true;
        }else{
            return false;
        }
    }

    public static function str_substr($start,$end,$str){ // 字符串截取函数      
        $temp = explode($start,$str,2);
        $content = explode($end,$temp[1],2);
        return $content[0];
    }

    public static function str_repalcestr($start,$end,$str){
        $temp = explode($start,$str,2);
        $content = explode($end,$temp[1],2);
        return trim($content[1]);
    }

}
