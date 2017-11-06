<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/5/18
 * Time: 下午1:27
 */


include __DIR__."/../../include/init.php";

$redis = new RedisHelp();


$key = "LIVEROOM";

$keyMap = $redis->hgetAll($key);

print_r($keyMap);

foreach ( $keyMap as $field => $item )
{
	echo "$field user list".json_encode($redis->smembers($item))."\n";

//	$redis->getMyRedis()->del($item);
//	$redis->hdel($key,$field);
}

//$redis->del($key);

