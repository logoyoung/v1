<?php

define('APP_NAME','dota');
define('APP_PATH', realpath(__DIR__.'/../'));
//http 访问命名空间，只要在该命名空间内的接口才能被外部访问
//命名空间 可自行修改
define('APP_NAMESPACE','\\'.APP_NAME.'\\app\\http\\');

//系统错误日志名
define('APP_ERROR_LOG',APP_NAME.'.error');

//公用日志类
define('APP_COMMON_LOG',APP_NAME.'.common');

require __DIR__.'/../bootstrap/i.php';

use system\System;
use system\Error;

//注册php自定义错误处理
Error::setLogName(APP_ERROR_LOG);
Error::register();

try {

    System::setAppName(APP_NAME);
    System::setNamespace(APP_NAMESPACE);
    //可注册回调 ，如请求拦截权限认证，改变http请求参数等，
    System::setCallback([new \dota\bootstrap\DotaAuth(), 'authorize']);
    System::run();

} catch (Exception $e) {
     $msg = "error|error_code:{$e->getCode()}; error_msg:{$e->getMessage()}; trace: {$e->getTraceAsString()};request_url:{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    write_log($msg,APP_ERROR_LOG);
    switch (get_hp_env())
    {
        case 'dev':
        case 'pre':
            render_error_json($msg, $e->getCode() == 0 ? 500 : $e->getCode());
            break;

        default:
            render_error_json($e->getMessage(), $e->getCode() == 0 ? 500 : $e->getCode());
            break;
    }

}