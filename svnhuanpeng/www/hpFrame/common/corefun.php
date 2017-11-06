<?php
if (!function_exists('get_config'))
{

    /**
     * 获取配置数据方法
     * @param  string $f    配置文件名 如 log.php
     * @param  string $name 需要获取的配置项 file
     * @return array | false
     */
    function get_config($f = '', $name = '')
    {
        $f = strtolower(trim($f));
        $name = trim($name);
        if (!$f)
        {
            return false;
        }
        static $_config = [];
        if (!isset($_config[$f]))
        {
            $file = CONFIG_DIR . $f . '.php';
            if (file_exists($file))
            {
                $_config[$f] = require $file;
            }
        }

        return $name ? (isset($_config[$f][$name]) ? $_config[$f][$name] : false) : (isset($_config[$f]) ? $_config[$f] : false);
    }

}

if (!function_exists('fetch_real_ip'))
{
    function fetch_real_ip(&$port)
    {
        $pat_ip_port = '/((\d{1,3}\.){3}\d{1,3}):(\d+)/s';
        $pat_ip = '/(\d{1,3}\.){3}\d{1,3}/s';
        $pat_not_internal = '/^(10|172\.16|192\.168)\./';

        $ip = '';
        $port = 0;

        // X-Forwarded-Addr IP:PORT
        if (isset($_SERVER["HTTP_X_FORWARDED_ADDR"]) && preg_match_all($pat_ip_port, $_SERVER['HTTP_X_FORWARDED_ADDR'], $matches))
        {
            for ($i = 0; $i < count($matches[1]); $i++)
            {
                if (!preg_match($pat_not_internal, $matches[1][$i]))
                {
                    $ip = $matches[1][$i];
                    $port = $matches[3][$i];
                    break;
                }
            }
        } // X-Forwarded-For (no port info)
        elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && preg_match_all($pat_ip, $_SERVER['HTTP_X_FORWARDED_FOR'], $matches))
        {
            foreach ($matches[0] as $ip)
            {
                if (!preg_match($pat_ip, $ip))
                {
                    break;
                }
            }
        } elseif (isset($_SERVER["HTTP_FROM"]) && preg_match($pat_ip, $_SERVER["HTTP_FROM"]))
        {
            $ip = $_SERVER["HTTP_FROM"];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match($pat_ip, $_SERVER['HTTP_CLIENT_IP']))
        {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } // directly access
        else
        {
            $ip = $_SERVER['REMOTE_ADDR'];
            $port = $_SERVER['REMOTE_PORT'];
        }

        return $ip;
    }
}


if (!function_exists('is_https'))
{
    function is_https()
    {
        if (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off'))
        {
            return true;
        }

        if (isset($_SERVER['REQUEST_SCHEME']) && (strtolower($_SERVER['REQUEST_SCHEME']) == 'https') )
        {
            return true;
        }

        if (isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] == '443'))
        {
            return true;
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https'))
        {
            return true;
        }

        if(isset($_SERVER['HTTP_X_FORWARDED_SSL']) && (strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) == 'on'))
        {
            return true;
        }

        return false;
    }

}

