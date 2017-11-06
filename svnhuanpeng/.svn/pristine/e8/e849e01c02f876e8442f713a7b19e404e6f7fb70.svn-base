<?php

set_time_limit(0);
//include '/data/huanpeng/include/init.php';

include __DIR__ . "/../../include/init.php";

use system\DbHelper;
use lib\Finance;
use lib\Anchor;
use lib\due\DueOrder;

class fixOrderIncome {

    public $finance = null;
    public $dueOrderObj = null;

    public function getFinance() {
        if (is_null($this->finance)) {
            $this->finance = new Finance();
        }
        return $this->finance;
    }

    public function getDueOrderObj() {
        if (is_null($this->dueOrderObj)) {
            $this->dueOrderObj = new DueOrder();
        }
        return $this->dueOrderObj;
    }

    public function action() {

        $list = $this->getOrderList();
        $this->fixIncome($list);
    }

    protected function getOrderList() {
        //2017-08-21 优惠券上线后,财务收益计算出错,现在同步下
        $sql = "select * from due_order where `ctime` > '2017-08-21 00:00:00'  and `status` = 1000 ";
        $db = DbHelper::getInstance("huanpeng");
        $res = $db->query($sql);
        return $res;
    }

    public function fixIncome($list) {
        $finance = $this->getFinance();
        foreach ($list as $orderInfo) {
            $financeOrder = $finance->getGuaranteeOrderInfo($orderInfo['otid']);
            $this->getDueOrderObj()->updateOrderIncomeByOrderId($orderInfo['order_id'], $financeOrder['income'] / 1000);
            $conArray[] = $content = sprintf("订单号:%s  收益:% 8s 改 收益:% 8s", $orderInfo['order_id'], $orderInfo['income'], $financeOrder['income']);
            write_log($content, 'fixOrderIncome');
        }

//        ==============150388671355055638============
//        pay:180000 | rate:0.060 | income:6000 | fixIncome:10800
//        reissue
//        before:6000 after 10800 reissue income 4800
//        uid:130011
//        successful.
//        ==============150366753901482204============
//        pay:60000 | rate:0.070 | income:3500 | fixIncome:4200
//        reissue
//        before:3500 after 4200 reissue income 700
//        uid:33125
//        successful.
//        历史原因  这两条 按上面提供的数据修改

        $insertData = [
            '150388671355055638' => ['income' => 6000, 'fixIncome' => 10800],
            '150366753901482204' => ['income' => 3500, 'fixIncome' => 4200],
        ];
        $sql = "SELECT * FROM `due_order` WHERE `otid` IN (150388671355055638,150366753901482204) LIMIT 2;";
        $db = DbHelper::getInstance("huanpeng");
        $result = $db->query($sql);
        if (!empty($result)) {
            foreach ($result as $row) {
                $insertData[$row['otid']]['order_id'] = $row['order_id'];
            }
            foreach ($insertData as $orderInfo) {
                $this->getDueOrderObj()->updateOrderIncomeByOrderId($orderInfo['order_id'], $orderInfo['fixIncome'] / 1000);
                $conArray[] = $content = sprintf("订单号:%s  收益:% 8s 改 收益:% 8s", $orderInfo['order_id'], $orderInfo['income'], $orderInfo['fixIncome']);
                write_log($content, 'fixOrderIncome');
            }
        }
        foreach ($conArray as $msg) {
            echo $msg, PHP_EOL;
        }
    }

    public function updateUserBalance($uid) {
        $user = new Anchor($uid);
        $finance = $this->getFinance();
        $res = $finance->getBalance($uid);
        $user->updateAnchorCoin($res['gb']);
        return;
    }

}

$m = new fixOrderIncome();
$m->action();
