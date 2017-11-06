<?php

if (!function_exists('parse_ini_file_multi'))
{
    function parse_ini_file_multi($file, $process_sections = true, $scanner_mode = INI_SCANNER_NORMAL)
    {
        $explode_str = '.';
        $escape_char = "'";
        $data        = parse_ini_file($file, $process_sections, $scanner_mode);
        if($data === false)
        {
            return false;
        }

        if (!$process_sections)
        {
            $data = (array) $data;
        }

        foreach ($data as $section_key => $section)
        {
            if(!is_array($section))
            {
                continue;
            }

            foreach ($section as $key => $value)
            {
                if (!strpos($key, $explode_str))
                {
                   continue;
                }

                if (substr($key, 0, 1) !== $escape_char)
                {
                    $sub_keys = explode($explode_str, $key);
                    $subs     =& $data[$section_key];
                    foreach ($sub_keys as $sub_key)
                    {
                        if (!isset($subs[$sub_key]))
                        {
                            $subs[$sub_key] = [];
                        }
                        $subs =& $subs[$sub_key];
                    }

                    $subs = $value;
                    unset($data[$section_key][$key]);

                } else
                {
                    $new_key = trim($key, $escape_char);
                    $data[$section_key][$new_key] = $value;
                    unset($data[$section_key][$key]);
                }
            }

        }

        if (!$process_sections)
        {
            $data = $data[0];
        }

        return $data;
    }
}

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
            $file_php = CONFIG_DIR . $f . '.php';
            $file_ini = CONFIG_DIR . $f . '.ini';

            if(file_exists($file_php))
            {
                $_config[$f] = require $file_php;
            } elseif (file_exists($file_ini)) {

                $_config[$f] = parse_ini_file_multi($file_ini, true);
            }

        }

        return $name ? (isset($_config[$f][$name]) ? $_config[$f][$name] : false) : (isset($_config[$f]) ? $_config[$f] : false);
    }

}

if(!function_exists('app_env'))
{
    function app_env()
    {
        static $app_evn = '';
        if(!$app_evn)
        {
            if(in_array(APP_RUN_HOST_NAME, (array) get_config(APP_SYSTEM_CONF,'DEV_SERVER')))
            {
                $app_evn = 'DEV';
            } elseif (in_array(APP_RUN_HOST_NAME, (array) get_config(APP_SYSTEM_CONF,'PRE_SERVER')))
            {
                $app_evn = 'PRE';

            } else
            {
                $app_evn = 'PRO';
            }

        }

        return $app_evn;
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