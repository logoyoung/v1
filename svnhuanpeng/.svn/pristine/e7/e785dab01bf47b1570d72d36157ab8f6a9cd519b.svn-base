<?php
// +----------------------------------------------------------------------
// | Op Log
// +----------------------------------------------------------------------
namespace HP\Log;

class Live {
    const LIVE_NOTICE=1;//警告
    const LIVE_STOP=2;//断流
    const LIVE_KILL=3;//封号
    static $LOG_HASH = [
        self::LIVE_NOTICE=>['name'=>'警告','category'=>'直播审核'],
        self::LIVE_STOP=>['name'=>'断流','category'=>'直播审核'],
        self::LIVE_KILL=>['name'=>'封号','category'=>'封号禁言'],
    ];
    static public function write($type,$reasontype=null,$reason=null){
        $time = time();
        $ip = ip2long(get_client_ip());
        $uid = \HP\Op\Admin::getUid();
        $uaid = intval(Common::getUAid());
        $id = M('AdminLogLive')->add(compact('type','uid','time','ip','uaid','reasontype','reason'));
    }
    static public function getHash($type=null)
    {
        return is_null($type)?self::$LOG_HASH:self::$LOG_HASH[$type];
    }
}