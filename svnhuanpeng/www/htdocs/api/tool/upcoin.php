<?php

/**
 * 供后台改变比率时调用的方法
 * date 2017-02-24 09:45
 * author yandong@6rooms.com
 */
include '../../../include/init.php';
$obj=new \lib\User(2785);
$res=$obj->afterRecharge(date('Y-m-d H:i:s'));
var_dump($res);
