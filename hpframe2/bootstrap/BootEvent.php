<?php
namespace bootstrap;

class BootEvent
{
    public static $callbacks = [];

    public static function init()
    {
        self::$callbacks['auth'] = [new \bootstrap\Authrize,'handle'];
    }

    public static function trigger($dispatcher)
    {
        self::init();

        foreach (self::$callbacks as $name => $call)
        {
            if(!call_user_func_array($call, [$dispatcher]))
            {
                return false;
            }
        }

        return true;
    }
}