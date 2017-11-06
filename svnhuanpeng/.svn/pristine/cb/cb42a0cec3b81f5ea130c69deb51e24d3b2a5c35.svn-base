<?php

/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/8/22
 * Time: 14:44
 */
include '../../../include/init.php';

use service\weixin\WeiXinEnterpriseService;

/**
 * 获取微信组成员userId 或单个用户userId 信息
 * Class getGroupUserIds
 */
class getGroupUserIds
{
    //5 默认为接口报警组
    const  DEFAULT_DEPARTMENT_ID = 5;
    //默认为接口报警应用
    const DEFAULT_AGENTID = 1000002;
    //组ID
    private $departmentIds;
    private $userId;
    private $agentId;

    //相关参数错误
    const ERROR_PARAMS = -993;

    public static $errorMsg = [
        self::ERROR_PARAMS => 'departmentIds 必须为整数',
    ];

    //初始化
    private function _init()
    {
        $this->departmentIds    = isset($_GET['departmentIds']) ? trim($_GET['departmentIds']) : self::DEFAULT_DEPARTMENT_ID;
        $this->userId   = isset($_GET['userId']) ? trim($_GET['userId']) : '';
        $this->agentId          = isset($_GET['agentId']) ? trim($_GET['agentId']) : self::DEFAULT_AGENTID;
        return true;
    }
    public function checkParams()
    {
        if($this->userId)
        {
            $this->departmentIds = '';
            return true;
        }
        if(is_int($this->departmentIds))
        {
            return true;
        }
        $code = self::ERROR_PARAMS;
        $msg =  self::$errorMsg[$code];
        render_error_json($msg, $code);
    }
    public function getUserIds()
    {
        $weixin = new WeiXinEnterpriseService();
        $weixin->setAgentId($this->agentId);
        if($this->userId)
        {
            $list = $weixin->getSingerUserInfo($this->userId);
        }else
        {
            $list = $weixin->getUserByDepartmentId($this->departmentIds);
        }
        return $list;

    }
    public function action()
    {
        $this->_init();
        $this->checkParams();
        $res = $this->getUserIds();
        if($res)
        {
            render_json($res);
        }else
        {
            $errorCode = 200;
            $content = ' get User failed please check log';
            render_json($content,$errorCode);
        }
    }
}
$action= new getGroupUserIds();
$action->action();