<?php
/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/8/7
 * Time: 15:41
 */

namespace service\robot;
use lib\room\RobotActive;
use service\user\UserDataService;
use system\RedisHelper;
use lib\LiveActivity;
use lib\robot\Robot;
use service\live\LiveService;
/**
 * 房间机器人服务
 * Class RoomRobotService
 * @package service\robot
 */
class RoomRobotService
{

    const TIMER_KEY = '_enterRoom';
    const TIMER_EXPIRE = 300;
    const LOG_NAME = 'RoomRobot';
    //db 配置文件的key
    const DB_CONF = 'huanpeng';
    const redis_CONF = 'huanpeng';
    const CLEAR_TIMER = 3;
    //机器人列表缓存键 一天
    const ALL_ROBOT_LIST_KEY_EXPIRE = 86400;
    //机器人发言信息缓存 10分钟
    const ROBOT_MSG_LIST_KEY_EXPIRE = 600;
    //后台控制主播列表缓存
    const ADMIN_ANCHOR_VIWERR_LIST_KEY_EXPIRE = 60;
    const ALL_ROBOT_LIST_KEY = '_allRobotList';
    const ROBOT_HEAD_LIST_KEY = '_robotHeadList';
    const ROBOT_MSG_LIST_KEY= '_robotMsgList';
    const ADMIN_ANCHOR_VIWERR_LIST_KEY = '_adminAnchorViewerList';
    //后台主播观众数开关
    const ADMIN_ANCHOR_VIEWER_SWITCH_ON = 1;
    //主播观众数速率开关
    const ANCHOR_VIEWER_SPEED_SWITCH_ON = 1;
    const ANCHOR_VIEWER_SPEED_SWITCH_OFF = 0;
    //睡眠开关
    const SLEEP_SWITCH_ON = 1;
    const SLEEP_SWITCH_OFF = 0;
    //默认按速率增长程序执行休眠数
    const SLEEP_SECOND  = 2;

    const IS_EXIST_ANCHOR = - 5001;
    public static $errorMsg =[
        self::IS_EXIST_ANCHOR =>'主播已存在,请勿重复添加',
        ];
    //房间机器人最大值
    private $_robotMaxCount = 20;
    //默认上升速率 单位 次 最小值为 2
    private $_raiseSpeed = 2;
    //默认下降速率 单位s 最小值为 2
    private $_reduceSpeed = 2;
    private $_robotTimer = 0;
    private $_sleepTime;
    private $_robotData;
    private $_userDataService;
    private $_msgList = ["666666666", "666", "233333333", "233", "FFFFFFFFFF", "主播，好有爱！", "求露脸～", "不要逗！", "主播，求BGM", "约吗？", "吓死宝宝了！", "我的内心几乎是崩溃的", "笑尿了", "演的不错", "漂亮", "啊啊啊", "哈哈哈啊啊", "哈哈", "呵呵", "完美", "套路", "٩( 'ω' )و", "0.0", "？？？", "。。。", "太简单了吧", "←_←", "→_→", "厉害了!", "那你很棒哦！", "尴尬", "还能这么玩", "困=_=", "看不懂啊", "好冷", "欢迎", "我服了", "服了", "没办法", "别闹了", "醉了", "来晚了。。", "给主播来波豆", "可以的", "关注了", "你好意思吗？" ];
    //睡眠开关
    private $_sleepSwitch;
    private $_sleep = [ 5, 10 ];
    private $_viewerNumSpaceValue = [ 1, 4 ];
    private $_viewerNumSpace;
    private $_robotActiveSpaceValue = [ 4, 7 ] ;
    private $_robotActiveSpace;
    private $_redis;
    private $_activeObj;
    private $_roomRobotList= [];
    private $_allRobotList = [];
    //后台主播机器观众信息
    private $_adminAnchorViewerInfo = [];
    //后台设定主播列表
    private $_adminluidList = [];
    //直播间观众数量增长规则
    public  $roomViewerNumRule = [
                '10'   => 50,
                '50'   => 55,
                '100'  => 60,
                '500'  => 65,
                '1000' => 70,
                '-1'   => 75,
    ];

    public function setMsgList($msgList)
    {
        $this->_msgList = empty($msgList) ?  $this->_msgList : $msgList;
    }
    public function setRaiseSpeed($raiseSpeed)
    {
        $this->_raiseSpeed = $raiseSpeed ? (int)$raiseSpeed : 1 ;
        return $this;
    }
    public function getRaiseSpeed()
    {
        return $this->_raiseSpeed;
    }
    public function setReduceSpeed($reduceSpeed)
    {
        $this->_reduceSpeed = $reduceSpeed ? (int)$reduceSpeed : 1;
        return $this;
    }
    public function getReduceSpeed()
    {
        return $this->_reduceSpeed;
    }
    public function setSleepTime($sleep)
    {
        if(!empty($sleep) && is_array($sleep))
        {
            $this->_sleep = $sleep;
        }
        return $this;
    }
    public function getSleepTime()
    {
        $this->_sleepTime = $this->_getSleepTime();
    }

    public function _getSleepTime()
    {
        return call_user_func_array( 'rand', $this->_sleep);
    }

    public function setViewerNumSpaceValue($viewerNumSpaceValue)
    {
        if(!empty($viewerNumSpaceValue) && is_array($viewerNumSpaceValue))
        {
            $this->_viewerNumSpaceValue = $viewerNumSpaceValue;
        }
        return $this;
    }
    /**
     * 获取观众数量增长间隔
     * @param $viewerNumSpace
     * @return array
     */
    public function getViewerNumSpace()
    {
        return call_user_func_array( 'rand', $this->_viewerNumSpaceValue);
    }
    /**
     *  初始化观众数间隔规则
     * @param $luid
     */
    public function initViewerNumSpaceRule($luid)
    {
        $this->_viewerNumSpace[$luid]['space'] = $this->getViewerNumSpace();
        $this->_viewerNumSpace[$luid]['runTime']  = 0;
        $log = "initViewerNumSpaceRule:" . json_encode( [ 'userid' => $luid, 'space' => $this->_viewerNumSpace[$luid]['space'], 'runtime' => 0 ] )."|class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
        $this->writelog($log);
    }

    public function setRobottActiveSpaceValue($robotActiveSpaceValue)
    {
        if(!empty($robotActiveSpaceValue) && is_array($robotActiveSpaceValue))
        {
            $this->_robottActiveSpaceValue = $robotActiveSpaceValue;
        }
        return $this;
    }

    public function getRobottActiveSpace()
    {
        return call_user_func_array( 'rand', $this->_robotActiveSpaceValue);
    }
    /**
     * 初始化机器人激活间隔规则（机器人进入、发言、退出操作时间间隔等）
     * @param $luid
     */
    public function initRobotActiveSpaceRule( $luid )
    {
        $this->_robotActiveSpace[$luid]['space'] = $this->getRobottActiveSpace();
        $this->_robotActiveSpace[$luid]['runTime']  = 0;
        $log = "initRobotActiveSpaceRule:" . json_encode( [ 'userid' => $luid, 'space' => $this->_robotActiveSpace[$luid]['space'], 'runtime' => 0 ] )."|class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
        $this->writelog($log);
    }

    public function setRobotMax($robotMaxCount)
    {
        $this->_robotMaxCount = $robotMaxCount;
        return $this;
    }

    public function getRobotMax()
    {
        return $this->_robotMaxCount;
    }
    /**
     * 调用机器人激活数据
     * @return RobotActive
     */
    public function getRobotActive()
    {
        if(isset($this->_activeObj))
        {
            if(is_null($this->_activeObj))
            {
                $this->_activeObj = new RobotActive(new \DBHelperi_huanpeng(),new \RedisHelp());
            }
            return $this->_activeObj;
        }else
        {
            $this->_activeObj = new RobotActive(new \DBHelperi_huanpeng(),new \RedisHelp());
            return $this->_activeObj;
        }
    }

    /**
     * 机器人数据服务
     * @return Robot
     */
    public function getRobotData()
    {
        if(isset($this->_robotData))
        {
            if(is_null($this->_robotData))
            {
                $this->_robotData = new Robot();
            }
            return $this->_robotData;
        }else
        {
            $this->_robotData = new Robot();
            return $this->_robotData;
        }
    }
    public function getUserService()
    {
        if(isset($this->_userDataService))
        {
            if(is_null($this->_userDataService))
            {
                $this->_userDataService = new UserDataService();
            }
            return $this->_userDataService;
        }else
        {
            $this->_userDataService = new UserDataService();
            return $this->_userDataService;
        }
    }
    #1.初始化配置 载入配置
    public function getInitChatMsg()
    {
        $chatMsg = $this->getChatMsg();
        if(is_array($chatMsg) && count($chatMsg) > 0)
        {
            $this->_msgList = $chatMsg;
        }
        //插入随机表情
        for ( $i = 1; $i <= 22; $i++ )
        {
            array_push( $this->_msgList, str_repeat( "[em_$i]", rand( 2, 5 ) ) );
        }
        return $this->_msgList;
    }
    //获取机器人聊天信息表
    public function getChatMsg()
    {
        $this->iniRobotMsg();
        $msgIndex = rand( 0, count( $this->_msgList ) - 1 );
        $msg      = $this->_msgList[$msgIndex];

        return $msg;
    }
    //直播间观众数量增长规则
    public function getRoomViewerNumRule( $viewerCount )
    {
        //增长规则
         $ruleTable = $this->roomViewerNumRule;
        foreach ( $ruleTable as $key => $value )
        {
            if ( $key == -1 )
            {
                return ['key'=>$key,'value'=>$viewerCount * $value];
            }
            else
            {
                if ( $viewerCount < $key )
                {
                    return ['key'=>$key,'value'=>$viewerCount * $value];
                }
            }
        }
    }

    /**
     * 直播间观众按速率增长规则
     * @param $viewerCount
     * @return int
     */
    public function getRoomViewerNumSpeedRule($viewerCount)
    {
//增长规则
        $ruleTable = $this->roomViewerNumRule;
        foreach ( $ruleTable as $key => $value )
        {
            if ( $key == -1 )
            {
                return rand( 1, 40 );
            }
            else
            {
                if ( $viewerCount < $key )
                {
                    return  rand( 1, $key - 1 );
                }
            }
        }
    }

    /**
     * 获取所有直播间uids
     * @return array
     */
    public function getLiveLuids()
    {
        //获取直播uids
        //$luids = LiveActivity::getLiveUids();
        $luids = LiveService::getLivingLuidByType();
        if(is_array($luids) && count($luids) > 0)
        {
            $log = "living uids:".json_encode( $luids )."|class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
           $this->writelog($log);
            return $luids;
        }
        $log = "null living uids |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
        $this->writelog($log);
        return [];
    }

    /**
     * 设置清理观众数时钟 默认clock > 3 清理
     * @param $luid
     */
    public function setViewerNumClearClock( $luid )
    {
        if ( isset( $this->_viewerNumSpace[$luid]['clock'] ) )
        {
            $this->_viewerNumSpace[$luid]['clock']++;
        }
        else
        {
            $this->_viewerNumSpace[$luid]['clock'] = 1;
        }
        $log =  "clear clock:" . json_encode( [ 'userid' => $luid, 'clock' => $this->_viewerNumSpace[$luid]['clock'] ] )."|class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
        $this->writelog($log);
    }

    /**
     * 重置清理观众数时钟
     * @param $luid
     */
    public function resetViewerNumClearClock($luid)
    {
        if ( isset( $this->_viewerNumSpace[$luid] ) )
        {
            $this->_viewerNumSpace[$luid]['clock'] = 0;
        }
    }

    /**
     * 是否可以清理
     * @param $luid
     * @return bool
     */
    public function isCanClear( $luid )
    {
        if ( isset( $this->_viewerNumSpace[$luid]['clock'] ) && $this->_viewerNumSpace[$luid]['clock'] >= self::CLEAR_TIMER )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * 清理房间机器人 和 房间人数
     * @param $luid
     */
    public function clear( $luid )
    {
        unset( $this->_viewerNumSpace[$luid] );
        //清空房间机器人
        $count = count( $this->_roomRobotList[$luid] ) - 1;

        for ( $i = 0; $i < $count; $i++ )
        {
            $this->robotExit( $luid );
        }

        unset($this->_roomRobotList[$luid]);
        unset($this->_robotActiveSpace[$luid]);

        //获取直播间对象
        $roomObj = $this->getRobotActive()->getLiveRoomObj( $luid );
        //获取虚拟观众数量
        $fictitious  = $roomObj->getLiveRoomUserCountFictitious();
        //清理观众数
        $roomObj->subFictitiousViewCount($fictitious);

    }

    /**
     * 设置观众数增长速度开关打开
     * @param $luid
     */
    public function setViewerNumSpeedSwitchOn($luid)
    {
        if ( isset( $this->_viewerNumSpace[$luid] ) )
        {
            $this->_viewerNumSpace[$luid]['switch'] = self::ANCHOR_VIEWER_SPEED_SWITCH_ON;
        }


    }
    /**
     * 设置观众数增长速度开关关闭
     * @param $luid
     */
    public function setViewerNumSpeedSwitchOff($luid)
    {
        if ( isset( $this->_viewerNumSpace[$luid] ) )
        {
            $this->_viewerNumSpace[$luid]['switch'] = self::ANCHOR_VIEWER_SPEED_SWITCH_OFF;
        }
    }

    /**
     * 更新观众数
     */
   public function updateViewerNum()
   {
        foreach ($this->_viewerNumSpace as $luid =>$space)
        {
            $runTime        = $space['runTime'];
            $space          = $space['space'];
            $speedSwitch    = $space['switch'];
            $log = "anchorViewerStatus:" . json_encode( [ 'userid' => $luid, 'space' => $space, 'runtime' => $runTime ])."|class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
            $this->writelog($log);
            switch ($speedSwitch)
            {
                //关
                case 0:
                    //执行次数小于间隔次数
                    if($runTime  < $space)
                    {
                        $this->_viewerNumSpace[$luid]['runTime']++;
                    }else
                    {
                        $this->OptionRoomViewNum($luid);
                        if ( $this->isCanClear( $luid ) )
                        {
                            $log =  "clearViewer:" . json_encode( [ 'clearUser' => $luid ] )."|class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                            $this->writelog($log);
                            $this->clear( $luid );
                        }
                        else
                        {
                            $this->initViewerNumSpaceRule( $luid );
                        }
                    }
                    break;
                //开
                case 1:
                    $this->OptionRoomViewNum($luid);
                    if ( $this->isCanClear( $luid ) )
                    {
                        $log =  "clearViewer:" . json_encode( [ 'clearUser' => $luid ] )."|class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                        $this->writelog($log);
                        $this->clear( $luid );
                    }
                    break;
                default:
                    break;
            }

        }

   }

    /**
     * 房间观众人数是否达到设定值范围
     * @param $ruleArr
     * @param $fictitious
     * @return bool
     */
    public function isRoomViewerNumInRange($ruleArr,$fictitious)
    {
        //先禁用速率
        return true;
        $viewer = $ruleArr['value'];
        $viewerMax = $viewer + $ruleArr['key'];
        $viewerMin = $viewer - $ruleArr['key'];
        if($viewerMin <= $fictitious && $fictitious <= $viewerMax)
        {
            return true;
        }
        $log =  "{$fictitious} Not in Num Range:{$viewerMin} to {$viewerMax}|class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
        $this->writelog($log);
        return false;
    }

    /**
     * 获取后台主播观众信息
     * @return array
     */
    public function getAdminAnchorViewerInfo()
    {
        $res = $this->getRobotData()->getAllAdminAnchorViewerInfo();
        if($res === false)
        {
            $log ="errror:mysql failed | error_msg: can't get adminAnchorViewerInfo |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
            $this->writelog($log);
            return [];
        }
        else
        {
            return $res;
        }
    }

    /**
     * 初始化后台设定主播观众信息
     * param $expire =true 主动刷新缓存
     */
    public function initAdminAnchorViewerInfo($expire = false)
    {
        if($expire)
        {
            $res = 0;
        }else
        {
            $key = $GLOBALS['env'].self::ADMIN_ANCHOR_VIWERR_LIST_KEY;
            $res = $this->getRedis()->get($key);
        }
        if(!$res)
        {
            $res = $this->getAdminAnchorViewerInfo();
            $res = json_encode($res);
            $status = $this->getRedis()->set($key,$res);
            if($status)
            {
                $this->getRedis()->expire($key,self::ADMIN_ANCHOR_VIWERR_LIST_KEY_EXPIRE);
                $log = "AdminAnchorViewerList:".json_encode($res)." |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                $this->writelog($log);
            }else
            {
                $log = "error:redis error can't add AdminAnchorViewerList |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                $this->writelog($log);
            }
        }
        $res = json_decode($res,true);
        if(!empty($res))
        {
            $this->_adminAnchorViewerInfo = [];
            foreach ($res as $key=>$value)
            {
                if($value['status'] == self::ADMIN_ANCHOR_VIEWER_SWITCH_ON)
                {
                    $this->_adminAnchorViewerInfo[$value['uid']] = $value;
                }
            }
            $this->_adminluidList = array_keys($this->_adminAnchorViewerInfo);
            $log ="adminLuidList:".json_encode($this->_adminAnchorViewerInfo)."|class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
            $this->writelog($log);
        }else
        {
            $this->_adminluidList = [];
            $log ="null adminLuidlist"."|class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
            $this->writelog($log);
        }
    }
    //获取后台设定主播列表
    public function getAdminAnchorViewerList()
    {
        return  $this->_adminluidList;
    }
    /**
     * 操作直播间观众数
     * @param $luid
     */
    public function OptionRoomViewNum( $luid )
    {
        $roomObj = $this->getRobotActive()->getLiveRoomObj( $luid );
        //真实观众数
        $viewerCount = $roomObj->getLiveRoomUserCount();
        //获取虚拟观众数量
        $fictitious  = $roomObj->getLiveRoomUserCountFictitious();
        //如果直播中断且无人观看
        if($viewerCount == 0)
        {
            $viewer = $fictitious;
            $this->setViewerNumClearClock( $luid );

        }else
        {
            ####加入手动设置主播观看人数
            //载入后台主播观众设置
            $this->initAdminAnchorViewerInfo();
            //判断luid是否在手动设置中
            if(in_array($luid,$this->_adminluidList))
            {
                //设置观众数
                $ruleArr = $this->getRoomViewerNumRule( $viewerCount );
                $key = $ruleArr['key'];
                //观众数 为 后台设置基础人气 + 实际人数规则生成树数
                $viewerNum = $this->_adminAnchorViewerInfo[$luid]['num'] + $ruleArr['value'];
                $ruleArr = ['key'=>$key,'value'=>$viewerNum];
                $speed = $this->_adminAnchorViewerInfo[$luid]['time'] / self::SLEEP_SECOND;
                $this->setRaiseSpeed($speed);
                $log ="adminAnchorViewerInfo:".json_encode(['luid'=>$luid,'num '=>$this->_adminAnchorViewerInfo[$luid]['num'],'time' =>$this->_adminAnchorViewerInfo[$luid]['time']])."|class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                $this->writelog($log);
            }else
            {
                //设置观众数
                $ruleArr = $this->getRoomViewerNumRule( $viewerCount );
            }
            $viewer = $ruleArr['value'];
            $this->resetViewerNumClearClock( $luid );
            //获取虚拟观众数量
            $fictitious  = $roomObj->getLiveRoomUserCountFictitious();
            //判断是否打开速率开关 是否已处于合理数值内
            if($this->isRoomViewerNumInRange($ruleArr,$fictitious))
            {
                $this->setViewerNumSpeedSwitchOff( $luid );
                $log ="SpeedSwitchoff by Luid {$luid}|class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                $this->writelog($log);

            }else
            {
                $this->setViewerNumSpeedSwitchOn( $luid );
                $log ="SpeedSwitchOn by Luid {$luid}|class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                $this->writelog($log);
            }
                $md = $viewer - $fictitious;
                $log =" {$luid} | {$fictitious} should to : {$viewer}|class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                $this->writelog($log);
                if ( $md > 0 )
                {
                    if($this->_viewerNumSpace[$luid]['switch'] == self::ANCHOR_VIEWER_SPEED_SWITCH_ON)
                    {
                        //按速率增长
                        $md = ceil($viewer/$this->_raiseSpeed)*self::SLEEP_SECOND + $this->getRoomViewerNumSpeedRule($viewerCount);
                    }else
                    {
                        $md = $md +  $this->getRoomViewerNumSpeedRule($viewerCount);
                    }
                    $log =" {$luid} | $fictitious} add up: {$md}| realview :{$viewerCount}class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                    $this->writelog($log);
                    $roomObj->addFictitiousViewCount( $md );
                }
                else
                {
                    if($this->_viewerNumSpace[$luid]['switch'] == self::ANCHOR_VIEWER_SPEED_SWITCH_ON)
                    {
                        //按速率减少
                        $md = ceil($md/$this->_reduceSpeed)*self::SLEEP_SECOND  - $this->getRoomViewerNumSpeedRule($viewerCount)*self::SLEEP_SECOND ;
                    }else
                    {
                        $md = $md - $this->getRoomViewerNumSpeedRule($viewerCount);
                    }
                    $log =" {$luid} | {$fictitious}  reduce : {$md}| realview :{$viewerCount}|class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                    $this->writelog($log);
                    $roomObj->subFictitiousViewCount( $md );
                }
            //更新房间数
            if ( $md != 0 )
            {
                $this->getRobotActive()->upDateRoomViewerMsg( $luid );
            }

        }
        $log ="optionViewerNum:" . json_encode( [ 'userid' => $luid, 'realViewer' => $viewerCount, 'fictitious' => $viewer ] )."|class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
        $this->writelog($log);
    }
    /**
     * 激活机器人
     */
   public function activeRobot()
   {
       foreach ( $this->_robotActiveSpace as $luid => $space )
       {

           $runTime  = $space['runTime'];
           $space = $space['space'];

           $log ="robotActiveStatus:".json_encode(['userid'=>$luid, 'space'=>$space,'runtime'=>$runTime])."|class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
           $this->writelog($log);

           if ( $runTime < $space )
           {
               $this->_robotActiveSpace[$luid]['runTime']++;
           }
           else
           {
               $robotList=  isset($this->_roomRobotList[$luid]) ?  $this->_roomRobotList[$luid] : [];
               $robotCount = count($robotList);
               $log = "roomRobot:".json_encode(['userid'=>$luid,'robotCount'=>$robotCount, 'list'=>$robotList]).__CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
               $this->writelog($log);
               if ( !$robotCount )
               {
                   $this->robotEnter( $luid );
                   $log = "robotEnter : create robot  for luid : {$luid}|class:".__CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                   $this->writelog($log);
               }
               elseif ( $robotCount > $this->getRobotMax() )//先按照每个房间20个假人设计
               {
                   $this->robotExit( $luid );
                   $log = "robotExit : room robot max and level | luid : {$luid}|class:".__CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                   $this->writelog($log);
               }
               else
               {
                   if ( rand( 1, 10 ) > 6 )
                   {
                       $this->robotMsg( $luid );
                       $log = "robotMsg : robot msg for luid : {$luid}|class:".__CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                       $this->writelog($log);
                   }
                   else
                   {
                       $this->robotEnterOrExit( $luid );
                       $log = "robotEnterOrExit:robot active enter room or level | luid : {$luid }|class:".__CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                       $this->writelog($log);
                   }
               }
               $this->initRobotActiveSpaceRule( $luid );
           }
       }

   }

    public function getNewRobot()
    {
        $this->initRobotInfo();

        $robotIndex = rand( 0, count( $this->_allRobotList ) - 1 );

        return $this->_allRobotList[$robotIndex];
    }
    public function robotEnter( $luid )
    {
        $uid  = $this->getNewRobot();
        $log = "robotuid : {$uid }|class:".__CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
        $this->writelog($log);
        $info = $this->getRobotInfo( $uid );
        $this->addRobot( $luid, [ $uid ] );
        $this->getRobotActive()->enterRoom( $luid, $info );
    }

    public function robotEnterOrExit( $luid )
    {
        if ( rand( 1, 10 ) > 8 )
        {
            $this->robotExit( $luid );
        }
        else
        {
            $this->robotEnter( $luid );
        }
    }
    public function robotMsg( $luid )
    {
        $uid = $this->getRoomOneRobot( $luid );
        if ( $uid )
        {
            $info = $this->getRobotInfo( $uid );
            $msg  = $this->getChatMsg();
            $this->getRobotActive()->msg( $luid, $info, $msg );
        }
    }
    /**
     * 机器人退出房间
     * @param $luid
     */
    public function robotExit( $luid )
    {
        $uid = $this->getRoomOneRobot( $luid );

        if ( $uid )
        {
            $info = $this->getRobotInfo( $uid );

            $this->subRobot( $luid, [ $uid ] );
            $this->getRobotActive()->exitRoom( $luid, $info );
        }
    }

    /**
     * 获取机器人信息
     * @param $uid
     * @return mixed
     */
    public function getRobotInfo( $uid )
    {
        $userService = $this->getUserService();
        $userService->setUid($uid);
        $userService->setUserInfoDetail(UserDataService::USER_STATIC_ACTICE_BASE);
        $result = $userService->getUserInfo();
        if($result == false)
        {
            $log = "error:can't get robot info| uid : {$uid} |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
            $this->writelog($log);
        }else
        {
            $log = "robotInfo : ".json_encode($result)."|class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
            $this->writelog($log);
        }
        return $result;
    }
    /**
     * 获取一个机器人
     * @param $luid
     * @return bool
     */
    public function getRoomOneRobot( $luid )
    {
        $robotCount = count( $this->_roomRobotList[$luid] );
        if ( $robotCount )
        {
            if ( $robotCount == 1 )
            {
                $index = 0;
            }
            else
            {
                $index = rand( 0, $robotCount - 1 );
            }
            $this->_roomRobotList[$luid] = array_values( $this->_roomRobotList[$luid] );
            $uid = $this->_roomRobotList[$luid][$index];
            return $uid;
        }
        return false;
    }

    /**
     * 添加机器人
     * @param $luid
     * @param $uids
     */
    public function addRobot( $luid, $uids )
    {

        foreach ( $uids as $uid )
        {
             if(!isset($this->_roomRobotList[$luid]))
            {
                $this->_roomRobotList[$luid] = [];
            }
            if(!is_array( $this->_roomRobotList[$luid] ))
            {
                $this->_roomRobotList[$luid] = [];
            }
            if ( !in_array( $uid, $this->_roomRobotList[$luid] ) )
            {
                array_push( $this->_roomRobotList[$luid], $uid );
            }
        }
        $key = $this->getRobotHeadListKey( $luid );

        $this->getRedis()->del( $key );
        $this->getRedis()->sAddArray( $key, $this->_roomRobotList[$luid] );
    }

    public function subRobot( $luid, $uids )
    {
        foreach ( $uids as $uid )
        {
            if ( !is_array( $this->_roomRobotList[$luid] ) )
            {
                $this->_roomRobotList[$luid] = [];
            }

            if ( in_array( $uid, $this->_roomRobotList[$luid] ) )
            {
                $index = array_search( $uid, $this->_roomRobotList );
                unset( $this->_roomRobotList[$luid][$index] );
            }
        }

        asort( $this->_roomRobotList[$luid] );
        $key = $this->getRobotHeadListKey( $luid );

        $this->getRedis()->del( $key );
        $this->getRedis()->sAddArray( $key, $this->_roomRobotList[$luid] );
    }

    public function getRobotHeadListKey( $luid )
    {
        return $GLOBALS['env'] .self::ROBOT_HEAD_LIST_KEY . $luid;
    }
    /**
     * 执行房间规则动作
     */
    public function runRoomRuleAction()
    {
        $this->getSleepTime();
        //获取主播
        $list = $this->getLiveLuids();
        if(!empty($list))
        {
            foreach ( $list as $luid )
            {
                if ( !isset( $this->_viewerNumSpace[$luid] ) )
                {
                    //设置观众间隔规则
                    $this->initViewerNumSpaceRule( $luid );
                    //打开速率开关
                    $this->setViewerNumSpeedSwitchOn($luid);
                }
                if ( !isset( $this->_robotActiveSpace[$luid] ) )
                {
                    //设置机器人数量间隔
                    $this->initRobotActiveSpaceRule( $luid );
                }
            }
            $this->updateViewerNum();
            foreach ($this->_viewerNumSpace as $luid =>$value)
            {
                if($value['switch'] == self::ANCHOR_VIEWER_SPEED_SWITCH_ON)
                {
                    $this->_sleepSwitch = self::SLEEP_SWITCH_OFF;
                    $log = "sleepSwitchOFF BY luid:{$luid} |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                    $this->writelog($log);
                }
            }
            if($this->_sleepSwitch == self::SLEEP_SWITCH_ON)
            {
                $this->activeRobot();
                $sleep = $this->_sleepTime;
                $log = "sleep:{$sleep}s |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                $this->writelog($log);
                unset($this->_activeObj);
                unset($this->_robotData);
                unset($this->_userDataService);
                sleep( $sleep );
            }else
            {
                $this->_robotTimer = $this->_robotTimer + self::SLEEP_SECOND;
                $log = "robot sleep:{$this->_robotTimer }s |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                $this->writelog($log);
                if($this->_robotTimer >=  $this->_sleepTime)
                {
                    $log = "robot active :{$this->_robotTimer }s |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                    $this->writelog($log);
                    $this->activeRobot();
                    $this->_robotTimer = 0;
                }
                $sleep = self::SLEEP_SECOND;
                $log = "sleep:{$sleep} s |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                $this->writelog($log);
                unset($this->_activeObj);
                unset($this->_robotData);
                unset($this->_userDataService);
                sleep($sleep);
            }
            $this->_sleepSwitch = self::SLEEP_SWITCH_ON;
            $log = "Open sleepSwitchON|class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
            $this->writelog($log);
        }else
        {
            $sleep = $this->_sleepTime;
            $log = "sleep:{$sleep}s |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
            $this->writelog($log);
            unset($this->_activeObj);
            unset($this->_robotData);
            unset($this->_userDataService);
            sleep( $sleep );
        }
    }

    /**
     * 载入机器人配置
     */
    public function initRobotInfo()
    {
        $key = $GLOBALS['env'] . self::ALL_ROBOT_LIST_KEY;
        $res = $this->getRedis()->sMembers($key);
        if($res)
        {
            $this->_allRobotList = $res;
        }else
        {
            $this->_allRobotList =[];
            $uids = $this->getRobotData()->getAllRobotUids();
            foreach ($uids as $uid)
            {
                $this->_allRobotList []=  $uid['uid'];
            }
            $status = $this->getRedis()->sAddArray($key,$this->_allRobotList);
            if($status)
            {
                $this->getRedis()->expire($key,self::ALL_ROBOT_LIST_KEY_EXPIRE);
                $log = "redis add allRobotList:".json_encode($this->_allRobotList)." |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                $this->writelog($log);
            }else
            {
                $log = "error:redis error can't add robotlist |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                $this->writelog($log);
            }
        }
    }

    /**
     * 初始化机器人聊天信息
     */
    public function iniRobotMsg()
    {
        $key = $GLOBALS['env'].self::ROBOT_MSG_LIST_KEY;
        $msgList = $this->getRedis()->sMembers($key);
        if($msgList)
        {
            $this->setMsgList($msgList);
        }else
        {
            $msgList = [];
            $msg = $this->getRobotData()->getAllRobotChatMsg();
            if($msg == false)
            {
                $log = "error:mysql | error_msg: can't get robot msg |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                $this->writelog($log);
            }
            else
            {
                if(!empty($msg))
                {
                    foreach ($msg as $value)
                    {
                        $msgList []= $value['msg'];
                    }
                }
            }
            $this->setMsgList($msgList);
            $status = $this->getRedis()->sAddArray($key,$this->_msgList);
            if($status)
            {
                $this->getRedis()->expire($key,self::ROBOT_MSG_LIST_KEY_EXPIRE);
                $log = "redis save All msg|class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                $this->writelog($log);
            }else
            {
                $log = "error:redis | error_msg: redis setMsgList failled |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                $this->writelog($log);
            }
        }
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

    public function viewerStautsArray()
    {
        $status = [

        ];
    }
    public function selectRoomRobotViewer($postData)
    {
        $page = $postData['page'];
        $pageSize = $postData['pageSize'];
        $robotData = $this->getRobotData();
        $robotData->setPage($page);
        $robotData->setPageSize($pageSize);
        $data = $robotData->selectAdminAnchorViewerInfo();
        if($data === false)
        {
            $log ="errror:mysql failed | error_msg: can't get selectRoomRobotViewer |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
            $this->writelog($log);
            return false;
        }
        return $data;
    }
    public function searchRoomRobotViewer($postData)
    {
        $robotData = $this->getRobotData();
        $data = $robotData->searachAdminAnchorViewerInfo($postData);
        if($data === false)
        {
            $log ="errror:mysql failed | error_msg: can't get selectRoomRobotViewer |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
            $this->writelog($log);
            return false;
        }
        return $data;
    }
    public function addRoomRobotViewer($postData)
    {
        $robotData = $this->getRobotData();
        #查是否存在
        $res = $robotData->searachAdminAnchorViewerInfo($postData);
        if(is_array($res) && count($res) == 0)
        {
            $data = $robotData->insertAdminAnchorViewerInfo($postData);
        }else
        {
            $code = self::IS_EXIST_ANCHOR;
            $msg = 'uid:'.$res[0]['uid'].self::$errorMsg[$code];
            render_error_json($msg, $code);
        }
        if( false === $data)
        {
            $log ="errror:mysql failed | error_msg: can't addRoomRobotViewer |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
            $this->writelog($log);
            return false;
        }
        return $data;

    }
    public function updateRoomRobotViewer($postData)
    {
        $robotData = $this->getRobotData();
        $data = $robotData->updateAdminAnchorViewerInfo($postData);
        if($data === false)
        {
            $log ="errror:mysql failed | error_msg: can't updateRoomRobotViewer |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
            $this->writelog($log);
            return false;
        }
        return $data;
    }
    public function updateRoomRobotStatusById($postData)
    {
        $robotData = $this->getRobotData();
        $data = $robotData->updateAdminAnchorViewerStatus($postData);
        if($data === false)
        {
            $log ="errror:mysql failed | error_msg: can't updateRoomRobotViewer |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
            $this->writelog($log);
            return false;
        }
        return $data;
    }
    public function deleteRoomRobotViewer($postData)
    {
        $robotData = $this->getRobotData();
        $data = $robotData->deleteRoomRobotViewer($postData);
        if($data === false)
        {
            $log ="errror:mysql failed | error_msg: can't updateRoomRobotViewer |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
            $this->writelog($log);
            return false;
        }
        return $data;

    }
    /**
     * 进入房间计时器
     */
    public function enterRoomTimer($roomId,$uid,$expire = self::TIMER_EXPIRE)
    {
        $key = $GLOBALS['env'].self::TIMER_KEY.$roomId.$uid;
        $res = $this->getRedis()->get($key);
        if(!$res)
        {
            $status = $this->getRedis()->set($key,$uid);
            if($status)
            {
                $this->getRedis()->expire($key,$expire);
            }else
            {
                $log = "error:redis error can't add room : {$roomId}  uid :{$uid} enterRoomTimer |class:" . __CLASS__ . ';func:' .  __FUNCTION__ .';line:' . __LINE__ ;
                $this->writelog($log);
            }
            return true;
        }
        return false;
    }
}