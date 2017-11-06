<?php
namespace system;
use Exception;

class View
{
    private static $errorViewFileCode = 570;
    private static $param = [];
    private static $headerView;
    private static $footerView;
    private static $ext   = '.php';

    public static function assign($var,$data = '')
    {
        self::$param[$var] = $data;
    }

    /**
     *  渲染模板
     * @param  string  $viewFile   模板名
     * @param  array   $data       数据
     * @param  integer $httpCode   http code
     * @param  boolean $getContent 只获取不输出渲染数据
     * @return [type]              [description]
     */
    public static function display($viewFile = '', array $data = [], $httpCode = 200, $getContent = false)
    {

        $viewFile = self::getViewFile($viewFile);
        if(!file_exists($viewFile))
        {
            throw new Exception("Invalid viewFile:{$viewFile}", self::$errorViewFileCode);
        }

        $data        = array_merge(self::$param, $data);
        self::$param = [];
        $obLevel = ob_get_level();
        ob_start();
        extract($data, EXTR_SKIP);

        try
        {
            if(self::$headerView)
            {
                include self::$headerView;
            }

            include $viewFile;

            if(self::$footerView)
            {
                include self::$footerView;
            }

        } catch (Exception $e) {
            while (ob_get_level() > $obLevel)
            {
                ob_end_clean();
            }

            throw $e;
        }

        $content = ltrim(ob_get_clean());
        if (!headers_sent())
        {
            http_response_code($httpCode);
            header("Content-Type:text/html; charset=utf-8");
        }

        //这里还可以做个优化，当想做静态化，应该只编译一次，，就是把这个$content,弄个变量存起来
        //但是没有这个需求，懒得搞
        if ($getContent)
        {
            return $content;
        }

        if($content)
        {
            echo $content;
        }

        if (function_exists('fastcgi_finish_request'))
        {
            fastcgi_finish_request();
        }
    }

    /**
     * 导入头部
     * @param  string $viewFile 模板名
     * @return [type]           [description]
     */
    public static function includeHeader($viewFile = '')
    {
        $viewFile = self::getViewFile($viewFile);
        if(!file_exists($viewFile))
        {
            throw new Exception("Invalid viewFile:{$viewFile}", self::$errorViewFileCode);
        }
        self::$headerView = $viewFile;
    }

    /**
     * 导入底部
     * @param  string $viewFile 模板名
     * @return [type]           [description]
     */
    public static function includeFooter($viewFile = '')
    {
        $viewFile = self::getViewFile($viewFile);
        if(!file_exists($viewFile))
        {
            throw new Exception("Invalid viewFile:{$viewFile}", self::$errorViewFileCode);
        }
        self::$footerView = $viewFile;
    }

    public static function getViewPath()
    {
        return get_config('system/view_conf','PATH');
    }

    private static function getViewFile($viewFile)
    {
        return self::getViewPath().$viewFile.self::$ext;
    }

}