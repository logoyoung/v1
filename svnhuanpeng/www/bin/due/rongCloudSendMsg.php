<?php
/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017-6-16
 * Time: 下午1:32:28
 * Desc: 融云守护脚本下发消息
 */
namespace due;

ignore_user_abort();
set_time_limit(0);

include '/data/huanpeng/include/init.php';

use system\RedisHelper;
use service\due\rongCloud\RongCloudServiceHelp;
use GuzzleHttp\json_decode;
use service\due\rongCloud\RongCloudService;

class rongCloudSendMsg
{

    private static $redisObj=null;

    private static $rongObj=null;

    const SEND_CLOUD_PID = 'sendCloudPid';

    public function __construct()
    {
        if(is_null(self::$redisObj))
            self::$redisObj = RedisHelper::getInstance("huanpeng");
        if(is_null(self::$rongObj)) 
            self::$rongObj = new RongCloudService();
    }

    /**
     * 获取对列内容 并发送消息(测试使用)
     * ------------------------
     */
    /*public function sendMsgToUser()
    {
        $rongServer = $this->rongObj->getInstance("android");
        
        $listLen = $this->redisObj->lLen(RongCloudServiceHelp::RONG_MSG_LIST_KEY);
        $content = '';
        for ($i = 0; $i < $listLen; $i ++) {
            $jsonData = $this->redisObj->rPop(RongCloudServiceHelp::RONG_MSG_LIST_KEY);
            $arrData = json_decode($jsonData, true);
            $result = $rongServer->sendMsg($arrData['fromuid'], $arrData['touid'], $arrData['content'], $arrData['sendCg'], $arrData['extraCode']);
            var_dump($result);
            if (strpos($result, "200")) {
                $content = '============' . PHP_EOL . date("Y-m-d H:i") . ' 发送消息成功' . PHP_EOL . '===========' . PHP_EOL . PHP_EOL;
            } else{
                $content = '**********' . PHP_EOL . date("Y-m-d H:i") . ' 发送消息失败' . PHP_EOL . '**********' . PHP_EOL . PHP_EOL;
            }
            echo $content;
            file_put_contents("/data/logs/due_popMsgList.log" . date("Ymd"), $content, FILE_APPEND);
        }
    }
*/
    /**
     * 死循环挂起执行（上线使用）
     * -------------------
     */
    public function sendMsgToUser_01()
    {
        $rongServer = self::$rongObj->getInstance("android");
        
        $rongIsAlive = $this->checkRongIsAlive();
        if (!$rongIsAlive)
            die("进程存活 运行中...");
        
        while (1) {
            $jsonData = self::$redisObj->lrange(RongCloudServiceHelp::RONG_MSG_LIST_KEY, 0, -1);
            if (empty($jsonData)) {
                sleep(2);
                continue;
            }
            self::$redisObj->ltrim(RongCloudServiceHelp::RONG_MSG_LIST_KEY, count($jsonData), -1);
            $content = ''; // 日志内容
            
            foreach ($jsonData as $json) {
                $arrData = json_decode($json, true);
                $result = $rongServer->sendMsg($arrData['fromuid'], $arrData['touid'], $arrData['content'], $arrData['sendCg'], $arrData['extraCode']);
                unset($jsonData);
//              var_dump($result);
                if (strpos($result, "200")) {
                    $content .= '============' . PHP_EOL . date("Y-m-d H:i:s") . "发送者：" . $arrData['fromuid'] . '  接收人：' . $arrData['touid'] . " 发送类型：" . $arrData['sendCg'] . ' 内容： ' . $arrData['content'] . '自定义编码：' . $arrData['extraCode'] . ' 发送消息成功' . PHP_EOL . '===========' . PHP_EOL . PHP_EOL;
                } else{
                    //发送失败的消息  再次扔到队列中 保证送达率
                    $result = self::$redisObj->rPush(RongCloudServiceHelp::RONG_MSG_LIST_KEY,$json);
                    $content .= '**********' . PHP_EOL . date("Y-m-d H:i:s") . ' 发送消息失败' . PHP_EOL . '**********' . PHP_EOL . PHP_EOL;
                }
            }
//             echo $content;
            file_put_contents("/data/logs/due_popMsgList.log" . date("Ymd"), $content, FILE_APPEND);
            usleep(500);
        }
    }
    /**
     * PHP调用shell环境 检查 融云脚本是否存活
     * ------------------------------
     * @return boolean
     */
    private function checkRongIsAlive(){
        $cmd = 'ps axu|grep "rongCloudSendMsg"|grep -v "grep"|wc -l';
        $ret = shell_exec("$cmd");
        $ret = intval(rtrim($ret, "rn"));
        
        if($ret > 1) {
           return false;
        }else{
           return true;
        } 
    }
}
$obj = new rongCloudSendMsg();
// $obj->sendMsgToUser();
$obj->sendMsgToUser_01();

?>