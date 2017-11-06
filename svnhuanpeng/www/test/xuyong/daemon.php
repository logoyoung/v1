<?php
require __DIR__.'/../../include/system/Daemon.php';

class Test extends Worker {
    public $c = 0;
    public function timeoutAlarm() {
        file_put_contents('/tmp/timeout.log',$this->data."|try_num{$this->timeout_num}\n",8);
    }

    public function job() {

        for($i = 1 ; $i <= 10; $i++ ){
            $this->data = $i;
            sleep(1);
            file_put_contents('/tmp/data.log',date('Y-m-d H:i:s').'==value: '.$i."\n",8);
            $this->c++;
        }
        sleep(1);
    }

}

$worker = new Test();
//日志目录
$worker->setLogPath('/tmp/');
// pid
$worker->setPidFile('t1');
//日志名
$worker->setLogFile('t1');
//超时间
$worker->setTimeout(15);
//超时重试
$worker->setTimeoutTryNum(1);
//开起调式
$worker->setDebug(true);
//需要开的进程数
$worker->setWorker(20);
$worker->run();
