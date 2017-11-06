<?php
// +----------------------------------------------------------------------
// | 登录超时
// +----------------------------------------------------------------------

namespace Common\Behavior;
class LoginexpireBehavior extends \Think\Behavior
{
    public function run(&$params)
    {
        if(!get_uid())return;
        if(isFront()){
            /*$time = 3600;
            $key = 'DB_EXPIRE';
            $now = time();
            $to = session($key);
            if($to&&$to<$now){
                \HP\Log\Www::write(\HP\Log\Www::LOGIN_EXPIRE,[],get_uid()); //coka,2016-06-07 ,写入日志
                \HP\User\Www::LogoutWithUid();
            }else{
                session($key,$now+$time);
            }*/
        }elseif(isAdmin()){
            $key = 'DB_EXPIRE';
            $toTime = session($key);
            if(empty($toTime)){
                //设置过期时间为4小时后所在天的20点
                session($key,strtotime(date('Y-m-d',time()+14444).' 20:00:00'));
                return;
            }
            if($toTime<time()){
                \HP\Op\Admin::logout();
                session($key,null);
            }
        }
    }
}