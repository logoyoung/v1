<?php
namespace yalong;
ini_set('display_errors',1);            //错误信息
ini_set('display_startup_errors',1);    //php启动错误信息
include __DIR__."/../../include/init.php";
use service\push\SystemMsgTask;

class testAddSystemMsg
{
    private $touid;
    private $title;
    private $msg;
    private $system = NULL;
    public function __construct(){
        $this->system = is_null($this->system) ? new SystemMsgTask() : $this->system;
    }
    public function setToUids($uids){
        $this->touid = $uids;
    }
    public function setTitle($title){
        $this->title = $title;
    }
    public function setMsg($msg){
        $this->msg= $msg;
    }
    public function test(){
        $this->system->addSystemMsg($this->touid,$this->title,$this->msg);
    }
    public function testAll(){
        $this->system->addAllUserMsg(1,'testing', 'going....');
    }
    public function getRedisData(){
        $this->system->getRedisData();
    }
}

$obj = new testAddSystemMsg();
$obj->setToUids('73163,2305,2295,62068,1860');
$obj->setTitle('系统消息测试');
$obj->setMsg('系统消息测试下发中...');
$obj->test(); 
sleep(1);
$obj->testAll();
$obj->getRedisData();

