<?php
namespace system;
use Exception;
use system\Router;

class Dispatcher
{

    const ERROR_ROUTER_EMPTY     = 550;
    const ERROR_NAMEPSACE_EMPTY  = 551;
    const ERROR_CONTROLLER_EMPTY = 552;
    const ERROR_CONTROLLER_INVALID = 553;
    const ERROR_ACTION_EMPTY     = 554;
    const ERROR_ACTION_INVALID   = 555;

    private $namespace;
    private $controller;
    private $action;
    private $match;
    private $router;
    private $params;
    private $callback;

    public function run()
    {

        if(!class_exists($this->controller))
        {
            throw new Exception("controller {$this->match['controller']} not found",self::ERROR_CONTROLLER_INVALID);
        }

        $controller = new $this->controller();
        if(!is_callable([$controller,$this->action]))
        {
            throw new Exception("action {$this->action} not found ",self::ERROR_ACTION_INVALID);
        }

        if($this->callback)
        {
            foreach ($this->callback as $call)
            {
                 call_user_func_array($call, [$this]);
            }
        }

        call_user_func_array([$controller,$this->action], [$this->params]);
    }

    public  function setRouter(Router $router)
    {

        $match  = $router->getMatch();
        $this->router = $router;

        if($match === false )
        {
            throw new Exception('empty router', self::ERROR_ROUTER_EMPTY);
        }

        $this->match = $match;

        if(!isset($this->match['namespace']) && !$this->match['namespace'])
        {
            throw new Exception('empty namespace', self::ERROR_NAMEPSACE_EMPTY);
        }

        $this->namespace = $this->match['namespace'];

        if(!isset($this->match['controller']) && !$this->match['controller'])
        {
            throw new Exception('empty controller', self::ERROR_CONTROLLER_EMPTY);
        }

        $this->controller = $this->namespace.$this->match['controller'];

        if(!isset($this->match['action']) && !$this->match['action'])
        {
            throw new Exception('empty action', self::ERROR_ACTION_EMPTY);
        }

        $this->action = $this->match['action'];

        return $this;
    }

    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getController()
    {
        return $this->controller;
    }

}