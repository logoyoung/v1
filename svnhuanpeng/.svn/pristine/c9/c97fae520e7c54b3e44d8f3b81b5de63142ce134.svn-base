<?php
// +----------------------------------------------------------------------
// Access 检测
// +----------------------------------------------------------------------

namespace HP\Secure;

class Accesslog
{
    static public function checkError($msg){
        $list1 = ['GET /index/get_img','POST /index/getcode'];
        foreach ($list1 as $item){
            if(strpos($msg, $item)!==false){
                return 1;
            }
        }
    }
    
    static public function getReferer($msg){
        preg_match('#HTTP\/1\.1"[\s\d]+"(.+?)"#', $msg, $match);
        return strlen($match[1])>5?$match[1]:'';
    }
    
    static public function getUrl($msg){
        preg_match('#\s([^\s]+?)\sHTTP\/1\.1"#', $msg, $match);
        return strval($match[1]);
    }
    
    static public function getUa($msg){
        $msg = trim($msg,'"');
        return substr(strrchr($msg,'"'),1);
    }
    static public function getUaid($msg){
        return \HP\Log\Common::getUAid(self::getUa($msg));
    }
}
