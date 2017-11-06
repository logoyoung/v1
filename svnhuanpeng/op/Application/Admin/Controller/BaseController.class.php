<?php

namespace Admin\Controller;

abstract class BaseController extends \Think\Controller
{

    const ACCESS_LOGIN = 1;
    const ACCESS_NOLOGIN = 2;

    public function __construct()
    {
        parent::__construct();
        $this->_accesscheck();
        $this->_usercheck();
    }

    /**
     * 如果返回空,那么走默认的RBAC验证
     * 
     * 如果返回scalar,那么代表整个controller的权限
     * self::ACCESS_LOGIN #登录通行
     * self::ACCESS_NOLOGIN #游客通行
     * 
     * 如果返回数组,那么下标是ACTION_NAME,代表action的权限
     * [
     *  'ACTION_NAME'=>self::ACCESS_LOGIN #登录通行
     *  'ACTION_NAME'=>self::ACCESS_NOLOGIN #游客通行
     *  'ACTION_NAME'=>[actionA] #按照actionA的key来验证
     *  'ACTION_NAME'=>[controllerB,actionB] #按照controllerB:actionA的key来验证
     * ]
     */
    protected function _access()
    {
        return [];
    }

    protected function _accesscheck()
    {
        if(C('ACL_SUPER')){
            return true;
        }
        $login = \HP\Op\Admin::getUid();
        $conf = $this->_access();
        if($conf===self::ACCESS_NOLOGIN){
            return true;
        }elseif($conf===self::ACCESS_LOGIN){
            $login or redirect('/');
            return true;
        }elseif(is_array($conf) and $conf = $conf[ACTION_NAME]){
            //无需登录的直接通过
            if($conf==self::ACCESS_NOLOGIN){
                return true;
                //需登录的验证登录
            }elseif($conf==self::ACCESS_LOGIN&&$login){
                return true;
            }elseif(is_array($conf)){
                list($key1, $key2) = $conf;
                if(empty($key2)){
                    $key2 = $key1;
                    $key1 = CONTROLLER_NAME;
                }
            }
        }else{
            $key1 = CONTROLLER_NAME;
            $key2 = ACTION_NAME;
        }
        $login or redirect('/');
        if(!\HP\Op\Admin::checkAccessWithController($key1, $key2)){
            $this->_deny();
            exit;
        }
    }
    
    public function _getdefaultviewfile(){
        //禁止默认访问带有下划线的模版
        if(strpos(ACTION_NAME,'_')!==false){
            return null;
        }
        $file = $this->view->parseTemplate();
        if(file_exists_case($file)){
            return $file;
        }
    }
    
    protected function _usercheck(){
        $user = \HP\Op\Admin::getUser();
        if(empty($user['email']) and $this->_access()!==self::ACCESS_NOLOGIN){
            return $this->error('请您先设置邮箱!','/');
            die;
        }
    }

    protected function _deny()
    {
        $this->display('Base/deny');
    }
}
