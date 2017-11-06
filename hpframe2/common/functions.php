<?php

if (!function_exists('write_log'))
{

    /**
     * 日志方法
     * @param  string|array $content  日志内容
     * @param  string $logName 自定义日志名，默认为hpframe_common
     * @return void
     */
    function write_log($content = '', $logName = APP_COMMON_LOG)
    {

        $logName = $logName ?: APP_COMMON_LOG;
        $content = is_string($content) ? $content : json_encode($content);
        $port = 0;
        $mode = 'fpm-fcgi';
        if (IS_CLI)
        {
            $clientIp = $serverIp = APP_RUN_HOST_NAME;
            $mode = 'cli';
        } else
        {
            $clientIp = fetch_real_ip($port);
            $serverIp = $_SERVER['SERVER_ADDR'];
        }
        $date    = date('Y-m-d H:i:s');
        $content = "{$date} | {$content} | client_ip:{$clientIp} | server_ip:{$serverIp}| mode:{$mode}\n";
        $logFile = LOG_DIR . $logName . '.log.' . date('Ymd');
        if (!file_exists($logFile))
        {
            touch($logFile);
            @chmod($logFile, 0777);
            clearstatcache();
        }
        file_put_contents($logFile, $content, FILE_APPEND);
        return;
    }

}

if (!function_exists('get_mysql_conf'))
{

    /**
     * 获取mysql配置
     * @return array |false
     */
    function get_mysql_conf()
    {
        return get_config('mysql/mysql_' .app_env());
    }

}

if (!function_exists('get_redis_conf'))
{

    /**
     * 获取redis配置
     * @return array |false
     */
    function get_redis_conf()
    {
        return get_config('redis/redis_' .app_env());
    }

}

if (!function_exists('array_values_to_string'))
{

    /**
     * 数组值统一转换成 string
     * @param  array $var
     * @return array
     */
    function array_values_to_string($var)
    {

        if (is_array($var))
        {

            foreach (array_keys($var) as $key)
            {
                $var[$key] = array_values_to_string($var[$key]);
            }

            return $var;
        }
        return (string) $var;
    }

}

if (!function_exists('hp_json_encode'))
{

    /**
     * json_encode
     * @param  string |array $var
     * @return string
     */
    function hp_json_encode($var)
    {

        $jsonStr = json_encode($var, JSON_UNESCAPED_UNICODE);
        if ($jsonStr !== false)
        {
            return $jsonStr;
        }

        return json_encode(array_convert_encoding($var, 'UTF-8'), JSON_UNESCAPED_UNICODE);
    }

}

if (!function_exists('render_json'))
{

    /**
     * json 数据 渲染 输出
     * @param  array | string $content  需要渲染数据
     * @param  string $errorCode 错误码 错误渲染请使用 render_error_json 方法）
     * @param  string $type   1前端不展示给用户，2前端展示给用户 （错误渲染请使用 render_error_json 方法）
     * @return json
     */
    function render_json($content = '', $errorCode = '0', $type = '1')
    {

        if (!headers_sent())
        {
            header('Content-type: application/json; charset=utf-8', true);
        }

        if ($content === null)
        {
            $content = new stdClass;
        }

        if ($errorCode != '0')
        {

            $data['status'] = '0';
            $data['content']['code'] = $errorCode;

            if (is_array($content))
            {
                $data['content'] = array_merge($data['content'], $content);
            } else
            {
                $data['content']['desc'] = $content;
            }

            $data['content']['type'] = $type;
        } else
        {
            $data['status'] = '1';
            $data['content'] = $content;
        }
        $data = xss_clean($data);
        exit(hp_json_encode(array_values_to_string($data)));
    }

}

if (!function_exists('render_error_json'))
{

    /**
     * json 错误渲染输出
     * @param  string $content    错误提示信息
     * @param  string $errorCode  错误码
     * @param  string $type       1前端不展示给用户，2前端展示给用户
     * @return json
     */
    function render_error_json($content = '', $errorCode = '-1', $type = '1')
    {
        render_json($content, $errorCode, $type);
    }

}

if (!function_exists('xss_clean'))
{

    function xss_clean($var)
    {
        if (empty($var))
        {
            return $var;
        }

        if (is_array($var))
        {
            foreach (array_keys($var) as $key)
            {
                $var[$key] = xss_clean($var[$key]);
            }

            return $var;
        }

        return filter_var($var, FILTER_SANITIZE_STRING);
    }

}

if (!function_exists('strip_tags_deep'))
{

    function strip_tags_deep($value)
    {
        return is_array($value) ? array_map('strip_tags_deep', $value) : strip_tags($value);
    }

}

if (!function_exists('filter_emoji'))
{

    /**
     * 过滤 emoji 字符支持 字符、数组 递归过滤
     * @param  string | array $var
     * @return string | array | ''
     */
    function filter_emoji($var)
    {
        if (empty($var))
        {
            return $var;
        }

        if (is_array($var))
        {

            foreach (array_keys($var) as $key)
            {
                $var[$key] = filter_emoji($var[$key]);
            }

            return $var;
        }

        $var = preg_replace_callback('#.#u', function (array $match)
        {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        }, $var);

        return $var;
    }

}

if (!function_exists('array_convert_encoding'))
{

    /**
     * 数组或字符串 字符集转换
     * @param  array $var
     * @return array
     */
    function array_convert_encoding($var, $charset = 'UTF-8')
    {

        if (is_array($var))
        {

            foreach (array_keys($var) as $key)
            {
                $var[$key] = array_convert_encoding($var[$key], $charset);
            }

            return $var;
        }

        return mb_convert_encoding($var, $charset);
    }

}