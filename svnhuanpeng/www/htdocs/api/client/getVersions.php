<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/11/18
 * Time: 下午5:33
 */

include '../../../include/init.php';

$redis = new redishelp();

$CLIENT_VERSION = 'clientversions:setversions';


$versions = $redis->get($CLIENT_VERSION);

exit(jsone($versions));