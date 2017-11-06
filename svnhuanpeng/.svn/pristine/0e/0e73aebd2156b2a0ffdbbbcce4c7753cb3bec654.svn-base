<?php

namespace HP\Util;

class PhoneArea{

    /**
     * 
     * coka 2016-08-10,取得手机号归属地
     */
    const REQUEST_URL = 'https://www.baidu.com/s?ie=utf-8&wd=';
    static public function getArea($phone){
        if(empty($phone)){
            return ['code' => 'error001','msg' => '手机号不能为空'];
            exit;
        }
        if(!self::isMobile($phone)){
            return ['code' => 'error002','msg' => '请输入正确的手机号'];
            exit;
        }
        $ch = curl_init();
        $url = 'http://apis.baidu.com/apistore/mobilenumber/mobilenumber?phone=';
        $header = array(
            'apikey: 8c45aebe28f1cbec7c0c63afcb9ffe24',
        );
        // 添加apikey到header
        curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求
        curl_setopt($ch , CURLOPT_URL , $url.$phone);
        $res = json_decode(curl_exec($ch))->retData;
        if(!$res) return ['code' => 'error003','msg' => '手机号不存在'];
        if(strlen(trim($res->province)) < 6){
            return ['code' => 'error004','msg' => '信息有误'];
        }
        $zxs = ['北京','上海','天津','重庆'];
        if(in_array($res->province, $zxs)){
            $res->province .='市';
            $res->city = '';
        }else{
            $res->province .='省';
        }
        return ['phone' => trim($res->phone),'phone_area' => trim($res->province),'phone_area2' => trim($res->city),'type' =>trim($res->supplier)];
        header("Content-type:text/html;charset=utf-8");
    }
    
    /**
     * 验证手机号是否正确
     */
    static public function isMobile($mobile){
        if(!is_numeric($mobile)){
            return false;
        }
        return preg_match('#^1[\d]{10}$#',$mobile) ? true : false;
    }

    static public function str_substr($start,$end,$str){ // 字符串截取函数      
        $temp = explode($start,$str,2);
        $content = explode($end,$temp[1],2);
        return $content[0];
    }

    static public function str_repalcestr($start,$end,$str){  
        $temp = explode($start,$str,2);
        $content = explode($end,$temp[1],2);
        return trim($content[1]);
    }

}
