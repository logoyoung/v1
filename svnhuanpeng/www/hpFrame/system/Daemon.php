<?php

class SystemMsgQueue {

    public static $msgtype_send      = 1;
    public static $msgtype_receive   = 1;
    public static $is_serialize      = true;
    public static $block_send        = false;
    public static $maxsize           = 6144;
    public static $option_receive    = MSG_IPC_NOWAIT;

    public static function getIp($ip) {
        return sprintf("%u",crc32($ip));
    }

    public static function getQueue($ip) {
        return msg_get_queue(self::getIp($ip));
    }

    public static function sendMsg($ip,$msg) {
        $r = msg_send(self::getQueue($ip),self::$msgtype_send, $msg,self::$is_serialize, self::$block_send,$err);
        return $r ? true : $err;
    }

    public static function getMsg($ip) {
        $r = msg_receive(self::getQueue($ip),self::$msgtype_receive ,$msgtype,self::$maxsize,$data,self::$is_serialize, self::$option_receive, $err);
         return ($r === true) ? $data : false;
    }

    public static function getMsgNum($ip) {
        $queue_status = msg_stat_queue(self::getQueue($ip));
        return (int) $queue_status['msg_qnum'];
    }

    public static function removeQueue($ip) {
        if(!msg_queue_exists(self::getIp($ip))){
            return true;
        }
        return msg_remove_queue(self::getQueue($ip));
    }
}

declare(ticks=1);

class Daemon {

    public static $default_log_path      = '/tmp/';
    public static $default_log_dir_name  = 'daemon_log';

    private $_info_dir;
    private $_mypid;
    private $_pid_file;
    private $_log_file;
    private $_terminate     = false;
    private $_workers_count = 0;
    private $_set_user      = false;
    private $_user          = 'www';
    private $_skill         = false;
    private $_start_count   = 0;
    private $_child         = [];

    /**
     *
     * @param string $pid_file
     * @param string $log_file
     * @param bool   $skill 强列重启
     */
    public function __construct($log_path = null,$log_dir_name = null,$pid_file = null, $log_file = null,$skill = false) {

        $log_path        = $log_path     ?:self::$default_log_path;
        $log_dir_name    = $log_dir_name ?: self::$default_log_dir_name;
        $this->_info_dir = rtrim($log_path,'/').DIRECTORY_SEPARATOR.$log_dir_name;
        if(!is_dir($this->_info_dir)){
            @mkdir($this->_info_dir, 0777,true);
        }
        $this->_skill = $skill;
        if (!$pid_file) {
            $pid_file = get_called_class();
        }
        if (!$log_file) {
            $log_file = get_called_class();
        }
        $this->_checkPcntl();
        $this->_setSignalHandler();

        $this->_setPidFile($pid_file);
        $this->_setLogFile($log_file);
        if (function_exists('gc_enable')) {
            gc_enable();
        }
    }

    private function _setPidFile($pid_file = null) {
        if ($pid_file) {
            $this->_pid_file = $this->_info_dir . DIRECTORY_SEPARATOR . trim($pid_file, '/') . '.pid';
            return;
        }
        exit('you should set the pid file');
    }

    private function _setLogFile($log_file = null) {
        if ($log_file) {
            $this->_log_file = $this->_info_dir . DIRECTORY_SEPARATOR . trim($log_file, '/') . '.log';
            return;
        }
        exit('you should set the log file');
    }

    public function setUser($user) {
        $this->_user = $user;
        return $this;
    }

    private function _checkPcntl() {
        if (!function_exists('pcntl_signal')) {
            exit('PHP does not appear to be compiled with the PCNTL extension');
        }
    }

    private function _setSignalHandler() {
        pcntl_signal(SIGTERM, array(__CLASS__, 'signalHandler'), false);
        pcntl_signal(SIGINT, array(__CLASS__, 'signalHandler'), false);
        pcntl_signal(SIGQUIT, array(__CLASS__, 'signalHandler'), false);
        pcntl_signal(SIGCHLD, array(__CLASS__, 'signalHandler'), false);
        pcntl_signal(SIGUSR1, array(__CLASS__, 'signalHandler'), false);
    }

    private function _restoreSignalHandler() {
        pcntl_signal(SIGTERM, SIG_DFL);
        pcntl_signal(SIGINT, SIG_DFL);
        pcntl_signal(SIGQUIT, SIG_DFL);
        pcntl_signal(SIGCHLD, SIG_DFL);
        pcntl_signal(SIGUSR1, SIG_DFL);
    }

    public function signalHandler($signo) {
        switch ($signo) {

            case SIGTERM:
            case SIGINT:
            case SIGQUIT:
                $child = array_keys($this->_child);
                if($child){
                    foreach($child as $_child_pid){
                        posix_kill($_child_pid,SIGTERM);
                    }
                }
                $this->_terminate = true;
                break;

            case SIGCHLD:
                while (($pid = pcntl_waitpid(-1, $status, WNOHANG)) > 0) {
                    $this->_workers_count--;
                    unset($this->_child[$pid]);
                    $this->log("The parent process receives the child:{$pid} process exit signal");
                }
                break;

            case SIGUSR1:
                break;

        }
    }

    public function daemonize() {
        set_time_limit(0);

        if (php_sapi_name() != 'cli') {
            $message = 'only run in cli mode';
            $this->log($message);
            exit($message);
        }

        $this->_checkPidFile();

        umask(0);

        if (pcntl_fork() != 0) {
            exit();
        }

        posix_setsid();


        if (pcntl_fork() != 0) {
            exit();
        }

        //change directory
        chdir('/');

        if ($this->_set_user) {
            if (!$this->_setUser($this->_user)) {
                $message = 'cannot change owner';
                $this->log($message);
                exit($message);
            }
        }

        // close open file description
        fclose(STDIN);
        fclose(STDOUT);

        $this->_createPidFile();
        return $this;
    }

    private function _checkPidFile() {
        if (!file_exists($this->_pid_file)) {
            return;
        }
        $pid = (int) trim(file_get_contents($this->_pid_file));
        if($this->_skill){
            posix_kill($pid, SIGTERM);
            usleep(100000);
            return;
        }
        if ($pid > 0 && posix_kill($pid, 0)) {
            $message = 'the daemon process is already started';
            $this->log($message);
            exit($message);
        }
        unlink($this->_pid_file);
        $this->getMsgObj()->removeQueue($pid);
        return;
    }

    private function _createPidFile() {
        if (!is_dir($this->_info_dir)) {
            mkdir($this->_info_dir);
        }

        $fp = fopen($this->_pid_file, 'w');
        if (!$fp) {
            exit('can not create pid file');
        }
        $this->_mypid = posix_getpid();
        fwrite($fp, $this->_mypid);
        fclose($fp);
        clearstatcache(true,$this->_pid_file);
        $this->log('create pid file ' . $this->_pid_file);
    }

    private function _deletePidFile() {
        $pidFile = $this->_pid_file;
        if (file_exists($pidFile)) {
            unlink($pidFile);
            $this->log('delete pid file ' . $pidFile);
        }
    }

    private function _setUser($name) {
        if (empty($name)) {
            return true;
        }

        $user = posix_getpwnam($name);
        if ($user) {
            $uid = $user['uid'];
            $gid = $user['gid'];
            $result = posix_setuid($uid);
            posix_setgid($gid);
            return $result;
        }

        return false;
    }

    public function start($count = 1) {
        $this->log('daemon process is running now');
        $this->_start_count = $count;

        while (true) {

            $this->_checkChildQuit();
            if ($this->_terminate || ($this->_start_count <= 0 && $this->_getMsgNum() <= 0 && count($this->_child) <= 0) ) {
                break;
            }

            if ( $this->_workers_count <  $this->_start_count ) {

                $pid = pcntl_fork();
                if ($pid > 0) {
                    $this->_workers_count++;
                    $this->_child[$pid] = date('Y-m-d H:i:s');
                    $this->log("child pid:{$pid} starting");
                } elseif ( $pid == 0) {
                    $this->_restoreSignalHandler();
                    return;
                }

            }

            sleep(1);
        }
        $this->_mainQuit();
        exit(0);
    }

    private function _getMsgNum() {
        return (int) SystemMsgQueue::getMsgNum($this->_mypid);
    }

    private function _checkChildQuit() {
        $num = $this->_getMsgNum();
        if( $num <= 0 ){
            return;
        }

        for( $i = 0; $i < $num; $i++ ) {
            $quit = SystemMsgQueue::getMsg($this->_mypid);
            if( $quit === false ){
                return;
            }
            $pid = (int) $quit['pid'];
            if($pid > 0){
                $this->_start_count--;
                posix_kill($pid, SIGTERM);
            }
        }
        return;
    }

    private function _deleleSystemMsgQueue() {
        return SystemMsgQueue::removeQueue($this->_mypid);
    }

    public function stopMsg($ppid,$pid) {
        return SystemMsgQueue::sendMsg($ppid,[ 'pid' => $pid,'time' => time() ] );
    }

    private function _mainQuit() {
        $this->_deleleSystemMsgQueue();
        $this->_deletePidFile();
        $this->log('daemon process exit now');
        return;
    }

    public function log($message) {
        $message = date('Y-m-d H:i:s') . " pid:" . posix_getpid() . " ppid:" . posix_getppid() ." ". $message . "\n";
        file_put_contents($this->_log_file, $message, FILE_APPEND);
    }

}

abstract class Worker {

    protected $pid;
    protected $ppid;
    protected $timeout = 0;
    protected $run     = true;
    protected $daemon;
    protected $worker  = 1;
    protected $timeout_try = 1;
    protected $timeout_num = 0;
    protected $data;
    protected $log_path = '/tmp';
    protected $log_dir_name = 'daemon_log';
    protected $pid_file = null;
    protected $log_file = null;
    protected $skill    = false;
    protected $debug    = true;
    public $status      = true;
    public $stop_time   = 0;
    public $smax_time   = 180;

    /**
     *  具体处理业务逻辑方法 (此方法执行一个死循环,退出请调用stop()方法)
     * @return void
     */
    abstract public function job();

    /**
     *  进程超时处理方法 （可以记录日志或设置报警程序）
     * @return void
     */
    public function timeoutAlarm() {

    }

    /**
     * 设置进程数
     * @param integer $worker 1
     */
    public function setWorker($worker = 1) {
        $worker       = (int) $worker;
        $this->worker = $worker > 0 ? $worker : 1;
        return $this;
    }

    /**
     * 设置每个进程超时间
     * @param integer $timeout 单位秒，0为永久（默认为不超时）
     */
    public function setTimeout($timeout = 0) {
        $this->timeout = (int) $timeout;
        return $this;
    }

    /**
     * 设置每个进程最多超时次数 （达到超时次数会自动退出重启）
     * @param integer $try_num 超时次数
     */
    public function setTimeoutTryNum($try_num = 1) {
        $this->timeout_try = (int) $try_num;
        return $this;
    }

    public function setLogPath($log_path = null) {
        if(!$log_path){
            return $this;
        }
        $this->log_path = trim($log_path);
        if(!is_dir($this->log_path) || !is_writable($this->log_path)) {
            exit('Invalid log path');
        }
        return $this;
    }

    public function setLogDirName($log_dir_name = null) {
        if(!$log_dir_name){
            return $this;
        }
        $this->log_dir_name = trim($log_dir_name);
        return $this;
    }

    public function setPidFile($pid_file = null) {
        if(!$pid_file){
            return $this;
        }
        $this->pid_file = trim($pid_file);
        return $this;
    }

    public function setLogFile($log_file = null) {
        if(!$log_file){
            return $this;
        }
        $this->log_file = trim($log_file);
        return $this;
    }

    public function setSkill($skill=true) {
        $this->skill = (bool) $skill;
        return $this;
    }

    public function setDebug($debug=true) {
        $this->debug = (bool) $debug;
        return $this;
    }

    private function _setChildSinalHandler() {
        pcntl_signal(SIGTERM, array(__CLASS__, 'singalChildHandler'), false);
        pcntl_signal(SIGINT, array(__CLASS__, 'singalChildHandler'), false);
        pcntl_signal(SIGQUIT, array(__CLASS__, 'singalChildHandler'), false);
        pcntl_signal(SIGCHLD, array(__CLASS__, 'singalChildHandler'), false);
        pcntl_signal(SIGUSR1, array(__CLASS__, 'singalChildHandler'), false);
        pcntl_signal(SIGALRM, array(__CLASS__, 'singalChildHandler'), false);
    }

    public function singalChildHandler($signo) {

        switch ($signo) {

            case SIGTERM:
            case SIGINT:
            case SIGQUIT:
                $this->run = false;
                if($this->debug){
                    $this->daemon->log("debug info rcv signo:{$signo}, ppid:{$this->ppid}, pid:{$this->pid}");
                }
                break;

            // timeout
            case SIGALRM:
                $this->timeout_num++;
                $this->timeoutAlarm();
                $this->setAlarm();
                if($this->debug){
                    $this->daemon->log("debug info rcv timeout signo:{$signo},ppid:{$this->ppid}, pid:{$this->pid}");
                }
                if($this->timeout_try < 0){
                    break;
                }
                if($this->timeout_num > $this->timeout_try) {
                    exit(0);
                }
                break;

            case SIGCHLD:
                while (($pid = pcntl_waitpid(-1, $status, WNOHANG)) > 0) {
                }
                break;

            case SIGUSR1:
                break;

        }

    }

    protected function setAlarm() {
        if($this->timeout){
             pcntl_alarm($this->timeout);
        }
        return $this;
    }

    public function run() {
        $this->daemon = new Daemon($this->log_path,$this->log_dir_name,$this->pid_file,$this->log_file,$this->skill);
        $this->daemon->daemonize();
        $this->daemon->start($this->worker);
        $this->_setChildSinalHandler();
        $this->pid    = posix_getpid();
        $this->ppid   = posix_getppid();

        while($this->run) {
            $this->setAlarm();
            if($this->status){
                $this->job();
            } else {
                if((time() - $this->stop_time) >= $this->smax_time){
                    break;
                }
                $this->daemon->log("{$this->ppid}, pid:{$this->pid} Wait for the parent process to send a stop signal");
                sleep(1);
            }
        }

        if($this->debug){
            $this->daemon->log("debug ppid:{$this->ppid}, pid:{$this->pid} exit now ");
        }

        exit(0);
    }

    public function stop() {
        $try = 3;
        do {
            $stop = $this->daemon->stopMsg($this->ppid,$this->pid);
            if($stop === true){
                $this->status    = false;
                $this->stop_time = time();
                return true;
            }
            sleep(1);
        } while( $try-- > 0);

        $this->daemon->log("stop error ppid:{$this->ppid}, pid:{$this->pid}  send SystemV error");
        return false;
    }

}