<?php
// +----------------------------------------------------------------------
// | Log公用方法
// +----------------------------------------------------------------------
namespace HP\Log;

class Common {
    static public function fromtype($index=false){
        $hash = [
            1 => 'PC网站',
            2 => '手机网页',
            3 => 'iOS客户端',
            4 => '安卓客户端',
            5 => '微信端',
        ];
        return $index===FALSE?$hash:$hash[$index];
    }

    static public function getUAid($ua=null)
    {
        static $uaid;
        if(is_null($ua)){
            $ua = $_SERVER['HTTP_USER_AGENT'];
        }elseif(empty ($ua)){
            return;
        }
        if($uaid)return $uaid;
        $uaDao = M('AdminLogUseragent');
        $idx = self::getUAidIndex($ua);
        $uaid=$uaDao->where('idx=%d and content="%s"',$idx,$ua)->getField('id');
        $uaid or $uaid=$uaDao->add(['idx'=>$idx,'content'=>$ua]);
        return $uaid;
    }
    static public function getUAidIndex($ua)
    {
        $ua = md5($ua);
        $idx= '';
        for($i=0;$i<3;$i++){
            $idx .= ord(substr($ua,$i,1));
        }
        return $idx;
    }
}