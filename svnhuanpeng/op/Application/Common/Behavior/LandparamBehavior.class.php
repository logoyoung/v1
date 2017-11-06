<?php
// +----------------------------------------------------------------------
// | 落地页参数保存
// +----------------------------------------------------------------------

namespace Common\Behavior;

class LandparamBehavior extends \Think\Behavior
{
    public function run(&$params)
    {
        //全局安全过滤
        \HP\Secure\Filter::exec();

        if(!isFront())return;
        C('INVITE_UID',session('INVITE_UID'));
        C('INVITE_CPS',session('INVITE_CPS'));
        //邀请参数
        if($code=I('get.i') or $code=I('get.invcode')){
            //个人用户
            if(is_numeric($code) && $code>0){
                $dao = D('user');
                if(C('INVITE_UID')!=$code and $user=$dao->field('id')->find($code)){
                    session('INVITE_UID',$user['id']);
                    C('INVITE_UID',$user['id']);
                    session('INVITE_CPS',null);
                    C('INVITE_CPS','');
                }
            //渠道用户
            }else{
                if(C('INVITE_CPS')!=$code){
                    session('INVITE_CPS',$code);
                    C('INVITE_CPS',$code);
                    session('INVITE_UID',null);
                    C('INVITE_UID','');
                }
            }
        }
        
        //切换版本
        if($ver=I('get.ft')){
            switch ($ver) {
                case 'pc':
                    if(!isPc()){
                        switchPc();
                        redirect($_SERVER['REQUEST_URI']);
                        die;
                    }
                    break;
                case 'wap':
                    if(!isWap()){
                        switchWap();
                        redirect($_SERVER['REQUEST_URI']);
                        die;
                    }
                    break;
            }
        }
        
        //pc前台退出浏览器会话超时
        if(isPc()){
            if(!$_COOKIE['l']){
                header('Set-Cookie:l=p; path=/');
                if(get_uid()){
                    \HP\Log\Www::write(\HP\Log\Www::LOGIN_EXPIRE);
                    session('[destroy]');
                }
            }
        }
    }
}
