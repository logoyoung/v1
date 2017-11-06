<?php
namespace system;

class Router
{
    private $namespace;
    private $basePath;
    private $requestUrl;

    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    public function getRequestUrl()
    {
        return $this->requestUrl;
    }

    public function getMatch($requestUrl = null, $requestMethod = null)
    {

        $match  = false;

        if($requestUrl === null)
        {
            $requestUrl = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : false;
        }

        if($requestUrl === false || $requestUrl === '/' )
        {
            return $match;
        }

        $requestUrl = trim($requestUrl,'/');
        $requestUrl = substr($requestUrl, strlen($this->basePath));

        if (($strpos = strpos($requestUrl, '?')) !== false)
        {
            $requestUrl = substr($requestUrl, 0, $strpos);
        }

        $this->requestUrl = $requestUrl;
        $requestArr = explode('/',$this->requestUrl);
        $match      = [
            'namespace'  => false,
            'controller' => false,
            'action'     => false,
        ];

        $match['action'] = array_pop($requestArr);
        if($requestArr)
        {
            $match['controller'] = ucfirst(array_pop($requestArr));
        }

        $match['namespace'] = $requestArr ? $this->namespace.implode('\\', $requestArr) : $this->namespace;
        $match['namespace'] = rtrim($match['namespace'], '\\').'\\';

        return $match;
    }

}