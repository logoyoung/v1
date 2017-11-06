<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
set_time_limit(0);
//include '/data/huanpeng/include/init.php';

include __DIR__ . "/../../include/init.php";
use service\due\DueOrderService;
use lib\due\DueOrder;
use lib\due\DueAppeal;

/**
 * 计划任务
 */
class orderCronTable {

    const LIMIT_NUMS = 100;
    const LOG_NAME = 'cronOrder.log';

    /**
     * 任务间隔时长
     */
    const SECONDS = 20;

    /**
     * 设置系统处理的UID
     */
    const SYSTEM_ID_1000 = -1000;

    public $currentNum = 0;
    public $orderService;


    ### 1小时未接单超时操作
    ### 1小时未接单超时操作
    ### 结束掉失效的订单
    public static $dueOrder = null;
    public static $dueOrderService = null;

    public function getDueOrder() {
        if (is_null(self::$dueOrder)) {
            self::$dueOrder = new DueOrder();
        }
        return self::$dueOrder;
    }

    public function getDueOrderService() {
        if (is_null(self::$dueOrderService)) {
            self::$dueOrderService = new DueOrderService();
        }
        return self::$dueOrderService;
    }

    /**
     * 接单超过陪玩时间24小时后 自动确认订单
     * 
     * @param int $toStatus
     * 订单创建一小时自动取消订单           DueOrder::ORDER_STATUS_02_TIMEOUT_CANCEL;
     * 
     *  $toStatus = DueOrder::ORDER_STATUS_15_TIMEOUT_OVER_FINISHED_ORDER;
      $toStatus = DueOrder::ORDER_STATUS_16_TIMEOUT_USER_BACK_ORDER;
      $toStatus = DueOrder::ORDER_STATUS_17_TIMEOUT_USER_NOT_APPEAL_CUSTOMER_SERVICE;

     * @param int $hours
     * @param type $limit
     */
    public function cronTimeOut(int $toStatus, int $seconds, $limit = self::LIMIT_NUMS) {
        $statusArray = DueOrder::$do_map[$toStatus];
        ## [ 排除掉自己]
        $doStatus = array_diff($statusArray, [$toStatus]);
        ##
        $systemId = self::SYSTEM_ID_1000;
        $time = date("Y-m-d H:i:s", time() - $seconds);
        foreach ($doStatus as $status) {
            $rows = $this->getDueOrder()->getOrderIdDataByStatusAndLtTime($status, $time, $limit);
            foreach ($rows as $row) {
                switch ($toStatus) {
                    case DueOrder::ORDER_STATUS_020_TIMEOUT_CANCEL:
                        $res = $this->getDueOrderService()->systemTimeOutCancleOrderByOrderId($systemId, $row['order_id']);
                        break;
                    case DueOrder::ORDER_STATUS_150_TIMEOUT_OVER_FINISHED_ORDER:
                        $res = $this->getDueOrderService()->systemTimeOutOverFinishedOrderByOrderId($systemId, $row['order_id']);
                        break;
                    case DueOrder::ORDER_STATUS_160_TIMEOUT_USER_BACK_ORDER:
                        $res = $this->getDueOrderService()->systemTimeOutBackOrderByOrderId($systemId, $row['order_id']);
                        break;
                    case DueOrder::ORDER_STATUS_170_TIMEOUT_USER_NOT_APPEAL_CUSTOMER_SERVICE:
                        $res = $this->getDueOrderService()->systemTimeOutUserNotAppealOrderByOrderId($systemId, $row['order_id']);
                        break;
                }
                $content = sprintf("%s toStatus:%-3d %s", $row['order_id'], $toStatus, $res === TRUE ? 'OK' : $res);
                write_log($content, self::LOG_NAME);
            }
        }
    }

    /**
     * 超过陪玩时间执行自动确认
     * @param type $limit
     */
    public function cronTimeOut2(int $seconds = 86400, $limit = self::LIMIT_NUMS) {
        $toStatus = DueOrder::ORDER_STATUS_070_TIMEOUT_FINISHED;
        $statusArray = DueOrder::$do_map[$toStatus];
        ## [ 排除掉自己]
        $doStatus = array_diff($statusArray, [$toStatus]);
        ##
        $systemId = self::SYSTEM_ID_1000;
        $time = time() - $seconds;
        foreach ($doStatus as $status) {
            $rows = $this->getDueOrder()->getAcceptingOrderIdDataByStatusAndTime($status, $time, $limit);
            foreach ($rows as $row) {
                $res = $this->getDueOrderService()->systemTimeOutFinishedOrderByOrderId($systemId, $row['order_id']);
                $content = sprintf("%s toStatus:%-3d  %s", $row['order_id'], $toStatus, $res === TRUE ? 'OK' : $res);
                write_log($content, self::LOG_NAME);
            }
        }
    }

    /**
     * 后台审核订单处理
     * @param type $limit
     */
    public function cronTimeOut3($limit = self::LIMIT_NUMS) {
//        $statusArray = DueOrder::$do_map[$toStatus];
        ## [ 排除掉自己]
//        $doStatus = array_diff($statusArray, [$toStatus]);
        ##
        $systemId = self::SYSTEM_ID_1000;
        $upOrders = [];
        $appeal = [DueAppeal::APPEAL_CLEAR_00_DEFAULT, DueAppeal::APPEAL_STATUS_01_AGREE, DueAppeal::APPEAL_STATUS_02_DISAGREE];
        foreach ($appeal as $status) {
            $result = $this->getAppealOrders($status);
            if (empty($result)) {
                continue;
            }
            if ($status == DueAppeal::APPEAL_STATUS_00_DEFAULT) {
                //延长锁定时间
            }
            if ($status == DueAppeal::APPEAL_STATUS_01_AGREE) {
                foreach ($result as $row) {
                    $res = $this->getDueOrderService()->customerServiceAgreeOrderByOrderId($systemId, $row['order_id'], $row['reply']);
                    if ($res === TRUE) {
                        $upOrders[] = $row['order_id'];
                    }
                    $content = sprintf("%s toStatus:%-3d %s", $row['order_id'], DueOrder::ORDER_STATUS_130_CUSTOMER_SERVICE_AGREE, $res === TRUE ? 'OK' : $res);
                    write_log($content, self::LOG_NAME);
                }
            }
            if ($status == DueAppeal::APPEAL_STATUS_02_DISAGREE) {
                foreach ($result as $row) {
                    $res = $this->getDueOrderService()->customerServiceDisagreeOrderByOrderId($systemId, $row['order_id'], $row['reply']);
                    if ($res === TRUE) {
                        $upOrders[] = $row['order_id'];
                    }
                    $content = sprintf("%s toStatus:%-3d %s", $row['order_id'], DueOrder::ORDER_STATUS_140_CUSTOMER_SERVICE_DISAGREE, $res === TRUE ? 'OK' : $res);
                    write_log($content, self::LOG_NAME);
                }
            }
            if (!empty($upOrders)) {
                $this->updateAppealOrders($upOrders);
            }
        }
    }

    /**
     * 获取订单
     * @param type $status
     * @param type $limit
     * @return type
     */
    private function getAppealOrders($status, $limit = 20) {
        $obj = new DueAppeal();
        $res = $obj->getAppealOrders($status, DueAppeal::APPEAL_CLEAR_00_DEFAULT, $limit);
        return $res;
    }

    /**
     * 更新
     * @param type $orders
     * @return type
     */
    private function updateAppealOrders($orders) {
        $obj = new DueAppeal();
        $res = $obj->updateAppealOrders($orders);
        return $res;
    }

    /**
     * 关闭(成功,取消,退款的)订单
     * @param type $toStatus   成功100,取消101,退款的102
     * @param type $limit 每个状态处理的条数
     */
    public function cronSystemEndOrder($toStatus = DueOrder::ORDER_STATUS_1000_ORDER_END_AND_SETTLEMENT, $limit = self::LIMIT_NUMS) {
        $statusArray = DueOrder::$do_map[$toStatus];
        ## [ 排除掉自己]
        $doStatus = array_diff($statusArray, [$toStatus]);
        ## [ 查找数据 ]
        ## [ 设置时间:给财务中心处理订单的时间 ]
        $time = date("Y-m-d H:i:s", time() - DueOrderService::TIMER_TIME_ORDER_STATUS_COMPARISON);
        ## [ set id ]
        $systemId = self::SYSTEM_ID_1000;
        foreach ($doStatus as $status) {
            $rows = $this->getDueOrder()->getOrderIdDataByStatusAndLtTime($status, $time, $limit);
            foreach ($rows as $row) {
                $res = 'not Ok';
                if ($toStatus == DueOrder::ORDER_STATUS_1000_ORDER_END_AND_SETTLEMENT) {
                    $res = $this->getDueOrderService()->systemEndOrderByOrderId($systemId, $row['order_id']);
                } elseif ($toStatus == DueOrder::ORDER_STATUS_1010_ORDER_CANCELLED) {
                    $res = $this->getDueOrderService()->systemRefundOrderByOrderId($systemId, $row['order_id']);
                } elseif ($toStatus == DueOrder::ORDER_STATUS_1020_ORDER_BACK) {
                    $res = $this->getDueOrderService()->systemBackOrderByOrderId($systemId, $row['order_id']);
                }
                $content = sprintf("%s toStatus:%-3d  %s", $row['order_id'], $toStatus, $res === TRUE ? 'OK' : $res);
                write_log($content, self::LOG_NAME);
            }
        }
    }

    /**
     * 获取当前轮训
     * @return type
     */
    public function getCurrentNum() {
        $this->currentNum++;
        if ($this->currentNum > 1000) {
            $this->currentNum = 0;
        }
        return $this->currentNum;
    }

    /**
     * 在这一步之前禁止链接数据库
     */
    public function doAction() {
        $num = $this->getCurrentNum() % 10;
        switch ($num) {
            case 1:
                $this->cronSystemEndOrder(DueOrder::ORDER_STATUS_1000_ORDER_END_AND_SETTLEMENT);
                break;
            case 2:
                $this->cronSystemEndOrder(DueOrder::ORDER_STATUS_1010_ORDER_CANCELLED);
                break;
            case 3:
                $this->cronTimeOut(DueOrder::ORDER_STATUS_020_TIMEOUT_CANCEL, DueOrderService::TIMER_TIME_OUT_01_HOUR);
                break;
            case 4:
                $this->cronTimeOut(DueOrder::ORDER_STATUS_150_TIMEOUT_OVER_FINISHED_ORDER, DueOrderService::TIMER_TIME_OUT_24_HOUR);
                break;
            case 5:
                $this->cronTimeOut(DueOrder::ORDER_STATUS_160_TIMEOUT_USER_BACK_ORDER, DueOrderService::TIMER_TIME_OUT_24_HOUR);
                break;
            case 6:
                $this->cronTimeOut(DueOrder::ORDER_STATUS_170_TIMEOUT_USER_NOT_APPEAL_CUSTOMER_SERVICE, DueOrderService::TIMER_TIME_OUT_24_HOUR);
                break;
            case 7:
                $this->cronTimeOut2(DueOrderService::TIMER_TIME_OUT_24_HOUR);
                break;
            case 8:
                $this->cronSystemEndOrder(DueOrder::ORDER_STATUS_1020_ORDER_BACK);
                break;
            case 9:
                $this->cronTimeOut3();
                break;
            default:
                break;
        }
        return TRUE;
    }

}

$do = new orderCronTable();

for ($i = 0; $i < 10; $i++) {
    $do->doAction();
}
