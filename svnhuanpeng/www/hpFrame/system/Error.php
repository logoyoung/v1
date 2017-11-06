<?php
namespace system;
use Exception;
use ErrorException;
use system\Timer;

class Error {

    private static $logName = 'system.error';

    public static function setLogName($logName)
    {
        self::$logName = $logName;
    }

    public static function register() {

        set_exception_handler([__CLASS__, 'xHandleException']);
        set_error_handler([__CLASS__, 'xHandleError']);
        register_shutdown_function([__CLASS__, 'xHandleShutdown']);
    }


    public static function xHandleException($e) {

        restore_error_handler();
        restore_exception_handler();
        $error = "error| error_code: {$e->getCode()}, error_msg: {$e->getMessage()} file:{$e->getFile()} line:{$e->getLine()} trace:{$e->getTraceAsString()}".self::getRequstUrl();
        self::log($error, self::$logName);
    }

    public static function xHandleError($errno, $errstr,$errfile, $errline,$p=[]) {

        if (error_reporting() & $errno) {
            throw new ErrorException($errstr,$errno,1,$errfile, $errline);
        }
        $message = 'warning| '.self::getErrorNameByType($errno)." {$errstr} file:{$errfile} line: {$errline}".self::getRequstUrl();
        self::log($message, self::$logName);
    }

    public static function xHandleShutdown() {

         $error   = error_get_last();
         $message = '';
         if(self::isFatalError($error)) {
            $errorNmae = self::getErrorNameByType($error['type']);
            $message   = "error| {$errorNmae},{$error['message']} file:{$error['file']} line: {$error['line']}".self::getRequstUrl();
         }

         if($message) {
            self::log($message, self::$logName);
         }

    }

    public static function isFatalError($error) {
        return isset($error['type']) && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING]);
    }

    public static function getErrorNameByType($type) {
        $names = [
            E_ERROR           => 'PHP Fatal Error',
            E_PARSE           => 'PHP Parse Error',
            E_CORE_ERROR      => 'PHP Core Error',
            E_COMPILE_ERROR   => 'PHP Compile Error',
            E_USER_ERROR      => 'PHP User Error',
            E_WARNING         => 'PHP Warning',
            E_CORE_WARNING    => 'PHP Core Warning',
            E_COMPILE_WARNING => 'PHP Compile Warning',
            E_USER_WARNING    => 'PHP User Warning',
            E_STRICT          => 'PHP Strict Warning',
            E_NOTICE          => 'PHP Notice',
            E_RECOVERABLE_ERROR => 'PHP Recoverable Error',
            E_DEPRECATED      => 'PHP Deprecated Warning',
        ];

        return isset($names[$type]) ? $names[$type] : 'Error';
    }

    public static function ignoreLog($errno) {
        return in_array($errno,[E_NOTICE]);
    }

    public static function log($msg,$logName)
    {
        write_log($msg,$logName);
    }

    public static function getRequstUrl()
    {
        if (IS_CLI)
        {
            return '';
        }

        return ";request_url:{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    }
}