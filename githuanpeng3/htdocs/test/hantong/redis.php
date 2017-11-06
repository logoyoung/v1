<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/4/23
 * Time: 下午2:48
 */
exit();

include "../../../include/init.php";

use \RedisHelp;


$redis = new RedisHelp();

$redis->zadd("test_rank",10,158);
var_dump($redis->zRank("test_rank",159));
var_dump($redis->zRank("test_rank",158));


