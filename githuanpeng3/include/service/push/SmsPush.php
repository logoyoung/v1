<?php
/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017年9月8日
 * Time: 下午3:44:50
 * Desc: 站内用户短信通知下发
 */
namespace service\push;

use service\weixin\WeiXinEnterpriseService;
use system\RedisHelper;
use lib\user\UserStatic;

require INCLUDE_DIR . 'mobileMessage.class.php';
class SmsPush
{
    const RETURN_CODE_01 = 1001; //短信余额不足
    const RETURN_CODE_02 = 1002; //预发送失败   内部下发redis等服务层问题<yalong2017@6.cn>
    const RETURN_CODE_05 = 1003; //参数不合法 请检查参数规范
    const RETURN_CODE_03 = 1000; //下发成功
    const RETURN_CODE_04 = 1004; //短信下发底层出现问题  联系秀场  短信下发接口相关同学
    const RETURN_CODE_06 = 1005; //任务失败
    const SAFE_SMS_NUMS  = 3000; //短信剩余 安全条数
    
    //redis 存储 key
    const SMS_HASH_ALL_USER = 'sms_hash_all_user'; //全站类型的短信下发 hash表
    const SMS_ALL_USER_TEXT = 'sms_all_user_text'; //全站用户短信内容 存放key
    
    const SMS_HASH_USERS    = 'sms_hash_users';    //部分用户短信的下发hash表
    const SMS_USERS_TEXT    = 'sms_users_text_';    //部分用户短信内容 存放key
    const SMS_ACCEPT_USERS  = 'sms_accept_users_';  //接收消息 用户存放list key
    //日志
    const SMS_LOG           = 'sms'; //日志名称
    
    static public $returnMsg = [
        self::RETURN_CODE_01 => '短信余额不足',
        self::RETURN_CODE_02 => '预发送失败',
        self::RETURN_CODE_03 => '下发成功',
        self::RETURN_CODE_04 => '短信下发底层出现问题',
        self::RETURN_CODE_05 => '请检查参数规范',
        self::RETURN_CODE_06 => '任务失败',
    ];
    //短信通知 下发任务  ******  安检     *********
    private function paramReg($uids,$type){
        $data = \sendMobileMsg::getDevMsgBalanceByUrl();
        if($type == 0){      //部分用户
            if($uids == '')
                return $this->returnData(self::RETURN_CODE_05);
                $uidLen = count($uids);
        }else{               //全站用户总数
            $userDao = new UserStatic();
            $uidLen = $userDao->getUserTotalNum();
            $uidLen = $uidLen[0]['total_num'];
        }
        $safeLen = $uidLen+self::SAFE_SMS_NUMS;
        if(isset($data['resuData'][0]['data'][0]['balance'])){
            if($data['resuData'][0]['data'][0]['balance'] > $safeLen){
                return $this->returnData(self::RETURN_CODE_03);
            }else{
                //调用 xingwei@6.cn 微信报警提示
                $wxWarning = new WeiXinEnterpriseService();
                $wxWarning->sendTextByDepartmentId("【pro】".self::$returnMsg[self::RETURN_CODE_01]." 返回码:".self::RETURN_CODE_01.PHP_EOL.__CLASS__." 第".__LINE__."行  机器名：".gethostname());
                write_log(__CLASS__." 第".__LINE__."行 | 短信余额即将用完不能执行该下发任务",self::SMS_LOG);
                return $this->returnData(self::RETURN_CODE_01);
            }
        }else{
            write_log(__CLASS__." 第".__LINE__."行 | 短信下发底层出现问题  联系秀场  短信下发接口相关同学",self::SMS_LOG);
            return $this->returnData(self::RETURN_CODE_04);
        }
    }
    //返回调用者 相关结果集处理
    private function returnData($code):array{
        return ['code'=>$code,'desc'=>self::$returnMsg[$code]];
    }
    //消息入缓存 redis
    private function smsIntoCache($uids,$title,$msg,$type){
        //压入缓存如果出问题  中途 return [code=>'',desc=>''] 格式错误 否则返回true
        $redis = RedisHelper::getInstance("huanpeng");
        $msg = json_encode(array('msg'=>$msg));
        if($type == 0){ //部分用户
            //部分用户下发  支持多用户下发任务
            $hlen = $redis->hLen(self::SMS_HASH_USERS);
            $hlen+=1;
            $res = $this->reSetHash($redis, self::SMS_HASH_USERS, self::SMS_USERS_TEXT.$hlen, $msg);
            if($res){
                foreach ($uids as $uid){
                    $this->reSetList($redis, self::SMS_ACCEPT_USERS.$hlen, $uid);
                    usleep(1);
                }
            }else {
                write_log(__CLASS__." 第".__LINE__."行 | 部分用户 短信内容 HASH表赋值 3次未能成功，下发任务失败",self::SMS_LOG);
                return $this->returnData(self::RETURN_CODE_06);
            }
        }else{ //全部用户   | 暂不支持  全站多任务下发（该功能慎用--望 后端 在提交时 咱三确认好再调用）
            $res = $this->reSetHash($redis, self::SMS_HASH_ALL_USER, self::SMS_ALL_USER_TEXT, $msg);
            if(!$res){
                write_log(__CLASS__." 第".__LINE__."行 | 全站用户 短信内容 HASH表赋值 3次未能成功，下发任务失败",self::SMS_LOG);
                return $this->returnData(self::RETURN_CODE_06);
            }
        }
        return true;
    }
    //种hash 重试3次
    private function reSetHash($redis,$key,$field,$value){
        for($i = 0;$i < 3; $i++){
            $res = $redis->hSet($key,$field,$value);
            if($res){
                $tip = 1;
                break;
            }
        }
        return isset($tip) && $tip ==1 ? true : false;
    }
    //list 用户uid 种植 重试3次
    private function reSetList($redis,$key,$value){
        for($i = 0;$i < 3; $i++){
            $res = $redis->rpush($key,$value);
            if($res){
                $tip = 1;
                break;
            }
        }
        return isset($tip) && $tip ==1 ? true : false;
    }
    /**
     * 服务层调用入口
     * ------------------------------------
     *               参数             必填           参数描述
     * @param string $uids    是        多个uid 以“ , ”（ 逗号 英文）隔开
     * @param string $title   否        任务标题（用作日志等 记录）
     * @param string $msg     是        短信内容
     * @param int    $type    是        0 部分用户下发短信通知；1全站用户
     */
    public function sendSMS($uids,$title,$msg,$type = 0){
        $uids = explode(",", $uids);
        $res = $this->paramReg($uids,$type);
        if($res['code'] != self::RETURN_CODE_03){
            return $res;
        }
        //存入redis 下发内容 以及接受者uid
        $res = $this->smsIntoCache($uids,$title,$msg,$type);
        if($res!==true){
            return $res;
        }
        //最终处理完成返回 正确结果如下格式
        return true;
    }
}

