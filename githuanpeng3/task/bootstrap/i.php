<?php

define('APP_NAME','task');

define('APP_PATH', realpath(__DIR__.'/../'));

//系统错误日志名
define('APP_ERROR_LOG', APP_NAME.'.error');

//公用日志类
define('APP_COMMON_LOG', APP_NAME.'.common');

//配置文件目录
define('APP_CONFIG_DIR',APP_PATH.'/config/');


require __DIR__.'/../../include/init.php';

$loader->addPrefix('task',__DIR__.'/../../');

ini_set('display_errors', 0);

switch(get_current_env()) {

    case 'DEV':
    case 'PRE':
        error_reporting(E_ALL);
        break;

    case 'PRO':
    default :
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        break;
}

use system\System;
use system\Error;

//注册php自定义错误处理
Error::setLogName(APP_ERROR_LOG);
Error::register();