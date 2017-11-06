<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */




include __DIR__ . "/../../include/init.php";

use lib\Finance;
use lib\AnchorExchange;
use lib\Anchor;

$db = new DBHelperi_huanpeng();

function uploadBalance($uid) {
    $user = new Anchor($uid);
    $balance = new Finance();
    $res = $balance->getBalance($uid);

    $a = $user->updateUserHpBean($res['hd']);
    $b = $user->updateUserHpCoin($res['hb']);
    $c = $user->updateAnchorBean($res['gd']);
    $d = $user->updateAnchorCoin($res['gb']);

    return $a && $b && $c && $d;
}

####
$userArray = [
    15225, 26080, 49005, 51340, 62108
];
$date = '2017-05-31';
$type = Finance::EXC_GB_RMB;
//select * from `exchange_detail_201705`  where  uid IN (15225,26080,49005,51340,62108) and type = 5
$tableName = 'exchange_detail_' . date("Ym", strtotime($date));
$where = " uid IN (" . implode(',', $userArray) . ") AND type = " . $type;
$res = $db->where($where)->select($tableName);
//內部充值
$finance = new Finance();
$anchorExchange = new AnchorExchange();
$desc = '错误扣除重新发放';
foreach ($res as $value) {
#### 内部发放
    $hb = 0;
    $hd = 0;
    $gb = $value['number'];
    $gd = 0;
    $finance->innerRecharge($value['uid'], $hb, $gb, $hd, $gd, 0, $desc, 0);
    echo "userId:" . $value['uid'] . " gb number" . $value['number'] . "\n";
    $r = uploadBalance($value['uid']);
    $msg = $r ? "余额更新成功\n" : "余额更新失败\n";
}














