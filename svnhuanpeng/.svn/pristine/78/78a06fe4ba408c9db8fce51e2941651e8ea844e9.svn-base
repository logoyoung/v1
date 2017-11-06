<?php

namespace Admin\Controller;

use HP\Cache\CacheKey;
use HP\Op\Admin;
use HP\Log\Op;
class RbaccopyController extends BaseController
{
    
    public function __construct()
    {
        parent::__construct();
        $this->copy = 'copy';
        $where = [];    
        $getrole = \HP\Op\Admin::getAclRoleEdit(get_uid());
        if($getrole){
            if(!isset($getrole['getroles'])) {
                return $this->error('权限不足，请联系技术人员');
            }
            $this->getrole = $getrole['getroles'];
            $where['id'] = array('in',$getrole['getroles']);
            $this->managerRole = $getrole['role'];
            
        }
        $this->allrole = D('AclRole')->where($where)->getField('id,name');
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

    public function user($q=null)
    {
        $dao = D('AclUser');
        if(IS_AJAX and I('get.act')=='del'){
            $data = [];
			$uid = I('post.uid');
			if($uid && $this->getrole){
				$uids = D('AclUserRole')->where(['role_id'=>['not in',$this->getrole]])->field('uid')->select();
				if(in_array($uid,array_column($uids, 'uid'))){
					return $this->error('404 not found');
				}
			}
            $saveData = ['status'=>I('post.status',0),'uuid'=>get_uid(),'udate'=>time()];
            if($dao->where('uid=%d', $uid)->save($saveData)){
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
        if(is_numeric($r) && ($this->allrole && isset($this->allrole[$r]))){
            $uids = D('AclUserRole')->where(['role_id'=>$r])->field('uid')->select();
            $uid =[];
            foreach ($uids as $v){
                $uid[] = $v['uid'];
            }
            $where['uid'][]=array('in',implode(',', $uid));
            $this->r=$r;
        }

        $s = I('get.s');
        is_numeric($s) or $s=1;
        if($s!=3){
            $where[]='status='.$s;
        }
        $this->s=$s;
        if($this->getrole){
            $roleHash = D('AclRole')->where(['id'=>['in',$this->getrole]])->getField('id,name');
            $uids = D('AclUserRole')->where(['role_id'=>['not in',$this->getrole]])->field('uid')->select();
            if($uids){
                $where['uid'][]=array('not in', array_column($uids, 'uid')); // 不要用in
            }
        }
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
        $this->display('Rbac/user');
    }
    
    public function usersave($uid=null)
    {
        $dao = D('AclUser');
        $roleDao = D('AclUserRole');
        is_numeric($uid) or $uid=null;
        if($uid && $this->getrole){
            $uids = D('AclUserRole')->where(['role_id'=>['not in',$this->getrole]])->field('uid')->select();
            if(in_array($uid,array_column($uids, 'uid'))){
                return $this->error('404 not found');
            }
        }
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
                        if($this->allrole && !isset($this->allrole[$v])) {
                            continue; // 防止其添加不让其控制的角色   
                        }
                        $insertData[] = ['uid'=>$uid,'role_id'=>$v];
                    }
                    $roleDao->addAll($insertData);
                }
                if($res){
                    Admin::clearByuid($uid);
                    return $this->success('操作成功!',U('rbac'.$this->copy.'/user'));
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
        $this->roles = $this->allrole;
        $this->role = $uid?$roleDao->where('uid='.$uid)->getField('role_id',true):[];
        $this->company = \HP\Op\Company::getCompangInfo();
        $this->display('Rbac/usersave');
    }
    
    public function role()
    {
        $dao = D('AclRole');
        if(IS_AJAX){
            if(I('get.act')=='del'){
                $data = [];
                $id = I('post.id');
                if($this->allrole && !isset($this->allrole[$id])) {
                    return false; 
                }
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
                        if($this->allrole && !isset($this->allrole[$id])) {
                            return false; 
                        }
                        $dao->where('id='.$id)->save(['name'=>$name]);
                    }else{
                        $data = ['name'=>$name];
                        if(isset($this->managerRole)) {
                            $data += ['ismanager'=>2,'parentrole'=>$this->managerRole];
                        }
                        $dao->add($data);
                    }
                    $res = !$dao->getError();
                    return $this->ajaxReturn(['status'=>($res?1:0)]);
                }
            }
        }
        $data = [];
        if($res = $this->allrole){
            foreach ($res as $k=>$item){
                $data[$k]['id'] = $k;
                $data[$k]['name'] = $item;
            }
            $this->data = $data;
        }
        $this->display('Rbac/role');
    }

    public function rolesave($id)
    {
        is_numeric($id) or E();
        if($id && $this->getrole){
            if(!in_array($id,$this->getrole)){
                return $this->error('404 not found');
            }
        }
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
        $this->treeAll = \HP\Op\Admin::getTree(get_uid());
        $this->accessId = D('AclRoleAccess')->where('id='.$id)->getField('access_id',true);
        $this->display('Rbac/rolesave');
    }
}
