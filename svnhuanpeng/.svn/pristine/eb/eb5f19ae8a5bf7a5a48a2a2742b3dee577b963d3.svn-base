<?php
namespace Company\Controller;
use HP\Op\Admin;
use HP\Log\Op;
class PublicController extends BaseController
{
    public function _access()
    {
        return self::ACCESS_NOLOGIN;
    }

    //login
    public function index()
    {
        if(Admin::getUser()){
            $this->profile();
        }else{
            $this->login();
        }
    }
    public function showlog($p=1)
    {
        import('Vendor.useragent.useragent');
        $pageSize = 8;
        $dao = M('AdminLogOpuser');
        $data = $dao->where('uid='.Admin::getUid())->page($p,$pageSize)->order('id desc')->select();
        foreach ($data as $key=>$item){
            $buffer_uaids[] = $item['uaid'];
        }
        if($buffer_uaids){
            $uaData = M('AdminLogUseragent')->getField('id,content',true);
        }
        foreach ($data as $key=>$item){
            $uaObject=\UserAgentFactory::analyze($uaData[$item['uaid']],null,'uas/img/');
            $data[$key]+=[
                'browsername'=>$uaObject->browser['title'],
                'browserimage'=>$uaObject->browser['image'],
                'osname'=>$uaObject->os['title'],
                'osimage'=>$uaObject->os['image'],
            ];
            $data[$key]+=Op::getHash($item['type']);
            $data[$key]['ip'] = long2ip($item['ip']);
            $data[$key]['day'] = date('j',$item['time']);
            $data[$key]['month'] = date('n',$item['time']);
            $data[$key]['time'] = date('H:i:s',$item['time']);
            switch ($item['type']) {
                case Op::LOGIN_FAIL:
                    $data[$key]['ico']='icon-question-sign';
                    break;

                default:
                    $data[$key]['ico']='icon-ok-sign';
                    break;
            }
        }
        return $this->ajaxReturn($data);
    }

    protected function profile()
    {
        $dao = D('AclUser');
        if(IS_POST){
            switch (I('request.act')){
                case 'info':
                    $uid=Admin::getUid() or E();
                    $user = Admin::getUser();
                    
                    if($dao->create()){
                        if(isset($dao->email)){
                            if($user['email']){
                                \HP\Log\Log::system(__FUNCTION__);
                                return $this->ajaxReturn(['status'=>4,'msg'=>'非法请求,已上报技术部']);
                            }
                            if(!filter_var($dao->email, FILTER_VALIDATE_EMAIL)){
                                return $this->ajaxReturn(['status'=>5,'msg'=>'邮箱格式不正确!']);
                            }
                        }
                        if(isset($dao->password)){
                            if(Admin::passwd(I('post.oldpassword'))!=$user['password']){
                                return $this->ajaxReturn(['status'=>2,'msg'=>'原密码输入不正确']);
                            }
                            if($msg = Admin::chkpasswd($dao->password)){
                                return $this->ajaxReturn(['status'=>3,'msg'=>$msg]);
                            }
                            $dao->password = Admin::passwd($dao->password);
                            Op::write(Op::CHANGE_PASS);
                        }else{
                            Op::write(Op::CHANGE_INFO);
                        }
                        $dao->where('uid='.$uid)->save();
                        Admin::clearUserByuid();
                        if($dao->getError()){
                            return $this->ajaxReturn(['status'=>2,'msg'=>'操作失败']);
                        }else{
                            return $this->ajaxReturn(['status'=>0,'msg'=>'操作成功']);
                        }
                    }else{
                        return $this->ajaxReturn(['status'=>1,'msg'=>$dao->getError()]);
                    }
                    break;
            }
        }
        $this->assign(Admin::getUser());
        $this->display('Base/'.__FUNCTION__);
    }

    protected function login()
    {
        if(IS_AJAX){
             $username=I('post.u');
             $password=I('post.p');
             if(empty($username) || empty($password)){
                 return $this->ajaxReturn(['status'=>1,'msg'=>'参数不全!']);
             }
             if(!C('INTRA_IP') and !(new \Think\Verify())->check(I('post.v'))){
                 return $this->ajaxReturn(['status'=>2,'msg'=>'验证码验证失败!']);
             }
             $dao = D('AclUser');
             if($user = $dao->where('username="%s"',$username)->find()){
                 if($user['status']=='1' and $user['password']==Admin::passwd($password)){
                    Admin::setUserWithHash($user);
                    Op::write(Op::LOGIN_SUCC,[],$user['uid']);
                    $dao->where(['uid'=>$user['uid']])->save([
                        'lastip'=>get_client_ip(1),
                        'lasttime'=>time(),
                    ]);
                    return $this->ajaxReturn(['status'=>0,'msg'=>'登录成功!']);
                 }else{
                     Op::write(Op::LOGIN_FAIL,[],$user['uid']);
                 }
             }
             return $this->ajaxReturn(['status'=>3,'msg'=>'登录失败!']);
        }
        $this->noverify = C('INTRA_IP');
        $this->display('Base/'.__FUNCTION__);
    }
    public function logout()
    {
        if(!($uid=Admin::getUid()))return;
        if(IS_AJAX){
            Admin::logout();
            return $this->ajaxReturn(['status'=>0]);
            dump('121');
        }
    }
    public function verify()
    {
        $Verify = new \Think\Verify(array(
            'length'=>4,
            'fontSize'=>20,
//            'imageW'=>200,
//            'imageH'=>40,
            'useCurve'=>false,
//            'fontttf'=>false,
//            'useNoise'=>false,
        ));
        $Verify->entry();
    }

    public function retake()
    {
        $dao = D('AclUser');
        $mck = 'retake_';
        if(I('act')=='do'){
            if(!($k=I('get.sk')) || !($ac = S($mck.$k))){
                return $this->ajaxReturn(['status'=>1,'msg'=>'操作超时!']);
            }
            $p=I('p');
            if($p!=I('p1')){
                return $this->ajaxReturn(['status'=>2,'msg'=>'两次密码不一致!']);
            }
            if($msg=Admin::chkpasswd($p)){
                return $this->ajaxReturn(['status'=>3,'msg'=>$msg]);
            }
            if($uid = $dao->where(['username'=>$ac])->getField('uid')){
                Op::write(Op::CHANGE_RETAKE,null,$uid);
                $dao->where(['uid'=>$uid])->limit(1)->save(['password'=>Admin::passwd($p)]);
                if(!$dao->getError()){
                    S($mck.$k,null);
                    return $this->ajaxReturn(['status'=>0,'msg'=>'操作成功']);
                }
            }
            return $this->ajaxReturn(['status'=>2,'msg'=>'操作失败']);
        }elseif(IS_AJAX){
            if(!($data=$dao->where(['username'=>I('post.u')])->find()))
            {
                return $this->ajaxReturn(['status'=>'2','msg'=>'帐户输入不正确~']);
            }
            if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
                return $this->ajaxReturn(['status'=>'1','msg'=>'帐户邮箱设置错误,请联系技术部找回']);
            }
            $key = \HP\Util\StringTool::getRandMd5();
            S($mck.$key,$data['username'],['expire'=>3600]);
            $href = U('/public/retake',['sk'=>$key],true,true);
            
            //发送邮件
            $content["content"] = "您正在申请重置运营后台帐户密码,如果不是您本人操作请及时联系技术部,不要将以下连接发送给其他人!<br/>您的密码重置连接为<a href='{$href}'>{$href}</a>[1小时内有效]"; 
            $content['email'] = $data['email'];
            $res = \HP\Service\Mail::sendMsg($content);
            return $this->ajaxReturn($res?['status'=>'0','msg'=>'邮件发送成功~']:['status'=>'3','msg'=>'邮件发送失败!']);
        }else{
            $k=I('get.sk') or E();
            if(!($ac = S($mck.$k))){
                return $this->error('您的链接无效,请重新申请!', '/');
            }
            $this->ac = $ac;
            $this->display('Base/'.__FUNCTION__);
        }
    }
    
    /*
     * 返回公司列表。
     * zwq add 2017年5月8日
     */
    
    public function getlist()
    {
        $dao = D('Company');
        $status = $_REQUEST['status'];
        $where = [];
        if ($status) {
            $where['status']=$status;
        }
        $results = $dao->where($where)->select();
        foreach ($results as $result){
            $data['value'] = $result['name'].'|'.$result['id'];
            $data['id'] = $result['id'];
            $datas[] = $data;
        }
        return $this->ajaxReturn( $datas );
    }
    
    /*
     * 返回渠道列表。
     * zwq add 2017年6月7日
     */
    
    public function getchannellist()
    {
        $dao = D('ChannelVersion');
        $status = $_REQUEST['status'];
        $where = [];
        if ($status) {
            $where['status']=$status;
        }
        $results = $dao->where($where)->select();
        foreach ($results as $result){
            $data['value'] = $result['channelname'].'|'.$result['channel'];
            $data['id'] = $result['channel'];
            $datas[] = $data;
        }
        return $this->ajaxReturn( $datas );
    }
}
