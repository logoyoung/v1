<?php
/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017年7月31日
 * Time: 上午11:37:04
 * Desc: 融云系统广播消息 定时下发脚本
 */
namespace due;
ignore_user_abort();
set_time_limit(0);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
include '/data/huanpeng/include/init.php';

use system\RedisHelper; 
use service\push\SystemPush;
use service\push\SystemMsgTask;
use lib\user\UserStatic;
class systemMsg
{
    //消息下发错误码 
    private $send_error_01 = 9001; //redis 服务异常
    private $send_over_code= 10000;//消息下发完成
    private $redisObj = NULL;
    private $pushObj = NULL;
    private $userObj = NULL;
    
    private $msg = [
        10000 => '系统广播消息下发完毕.',
        9001  => 'redis 服务可能异常', 
    ];
    const SYSTEM_ACCEPT_KEY_PREFIX = 'systemAcceptUids_';
    const FIRST_SEND_MSG_NUMBER    = 1000; //一次取 用户uid的偏移量
    
    public function __construct(){
        if(is_null($this->redisObj))
            $this->redisObj = RedisHelper::getInstance("huanpeng");
        if(is_null($this->pushObj))
            $this->pushObj= new SystemPush();
        if(is_null($this->userObj))
            $this->userObj= new UserStatic();
    }
    public function sendSystemMsg()
    {   
        try{
            //系统消息 key不存在 说明 已经下发完毕 或者 暂时 未下发消息
            if(!$this->redisObj->exists(SystemMsgTask::SYSTEM_HAS_MSG_01))
                return false;
            //redis获取 hasTab 等待下发的消息列表
            $hasMsg = $this->redisObj->hGetAll(SystemMsgTask::SYSTEM_HAS_MSG_01);
            var_dump($hasMsg); 
            if(empty($hasMsg)) return false;
            foreach($hasMsg as $k=>$v)
            {
                write_log("1、消息体key:".SystemMsgTask::SYSTEM_HAS_MSG_01."/{$k}".PHP_EOL." 指定用户下发消息体：".json_encode($v),'SystemMsgError');
                //获取每条  消息提对应下发的用户list
                $prefix = explode("_", $k);
                $listkey = self::SYSTEM_ACCEPT_KEY_PREFIX.$prefix[1];
                //redis pop 接收人uid list 
                $ltouid = $this->redisObj->lrange($listkey,0,-1);
                write_log("1、接收用户队列key:{$listkey}".PHP_EOL."指定用户uid：".json_encode($ltouid),'SystemMsgError');
                $res = $this->_sendSystemMsg($v, $ltouid);sleep(1);
                if($res)
                {
                    foreach($ltouid as $v)
                    {
                        $this->redisObj->lPop($listkey);
                    }
                }
                //检索 接收用户uid  list列表中是否还有，没有 本次消息发送完毕 清除 redis相应 key
                $touidList = $this->redisObj->lrange($listkey,0,-1);
                if(empty($touidList))
                {
                    echo '完毕..'.PHP_EOL."----------------------".PHP_EOL;
                    //消息下发完毕  清除 历史消息残余信息
                    $res1 = $this->redisObj->delete($listkey);
                    $res2 = $this->redisObj->hDel(SystemMsgTask::SYSTEM_HAS_MSG_01,$k);
                    write_log("1、清空相应消息体 key:".SystemMsgTask::SYSTEM_HAS_MSG_01."/{$k}：".json_encode($res2),'SystemMsgError');
                } 
            }
        }catch (\Exception $e)
        {
            return false;
            write_log(__CLASS__." 第".__LINE__."行 ".$e->getMessage(),'SystemMsgError');
        }
    } 
    //调用服务层下发 欢朋socket消息
    private function _sendSystemMsg($hasMsg,$ltouid)
    {
        $hasMsg = json_decode($hasMsg,true);  
        $res = $this->pushObj->send($ltouid, $hasMsg['title'], $hasMsg['msg'], $hasMsg['action'], $hasMsg['custom']);
        return $res;
    } 
    // 处理全站用户  系统消息下发
    public function sendAllUserMsg()
    {
//         var_dump($this->redisObj->delete(SystemMsgTask::SYSTEM_HAS_MSG_02));
//         var_dump($this->redisObj->delete(SystemMsgTask::SYSTEM_ALL_USER_MSG_LIST));
//         var_dump($this->redisObj->hGetAll(SystemMsgTask::SYSTEM_HAS_MSG_02));
//         var_dump($this->redisObj->lrange(SystemMsgTask::SYSTEM_ALL_USER_MSG_LIST,0,-1)); exit;
        $uidstr = 0;
        while (true)
        {
            $popMsg = $this->redisObj->lPop(SystemMsgTask::SYSTEM_ALL_USER_MSG_LIST);
            write_log("2、全站接收 - 消息队列：".json_encode($popMsg),'SystemMsgError');
            if(!$popMsg)
            {
                break; //直接死掉，一分钟后 定时脚本会重新唤醒
            }
            $hashMsg = $this->redisObj->hGet(SystemMsgTask::SYSTEM_HAS_MSG_02,$popMsg);
            //清除 hash 消息体
            if($this->redisObj->hExists(SystemMsgTask::SYSTEM_HAS_MSG_02,$popMsg)){
                $res3 = $this->redisObj->hDel(SystemMsgTask::SYSTEM_HAS_MSG_02,$popMsg);
                write_log("2、清空相应消息体：".SystemMsgTask::SYSTEM_HAS_MSG_02."/{$popMsg}：".PHP_EOL.json_encode($res3),'SystemMsgError');
            }
            write_log("2、全站系统消息".SystemMsgTask::SYSTEM_HAS_MSG_02."/{$popMsg}：".PHP_EOL.json_encode($hashMsg),'SystemMsgError');
            $page = 1;
            while (true)
            {
                $uids = $this->userObj->getUserStaticList($page, self::FIRST_SEND_MSG_NUMBER,['uid']);
                $uidstr+=count($uids);
                $page++;
                if(!empty($uids))
                {
                    $uids = array_column($uids, 'uid');
                    write_log("2、全站消息下发 用户数：$uidstr",'SystemMsgError');
                    $res = $this->_sendSystemMsg($hashMsg, $uids);
                }else
                {
                    break;
                }
                sleep(1);
            }
        } 
    }
    //清理缓存数据
    public function clearMsgCache(){
        $hasMsg = $this->redisObj->hGetAll(SystemMsgTask::SYSTEM_HAS_MSG_01);
        $keys = $this->redisObj->keys("*".self::SYSTEM_ACCEPT_KEY_PREFIX."*");
        var_dump($hasMsg);
        var_dump($keys);
        foreach($hasMsg as $k=>$v){
            $this->redisObj->hDel(SystemMsgTask::SYSTEM_HAS_MSG_01,$k);
        }
        foreach($keys as $v){
            $this->redisObj->delete($v);
        }
        $hasMsg = $this->redisObj->hGetAll(SystemMsgTask::SYSTEM_HAS_MSG_01);
        $keys = $this->redisObj->keys("*".self::SYSTEM_ACCEPT_KEY_PREFIX."*");
        var_dump($hasMsg);
        var_dump($keys);
        //----------------------------
        echo PHP_EOL.'-------------------'.PHP_EOL;
        //打印全站系统消息
        var_dump($this->redisObj->hGetAll(SystemMsgTask::SYSTEM_HAS_MSG_02));
        var_dump($this->redisObj->lrange(SystemMsgTask::SYSTEM_ALL_USER_MSG_LIST,0,-1));
        //清理全站消息 key
        var_dump($this->redisObj->delete(SystemMsgTask::SYSTEM_HAS_MSG_02));
        var_dump($this->redisObj->delete(SystemMsgTask::SYSTEM_ALL_USER_MSG_LIST));
        //测试是否清理
        var_dump($this->redisObj->hGetAll(SystemMsgTask::SYSTEM_HAS_MSG_02));
        var_dump($this->redisObj->lrange(SystemMsgTask::SYSTEM_ALL_USER_MSG_LIST,0,-1));
    }
    public function getData(){
        echo "指定用户下发的系统消息信息：".PHP_EOL;
        $hasMsg = $this->redisObj->hGetAll(SystemMsgTask::SYSTEM_HAS_MSG_01);
        $keys = $this->redisObj->keys("*".self::SYSTEM_ACCEPT_KEY_PREFIX."*");
        var_dump($hasMsg);
        echo "-----------------------".PHP_EOL;
        var_dump($keys);
        echo PHP_EOL.PHP_EOL."全站用户下发系统消息：".PHP_EOL;
        var_dump($this->redisObj->hGetAll(SystemMsgTask::SYSTEM_HAS_MSG_02));
        echo "-----------------------".PHP_EOL;
        var_dump($this->redisObj->lrange(SystemMsgTask::SYSTEM_ALL_USER_MSG_LIST,0,-1));
    }
}

$rongSendMsgObj = new systemMsg(); 

$rongSendMsgObj->getData();
//  $rongSendMsgObj->clearMsgCache();exit;

$rongSendMsgObj->sendSystemMsg(); 
sleep(1);
$rongSendMsgObj->sendAllUserMsg();



