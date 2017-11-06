<?php
require_once __DIR__ . '/../../include/init.php';
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/9/7
 * Time: 10:16
 */
use system\RedisHelper;
use service\live\LiveLengthService;
//重置缓存服务 xuyong
use service\event\EventManager;
use lib\MsgPackage;
use lib\SocketSend;
/**
 * 主播每日时长奖励 10分钟10经验 60分钟60经验 180分钟180经验
 * Class anchorDayLengthExpReward
 */
class anchorDayLengthExpReward
{
    private $_liveLengthService;
    private $_anchorDataObj;
    private $_resetCacheService;
    private $_redis;
    //奖励规则缓存key
    private $_rewardRulekey = [];
    //需要再次缓存uids
    private $_saveRedisUids = [];
    private $_saveAnchorInfo = [];
    //旧缓存 如果数据库执行失败需要回退
    private $_oldsaveRedisUids = [];
    const redis_CONF ='huanpeng';
    const ADD_EXP_KEY = '_anchorAddExp_';
    const LOG_NAME = 'anchorDayExpReward';
    const DATE_KEY = 'anchorLiveLengthDate';
    //发消息奖励类型 1为观看奖励 2为直播奖励
    const MSG_REWARD_TYPE = 2;
    //奖励等级状态最高
    const REWARD_LEVEL_MAX = 10;
    //降低规则 按从小到大递增 顺序不可改变 key 为时间 value为经验值
    public $rewardRule = [
        '900'   =>  15,
        '3600'  =>  60,
        '7200'  =>  180,
    ];
    //发送消息奖励等级 发消息用 hantong@6.cn 提供 1为1档 5为2档 10为3档 最高档
    public $rewardLevel = [
        '900' => 1,
        '3600' => 5,
        '7200'=> 10,
    ];
    //每日最多增长经验在规则数组中的key
    const MAX_DAY_EXP_KEY = 7200;

    /**
     * 获取主播时长服务
     * @return $this
     */
    public function initLiveLengthService()
    {
        if (isset($this->_liveLengthService))
        {
            if(is_null($this->_liveLengthService))
            {
                $this->_liveLengthService = new LiveLengthService();
            }
        }else
        {
            $this->_liveLengthService = new LiveLengthService();
        }
        return $this;
    }

    public function initResetCacheSevcie()
    {
        if(isset($this->_resetCacheService))
        {
            if(is_null($this->_resetCacheService))
            {
                $this->_resetCacheService = new EventManager();
            }
        }else
        {
            $this->_resetCacheService = new EventManager();
        }
        return $this;
    }

    /**
     * 重置主播相关信息缓存
     * @param $uid @主播uid
     */
    public function resetAnchorInfoCache()
    {
        $uids = array_keys($this->_saveAnchorInfo);
        $i = 0;
        foreach ($uids as $uid)
        {
            $event = $this->_resetCacheService;
            $res = $event->trigger($event::ACTION_ANCHOR_RESET_CACHE,['uid' => $uid]);
            //写日志
            if($res)
            {
                $log = "resetAnchorInfoCache success|uid: {$uid}|class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                $this->writelog($log);
            }else
            {
                $log = "error: resetAnchorInfoCache failed|uid: {$uid}|class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                $this->writelog($log);
            }
            $i++;
            if($i == 100)
            {
                sleep(1);
                $i = 0;
            }
        }
    }

    /**
     * 获取发消息奖励等级
     * @param $ruleKey
     * @return int
     */
    public function getMsgRewardLevelByRuleKey($ruleKey)
    {
        if(isset($this->rewardLevel))
        {
            return $this->rewardLevel[$ruleKey];
        }
        return 1;
    }

    /**
     * 发送奖励消息
     */
    public function sendRewardMsg()
    {
        foreach ($this->_saveAnchorInfo as $key =>$value)
        {
            $luid = $uid =  $value['uid'];
            $rewordType = self::MSG_REWARD_TYPE;
            $rewordLevel = $this->getMsgRewardLevelByRuleKey($value['ruleKey']);
            $exp = $value['exp'];
            #发送消息
            $msg  = MsgPackage::getLiveLengthExpRewardMsgSocketPackage($luid,$uid,$rewordType,$rewordLevel,$exp);
            $res = SocketSend::sendMsg($msg);
            if($res)
            {
                $log = "sendRewardMsg|uid {$luid}|rewordType : {$rewordType}|rewordLevel {$rewordLevel}|exp :{$exp}|class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                $this->writelog($log);
            }else
            {
                $log = "error : sendRewardMsg failed |uid {$luid}|rewordType : {$rewordType}|rewordLevel {$rewordLevel}|exp :{$exp}|class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                $this->writelog($log);
            }
        }
    }

    /**
     * 获取奖励规则
     * @param $key
     * @return int|mixed
     */
    public function getRewardRuleValue($key)
    {
        if(isset($this->rewardRule[$key]))
        {
            return $this->rewardRule[$key];
        }
        return 0;

    }
    /**
     * 获取主播增加的经验
     * @param $uid
     * @param $liveLength
     */
    public function getAddExp($uid,$liveLength,$rewardStatus,$day)
    {
        #大于10 小于 60
        #大于60 小于180
        #大于180
        $newExp    = [];
        $exp = 0;
        $oldExp = 0;
        //获取对应时长的增长经验
        foreach ($this->rewardRule as $key=>$value)
        {
            if($liveLength >= $key)
            {
                $newExp =[ 'exp'=>$value,'ruleKey'=>$key,'day'=>$day];
            }
        }
        //判断主播是否有对应规则的增长经验值
        if(!empty($newExp))
        {
            //判断之前是否有过奖励
            if($rewardStatus)
            {
                $rewardLevel = array_flip($this->rewardLevel);
                $oldRuleKey = $rewardLevel[$rewardStatus];
                //获取对应规则的增长经验
                $oldExp = $this->getRewardRuleValue($oldRuleKey);
            }
                //判断获取的经验值是否大于旧经验
                /*            $log = "getaddExp | uid: {$uid} | exp:{$exp} | oldExp: {$oldExp} |ruleKey: {$ruleKey}|class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                            $this->writelog($log);*/
                $exp = $newExp['exp'];
                if ($exp > $oldExp)
                {
                    //减去之前增长经验 得到实际经验
                    $exp = $exp - $oldExp;
                    $newRewardStatus = $this->rewardLevel[$newExp['ruleKey']];
                    //写入预存数据库主播信息数组 exp增加经验值 ruleKey 规则key
                    $this->_saveAnchorInfo[$uid] = ['uid' => $uid, 'exp' => $exp, 'ruleKey' => $newExp['ruleKey'],'reward_status'=> $newRewardStatus,'day'=>$newExp['day']];
                }
        }
        return true;
    }

    public function updateAnchorInfo()
    {
        $i = 0;
        foreach ($this->_saveAnchorInfo as $key =>$value)
        {
            #更新奖励状态
            //使用旧数据操作方式 hantong
            $this->_anchorDataObj = new lib\Anchor($key,'');
            //增加主播经验
            $this->_anchorDataObj->addAnchorExp($value['exp']);
            unset($this->_anchorDataObj );
            $LiveLengthService  = $this->_liveLengthService;
            $LiveLengthService->setLuid($key);
            $res = $LiveLengthService->updateAnchorRewardStatus($value['day'],$value['reward_status']);
            if($res)
            {
                $log = "updateAnchorInfo | uid: {$key} | exp:{$value['exp']}| ruleKey :{$value['ruleKey']}|'reward_status' :{$value['reward_status']}| day : {$value['day']} | res: ".json_encode($res)." |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
            }else
            {
                $log = "error : updateAnchorInfo failed  | uid: {$key} | exp:{$value['exp']}| ruleKey :{$value['ruleKey']}|'reward_status' :{$value['reward_status']}| day : {$value['day']} | res: ".json_encode($res)." |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
            }
            $this->writelog($log);
            $i++;
            if($i == 100)
            {
                sleep(1);
                $i = 0;
            }
        }
        return true;
    }
    public function main()
    {
        #获取所有今日直播表中主播
        $date = date("Y-m-d");
        $allAnchor = [];
        //目前仅有uid length
        $allAnchorData = $this->_liveLengthService->getAllAnchorDayLiveLength($date);
        if(!empty($allAnchorData))
        {
            foreach ($allAnchorData as $key =>$value)
            {
                //取出未到最高档奖励的主播
                if($value['reward_status'] != self::REWARD_LEVEL_MAX )
                {
                    $allAnchor [$value['uid']]= ['length' => $value['length'],'reward_status'=> $value['reward_status'] ];
                }
            }

            #执行今日经验规则判断uid是否在其中 10分钟 uid放入缓存 60分钟uid放入缓存 180分钟中uid 放入缓存
            foreach ($allAnchor as $key =>$value)
            {
                $this->getAddExp($key,$value['length'],$value['reward_status'],$date);
            }
            $log = "saveAnchorInfo ".json_encode($this->_saveAnchorInfo)." |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
            $this->writelog($log);
            if(!empty($this->_saveAnchorInfo))
            {
                #写表操作
                $this->updateAnchorInfo();
                #更新个人缓存
                $this->resetAnchorInfoCache();
                #发送消息
                $this->sendRewardMsg();
            }
        }else
        {
            $log = "error : null data in live_length table |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
            $this->writelog($log);
        }
    }
    public function run()
    {
        //初始化
        $this->initLiveLengthService();
        $this->initResetCacheSevcie();


        //主程序
        $this->main();

        //清理回收
        unset($this->_liveLengthService);
        unset($this->_anchorDataObj);
        unset($this->_resetCacheService);
    }
    /**
     * 获取redis资源
     * @return \redis
     */
    public function getRedis()
    {
        if (is_null($this->_redis))
        {
            $this->_redis = RedisHelper::getInstance(self::redis_CONF);
        }
        return $this->_redis;
    }
    /**
     * 写日志
     * @param $content
     */
    public function writelog($content)
    {
        $logName = self::LOG_NAME;
        write_log($content,$logName);
    }
}
$anchorDayLengthExpReward =  new anchorDayLengthExpReward();
$anchorDayLengthExpReward->run();