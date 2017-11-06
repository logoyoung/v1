<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include __DIR__ . "/../../include/init.php";

use system\DbHelper;

class fix {

    public function fixcoupon() {
        $db = DbHelper::getInstance("huanpeng");
        $sql = "update `due_user_coupon` set `status` = 2 , `orderid` = 170825212309029113 where id = 3779 limit 1;";
        $db->execute($sql);
    }

}

$n = new fix();
$n->fixcoupon();
