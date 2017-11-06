<?php

// +----------------------------------------------------------------------
// 兼容bootcss分页
// +----------------------------------------------------------------------

namespace HP\Util;

class StringTool
{

    static public function guid()
    {
        if (function_exists('com_create_guid')) {
                return com_create_guid();
        } else {
            mt_srand((double) microtime() * 10000);
            $charid = strtoupper(md5(uniqid(rand(), true)));
            return substr($charid, 0, 8) . '-'
                    . substr($charid, 8, 4) . '-'
                    . substr($charid, 12, 4) . '-'
                    . substr($charid, 16, 4) . '-'
                    . substr($charid, 20, 12);
        }
    }
    
    static public function getRandMd5()
    {
        mt_srand((double) microtime() * 10000);
        return md5(uniqid(rand(), true));
    }

    static public function format_bytes($size) { 
        $units = array(' B', ' KB', ' MB', ' GB', ' TB'); 
        for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024; 
        return round($size, 2).$units[$i]; 
    }
    
    static public function privateRealname($str){
        return $str?substr($str,0,3).'**':'';
    }
    
    static public function privateRealnameOp($str){
        if(\HP\Op\Admin::checkAccessWithKey("full_string")) return $str ? $str : '';
        return $str?substr($str,0,3).'**':'';
    }
    /*
     * 身份证
     */
    static public function privateCard($str){
        return $str?substr($str,0,3).'**********'.substr($str,-3):'';
    }
    static public function privateCardOp($str){
        if(\HP\Op\Admin::checkAccessWithKey("full_string")) return $str ? $str : '';
        return $str?substr($str,0,3).'**********'.substr($str,-3):'';
    }
    /*
     * 银行卡
     */
    static public function privateBankCard($str){
        return $str?substr($str,0,4).  str_repeat('*',strlen($str)-6).substr($str,-4):'';
    }
    static public function privateBankCardOp($str){
        if(\HP\Op\Admin::checkAccessWithKey("full_string")) return $str ? $str : '';
        return $str?substr($str,0,4).  str_repeat('*',strlen($str)-6).substr($str,-4):'';
    }
    static public function privateMobile($phone){
        return $phone?substr($phone,0,3).'*****'.substr($phone,-3):'';
    }
    
    static public function privateMobileOp($phone){
        if(\HP\Op\Admin::checkAccessWithKey("full_string")) return $phone ? $phone : '';
        return $phone?substr($phone,0,3).'*****'.substr($phone,-3):'';
    }
    static public function privateUserame($str){
        $strlen = strlen($str);
        if($strlen==11){
            return self::privateMobile($str);
        }
        $i = $strlen>11?4:2;
        return substr($str,0,$i).str_repeat('*',$strlen-2*$i).substr($str,-$i);
    }
    static public function privateEmail($email){
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
            return '';
        }
        list($e1,$e2) = explode('@', $email);
        if(empty($e2))return '';
        if(strlen($e1)>3){
            $e1 = substr($e1,0,2).str_repeat('*',strlen($e1)-3).substr($e1,-1);
        }
        return $e1.'@'.$e2;
    }
    static public function getEmailLogin($email){
        $url = 'http://www.baidu.com';
        if(filter_var($email,FILTER_VALIDATE_EMAIL) and $e=substr(strstr($email,'@'),1)){
            switch ($e) {
                case 'hotmail.com':
                    $url = 'http://www.'.$e;
                    break;
                default:
                    $url = 'http://mail.'.$e;
                    break;
            }
        }
        return $url;
    }
}
