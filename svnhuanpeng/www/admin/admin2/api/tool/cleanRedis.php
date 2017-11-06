<?php

header("Content-Type: text/html;charset=utf-8");
include '../../includeAdmin/init.php';
include '../../includeAdmin/Redis.class.php';
//$redobj=new RedisHelp();
//$redobj->flushAll();
echo  '<font color="green">数据清空完毕!</font>';
