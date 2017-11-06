<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

set_time_limit(0);
//include '/data/huanpeng/include/init.php';

include __DIR__ . "/../../include/init.php";

use lib\due\DueOrder;
use system\DbHelper;
use system\RedisHelper;
use service\weixin\WeiXinEnterpriseService;

class orderMoniter {

    const LIMIT_NUM = 30;
    const CACHE_KEY = 'orderMoniter_offset';

    public function action() {
        $list = $this->getErrorOrder();
        $this->sendErrorMessage($list);
    }

    protected function getErrorOrder() {
        $redis = RedisHelper::getInstance("huanpeng");
        $orderId = $redis->get(self::CACHE_KEY);
        $orderId || $orderId = 0;
        $sql = "SELECT   `order_id` ,`status`,`uid`,count(*) as num FROM due_order_log where `order_id` >{$orderId} GROUP BY `order_id` ,`status` HAVING num >30 ";
        $db = DbHelper::getInstance("huanpeng");
        $res = $db->query($sql);
        $last = count($res);
        if ($last > 0) {
            $redis->set(self::CACHE_KEY, $res[$last - 1]['order_id']);
        }
        return $res;
    }

    public function sendErrorMessage($list) {

        $weixin = new WeiXinEnterpriseService();
        $agentId = WeiXinEnterpriseService::API_ALERT_AGNET_ID;
        foreach ($list as $key => $value) {
            $contentFormat = " [订单异常]\n 订单号码: %d \n 用户标识: %d\n 执行操作: %s\n 执行次数: %d\n";
            $content = sprintf($contentFormat, $value['order_id'], $value['uid'], DueOrder::$order_status[110], $value['num']);
            $userId = 'liupeng@6.cn';
            $weixin->setAgentId($agentId);
            $weixin->sendTextByDepartmentId($content, 0, $userId);
            if ($key > 10) {
                $contentFormat = "报错太多,共: %d条,其余不显示\n ";
                $content = sprintf($contentFormat, count($list));
                $userId = 'liupeng@6.cn';
                $weixin->setAgentId($agentId);
                $weixin->sendTextByDepartmentId($content, 0, $userId);
                break;
            }
        }
    }

}

$m = new orderMoniter();
$m->action();
