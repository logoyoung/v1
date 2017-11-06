<?php
namespace system;
use Exception;
use system\Router;
use system\Dispatcher;

class System
{

    const ERROR_CALLBACK = 601;

    private static $appName;
    private static $namespace;
    private static $logName;
    private static $callback;

    public static function setAppName($appName)
    {
        self::$appName = $appName;
    }

    public static function setNamespace($namespace)
    {
        self::$namespace = $namespace;
    }

    public static function setCallback($callback)
    {
        if(!method_exists($callback[0],$callback[1]))
        {
            throw new Exception('Invalid callback',self::ERROR_CALLBACK);
        }

        self::$callback[] = $callback;
    }

    public static function run()
    {

        try {

            $router = new Router();
            $router->setNamespace(self::$namespace);
            $dispatcher = new Dispatcher();
            $dispatcher->setRouter($router);
            $dispatcher->setCallback(self::$callback);
            $dispatcher->run();

        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function getNamespace()
    {
        return self::$namespace;
    }

}