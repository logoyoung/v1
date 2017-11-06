<?php
// +----------------------------------------------------------------------
// | Admin Info
// +----------------------------------------------------------------------
namespace HP\Op;
use HP\Cache\CacheKey;
class Admin extends \HP\Cache\Proxy{
    const LOGIN_KEY='u';
    const TREE_ALL=1;
    /*
     * 登录方法
     */
    static public function setUserWithHash(array $user){
        return session(self::LOGIN_KEY,['uid'=>$user['uid'],'realname'=>$user['realname']]);
    }
    /*
     * 退出登录
     */
    static public function logout(){
        \HP\Log\Op::write(\HP\Log\Op::LOGOUT_SUCC);
        self::clearByuid();
        self::clearUserByuid();
        session('[destroy]');
    }
    /*
     * 检测用户密码
     * 合格返回fasle
     * 不合格返回msg
     */
    static public function chkpasswd($passwd)
    {
        if(strlen($passwd)<6 || is_numeric($passwd)){
            return '密码必须大于5位,非纯数字~';
        }
        return false;
    }
    /*
     * 用户加密方法
     */
    static public function passwd($passwd)
    {
        return md5(md5($passwd));
    }
    static public function getUid()
    {
        return session(self::LOGIN_KEY.'.uid');
    }
    
    static public function getUser($force=false)
    {
        if(!($uid = self::getUid())){
            return null;
        }
        return self::QueryCacheScalar(\HP\Cache\CacheKey::ADMIN_USER.self::getUid(), 86400, __CLASS__.'::getUserFromDB',[],$force);

    }
    
    static public function getUserFromDB($force=false)
    {
        if(!($uid = self::getUid())){
            return null;
        }
        static $buffer;
        if(empty($buffer[$uid]) || $force){
            $user = D('AclUser')->find($uid);
            $user['role'] = D('AclUserRole')->where(['uid'=>$uid])->getField('role_id',true);
            $buffer[$uid] = $user;
        }
        return $buffer[$uid];
    }
    
    static public function getTree($uid=null)
    {
        $uid or $uid=self::getUid();
        if(in_array($uid,[self::TREE_ALL])){
            $userinfo = self::getInfo($uid);
        }else{
            $userinfo = self::QueryCacheScalar(\HP\Cache\CacheKey::ADMIN_USERINFO.self::getUid(),86400,__CLASS__.'::getInfo',[],self::getUid());
        }
        return $userinfo['tree'];
    }
    static public function getTreeAll()
    {
        return self::getTree(self::TREE_ALL);
    }
    static public function getAccess($uid=null)
    {
        $uid or $uid=self::getUid();
        if(in_array($uid,[self::TREE_ALL])){
            $userinfo = self::getInfo($uid);
        }else{
            $userinfo = self::QueryCacheScalar(\HP\Cache\CacheKey::ADMIN_USERINFO.self::getUid(),86400,__CLASS__.'::getInfo',[],self::getUid());
        }
        return $userinfo['access'];
    }
    /*
     * 验证权限
     */
    static public function checkAccessWithKey($key,$uid=null)
    {
        $uid or $uid=self::getUid();
        return in_array(strtolower($key),self::getAccess($uid));
    }
    static public function checkAccessWithController($controller,$action,$uid=null)
    {
        $uid or $uid=self::getUid();
        return strcasecmp(C('DEFAULT_CONTROLLER'),$controller)===0 || in_array(strtolower($controller.':'.$action),self::getAccess($uid));
    }

    static public function getInfo($uid)
    {
        static $buffer;
        if(empty($uid))return [];
        if(!is_null($buffer[$uid]))return $buffer[$uid];
        //获取权限
        if(C('ACL_SUPER') || in_array($uid,[self::TREE_ALL])){//超级权限
            $accessData=D('acl_access')->where('1')->order('type asc,sort desc,id desc')->select();
        }else{
            if($roleIds=D('acl_user_role')->where('uid=%d',[$uid])->getField('role_id',true)){
                if($accessIds=D('acl_role_access')->where('id in(%s)',[join(',',$roleIds)])->getField('access_id',true)){
                    $accessData=D('acl_access')->where('id in(%s) or type in(1,2)',[join(',',$accessIds)])->order('type asc,parent_id asc,sort desc,id desc')->select();
                }
            }
        }
        if(empty($accessData))return[];
        //构造菜单树
        $access = array();
        $tree = array();
        foreach ($accessData as $item){
            switch ($item['type']) {
                case '1':
                    $tree[$item['id']]['name'] = $item['name'];
                    $tree[$item['id']]['icon'] = $item['icon'];
                    break;
                case '2':
                    $buffer_tree_index[$item['id']]=$item['parent_id'];
                    $tree[$item['parent_id']]['child'][$item['id']]['name'] = $item['name'];
                    $tree[$item['parent_id']]['child'][$item['id']]['icon'] = $item['icon'];
                    break;
                case '3':
                    if($buffer_tree_index[$item['parent_id']]){
                        $tree[$buffer_tree_index[$item['parent_id']]]['child'][$item['parent_id']]['child'][$item['id']] 
                                =['name'=>$item['name'],'icon'=>$item['icon'],'url'=>U($item['controller'].'/'.$item['action'])];
                    }
                    $access[$item['id']] = strtolower($item['controller'].':'.$item['action']);
                    break;
                case '4':
                    //获取全部菜单特殊输出
                    if($uid==self::TREE_ALL){
                        if($buffer_tree_index[$item['parent_id']]){
                            $tree[$buffer_tree_index[$item['parent_id']]]['child'][$item['parent_id']]['child'][$item['id']] 
                                    =['name'=>$item['name'],'icon'=>$item['icon'],'url'=>''];
                        }
                    }
                    $access[$item['id']] = strtolower($item['controller']);
                    break;
            }
        }
        foreach ($tree as $key=>$item){
            if($item['child']){
                foreach ($item['child'] as $key1=>$item1){
                    if(empty($item1['child'])){
                        unset($tree[$key]['child'][$key1]);
                    }
                }
            }
            if(empty($tree[$key]['child'])){
                unset($tree[$key]);
            }
        }
        $_re = compact('tree','access');
        $buffer[$uid] = $_re;
        return $_re;
    }
    
    static public function clearByrole($id){
        $dao = D('AclUserRole');
        $uids = $dao->where(["role_id"=>$id])->getField('uid',true);
        if(!$uids) return;
        foreach ($uids as $uid){
            self::clearByuid($uid);
        }
    }
    
    static public function clearByaccess($id){
        $dao = D("AclRoleAccess");
        $where["access_id"] = $id;
        $roles = $dao->where($where)->select();
        if($roles){
            foreach ($roles as $role){
                $rid = $role['id'];
                self::clearByrole($rid);
            }
        }
    }
    
    static public function clearByuid($uid){
        $uid or $uid=get_uid();
        self::QueryCacheScalar(CacheKey::ADMIN_USERINFO.$uid, 86400, __CLASS__.'::getInfo',[self::CACHE_UPDATE=>true],$uid);
    }
    
    
    static public function clearUserByuid($uid){
        $uid or $uid=get_uid();
        self::QueryCacheScalar(CacheKey::ADMIN_USER.$uid, 86400, __CLASS__.'::getUserFromDB',[self::CACHE_UPDATE=>true]);
    }
    
    static public function getUserByRole($role){
        is_array($role) or $role=[$role];
        $uids = D('AclUserRole')->where(['role_id'=>['in',$role]])->getField('uid',true);
        return $uids?D('aclUser')->where(['uid'=>['in',$uids]])->select(['index'=>'uid']):[];
    }
    static public function getUserCps(){
        return self::getUserByRole(2);
    }
    static public function getUserPawn(){
        return self::getUserByRole(3);
    }
    
    static public function getAclGroups($index=false){
        $config = [
            1=>'销售一组',
            2=>'销售二组',
            3=>'债权一组',
        ];
        return $index===false?$config:$config[$index];
    }
    /*
     * 根据uid获取有权限的用户组
     */
    static public function getAclRoleEdit($uid){
        $roles = D('aclUserRole')->where(['uid'=>$uid])->getField('role_id',true);
        if($roles){
            $dao = D('aclRole');
            $getroles = $dao->where(['id'=>['in',$roles]])->field('id,ismanager')->select();
            $tmp = [];
            if($getroles) {
                $role = array_unique(array_column($getroles, 'ismanager'));
                if(in_array(0, $role)) {
                    return false;
                }
                if(in_array(1, $role)) {
                    foreach($getroles as $k=>$v) {
                        if($v['ismanager'] == 1) {
                            $tmp[] = $v['id'];
                        }                        
                    }
                }
                if(in_array(2, $role) && count($role) == 1) {
                    return true;
                }
            }
            if($tmp) {
                $getroles = $dao->where(['parentrole'=>[['neq',0],['in',implode(',',$tmp)]]])->getField('id',true);
                return ['getroles' => $getroles, 'role'=>$tmp[0]];
            }
        }
        return true;
    }
}