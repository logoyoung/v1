<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/6/21
 * Time: 下午11:24
 */

include __DIR__."/../../include/init.php";


$redis = (new RedisHelp())->getMyRedis();


function f($redis,$channel, $msg){
	echo "channel->$channel send msg->$msg";
}

$redis->subscribe(['testMsg'], 'f');