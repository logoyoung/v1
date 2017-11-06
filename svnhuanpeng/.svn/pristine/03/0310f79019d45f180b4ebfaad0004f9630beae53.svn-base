<?php
// +----------------------------------------------------------------------
// | Op Log
// +----------------------------------------------------------------------
namespace HP\Log;

class Op {
    const LOGIN_SUCC=1;
    const LOGIN_FAIL=2;
    const LOGOUT_SUCC=3;
    const CHANGE_INFO=4;
    const CHANGE_PASS=5;
    const CHANGE_REPASS=6;
    const CHANGE_COMPANY=7;
    const CHANGE_ANCHOR=8;
    const CHANGE_ACCESS=9;
    const CHANGE_ANCHORBANK=10;
    static $LOG_HASH = [
        self::LOGIN_SUCC=>['name'=>'登录成功','category'=>'帐户信息'],
        self::LOGIN_FAIL=>['name'=>'登录失败','category'=>'帐户信息'],
        self::LOGOUT_SUCC=>['name'=>'退出登录','category'=>'帐户信息'],
        self::CHANGE_INFO=>['name'=>'修改信息','category'=>'帐户信息'],
        self::CHANGE_PASS=>['name'=>'修改密码','category'=>'帐户信息'],
        self::CHANGE_REPASS=>['name'=>'找回密码','category'=>'帐户信息'],
        self::CHANGE_COMPANY=>['name'=>'签约公司','category'=>'签约管理'],
        self::CHANGE_ACCESS=>['name'=>'分配角色权限','category'=>'分配角色权限'],
        self::CHANGE_ANCHORBANK=>['name'=>'更新银行卡信息','category'=>'主播信息'],
    ];
    static $LOG_IN_DB=[self::LOGIN_SUCC,self::LOGIN_FAIL,self::LOGOUT_SUCC,self::CHANGE_INFO,self::CHANGE_PASS];
    static public function write($type,$msg=null,$uid=0){
        if(1||in_array($type,self::$LOG_IN_DB)){
            $time = time();
            $ip = ip2long(get_client_ip());
            $uid = $uid?intval($uid):\HP\Op\Admin::getUid();
            $uaid = intval(Common::getUAid());
            $id = M('AdminLogOpuser')->add(compact('type','uid','time','ip','uaid'));
            if($id && $msg){
                M('AdminLogOpuserAdd')->add(['lid'=>$id,'msg'=>is_array($msg)?json_encode($msg):$msg]);
            }
        }
    }
    static public function getHash($type=null)
    {
        return is_null($type)?self::$LOG_HASH:self::$LOG_HASH[$type];
    }
}