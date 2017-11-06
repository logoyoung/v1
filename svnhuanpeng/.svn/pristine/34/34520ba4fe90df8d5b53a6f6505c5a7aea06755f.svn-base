<?php
// +----------------------------------------------------------------------
// 全局的安全过滤
// 44472@163.com
// +----------------------------------------------------------------------

namespace HP\Secure;

class Filter
{
    static public function exec(){
        self::exec_get($_GET);
        self::exec_get($_POST,['description','content']);
        self::exec_get($_COOKIE);
    }

    static public function exec_get($data,$exclude=[]){
        $preg = "'|<[^>]*?>|\\b(and|or)\\b.+?(>|<|=|\\bin\\b|\\blike\\b|\\brlike\\b|\\bREGEXP\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|<\\s*iframe\\b|\\bon[a-z]+\\s*=.*|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
        if(is_array($data)){
            foreach ($data as $k=>$v){
                $data[$k] = (is_string($v) && in_array($k,$exclude))?$v:self::exec_get($v);
            }
        }else{
            if(preg_match("/" . $preg . "/is", $data) == 1){
                \HP\Log\Log::secure(__METHOD__.'#'.MODULE_NAME.'#'.$data);
                require (APP_PATH.'Www/View/Base/errorhack.html');die;
            }
        }
    }
    static public function exec_post($data,$exclude=[]){
        $preg = "\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b|\\brlike\\b|\\bREGEXP\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|<\\s*iframe\\b|\\bon[a-z]+\\s*=.*|\\bjavascript.*?\\:|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
        if(is_array($data)){
            foreach ($data as $k=>$v){
                $data[$k] = (is_string($v) && in_array($k,$exclude))?$v:self::exec_get($v);
            }
        }else{
            if(preg_match("/" . $preg . "/is", $data) == 1){
                \HP\Log\Log::secure(__METHOD__.'#'.MODULE_NAME.'#'.$data);
                require (APP_PATH.'Www/View/Base/errorhack.html');die;
            }
        }
    }
    static public function exec_cookie($data,$exclude=[]){
        $preg = "\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b|\\brlike\\b|\\bREGEXP\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|<\\s*iframe\\b|\\bon[a-z]+\\s*=.*|\\bjavascript.*?\\:|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
        if(is_array($data)){
            foreach ($data as $k=>$v){
                $data[$k] = (is_string($v) && in_array($k,$exclude))?$v:self::exec_get($v);
            }
        }else{
            if(preg_match("/" . $preg . "/is", $data) == 1){
                \HP\Log\Log::secure(__METHOD__.'#'.MODULE_NAME.'#'.$data);
                require (APP_PATH.'Www/View/Base/errorhack.html');die;
            }
        }
    }
}
