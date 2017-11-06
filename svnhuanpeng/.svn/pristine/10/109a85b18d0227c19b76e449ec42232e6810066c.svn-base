<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/8/22
 * Time: 10:01
 */
include '../../../include/init.php';

use service\weixin\WeiXinEnterpriseService;

/**
 * 微信发消息接口
 * Class sendTextMsg
 */
class sendTextMsg
{
    //报警日志 huanpeng_apiTest.log
    //5 默认为接口报警组
    const  DEFAULT_DEPARTMENT_ID = 5;
    //默认为接口报警应用
    const DEFAULT_AGENTID = 1000002;
    //相关参数错误 userId 或 tagId 不能为空
    const ERROR_PARAMS = -993;
    public static $errorMsg = [
        self::ERROR_PARAMS => 'userId 或 tagId 不能为空',
    ];
    //消息内容
    private $content;
    //组ID
    private $departmentIds;
    //人员id
    private $userId;
    private $tagId;
    //默认为此应用id
    private $agentId;
    private $corpsecret;
    //初始化
    private function _init()
    {
        $this->content          = isset($_GET['content']) ? trim($_GET['content']) : '';
        $this->departmentIds    = isset($_GET['departmentIds']) ? trim($_GET['departmentIds']) : self::DEFAULT_DEPARTMENT_ID;
        $this->userId           = isset($_GET['userId']) ? trim($_GET['userId']) : '';
        $this->tagId            = isset($_GET['tagId']) ? trim($_GET['tagId']) : '';
        $this->agentId          = isset($_GET['agentId']) ? trim($_GET['agentId']) : self::DEFAULT_AGENTID;
        return true;
    }
    public function checkParams()
    {
        //单个人需要部门为空
        if($this->userId || $this->tagId)
        {
            $this->departmentIds = '';
            return true;
        }
        $code = self::ERROR_PARAMS;
        $msg =  self::$errorMsg[$code];
        render_error_json($msg, $code);
    }
    public function getSendSerivce()
    {
        $weixin = new WeiXinEnterpriseService();
        $weixin->setAgentId($this->agentId);
        $res = $weixin->sendTextByDepartmentId($this->content , $this->departmentIds,$this->userId,$this->tagId,$this->agentId);
        return $res;

    }
    public function action()
    {
        $this->_init();
        $this->checkParams();
        $res = $this->getSendSerivce();
        if($res)
        {
            $errorCode = 0;
            $content = 'send success';
        }else
        {
            $errorCode = 200;
            $content = ' send failed please check log';
        }
        render_json($content,$errorCode);
    }
}
$send = new sendTextMsg();
$send->action();