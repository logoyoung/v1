<?php
ini_set('memory_limit', '256M');
require __DIR__.'/../../bootstrap/i.php';
use service\hpAlerm\helper\LogParser;
use service\weixin\WeiXinEnterpriseService;

/**
 * php
 * v1.0
 */
class phpErrorMonitor
{

    private $_log   = '/data/logs/huanpeng.error.log';
    private $_tl    = 0;
    private $_sleep = 10;
    private $_d;

    public function run()
    {
         $this->_d = date('Ymd');

         while (true)
         {

            if(!self::checkMonitorCodeStatus())
            {
                exit;
            }

            if (!$this->_log)
            {
                sleep($this->_sleep);
                continue;
            }

            if($this->_d != date('Ymd'))
            {
                $this->_tl = 0;
                $this->_d  = date('Ymd');
            }

            $parser  = new LogParser;
            $parser->setLogFile($this->_log);
            $parser->setTl($this->_tl);
            $parser->setRegex('#\bPHP (Parse error|Fatal error)\b#');
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
            $alerm->sendTextByDepartmentId("PHP Fatal error;运行环境:".get_hp_env().";机器名:".gethostname()."; 最近:{$this->_sleep} 秒; 错误总数:{$msg['num']}; error_msg: {$msg['msg']}");
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

$obj = new phpErrorMonitor;
$obj->run();