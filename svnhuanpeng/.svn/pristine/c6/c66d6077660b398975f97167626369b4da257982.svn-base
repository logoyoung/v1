<?php

/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/8/15
 * Time: 17:49
 */
require __DIR__.'/../../include/init.php';
use service\weixin\WeiXinEnterpriseService;
$action = new WeiXinEnterpriseService();
//走文件设置 如果不设置 默认走redis
$action->setFromFile();
//获取agentId
//print_r($action->getAgentList());
//获取部门列表
//print_r($action->getDepartmentList());
//发消息 默认发送给接口报警组 需要自定义找韩童
//获取部门成员
print_r($action->getUserByDepartmentId(5));
//$msg='测试消息';
//$action->sendTextByDepartmentId($msg);
//可以自定义参数 发送给 单个用户 等等
//$action->sendTextByDepartmentId($content,$departmentIds=5,$userId ='',$tagId = '',$agentid = 1000002);
