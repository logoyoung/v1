<?php
namespace system;

class InputHelper
{

    private static $url;
    private static $headers;
    private static $rowBody;
    private static $body;
    private static $queryParams;
    private static $isHttps;
    private static $hostInfo;
    private static $port;
    private static $securePort;

    public static function getHostInfo()
    {
        if (self::$hostInfo === null)
        {
            $secure = self::isHttps();
            $http   = $secure ? 'https' : 'http';
            if (isset($_SERVER['HTTP_HOST']))
            {
                self::$hostInfo = $http . '://' . $_SERVER['HTTP_HOST'];
            } elseif(isset($_SERVER['SERVER_NAME']))
            {
                self::$hostInfo = $http . '://' . $_SERVER['SERVER_NAME'];
                $port           = $secure ? self::getSecurePort() : self::getPort();
                if (($port !== 80 && !$secure) || ($port !== 443 && $secure))
                {
                    self::$hostInfo .= ':' . $port;
                }
            }
        }

        return self::$hostInfo;
    }

    public static function get($key = null, $default = null, $filter = true)
    {
        $param = self::getQueryParams();
        if ($key !== null)
        {
            $param = isset($param[$key]) ?  $param[$key] : $default;
        }

        if($filter)
        {
            $param = self::xssClean($param);
        }

        return $param;
    }

    public static function post($key = null, $default = null, $filter = true)
    {
        $param = self::getBody();
        if ($key !== null)
        {
            $param = isset($param[$key]) ?  $param[$key] : $default;
        }

        if($filter)
        {
            $param = self::xssClean($param);
        }

        return $param;
    }

    public static function input($key = null, $default = null, $filter = true)
    {
        if($param = self::get($key, $default, $filter))
        {
            return $param;
        }

        if($param = self::post($key, $default, $filter))
        {
            return $param;
        }

        if($param = self::cookie($key, $default, $filter))
        {
            return $param;
        }

        return null;
    }

    public static function cookie($key = null, $default = null, $filter = true)
    {
        $param = $_COOKIE;
        if($key !== null)
        {
            $param = isset($param[$key]) ?  $param[$key] : $default;
        }

        if($filter)
        {
            $param = self::xssClean($param);
        }

        return $param;
    }

    public static function setGet($key, $value)
    {
        $_GET[$key] = $value;
        return $_GET[$key];
    }

    public static function setPost($key, $value)
    {
        $_POST[$key] = $value;
        return $_POST[$key];
    }

    public static function getRawBody()
    {
        if(self::$rowBody === null)
        {
            self::$rowBody = file_get_contents('php://input');
        }

        return self::$rowBody;
    }

    public static function getBody()
    {
        if (self::$body === null)
        {
            if (self::getMethod() === 'POST')
            {
                self::$body = $_POST;
            } else
            {
                self::$body = [];
                mb_parse_str(self::getRawBody(), self::$body);
            }
        }

        return self::$body;
    }

    public static function getQueryParams()
    {
        if (self::$queryParams === null)
        {
            self::$queryParams = $_GET;
        }

        return self::$queryParams;
    }

    public static function getHeaders()
    {
        if (self::$headers === null)
        {
            self::$headers = [];
            if (function_exists('getallheaders'))
            {
                $h = getallheaders();
                foreach ($h as $name => $value)
                {
                    self::$headers[$name] = $value;
                }

            } elseif (function_exists('http_get_request_headers'))
            {
                $h = http_get_request_headers();
                foreach ($h as $name => $value)
                {
                    self::$headers[$name] = $value;
                }

            } else
            {
                foreach ($_SERVER as $name => $value)
                {
                    if (strncmp($name, 'HTTP_', 5) === 0)
                    {
                        $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                        self::$headers[$name] = $value;
                    }
                }
            }

        }

        return self::$headers;
    }

    public static function getQueryString()
    {
        return isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
    }

    public static function getAbsoluteUrl()
    {
        return self::getHostInfo() . self::getUrl();
    }

    public static function getUrl()
    {
        if (self::$url === null)
        {
            self::$url = self::resolveRequestUri();
        }

        return self::$url;
    }

    public function getPort()
    {
        if (self::$port === null)
        {
            self::$port = !self::isHttps() && isset($_SERVER['SERVER_PORT']) ? (int) $_SERVER['SERVER_PORT'] : 80;
        }

        return self::$port;
    }

    public static function getSecurePort()
    {
        if (self::$securePort === null)
        {
            self::$securePort = self::isHttps() && isset($_SERVER['SERVER_PORT']) ? (int) $_SERVER['SERVER_PORT'] : 443;
        }

        return self::$securePort;
    }

    public static function isGet()
    {
        return self::getMethod() === 'GET';
    }

    public static function isOptions()
    {
        return self::getMethod() === 'OPTIONS';
    }

    public static function isHead()
    {
        return self::getMethod() === 'HEAD';
    }

    public static function isPost()
    {
        return self::getMethod() === 'POST';
    }

    public static function isPut()
    {
        return self::getMethod() === 'PUT';
    }

    public static function isPatch()
    {
        return self::getMethod() === 'PATCH';
    }

    public static function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    public static function isPjax()
    {
        return self::isAjax() && !empty($_SERVER['HTTP_X_PJAX']);
    }

    public static function isFlash()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) &&
            (stripos($_SERVER['HTTP_USER_AGENT'], 'Shockwave') !== false || stripos($_SERVER['HTTP_USER_AGENT'], 'Flash') !== false);
    }

    public static function isHttps()
    {
        if(self::$isHttps === null)
        {
            self::$isHttps = isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'on') === 0 || $_SERVER['HTTPS'] == 1)
            || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0;
        }

        return self::$isHttps;
    }

    public static function getAuthUser()
    {
        return isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null;
    }

    public static function getAuthPassword()
    {
        return isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : null;
    }

    public static function getContentType()
    {
        if (isset($_SERVER["CONTENT_TYPE"]))
        {
            return $_SERVER["CONTENT_TYPE"];
        } elseif (isset($_SERVER["HTTP_CONTENT_TYPE"]))
        {
            return $_SERVER["HTTP_CONTENT_TYPE"];
        }

        return null;
    }

    public static function getMethod()
    {
        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']))
        {
            return strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
        }

        return isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
    }

    protected static function resolveRequestUri()
    {
        if (isset($_SERVER['HTTP_X_REWRITE_URL']))
        {
            $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
        } elseif (isset($_SERVER['REQUEST_URI']))
        {
            $requestUri = $_SERVER['REQUEST_URI'];
            if ($requestUri !== '' && $requestUri[0] !== '/')
            {
                $requestUri = preg_replace('/^(http|https):\/\/[^\/]+/i', '', $requestUri);
            }
        } elseif (isset($_SERVER['ORIG_PATH_INFO']))
        {
            $requestUri = $_SERVER['ORIG_PATH_INFO'];
            if (!empty($_SERVER['QUERY_STRING']))
            {
                $requestUri .= '?' . $_SERVER['QUERY_STRING'];
            }

        } else
        {
            return '';
        }

        return $requestUri;
    }

    public static function xssClean($var)
    {
        if (empty($var))
        {
            return $var;
        }

        if (is_array($var))
        {
            foreach (array_keys($var) as $key)
            {
                $var[$key] = self::xssClean($var[$key]);
            }

            return $var;
        }

        return filter_var($var, FILTER_SANITIZE_STRING);
    }
}