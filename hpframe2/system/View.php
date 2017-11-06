<?php
namespace system;
use Exception;

class View
{
    private static $errorViewFileCode = 570;
    private static $param = [];
    private static $ext   = '.php';
    private static $tplFiles =[];

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
    public static function display($viewFile = '', array $data = [],$httpCode = 200, $getContent = false) {

        self::$param = array_merge(self::$param, $data);
        $data    = null;
        $obLevel = ob_get_level();
        ob_start();
        extract(self::$param, EXTR_SKIP);

        try {

            if($viewFile) {
                self::layout($viewFile);
            }

            //布局渲染
            if(self::$tplFiles) {
                foreach (self::$tplFiles as $tpl) {
                    include $tpl;
                }
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

    public static function layout(...$tpls) {
        foreach ($tpls as $tpl) {
            self::$tplFiles[] = self::getViewFile($tpl);
        }
        return true;
    }

    public static function getViewPath()
    {
        return get_config('system/view_conf','PATH');
    }

    private static function getViewFile($viewFile)
    {
        $viewFile = self::getViewPath().$viewFile.self::$ext;
        if(!file_exists($viewFile)) {
            throw new Exception("Invalid viewFile:{$viewFile}", self::$errorViewFileCode);
        }

        return $viewFile;
    }

    public static function getJsUrl()
    {
        return get_config('system/view_conf','STATIC_DOMAIN_URL').get_config('system/view_conf','JS');
    }

    public static function getCssUrl()
    {
        return get_config('system/view_conf','STATIC_DOMAIN_URL').get_config('system/view_conf','CSS');
    }

    public static function getImgUrl()
    {
        return get_config('system/view_conf','STATIC_DOMAIN_URL').get_config('system/view_conf','IMG');
    }

    public static function getVer()
    {
        return get_config('system/view_conf','VER');
    }
}