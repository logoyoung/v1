<?php

define('CHARSET','UTF-8');

mb_internal_encoding(CHARSET);

define('TIME_ZONE','Asia/Shanghai');

date_default_timezone_set(TIME_ZONE);

define('START_TIME', microtime(true));

define('IS_CLI', (PHP_SAPI == 'cli' ? true : false));

defined('APP_PATH') or define('APP_PATH', realpath(__DIR__.'/../'));

//配置文件目录
defined('CONFIG_DIR') or define('CONFIG_DIR', APP_PATH.'/config/');

//核心系统配置
defined('APP_SYSTEM_CONF') or define('APP_SYSTEM_CONF', 'system/system_conf');

defined('APP_RUN_HOST_NAME') or define('APP_RUN_HOST_NAME', gethostname());

//载入核心处理函数，注意这个文件里不要过多写非核心的进去
require APP_PATH.'/common/corefun.php';

//项目名
define('APP_NAME', get_config(APP_SYSTEM_CONF,'APP_NAME'));

//日志目录
define('LOG_DIR', get_config(APP_SYSTEM_CONF,'LOG_DIR'));

//http 访问命名空间，只要在该命名空间内的接口才能被外部访问
//命名空间 可自行修改
define('APP_NAMESPACE', get_config(APP_SYSTEM_CONF,'APP_NAMESPACE'));

//系统错误日志名
define('APP_ERROR_LOG', APP_NAME.'_php_error');

//公用日志类
define('APP_COMMON_LOG', APP_NAME.'._common');

require APP_PATH.'/system/ClassLoader.php';

$loader = new \system\ClassLoader();
$loader->addPrefix('', APP_PATH);
$loader->register();
$loader->setUseIncludePath(true);

ini_set('display_errors', 0);

switch(app_env()) {

    case 'DEV':
    case 'PRE':
        error_reporting(E_ALL);
        break;

    case 'PRO':
    default :
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        break;
}

//错误日志记录
\system\Error::setLogName(APP_ERROR_LOG);
//注册php自定义错误处理
\system\Error::register();

require APP_PATH.'/common/functions.php';