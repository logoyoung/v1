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
use service\due\rongCloud\RongCloudService;
use service\due\rongCloud\RongCloudServiceHelp;
class rongCloudSystemMsg
{
    //消息下发错误码
    private $send_error_01 = 9001; //融云系统广播消息 下发失败
    private $send_error_02 = 9002; //redis 服务异常
    private $send_over_code= 10000;//消息下发完成
    private $redisObj = NULL;
    private $rongCloud = NULL;
    
    private $msg = [
        10000 => '系统广播消息下发完毕.',
        9002  => 'redis 服务可能异常',
        9001  => '融云系统广播消息下发失败'
    ];
    const RONG_ACCEPT_KEY_PREFIX = 'rongAcceptUids_';
    
    public function __construct(){
        if(is_null($this->redisObj))
            $this->redisObj = RedisHelper::getInstance("huanpeng");
        if(is_null($this->rongCloud))
            $this->rongCloud = new RongCloudService();
    }
    public function sendSystemMsg(){
        $rongServer = $this->rongCloud->getInstance("android");
        //下发 消息计数
        $io = 1;
        //系统广播消息 下发限制次数
        $msgNum = RongCloudServiceHelp::RONG_SYS_MSG_NUM; 
        try{
            //融云系统广播消息 key不存在 说明 已经下发完毕 或者 暂时 未下发消息
            if(!$this->redisObj->exists(RongCloudServiceHelp::RONG_HAS_MSG_01))
                return false;
            //redis获取 hasTab 等待下发的消息列表
            $hasMsg = $this->redisObj->hGetAll(RongCloudServiceHelp::RONG_HAS_MSG_01);
            foreach($hasMsg as $k=>$v){
                //获取每条  消息提对应下发的用户list
                $prefix = explode("_", $k);
                $listkey = self::RONG_ACCEPT_KEY_PREFIX.$prefix[1];
                //redis pop 接收人uid list 
                $ltouid = $this->redisObj->lrange($listkey,0,-1);
                foreach($ltouid as $vo){
                    if(!$this->_sendSystemMsg($rongServer,$v,$vo)){
                        $res = $this->resendMsg($rongServer,$v,$vo);
                        if(!$res){
                            //重试三次失败  直接pop掉 不再下发
                            $this->redisObj->lPop($listkey);
                            write_log(__CLASS__." 第".__LINE__."行   融云广播消息 下发用户：{$ltouid} 重试三次依然失败".PHP_EOL,'rongSystemMsgError');
                        }else{
                            $io+=1;
                            $this->redisObj->lPop($listkey);
                        }
                    }else{
                        $io+=1;
                        $this->redisObj->lPop($listkey);
                    }
                    //发送 100 整数倍 睡觉一秒钟
                    if($io % $msgNum == 0) sleep(1);
                    //检索 接收用户uid  list列表中是否还有，没有 本次消息发送完毕 清除 redis相应 key
                    $touidList = $this->redisObj->lrange($listkey,0,-1);
                    if(empty($touidList)){
                        echo '完毕..'.PHP_EOL."----------------------".PHP_EOL;
                        //消息下发完毕  清除 历史消息残余信息
                        $this->redisObj->delete($listkey);
                        $this->redisObj->hDel(RongCloudServiceHelp::RONG_HAS_MSG_01,$k);
                    } 
                }
            }
        }catch (\Exception $e){
            return false;
            write_log(__CLASS__." 第".__LINE__."行 ".$e->getMessage(),'rongSystemMsgError');
        }
    }
    //发送失败 重试三次
    private function resendMsg($rongServer,$hasMsg,$ltouid){
        for($i = 0; $i < 3; $i++){
//             echo $i.PHP_EOL;
            if($this->_sendSystemMsg($rongServer, $hasMsg,$ltouid)){
                $sendSuccessTag = 100;
                break;
            }
        }
        return !isset($sendSuccessTag) ? false : true;
    }
    //调用服务层下发 融云消息
    private function _sendSystemMsg($rongServer,$hasMsg,$ltouid){
        $hasMsg = json_decode($hasMsg,true); 
        $content = json_encode($hasMsg['content'],true);
        $pushContent= json_encode($hasMsg['pushContent'],true);
        $res = $rongServer->sendSystemMsg($hasMsg['fid'],$ltouid,$hasMsg['sendCg'],$content, $pushContent, $hasMsg['pushData'], $hasMsg['isCounted'], $hasMsg['isPersisted']);
        return $res;
    }
    //测试获取 缓冲中的数据
    public function getData(){ 
        $hasData = $this->redisObj->hGetAll(RongCloudServiceHelp::RONG_HAS_MSG_01);
        $this->redisObj->hDel(RongCloudServiceHelp::RONG_HAS_MSG_01,'HasField_1');
//         $this->redisObj->hDel(RongCloudServiceHelp::RONG_HAS_MSG_01,'HasField_2');
//         $this->redisObj->hDel(RongCloudServiceHelp::RONG_HAS_MSG_01,'HasField_3');
//         $this->redisObj->delete('rongAcceptUids_1');
//         $this->redisObj->delete('rongAcceptUids_2');
//         $this->redisObj->delete('rongAcceptUids_3');
        var_dump($hasData);
        foreach ($hasData as $k=>$v){
            $prefix = explode("_", $k); 
            $listkey = self::RONG_ACCEPT_KEY_PREFIX.$prefix[1];
            $listData = $this->redisObj->lrange($listkey,0,-1);
            var_dump($listData);
        }
    }
}

$rongSendMsgObj = new rongCloudSystemMsg();
// $rongSendMsgObj->getData();
$data = $rongSendMsgObj->sendSystemMsg();



