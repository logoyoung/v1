<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/6/21
 * Time: 下午11:15
 */

include __DIR__."/../../include/init.php";

$redis = (new RedisHelp())->getMyRedis();


$redis->publish('testMsg','lalala');