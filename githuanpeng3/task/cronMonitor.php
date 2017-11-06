<?php
require __DIR__.'/bootstrap/i.php';

/**
 * cron管理监控
 *
 */
#部署机器名Huanpeng_adm_nfs_28_11,HangpengW28_119,HangpengW28_118
//*/1 * * * * /usr/local/php7/bin/php /usr/local/huanpeng/task/cronMonitor.php > /dev/null 2>&1 &
class CronMonitor
{

    //日志目录
    const CRON_RUN_LOG_DIR = '/data/logs/crontab/task/';
    //cron 配置文件
    const CRON_CONFIG_FILE = APP_CONFIG_DIR.'crontab.%s.php';
    private $php           = '/usr/local/php7/bin/php';
    private $bash          = '/bin/bash';
    //启运监控日志
    private $monitorFile   = self::CRON_RUN_LOG_DIR.'monitor.log';
    //执行脚本输入日志 （按天切）
    private $errorFile     = self::CRON_RUN_LOG_DIR.'crontab_log.%s.log';
    //cron启动日志（按天切）
    private $startLogFile  = self::CRON_RUN_LOG_DIR.'crontab_start_log.%s.log';
    private $monitorData;
    private $cronConfData;

    public function run()
    {

        $this->checkSelfRun();

        //创日志目录
        if(!$this->initDir())
        {
            $msg = '创建crontab私有日志目录失败，请检查目录是否可写';
            $this->log("error|{$msg}");
            exit($msg);
        }

        $this->log('notice|crontab cronMonitor.php start');

        while (true)
        {
            //校验监控代码是否有变动
            if(!$this->checkMonitorCodeStatus())
            {
                $this->log('notice|crontab cronMonitor stop,监控代码是有变动,自动退出');
                exit;
            }

            //校验配置文件是否有变动
            if(!$this->checkConfStatus())
            {
                $this->log('notice|crontab cronMonitor stop，crontab配置文件有变动,自动退出');
                exit;
            }

            //初始化crontab运行监控（不存在创建）
            if($this->initMonitorData() === false)
            {
                $this->log('error|crontab cronMonitor stop，创建crontab monitor file失败');
                exit;
            }

            //初始化cron配置文件
            if($this->initCronConfData() === false)
            {
                $this->log('error|crontab cronMonitor stop，cron 配置文件为空，请检查cron配置文件');
                exit;
            }

            $monitorLog = [];
            foreach ($this->cronConfData as $name => $conf)
            {

                //更新cron监控时间
                $conf->setMonitorTime(date('Y-m-d H:i:s'));
                //获取任务执行状态
                $jobStatus = $conf->getJobStatus($conf->getCronTime());
                //获取任务是否在运行中
                $runing    = $this->getPidsByScript($conf);

                //是否到执行时间
                if(!$conf->getIsStartCron())
                {
                    $monitorLog[$name] = $conf->getParam();
                    continue;
                }

                //本次任务执行过了，不作再次启动
                if($jobStatus)
                {
                    //$this->log("notice|本次任务执行过了，不作再次启动;name:{$conf->getName()}; script:{$conf->getScript()}");
                    $monitorLog[$name] = $conf->getParam();
                    continue;
                }

                //当前脚本正在运行中，不作再次启动
                if($runing)
                {
                    //$this->log("notice|当前脚本正在运行中，不作再次启动;name:{$conf->getName()}; script:{$conf->getScript()}");
                    $monitorLog[$name] = $conf->getParam();
                    continue;
                }

                //启动脚本
                $this->runCmd($conf);
                $monitorLog[$name] = $conf->getParam();

            }

            //写入监控日志
            file_put_contents($this->monitorFile, json_encode($monitorLog));
            unset($monitorLog);
            sleep(3);
        }

    }

    /**
     * 获取crontab运行监控
     * @return
     */
    private function initMonitorData()
    {
        $this->monitorData = [];
        if(!file_exists($this->monitorFile))
        {
            if(touch($this->monitorFile))
            {
                chmod($this->monitorFile, 0777);
                clearstatcache();
                return [];
            }

            return false;
        }

        $monitorStr = trim(file_get_contents($this->monitorFile));
        if($monitorStr)
        {
            $monitorArr        = json_decode($monitorStr, true);
            $this->monitorData = $monitorArr ? $monitorArr : [];
        }

        return true;
    }

    /**
     * 获取cron配置
     * @return array
     */
    private function initCronConfData()
    {

        static $conf = '';

        if(!$conf)
        {

            $conf = require self::getConfigFile();
            if(!$conf)
            {
                return false;
            }

            $this->cronConfData = [];
        }

        foreach ($conf as $name => $v)
        {

            $ct = CrontabParse::parse($v['crontab']);

            if(!isset($this->cronConfData[$name]))
            {
                if(!$v['status'])
                {
                    continue;
                }

                if(!isset($v['server']) || !$v['server'] || !is_array($v['server']))
                {
                    $this->log("error|无效的脚本机名 :name:{$name}:param:".json_encode($v));
                    continue;
                }

                $localHostName = self::getLocalHostName();
                if(!$localHostName || !in_array($localHostName, $v['server']))
                {
                    //$this->log("notice|脚本配置不在这个机器上运行 :name:{$name}:param:".json_encode($v));
                    continue;
                }

                if(!$v['script'])
                {
                    $this->log("error|无效的script :name:{$name}:param:".json_encode($v));
                    continue;
                }

                if (!$ct)
                {
                    $this->log("error|crontab 格式有误:name:{$name}:param:".json_encode($v));
                    continue;
                }

                $obj = new CronStatus();
                $obj->setName($name);
                $obj->setServer($v['server']);
                $obj->setCrontab($v['crontab']);
                $obj->setScript($v['script']);
                $obj->setStatus($v['status']);
                $obj->setCmd($v['cmd']);
                $obj->setLocalHostname($localHostName);
                unset($localHostName);
                $this->cronConfData[$name] = $obj;
            }

            $this->cronConfData[$name]->setCronTime($ct);
            $this->checkIsStartCorn($this->cronConfData[$name]);
        }

        return true;
    }

    public function checkIsStartCorn($confObj)
    {

        $confObj->setIsStartCron( (time() >= $confObj->getCronTime() ) ? 1 : 0);
    }


    private function runCmd($conf)
    {
        $bin  = ($conf->getCmd() == 'php') ? $this->php : $this->bash;
        $cmd  = "{$bin} {$conf->getScript()} >>{$this->getErrorFile()} 2>&1 &";
        $hd   = popen($cmd, 'r');
        pclose($hd);
        $conf->setRunTime(time());
        $conf->setJobStatus($conf->getCronTime(),1);
        $conf->setRunStatus(1);
        $this->log("notice|start success :{$conf->getScript()}");
        return true;
    }

    private function getPidsByScript($conf)
    {
        $cmd  = "ps -ef | grep -v 'grep' | grep '{$conf->getScript()}' | awk '{print \$2,\$8,\$9,\$10}'";
        $p    =  popen($cmd, 'r');
        $read = stream_get_contents($p);
        pclose($p);

        if(!$read)
        {
            return false;
        }

        $read = array_map(function($v) use($conf) {
                $v  = array_filter(explode(" ", $v));
                $r  = array_slice($v, 2);
                ksort($r);
                $c  = array_filter(explode(" ", $conf->getScript()));
                ksort($c);
                //只处理cron monitor启动的脚本
                if($r == $c) { return $v; }
        }, explode("\n", trim($read)));

        return $read;
    }

    private function checkSelfRun() {
        $cmd = "ps -ef | grep -v 'grep' | grep '{$GLOBALS['argv'][0]}' | awk '{print \$8,\$9}'";
        $procTotal = 0;
        $p = popen($cmd, 'r');
        while(!feof($p)) {
            $line = trim(fgets($p, 1024));
            if(empty($line))
            {
                continue;
            }
            list($bin, $script) = explode(' ', $line);
            if(substr($bin, -3)!='php')
            {
                continue;
            }
            if(substr($script, -strlen($GLOBALS['argv'][0])) != $GLOBALS['argv'][0])
            {
                continue;
            }
            $procTotal++;
        }
        pclose($p);
        if($procTotal > 1) {
            exit;
        }
        $this->log('notice|monitor启动的脚本');
        return;
    }

    public static function getLocalHostName()
    {
        return gethostname();
    }

    public function log($msg)
    {
        $msg     = date('Y-m-d H:i:s').'|'.$msg;
        $logFile = sprintf($this->startLogFile, date('Ymd'));
        @file_put_contents($logFile, $msg."\n",8);
    }

    private function getErrorFile()
    {
        return sprintf($this->errorFile, date('Ymd'));
    }

    private function initDir()
    {
        if(is_dir(self::CRON_RUN_LOG_DIR))
        {
            return true;
        }
        $r = mkdir(self::CRON_RUN_LOG_DIR, 0777, true);
        clearstatcache();
        return $r;
    }

    public static function getConfigFile()
    {
        return sprintf(self::CRON_CONFIG_FILE,get_hp_env());
    }

    /**
     * 校验配置文件是否有变动
     * @return true | false (没有变动返回为true,有变动返回false)
     */
    private function checkConfStatus()
    {
        static $md5 = '';
        $configFile = self::getConfigFile();

        if (!$md5)
        {
            if(!file_exists($configFile))
            {
                $this->log("error|crontab 配置文件不存在，{$configFile}");
                exit("error|crontab 配置文件不存在，{$configFile}");
            }
            $md5 = md5_file($configFile);

            return true;
        }

        return (md5_file($configFile) == $md5 );
    }

    /**
     * 校验 监控代码是否有变动
     * @return true | false (没有变动返回为true,有变动返回false)
     */
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

class CronStatus
{

    private $param = [];

    //cron名
    public function setName($name)
    {
        $this->param['name'] = $name;
        return $this;
    }

    public function getName()
    {
        return isset($this->param['name']) ? $this->param['name'] : '';
    }

    //运行机器
    public function setServer(array $server)
    {
        $this->param['server'] = $server;
        return $this;
    }

    public function getServer()
    {
        return isset($this->param['server']) ? $this->param['server'] : [];
    }

    //crontab
    public function setCrontab($crontab)
    {
        $this->param['crontab'] = $crontab;
    }

    public function getCrontab()
    {
        return isset($this->param['crontab']) ? $this->param['crontab'] : '';
    }

    //script name
    public function setCmd($cmd)
    {
        $this->param['cmd'] = $cmd;
        return $this;
    }

    public function getCmd()
    {
        return isset($this->param['cmd']) ? $this->param['cmd'] : false;
    }

    //脚本名
    public function setScript($script)
    {
        $this->param['script'] = $script;
        return $this;
    }

    public function getScript()
    {
        return isset($this->param['script']) ? $this->param['script'] : false;
    }

    //脚本是否关闭 1正常，2关闭
    public function setStatus($status)
    {
        $this->param['status'] = $status;
        return $this;
    }

    public function getStatus()
    {
        return isset($this->param['status']) ? $this->param['status'] : 0;
    }

    //上一次运行时间
    public function setPreCronRunTime($preRunTime)
    {
        $this->param['pre_cron_run_time'] = date('Y-m-d H:i:s',$preRunTime);
        return $this;
    }

    public function getPreCronRunTime()
    {
        return isset($this->param['pre_cron_run_time']) ? $this->param['pre_cron_run_time'] : 0;
    }

    //当前运行时间
    public function setRunTime($runTime)
    {
        $this->param['run_time'] = $runTime;
        return $this;
    }

    public function getRunTime()
    {
        return isset($this->param['run_time']) ? $this->param['run_time'] : 0;
    }

    public function setRunStatus($s = 0)
    {
        $this->param['run_status'] = (int) $s;
        return $this;
    }

    public function getRunStatus()
    {
        return isset($this->param['run_status']) ? (int) $this->param['run_status'] : 0;
    }

    public function setCronTime($time)
    {
        $time = (int) $time;
        if($time == 0)
        {
            return false;
        }

        $t  = date('Y-m-d H:i:00',$time);
        $ct = strtotime($t);
        if(isset($this->param['cron_time']) && $this->param['cron_time'] == $ct)
        {
            return $this;
        }

        if(isset($this->param['cron_time']))
        {
            unset($this->param[$this->getJobStatusKey($this->param['cron_time'])]);
            $this->setJobStatus($ct,0);
            $this->setPreCronRunTime($this->getRunTime());
            $this->setRunTime(0);
        }

        $this->param['cron_time'] = $ct;
        $this->setCronTimeStr($t);

        return $this;
    }

    public function getCronTime()
    {
        return isset($this->param['cron_time']) ? (int) $this->param['cron_time'] : 0;
    }

    public function setCronTimeStr($time)
    {
        $this->param['cron_time_str'] = $time;
        return $this;
    }

    public function getCronTimeStr()
    {
        return isset($this->param['cron_time_str']) ? $this->param['cron_time_str'] : 0;
    }

    public function setIsStartCron($s = 0)
    {
        $this->param['is_start_cron'] = (int) $s;
        return $this;
    }

    public function getIsStartCron()
    {
        return isset($this->param['is_start_cron']) ? (int) $this->param['is_start_cron'] : 0;
    }

    public function setJobStatus($ct,$status = 0)
    {
        $key = $this->getJobStatusKey($ct);
        $this->param[$key] = (int) $status;
        return $this;
    }

    public function getJobStatus($ct)
    {
        $key = $this->getJobStatusKey($ct);
        return isset($this->param[$key]) ? (int) $this->param[$key] : 0;
    }

    public function getJobStatusKey($ct)
    {
        return "job_status_{$ct}";
    }

    public function setMonitorTime($time)
    {
        $this->param['monitor_time'] = $time;
        return $this;
    }

    public function getMonitorTime()
    {
        return isset($this->param['monitor_time']) ? (int) $this->param['monitor_time'] : 0;
    }

    public function setLocalHostname($name)
    {
        $this->param['local_host_name'] = $name;
        return $this;
    }

    public function getLocalHostName()
    {
        return isset($this->param['local_host_name']) ? $this->param['local_host_name'] : '';
    }

    public function getParam()
    {
        return $this->param;
    }
}

class CrontabParse
{

    public static function parse($_cron_string,$_after_timestamp = null)
    {
        if(!preg_match('/^((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)$/i',trim($_cron_string))){
            throw new InvalidArgumentException("Invalid cron string: ".$_cron_string);
        }
        if($_after_timestamp && !is_numeric($_after_timestamp)){
            throw new InvalidArgumentException("\$_after_timestamp must be a valid unix timestamp ($_after_timestamp given)");
        }
        $cron   = preg_split("/[\s]+/i",trim($_cron_string));
        $start  = empty($_after_timestamp)?time():$_after_timestamp;

        $date   = [    'minutes'   => self::_parseCronNumbers($cron[0],0,59),
                        'hours'    => self::_parseCronNumbers($cron[1],0,23),
                        'dom'      => self::_parseCronNumbers($cron[2],1,31),
                        'month'    => self::_parseCronNumbers($cron[3],1,12),
                        'dow'      => self::_parseCronNumbers($cron[4],0,6),
                ];

        for($i = 0; $i <= 60*60*24*366; $i += 60)
        {
            if( in_array(intval(date('j',$start+$i)),$date['dom']) &&
                in_array(intval(date('n',$start+$i)),$date['month']) &&
                in_array(intval(date('w',$start+$i)),$date['dow']) &&
                in_array(intval(date('G',$start+$i)),$date['hours']) &&
                in_array(intval(date('i',$start+$i)),$date['minutes'])

                )
            {
                    return $start+$i;
            }
        }

        return null;
    }

    protected static function _parseCronNumbers($s,$min,$max)
    {
        $result = [];

        $v = explode(',',$s);
        foreach($v as $vv)
        {
            $vvv  = explode('/',$vv);
            $step = empty($vvv[1])?1:$vvv[1];
            $vvvv = explode('-',$vvv[0]);
            $_min = count($vvvv)==2?$vvvv[0]:($vvv[0]=='*'?$min:$vvv[0]);
            $_max = count($vvvv)==2?$vvvv[1]:($vvv[0]=='*'?$max:$vvv[0]);

            for($i=$_min;$i<=$_max;$i+=$step){
                $result[$i]=intval($i);
            }
        }
        ksort($result);
        return $result;
    }
}

$cronMonitor = new CronMonitor();
$cronMonitor->run();