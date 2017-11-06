<?php

namespace Admin\Controller;

use HP\Cache\CacheKey;
use HP\Op\Admin;
use HP\Log\Op;
class RbacController extends BaseController
{
    
    public function __construct()
    {
        parent::__construct();
        $this->copy = '';
    }

    protected function _access()
    {
        return [
            'usersave'=>['user'],
            'rolesave'=>['role'],
            'accesssave'=>['access'],
            'menurole'=>['access'],
        ];
    }

    public function access()
    {
        $dao = D('AclAccess');
        if(IS_AJAX and I('get.act')=='del'){
            $data = [];
            $id = I('post.id');
            if($dao->delete($id)){
                D('AclRoleAccess')->where('access_id=%d',$id)->delete();
                $data['status']=1;
            }else{
                $data['status']=0;
            }
            return $this->ajaxReturn($data);
        }
        $data = [];
        if($res = $dao->order('type asc,parent_id asc,sort desc,id desc')->select()){
            foreach ($res as $item){
                $item['typecn'] = $dao->getType($item['type']);
                $item['parentname'] = $data[$item['parent_id']]['name'];
                $data[$item['id']] = $item;
            }
            $this->data = $data;
        }
        $this->show();
    }

    public function accesssave($id=null)
    {
        $dao = D('AclAccess');
        is_numeric($id) or $id=null;
        if(IS_POST){
            if($dao->create()){
                if($id){
                    $res = $dao->where('id='.$id)->save();
                    Admin::clearByaccess($id);
                }else{
                    $res = $dao->add();
                }
                if($res){
                    return $this->success('操作成功!',U('rbac/access'));
                }else{
                    return $this->error('操作失败!');
                }
            }else{
                return $this->error($dao->getError());
            }
        }
        $assign = $id?$dao->find($id):[];
        if($res = $dao->where('type in(1,2)')->select()){
            foreach ($res as $item){
                $key = 'parent'.$item['type'];
                $assign[$key][] = $item;
            }
        }
        $assign['icon'] or $assign['icon']='icon-list';
        $this->assign($assign);
        $this->show();
    }

    public function user($q=null)
    {
        $dao = D('AclUser');
        if(IS_AJAX and I('get.act')=='del'){
            $data = [];
            $saveData = ['status'=>I('post.status',0),'uuid'=>get_uid(),'udate'=>time()];
            if($dao->where('uid=%d',I('post.uid'))->save($saveData)){
                $data['status']=1;
            }else{
                $data['status']=0;
            }
            return $this->ajaxReturn($data);
        }
        $data = [];
        $roleHash = D('AclRole')->getField('id,name');
        $groupHash = \HP\Op\Admin::getAclGroups();
        $statusHash = [0=>'删除',1=>'正常'];
        $buffer_role = [];
        foreach (D('AclUserRole')->select() as $item){
            $buffer_role[$item['uid']][$item['role_id']] = $roleHash[$item['role_id']];
        }
        $where=[];
        if($q){
            $q=addslashes(strip_tags($q));
            $where[]='username like "%'.$q.'%" or realname  like "%'.$q.'%"';
            $this->q=$q;
        }
        $r=I('get.r');
        if(is_numeric($r)){
            $uids = D('AclUserRole')->where(['role_id'=>$r])->field('uid')->select();
            $uid =[];
            foreach ($uids as $v){
                $uid[] = $v['uid'];
            }
            $where['uid']=array('in',$uid);
            $this->r=$r;
        }
        $g=I('get.g');
        if(is_numeric($g)){
            $where[]='groups='.$g;
            $this->g=$g;
        }
        $o=I('get.o');
        if(is_numeric($o)){
            if($o==1){
                $where[]='outdate!="0000-00-00"';
            }else if($o==2){
                $where[]='outdate="0000-00-00"';
            }
            $this->o=$o;
        }
        $s = I('get.s');
        is_numeric($s) or $s=1;
        if($s!=3){
            $where[]='status='.$s;
        }
        $this->s=$s;
        $count = $dao->where($where)->count();
        $Page = new \HP\Util\Page($count,$_GET['export'] ? 0 : 10);
        if($res = $dao->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('uid asc')->select()){
            foreach ($res as $item){
                $item['role'] = array_filter($buffer_role[$item['uid']]);
                $item['last'] = $item['lasttime']?(get_date($item['lasttime']).'|'.long2ip($item['lastip'])):'从未登录';
                $data[$item['uid']] = $item;
            }
            $this->data = $data;
        }
        $statuscn = [
            '0'=>'删除',
            '1'=>'正常',
        ];
        if($_GET['export']==1){
            $datas = $data;
            $excel[] = array('用户ID','用户名','状态','姓名','邮箱','电话','角色','最后登录');
            foreach ($datas as $data) {
                 $excel[] = array($data['uid'],$data['username'],$statuscn[$data['status']],$data['realname'],$data['email'],$data['mobile'],join(';',$data['role']),$data['last']);
            }
            \HP\Util\Export::outputCsv($excel,date('Y-m-d').'用户管理');
       
        }
        $this->statusHash = $statusHash;
        $this->roleHash = $roleHash;
        $this->groupHash = $groupHash;
        $this->page = $Page->show();
        $this->show();
    }

    public function usersave($uid=null)
    {
        $dao = D('AclUser');
        $roleDao = D('AclUserRole');
        is_numeric($uid) or $uid=null;
        
        if(IS_POST){
            if($dao->create()){
                if($dao->password){
                    $dao->password = \HP\Op\Admin::passwd($dao->password);
                }else{
                    unset($dao->password);
                }
                $dao->username and $dao->username = trim($dao->username);
                $roles=I('post.role',[]);
                if($uid){
                	$dao->uuid = get_uid();
                	$dao->udate = time();
                    $dao->where('uid='.$uid)->save();
                    if($haveRoles = $roleDao->where('uid='.$uid)->getField('role_id',true)){
                        foreach ($haveRoles as $k=>$v){
                            if(($kk=array_search($v,$roles))!==false){
                                unset($roles[$kk]);
                                unset($haveRoles[$k]);
                            }
                        }
                        if($haveRoles){
                            foreach ($haveRoles as $v){
                                $roleDao->where('uid=%d and role_id=%d',$uid,$v)->delete();
                            }
                        }
                    }
                }else{
                	$dao->cuid = get_uid();
                	$dao->cdate = time();
                    $uid = $dao->add();
                }
                $res = !$dao->getError();
                if($roles){
                    $insertData = [];
                    foreach ($roles as $v){
                        $insertData[] = ['uid'=>$uid,'role_id'=>$v];
                    }
                    $roleDao->addAll($insertData);
                }
                if($res){
                    Admin::clearByuid($uid);
                    return $this->success('操作成功!',U('rbac/user'));
                }else{
                    return $this->error('操作失败!');
                }
            }else{
                return $this->error($dao->getError());
            }
        }
        $assign = $uid?$dao->find($uid):[];
        $this->assign($assign);
        $this->promocodes = D('Promocode')->where(['status'=>1])->field('promocode,name')->select();
        $this->roles = D('AclRole')->getField('id,name');
        $this->allgroups = \HP\Op\Admin::getAclGroups();
        $this->role = $uid?$roleDao->where('uid='.$uid)->getField('role_id',true):[];
        $this->company = \HP\Op\Company::getCompangInfo();
        $this->show();
    }

    public function role()
    {
        $dao = D('AclRole');
        if(IS_AJAX){
            if(I('get.act')=='del'){
                $data = [];
                $id = I('post.id');
                if($dao->delete($id)){
                    D('AclRoleAccess')->where('id=%d',$id)->delete();
                    D('AclUserRole')->where('role_id=%d',$id)->delete();
                    $data['status']=1;
                }else{
                    $data['status']=0;
                }
                return $this->ajaxReturn($data);
            }elseif(I('get.act')=='save'){
                if($name=I('post.name')){
                    if($id=I('post.id') and is_numeric($id)){
                        $dao->where('id='.$id)->save(['name'=>$name]);
                    }else{
                        $dao->add(['name'=>$name]);
                    }
                    $res = !$dao->getError();
                    return $this->ajaxReturn(['status'=>($res?1:0)]);
                }
            }
        }
        $data = [];
        if($res = $dao->select()){
            foreach ($res as $item){
                $data[$item['id']] = $item;
            }
            $this->data = $data;
        }
        $this->show();
    }

    public function rolesave($id)
    {
        is_numeric($id) or E();
        if(IS_AJAX){
            $dao = D('AclRoleAccess');
            $roleid = array_filter(explode(',', I('post.ids')));
            $haveid = $dao->where('id=%d',$id)->getField('access_id',true);
            $del=[];
            foreach ($haveid as $k=>$v){
                if(($key=array_search($v,$roleid))!==false){
                    unset($haveid[$k]);
                    unset($roleid[$key]);
                }else{
                    $del[$v]=$v;
                }
            }
            $add=[];
            foreach ($roleid as $v){
                $add[] = ['id'=>$id,'access_id'=>$v];
            }
            $add and $dao->addAll($add);
            $del and $dao->where('id=%d and access_id in(%s)',$id,join(',', $del))->delete();
            Admin::clearByrole($id);
            Op::write(Op::CHANGE_ACCESS,[],$user['uid']);
            return $this->ajaxReturn(['status'=>$dao->getError()?'1':'0']);
        }
        $this->treeAll = \HP\Op\Admin::getTreeAll();
        $this->accessId = D('AclRoleAccess')->where('id='.$id)->getField('access_id',true);
        $this->show();
    }
    
    /**
     * add ,coka ,2016-06-12
     * 权限菜单反查
     */
    public function menurole($id = null){
        if(empty($id)){
            $this->error('请输入正确的菜单ID');
        }
        $araDao = D('AclRoleAccess');
        $aaDao = D('AclAccess');
        $arDao = D('AclRole');
        
        $aaRes= $aaDao->select(['index'=>'id']);
        if(!array_key_exists($id,$aaRes)){
            $this->error('查无此菜单');
        }
        //取其最终菜单
         
        if($aaRes[$id]['type']=='1'){
            //二级菜单
            $menu2=[];
            foreach($aaRes as $item){
                if($item['parent_id'] ==$id){
                    $menu2[]=$item['id'];
                }
            }
            //三级菜单
            $menu3=[];
            foreach($aaRes as $item){
                if(in_array($item['parent_id'],$menu2)){
                    $menu3[]=$item['id'];
                }
            }
            
            
        }
        
        if($aaRes[$id]['type']=='2'){
            //三级菜单
            $menu3=[];
            foreach($aaRes as $item){
                if($item['parent_id'] ==$id){
                    $menu3[]=$item['id'];
                }
            }
             
            
        }
        
        if($aaRes[$id]['type']=='3' or $aaRes[$id]['type']=='4'){
            //三级菜单
            $menu3=[];
            foreach($aaRes as $item){
                if($item['id'] ==$id){
                    $menu3[]=$item['id'];
                }
            }
        }
        
        $arares = $araDao->where(array("access_id"=>array('in',$menu3)))->select();
        $arlist = $arDao->field('id,name')->select(['index'=>'id']);
        foreach($arares as &$item){
            $item['rolename'] = $arlist[$item['id']]['name'];
            $item['menuname'] = $aaRes[$item['access_id']]['name'];
        }
        $list = [];
        foreach($arares as &$item){
                $list[$item['access_id']]['menuname'] = $item['menuname'];
                $list[$item['access_id']]['rolename'][] = $item['rolename'];
             
        }
        $this->subtitle= $aaRes[$id]['name'];
        $this->list = $list;
        $this->title='角色反查';
        $this->display();
        
        
    }
    
    public function userlog(){
        $dao = D('logOpuser');
        $where = [];
        if(I('get.id')){
            $id = I('get.id');
            $where['log.uid'] = $id;
            $this->id = $id;
        }
        if(I('get.n')){
            $n = I('get.n');
            $where['user.realname'] = $n;
            $this->n = $n;
        }
        if(I('get.t')){
            $t = I('get.t');
            $where['log.type'] = $t;
            $this->t = $t;
        }
        if(I('get.ip')){
            $ip = I('get.ip');
            $ipnum = sprintf('%u',ip2long($ip));
            $where['log.ip'] = $ipnum;
            $this->ip = $ip;
        }
        $count = $dao->alias('log')->join('left join admin_acl_user user on log.uid = user.uid')->where($where)->count();
        $Page = new \HP\Util\Page($count,10);
        $data = $dao->alias('log')
                ->join('left join admin_acl_user user on log.uid = user.uid')
                ->where($where)->limit($Page->firstRow.','.$Page->listRows)
                ->field('log.*,user.realname')->order('id desc')->select();
        $type = \HP\Log\Op::getHash();
        $this->type = $type;
        foreach ($data as $k=>$v){
            $data[$k]['ipaddr'] = long2ip($v['ip']);
            $data[$k]['timestr'] = get_date($v['time']);
            $data[$k]['typecn'] = $type[$v['type']]['name'];
        }
        $this->data = $data;
        $this->page = $Page->show();
        $this->display();
    }
    /**
     * 前台用户登录日志
     * 16-11-4 上午9:04,coka
     * 
     */
    public function wwwlog(){
        $logDao = D('LogWwwuser');
        if(I('get.id')){
            $id = I('get.id');
            $where['lwu.uid'] = $id;
            $this->id = $id;
        }
        if(I('get.n')){
            $n = I('get.n');
            $where['user.realname'] = $n;
            $this->n = $n;
        }
        if($t = I('get.t')){
            $where['lwu.type'] = ['in',$t];
            $this->t = $t;
        }
        $sdate = I('get.sdate');
        $edate = I('get.edate');
        if($sdate&&$edate){
            $where['lwu.time'] = ['between',[strtotime($sdate),strtotime($edate.' 23:59:59')]];
        }elseif($sdate){
            $where['lwu.time'] = ['egt',strtotime($sdate)];
        }elseif($edate){
            $where['lwu.time'] = ['elt',strtotime($edate.' 23:59:59')];
        }
        $this->sdate = $sdate;
        $this->edate = $edate;
        
        if(I('get.ip')){
            $ip = I('get.ip');
            $ipnum = sprintf('%u',ip2long($ip));
            $where['lwu.ip'] = $ipnum;
            $this->ip = $ip;
        }
        if(I('get.ua')){
            $ua = I('get.ua');
            $where['lua.content'] = ['like',"%$ua%"];
            $this->ua = $ua;
        }
        
        if($id2=I('get.id2')){
            $ips = $logDao->where(['uid'=>$id2,'uaid'=>['neq',1999]])->getField('distinct(ip),1',true);
            //intraip
            $intraIps = D('ipMark')->where(['type'=>'1'])->getField('ip',true);
            
            foreach ($ips as $k=>$v){
                if(in_array($k,$intraIps)){
                    unset($ips[$k]);
                }
            }
            if($ips){
                $where['lwu.ip'] = ['in',array_keys($ips)];
            }
            $this->id2 = $id2;
        }
        
        $count = $logDao->alias('lwu')
                ->join('left join user on lwu.uid=user.id')
                ->join('left join log_useragent lua on lua.id= lwu.uaid')
                ->join('left join log_wwwuser_add lwa on lwa.lid = lwu.id ')->where($where)->count();
        $Page = new \HP\Util\Page($count,$_GET['export']?null:10);
        $data = $logDao->alias('lwu')
                ->join('left join user on lwu.uid=user.id')
                ->join('left join log_useragent lua on lua.id= lwu.uaid')
                ->join('left join log_wwwuser_add lwa on lwa.lid = lwu.id ')
                ->where($where)
                ->field('lwu.id as id ,lwu.uid,user.realname,lwu.type,lwu.ip,lwu.time,lua.content as content,lwa.msg as msg')
                ->limit($Page->firstRow.','.$Page->listRows)->order('time desc')->select();
        foreach($data as &$v){
            $v['type'] = \HP\Log\Www::getHash($v['type'])['name'];
            $v['time'] = date('Y-m-d H:i:s',$v['time']);
            $v['ip'] = long2ip($v['ip']);
            $v['realname'] = \HP\Util\StringTool::privateRealnameOp($v['realname']);
        }
        if($_GET['export']){
            $excel[] = array('ID','用户ID','用户名','操作类型','IP地址','操作时间','UserAgent');
            foreach($data as $item){
                $excel[] = array($item['id'],$item['uid'],$item['realname'],$item['type'],$item['ip'],$item['time'],$item['content']);
            }
            return \HP\Util\Export::outputCsv($excel,'用户日志导出');
        }
        $type = \HP\Log\Www::getHash();
        $this->type = $type;
        $this->data = $data;
        $this->page = $Page->show();
        $this->display();
    }
}
