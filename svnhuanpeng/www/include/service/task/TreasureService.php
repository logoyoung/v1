<?php
namespace service\task;
use service\common\AbstractService;
use lib\LiveRoom;
use lib\TreasureBox;
use service\user\UserDataService;
use Exception;
use service\event\EventManager;

/**
 * 宝箱服务
 * @author xuyong <[<email address>]>
 */
class TreasureService extends AbstractService
{

    private $_treasureID;
    private $_uid;
    private $_luid;
    private $_treasureDao;

    //开启宝箱调用底层服务异常
    const ERROR_OPEN_TREASURE       = -11601;
    //缺少参数或者参数类型错误
    const ERROR_OPEN_TREASURE_PARAM = -11605;
    //请重新登录
    const ERROR_OPEN_TREASURE_USER  = -11606;
    //请绑定手机
    const ERROR_OPEN_TREASURE_USER_PHONE = -11607;
    //没有获取到主播房间尚未领取的宝箱ID列表
    const ERROR_TREASURE_TBOX_LIST      = -11608;
    //获取未领取的宝箱ID列表时uid为空
    const ERROR_GET_TREASURE_UID_EMPTY  = -11609;
    //开启宝箱次数已达上线
    const ERROR_TREASURE_OPEN_MAX       = -11610;

    public static $errorMsg = [
        self::ERROR_OPEN_TREASURE           => '开启宝箱调用底层服务异常',
        self::ERROR_OPEN_TREASURE_PARAM     => '缺少参数或者参数类型错误',
        self::ERROR_OPEN_TREASURE_USER      => '请重新登录',
        self::ERROR_OPEN_TREASURE_USER_PHONE => '请绑定手机',
        self::ERROR_TREASURE_TBOX_LIST      => '没有获取到主播房间尚未领取的宝箱ID列表',
        self::ERROR_GET_TREASURE_UID_EMPTY  => '获取未领取的宝箱ID列表时uid为空',
        //-4049
        TreasureBox::ERROR_BOX_NOT_EXIST    => '该宝箱不存在',
        // -4048
        TreasureBox::ERROR_BOX_NOT_GET_TIME => '还未到领取时间',
        //-4055
        TreasureBox::ERROR_BOX_CLOSED       => '很遗憾，您没有领取到',
        //-1111
        TreasureBox::ERROR_GET_BOX_FAILED   => '您没有抢到',
        self::ERROR_TREASURE_OPEN_MAX       => '很遗憾，您没有领取到',

    ];

    /**
     * 用户uid
     * @param [type] $uid [description]
     */
    public function setUid($uid)
    {
        $this->_uid = $uid;
        return $this;
    }

    public function getUid()
    {
        return $this->_uid;
    }

    /**
     * 主播uid
     * @param [type] $luid [description]
     */
    public function setLuid($luid)
    {
        $this->_luid        = $luid;
        $this->_treasureDao = null;
        return $this;
    }

    public function getLuid()
    {
        return $this->_luid;
    }

    public function setTreasureID($treasureID)
    {
        $this->_treasureID = $treasureID;
        return $this;
    }

    public function getTreasureID()
    {
        return $this->_treasureID;
    }

    /**
     * 获取主播房间尚未领取的宝箱ID列表
     * @return array | null
     */
    public function getUnReceiveTreasureBoxInfoList()
    {
        $treasureDao = $this->getTreasureDao();
        $list        = $treasureDao->getUnReceiveTreasureBoxInfoList($this->getUid());

        if(!$list)
        {
            $code = self::ERROR_TREASURE_TBOX_LIST;
            $msg  = self::$errorMsg[$code];
            $log  = "error |error_code:{$code};msg:{$msg};uid:{$this->getUid()};luid:{$this->getLuid()}|class:".__CLASS__.';func:'.__FUNCTION__.';line:'.__LINE__.$this->getCaller();
            write_log($log);
        }

        $list['timeOut'] = TREASURE_TIME_OUT;
        if(!isset($list['list']) || empty($list['list']) )
        {
            return $list;
        }

        $uids     = array_column($list['list'], 'uid');
        $userData = [];
        if($uids)
        {
            $userService = new UserDataService();
            $userService->setCaller('class:'.__CLASS__.';func:'.__FUNCTION__.';line:'.__LINE__);
            $userService->setUid($uids);
            $userData    = $userService->batchGetUserInfo();

        } else
        {
            $code = self::ERROR_GET_TREASURE_UID_EMPTY;
            $log  = "error |error_code:{$code};msg:{$msg};uid:{$this->getUid()};luid:{$this->getLuid()}|class:".__CLASS__.';func:'.__FUNCTION__.';line:'.__LINE__.$this->getCaller();
             write_log($log);
        }

        foreach ($list['list'] as &$v)
        {
            $v['nick'] = isset($userData[$v['uid']]['nick']) ? $userData[$v['uid']]['nick'] : '';
        }

        return $list;
    }

    /**
     * 开启宝箱
     * @return int | Exception
     */
    public function openTreasureBox()
    {
        $result      = '';
        $logMsg      = "treasureID:{$this->getTreasureID()};uid:{$this->getUid()};luid:{$this->getLuid()};result:{$result}";
        write_log("notice |接收到开启宝箱请求: ".$logMsg.$this->getCaller());
        $treasureDao = $this->getTreasureDao();
        $openStatus  = $treasureDao->openTreasureBox($this->getUid(),$this->getTreasureID(),$result);
        $logMsg      = "treasureID:{$this->getTreasureID()};uid:{$this->getUid()};luid:{$this->getLuid()};result:{$result}";
        if($openStatus)
        {

            //开启宝箱次数已达上限
            if((int) $result == 0)
            {
                $code   = TreasureBox::ERROR_BOX_CLOSED;
                $msg    = self::$errorMsg[$code];
                $logMsg = "notice |开启宝箱次数已达上限,error_code:{$code}|".$logMsg.$this->getCaller();
                write_log($logMsg);
                throw new Exception($msg, $code);
            }

            $logMsg = "success |成功开启宝箱，领取到:$result|".$logMsg.$this->getCaller();
            write_log($logMsg);

            $event = new EventManager();
            $event->trigger(EventManager::ACTION_USER_MONEY_UPDATE,['uid' => $this->getUid()]);
            $event = null;

            //返回领取到的数量
            return $result;
        }

        $code   = ($result && isset(self::$errorMsg[$result])) ? $result : self::ERROR_OPEN_TREASURE;
        $msg    = self::$errorMsg[$code];
        $logMsg = "error |error_code:{$code};msg:{$msg}|{$logMsg}".$this->getCaller();
        write_log($logMsg);

        $errorCode = TreasureBox::ERROR_GET_BOX_FAILED;
        $msg       = self::$errorMsg[$errorCode];
        if(isset(self::$errorMsg[$result]))
        {
            $errorCode = $result;
            $msg       = self::$errorMsg[$errorCode];
        }

        throw new Exception($msg, $errorCode);
    }


    public function getTreasureOwnerUid()
    {
        $treasureDao = $this->getTreasureDao();
        return $treasureDao->getTreasureOwnerUid($this->getTreasureID());
    }

    public function getTreasureDao()
    {
        if(!$this->_treasureDao)
        {
            $this->_treasureDao = new LiveRoom($this->getLuid());
        }

        return $this->_treasureDao;
    }

}