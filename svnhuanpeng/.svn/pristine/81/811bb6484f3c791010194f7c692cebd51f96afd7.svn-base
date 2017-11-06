<?php
ini_set('memory_limit', '256M');
require __DIR__.'/../../bootstrap/i.php';
use service\hpAlerm\helper\LogParser;
use service\weixin\WeiXinEnterpriseService;

/**
 * rds
 * v1.0
 */
class redisMonitor
{

    private $_log   = '/data/logs/redis_error.log.%s';
    private $_tl    = 0;
    private $_sleep = 10;
    private $_logFile;

    public function getErrorFile()
    {

        $file = sprintf($this->_log, date('Ymd'));
        if(!file_exists($file))
        {
             clearstatcache();
             $this->_logFile = null;
             $this->_tl      = 0;
             return false;
        }

        if($file == $this->_logFile)
        {
             return $this->_logFile;
        }

        $this->_tl      = 0;
        $this->_logFile = $file;

        return $this->_logFile;
    }

    public function run()
    {

        while (true)
        {

            if(!self::checkMonitorCodeStatus())
            {
                exit;
            }

            $file    = $this->getErrorFile();
            if (!$file)
            {
                sleep($this->_sleep);
                continue;
            }

            $parser  = new LogParser;
            $parser->setLogFile($file);
            $parser->setTl($this->_tl);
            $msg     = $parser->getMatch();

            if(!isset($msg['num']) || ($msg['num'] <= 0))
            {
                unset($parser);
                sleep($this->_sleep);
                continue;
            }

            $this->_tl = $msg['tl'];
            $alerm = new WeiXinEnterpriseService;
            $alerm->setFromFile(true);
            $alerm->sendTextByDepartmentId("redis服务异常; 环境:".get_hp_env().";机器名:".gethostname()."; 最近:{$this->_sleep} 秒; 错误总数:{$msg['num']}; error_msg: {$msg['msg']}");
            unset($alerm,$parser);
            sleep($this->_sleep);
        }

    }

    private function checkMonitorCodeStatus()
    {
        static $md5 = '';

        if (!$md5)
        {
            $md5  = md5_file(__FILE__);

            return true;
        }

        return (md5_file(__FILE__) == $md5 );
    }
}

$obj = new redisMonitor;
$obj->run();
