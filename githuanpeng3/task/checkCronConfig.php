<?php
require __DIR__.'/bootstrap/i.php';

class checkCronConfig
{
      //cron 配置文件
    const CRON_CONFIG_FILE = APP_CONFIG_DIR.'crontab.%s.php';

    public static function getConfigFile($e)
    {
        return sprintf(self::CRON_CONFIG_FILE,$e);
    }

    public static function getAllEnv()
    {
        return ['dev','pre','pro'];
    }

    public static function check()
    {

        foreach (self::getAllEnv() as $_c)
        {
            try {

                $f      = self::getConfigFile($_c);
                $config = require $f;

                foreach ($config as $n => $v)
                {

                    if(!$v['server'])
                    {
                        throw new Exception("配置文件key:{$n} empty server");
                    }

                    $script = $v['script'];
                    if(!$script)
                    {
                        throw new Exception("配置文件key:{$n} empty script");
                    }

                    list($script) = explode(" ", $script);

                    if(!file_exists(trim($script)))
                    {
                        throw new Exception("环境:{$_c}; 配置文件key:{$n} Invalid script");
                    }

                    if(!$v['crontab'])
                    {
                        throw new Exception("环境:{$_c}; 配置文件key:{$n} empty crontab");
                    }

                    if(! \system\CrontabParse::parse($v['crontab']))
                    {
                        throw new Exception("环境:{$_c}; 配置文件key:{$n} crontab syntax error");
                    }

                    if(!$v['cmd'])
                    {
                        throw new Exception("环境:{$_c}; 配置文件key:{$n} empty cmd");
                    }

                    if(!isset($v['status']))
                    {
                        throw new Exception("环境:{$_c}; 配置文件key:{$n} empty status");
                    }
                }


            } catch (Exception $e)
            {
                echo $f," test failed {$e->getMessage()} \n";
                continue;
            }

            echo $f," syntax is ok \n";
        }
    }
}

checkCronConfig::check();