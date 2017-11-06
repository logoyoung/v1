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

$finance = new Finance();
$anchorExchange = new AnchorExchange();
### [ 需要修复的数据 ]
$userArray = [
    'uid' => '49005',
    'name' => '❀    念昔',
    'gb' => '228',
    'gd' => '4.7',
    'rmb' => '232',
];


### [是否进行金豆转换成金币]
//$userMoeny = $finance->getBalance($userArray['uid']);
//$cj = $userArray['rmb'] - $userArray['gb'];
//
//if ($userMoeny['gd'] > $cj) {
//    ###[ 金豆 兑换 金币 ]
//    $otid = getOtid();
//    $data = array(
//        'otid' => $otid,
//        'uid' => $userArray['uid'],
//        'type' => Finance::EXC_GD_GB,
//        'beforefrom' => $userMoeny['gd'],
//        'beforeto' => $userMoeny['gb'],
//        'afterfrom' => $userMoeny['gd'] - $cj,
//        'afterto' => $userMoeny['gb'] + $cj,
//        'number' => $cj,
//        'message' => "修复:用户没有体现,但是财务发钱了",
//        'ctime' => date('Y-m-d H:i:s'),
//        'status' => AnchorExchange::EXCHANGE_STATUS_03
//    );
//    $anchorExchange->insert($data);
//    $res = $finance->excGD2GB($userArray['uid'], $cj, "修复:用户没有提现,但是财务发钱了", $otid);
//    if ($finance->checkBizResult($res)) {
//        echo "金豆 兑换 金币 成功\n";
//    } else {
//        exit("金豆兑换金币失败 \n");
//    }
//} else {
//    exit("金豆金额不足 \n");
//}

$userMoeny = $finance->getBalance($userArray['uid']);
if ($userMoeny['gb'] >= $userArray['rmb']) {
    ###[ 提现 ]
    $otid = getOtid();
    $data = array(
        'otid' => $otid,
        'uid' => $userArray['uid'],
        'type' => Finance::EXC_GB_RMB,
        'beforefrom' => $userMoeny['gb'],
        'beforeto' => 0,
        'afterfrom' => $userMoeny['gd'] - $userArray['rmb'],
        'afterto' => 0,
        'number' => $userArray['rmb'],
        'message' => "修复:用户没有体现,但是财务发钱了",
        'ctime' => date('Y-m-d H:i:s'),
        'status' => AnchorExchange::EXCHANGE_STATUS_03
    );
    $anchorExchange->insert($data);
    $res = $finance->withdraw($userArray['uid'], $userArray['rmb'], "修复:用户没有提现,但是财务发钱了", $otid);
    if ($finance->checkBizResult($res)) {
        echo "操作成功 \n";
    } else {
        exit("提现失败 \n");
    }
} else {
    exit("提现余额不足 \n");
}
uploadBalance($userArray['uid']);










