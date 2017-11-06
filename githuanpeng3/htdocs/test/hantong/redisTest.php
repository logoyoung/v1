<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/4/27
 * Time: 10:47
 */
exit();
include __DIR__."/../../../include/init.php";

use \RedisHelp;


$redis = new RedisHelp();


$key="redis_zset_test";


for ($i=1;$i<=100; $i++)
{
	$redis->zadd($key,$i,$i);
}

print_r($redis->zRevRange($key,0,-1,true));




$count = $redis->zcard($key);
echo $count.'\n';
if($count > 50)
{
	$count -=50;
	echo $count."\n";
	$redis->getMyRedis()->zDeleteRangeByRank($key,0,$count-1);
}


$count = $redis->zcard($key);
echo "$count\n";
print_r($redis->zRevRange($key,0,-1,true));


$redis->del($key);