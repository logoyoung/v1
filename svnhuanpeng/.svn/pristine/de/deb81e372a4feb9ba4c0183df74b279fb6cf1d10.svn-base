<?php

/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/2/3
 * Time: 下午5:26
 */
include '../init.php';
require INCLUDE_DIR . 'redis.class.php';
$db = new DBHelperi_huanpeng();

$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : 0;
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';

$money = isset($_POST['money']) ? (int) $_POST['money'] : 0;


if (!$uid || !$encpass || !$money)
    exit(jsone(array('code' => -1, 'desc' => '参数错误')));

$code = checkUserState($uid, $encpass, $db);
if ($code !== true)
    error($code);


$sql = "update useractive set hpcoin = hpcoin + $money WHERE uid = $uid";
if ($db->query($sql)) {
    $redisobj = new RedisHelp();
    $rkey = "SHAMAPI_RECHARGE_$uid";
    if ($redisobj->isExists($rkey) === false) {//同步任务 
        synchroTask($uid, 30, 0, 200, $db);
        $redisobj->set($rkey, 1);
    }
    $money = $money / 10;
    $sql = "insert into billdetail (beneficiaryid, income, type) value($uid, $money, ".BILL_RECHARGE.")";
    $db->query($sql);
    exit(json_encode(array('isSuccess' => 1)));
} else {
    exit(json_encode(array('isSuccess' => 0)));
}