<?php
namespace service\push;

use system\RedisHelper;

class SystemMsgTask
{
    //系统消息部分用户下发  消息体内容  
    const SYSTEM_HAS_MSG_01 = 'systemHasMsg01'; //01has 系统指定用户下发 消息体
    const SYSTEM_HAS_MSG_PREFIX= 'systemHasField_'; //多条消息前缀
    //系统消息 接收人uid list key
    const SYSTEM_ACCEPT_KEY_PREFIX = 'systemAcceptUids_';
    
    //系统消息全站用户下发 
    const SYSTEM_HAS_MSG_02 = 'systemHasMsg02'; //02has 系统全站用户下发 消息体
    const SYSTEM_HAS_ALL_USER_MSG_PREFIX= 'systemAllUserMsg_'; //多条消息前缀
    const SYSTEM_ALL_USER_MSG_LIST = 'systemAllUserMsgList'; //存放hash消息体 key 键值队列
    
    private $redis = NULL;
    
    public function __construct()
    {
        $this->redis = is_null($this->redis) ? RedisHelper::getInstance("huanpeng") : $this->redis;
    }
    
    /**
     * 发送系统消息  缓冲池
     * ---------------
     * @param $fromUserId int 发送者uid
     * @param $toUserId   string 接收者uid 多个uid 以英文  , 逗号隔开
     * @param $title      string 消息标题
     * @param $msg        string 消息内容
     * @param $action     'site-msg' 系统消息 客户端处理动作：'site-msg' 消息体点击跳转至 打开站内信列表；'nothing' 打开APP
     * @param $custom     string 自定义 预留参数  格式未定
     * @return bool
     */
    public function addSystemMsg(string $toUserId,string $title,string $msg,$action= 'site-msg',$custom = array()){
        if($toUserId == '' || $msg == '')
        {
            write_log(__CLASS__." 第".__LINE__."行 接收者uid为空或下发消息体为空",'SystemMsgError');
            return false;
        } 
        // 拼装消息体    | 将消息体 存入redis hastab 
        $toUserId = explode(",", $toUserId);
        $data['title']= $title;
        $data['msg']= $msg;
        $data['action']= $action;
        $data['custom']= $custom;
        $datas = json_encode($data);
        
        $haslen = $this->redis->hLen(self::SYSTEM_HAS_MSG_01);
        $haslen+=1;  //用作 has消息体 和 接收人uid list 进行一一对应的标识
        
        //设置多条消息  has
        $result = $this->reSetHash(self::SYSTEM_HAS_MSG_01,self::SYSTEM_HAS_MSG_PREFIX.$haslen,$datas);
        //将接收uid用户压入redis list 等待下发 popMsg 
        foreach($toUserId as $v)
        {
            $v = intval($v);
            $this->redis->rPush(self::SYSTEM_ACCEPT_KEY_PREFIX.$haslen,$v);
        }
        if($result) return true;
        else {
            write_log(__CLASS__." 第 ".__LINE__."行  批量用户下发系统消息 入队尝试3次 仍然失败，请检查redis问题< yalong2017@6.cn >",'SystemMsgError');
            return false ;
        }
    }
    /**
     * 发送全站系统消息
     * ------------
     * @param $adminUid   string 操作者 uid
     * @param $title      string 消息标题
     * @param $msg        string 消息内容
     * @param $action     'site-msg' 系统消息 客户端处理动作：'site-msg' 消息体点击跳转至 打开站内信列表；'nothing' 打开APP
     * @param $custom     string 自定义 预留参数  格式未定
     */
    public function addAllUserMsg(int $adminUid=0,string $title,string $msg,$action= 'site-msg',$custom = array()){
        //拼 消息体 存 hash
        $data['title']  = $title;
        $data['msg']    = $msg;
        $data['action'] = $action;
        $data['custom'] = $custom;
        $datas = json_encode($data);
        var_dump($this->redis->hLen(self::SYSTEM_HAS_MSG_02));
        $haslen = $this->redis->hLen(self::SYSTEM_HAS_MSG_02);
        $haslen+=1;  //用作 has消息体 和 接收人uid list 进行一一对应的标识
        //设置多条消息  has 
        $res1 = $this->reSetHash(self::SYSTEM_HAS_MSG_02,self::SYSTEM_HAS_ALL_USER_MSG_PREFIX.$haslen,$datas);
        $res2 = $this->reSetList(self::SYSTEM_ALL_USER_MSG_LIST,self::SYSTEM_HAS_ALL_USER_MSG_PREFIX.$haslen);
        if($res1 && $res2) return true;
        else
        {
            write_log(__CLASS__." 第 ".__LINE__."行  操作管理员UID：{$adminUid} 系统全站消息 入队尝试3次 仍然失败，请检查redis问题",'SystemMsgError');
            return false;
        }
    }
    //种 hash 尝试3次
    private function reSetHash($key,$field,$value)
    {
        for($i=0;$i<3;$i++)
        {
            $result = $this->redis->hSet($key,$field,$value);
            if($result)
            {
                $res = true;
                break;
            }
        }
        return !isset($res) && $res!=true ? false : true;
    }
    //种 list 尝试3次
    private function reSetList($key,$value)
    {
        for($i=0;$i<3;$i++)
        {
            $result = $this->redis->rPush($key,$value);
            if($result)
            {
                $res = true;
                break;
            }
        }
        return !isset($res) && $res!=true ? false : true;
    }
    //测试查看 hash 消息体 以及 接收消息用户 是否压入 redis
    public function getRedisData()
    {
        $data1 = $this->redis->hGetAll(self::SYSTEM_HAS_MSG_01);
        $data2 = $this->redis->hGetAll(self::SYSTEM_HAS_MSG_02);
        var_dump($data1);
        var_dump($data2);
//         foreach($data as $k=>$v)
//         {
//             $prefix = explode("_", $k);
//             $listkey = self::SYSTEM_ACCEPT_KEY_PREFIX.$prefix[1];
//             $this->redis->hDel(self::SYSTEM_HAS_MSG_01,$k);
//             $this->redis->delete($listkey);
//         }  
    }
}

