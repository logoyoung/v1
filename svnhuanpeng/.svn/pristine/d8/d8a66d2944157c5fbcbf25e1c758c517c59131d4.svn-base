<?php

//项目路径 (不定义走默认配置)
define('APP_PATH', realpath(__DIR__.'/../'));

//配置文件目录 (不定义走默认配置)
define('CONFIG_DIR', APP_PATH.'/config/');

//核心系统配置文件 (不定义走默认配置)
define('APP_SYSTEM_CONF', 'system/system_conf');

require __DIR__.'/../bootstrap/init.php';

try {

    \system\System::setNamespace(APP_NAMESPACE);
    //可注册回调 ，如请求拦截权限认证，改变http请求参数等，
    \system\System::setCallback([new \bootstrap\Auth(), 'authorize']);
    \system\System::run();

} catch (Exception $e) {
    $msg = "error|error_code:{$e->getCode()}; error_msg:{$e->getMessage()}; trace: {$e->getTraceAsString()};request_url:{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    write_log($msg,APP_ERROR_LOG);
    switch (APP_ENV)
    {
        case 'DEV':
        case 'PRE':
            render_error_json($msg, $e->getCode() == 0 ? 500 : $e->getCode());
            break;

        default:
            render_error_json($e->getMessage(), $e->getCode() == 0 ? 500 : $e->getCode());
            break;
    }
}