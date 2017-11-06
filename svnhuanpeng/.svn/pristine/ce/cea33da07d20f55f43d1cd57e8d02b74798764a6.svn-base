<?php

namespace service\room;

use service\common\AbstractService;
use lib\Anchor;
use lib\LiveRoom;
use service\user\UserDataService;
use Exception;
use service\user\UserDisableStatusService;
use service\room\helper\RoomidRedis;
use lib\room\Roomid;
use service\event\EventManager;
use service\user\UserAuthService;

/**
 * 房间管理员服务
 *
 */
class RoomManagerService extends AbstractService
{

    //获取房管列表时roomid不能为空
    const ERROR_CODE_MANAGER_PARAM = -8501;
    //底层服务房间roomid转主播uid异常
    const ERROR_CODE_RID_TO_UID = -8502;
    //设置禁言设用底层服务异常
    const ERROR_SET_SILENCED = -8503;
    //从数据库获取主播公告内容异常
    const ERROR_CODE_ANCHOR_NOTICE = -8007;
    //－1表示没有公告
    const STATUS_TYPE_N = -1;
    //1审核通过
    const STATUS_TYPE_F = 1;
    //未通过2
    const STATUS_TYPE_U = 2;

    private $_anchorDao;
    private $_anchorDB;
    //房间uid
    private $_roomid;
    //主播uid
    private $_uid;
    //操作者uid
    private $_acUid;
    private $_roomManagerDao;
    private $_roomManagerDb;
    private $_liveRoomDao;
    private $_targetUid;
    private $_managerUid;
    private $_fromDb       = false;
    private $_fromDbMaster = false;
    private $_roomidDb  = false;
    private $_anchorPdoDb  = false;
    private $_roomidRedis  = false;
    private $_page = 0;
    private $_size = 0;
    private $_infoLog      = 'room_manager_access';

    //禁言时间
    private $_timeLength;
    public static $errorMsg = [
        self::ERROR_CODE_MANAGER_PARAM => '获取房管员列表时roomid不能为空',
        self::ERROR_CODE_RID_TO_UID => '底层服务房间roomid转主播uid异常',
        self::ERROR_SET_SILENCED => '设置禁言设用底层服务异常',
        self::ERROR_CODE_ANCHOR_NOTICE => '从数据库获取主播公告内容异常',
    ];

    /**
     * 设置房间id
     * @param int $roomid
     */
    public function setRoomid($roomid)
    {
        $this->_roomid = is_array($roomid) ? array_values((array_unique($roomid))) : $roomid;
        return $this;
    }

    /**
     * 获取房间id
     * @return int
     */
    public function getRoomid()
    {
        return $this->_roomid;
    }

    /**
     * 设置 主播uid
     * @param int $uid
     */
    public function setUid($uid)
    {

        $this->_uid = is_array($uid) ? array_values((array_unique($uid))) : $uid;
        $this->_roomManagerDao = null;
        $this->_anchorDB       = null;
        $this->_anchorDao      = null;
        $this->_roomidDb       = false;
        $this->_anchorPdoDb    = false;
        $this->_fromDb         = false;
        $this->_fromDbMaster   = false;
        $this->_roomidRedis    = false;
        return $this;
    }

    public function setFromDb($fromDb = true)
    {
        $this->_fromDb = $fromDb;
        return $this;
    }

    public function getFromDb()
    {
        return $this->_fromDb;
    }

    public function setFromDbMaster($master = true)
    {
        $this->_fromDbMaster = $master;
        return $this;
    }

    public function getFromDbMaster()
    {
        return $this->_fromDbMaster;
    }

    /**
     * 获取 uid
     * @return int
     */
    public function getUid()
    {
        return $this->_uid;
    }

    /**
     *  设置 操作者uid
     * @param [type] $acUid [description]
     */
    public function setAcUid($acUid)
    {
        $this->_acUid = $acUid;
        return $this;
    }

    /**
     * 获取 操作者uid
     * @return [type] [description]
     */
    public function getAcUid()
    {
        return $this->_acUid;
    }

    /**
     * 设置 或 校验是否被 禁言的 uid
     * @param int $targetUid
     */
    public function setTargetUid($targetUid)
    {
        $this->_targetUid = $targetUid;
        return $this;
    }

    /**
     * 获取 禁言的 uid
     * @return int
     */
    public function getTargetUid()
    {
        return $this->_targetUid;
    }

    public function setManagerUid($managerUid)
    {
        $this->_managerUid = $managerUid;
        return $this;
    }

    public function getManagerUid()
    {
        return $this->_managerUid;
    }

    /**
     * 禁言时间
     * @param [type] $silenceTime [description]
     */
    public function setTimeLength($silenceTime)
    {
        $this->_timeLength = (int) $silenceTime;
        return $this;
    }

    /**
     * 获取禁言时间
     * @return [type] [description]
     */
    public function getTimeLength()
    {
        return $this->_timeLength ? $this->_timeLength : 0;
    }

    public function setPage($page)
    {
        $this->_page = (int) $page;
        return $this;
    }

    public function getPage()
    {
        return $this->_page;
    }

    public function setSize($size)
    {
        $this->_size = (int) $size;
        return $this;
    }

    public function getSize()
    {
        return $this->_size;
    }

    /**
     *  roomid 转换相对应的主播uid
     * @return int | false
     */
    public function getUidByRoomid()
    {
        $roomid = $this->getRoomid();
        if(!$roomid)
        {
            return false;
        }

        $roomid = (array) $roomid;
        $result = [];
        $dbRid  = [];
        $roomidRedis = $this->getRoomidRedis();
        if(!$this->getFromDb() && $roomidRedis->getRedis()->ping())
        {
            foreach ($roomid as $rid)
            {
                $uid = $roomidRedis->getUidByRoomid($rid);
                if($uid === -1)
                {
                    $dbRid[] = $rid;
                    continue;
                }

                if($uid === false )
                {
                    $dbRid[] = $rid;
                    //redis error log
                    $this->logError("redis 服务异常;roomid:".implode(',', $dbRid).";fun:".__FUNCTION__.';line:'.__LINE__);
                    continue;
                }

                $result[$rid] = (int) $uid;
            }

            if($dbRid)
            {
                $roomidDb = $this->getRoomidDb();
                $roomidDb->setMaster($this->getFromDbMaster());
                $dbData   = $roomidDb->getUidByRoomids($dbRid);
                if($dbData === false)
                {
                    //do db error log
                    $this->logError("mysql 服务异常;roomid:".implode(',', $dbRid).";fun:".__FUNCTION__.';line:'.__LINE__);
                }

                if($dbData)
                {
                    $result  = (array) $result + (array) $dbData;
                    $event   = new EventManager;
                    $action  = EventManager::ACTION_ANCHOR_RESET_CACHE;

                    foreach ($dbData as $uk => $uv)
                    {
                        $event->trigger($action,['uid' => $uv]);
                    }

                    $event   = null;
                }
            }

        } else
        {
            $roomidDb = $this->getRoomidDb();
            $roomidDb->setMaster($this->getFromDbMaster());
            $result   = $roomidDb->getUidByRoomids($roomid);
            if($result === false)
            {
                //do db error log
                $this->logError("mysql 服务异常;roomid:".implode(',', $roomid).";fun:".__FUNCTION__.';line:'.__LINE__);
                return 0;
            }
        }

        return !is_array($this->getRoomid()) ? (isset($result[$this->getRoomid()]) ? $result[$this->getRoomid()] : 0) :  $result;
    }

    public function getUidToRoomId()
    {
        return $this->getRoomidByUid();
    }

    /**
     * 通过uid 获取房间号
     * @return int | array
     */
    public function getRoomidByUid()
    {
        $uid = $this->getUid();
        if(!$uid)
        {
            return 0;
        }

        $uid    = (array) $uid;
        $result = [];
        $dbUid  = [];
        $roomidRedis = $this->getRoomidRedis();
        if(!$this->getFromDb() && $roomidRedis->getRedis()->ping())
        {
            foreach ($uid as $v)
            {
                $roomid = $roomidRedis->getRoomidByUid($v);
                if($roomid === -1 )
                {
                    $dbUid[] = $v;
                    continue;
                }

                if($roomid === false )
                {
                    $dbUid[] = $v;
                    //redis error log
                    $this->logError("redis 服务异常;uid:{$v};fun:".__FUNCTION__.';line:'.__LINE__);
                    continue;
                }

                $result[$v] = (int) $roomid;
            }

            if($dbUid)
            {
                $roomidDb = $this->getRoomidDb();
                $roomidDb->setMaster($this->getFromDbMaster());
                $dbData   = $roomidDb->getRoomidByUids($dbUid);
                if($dbData === false)
                {
                    //do db error log
                    $this->logError("mysql 服务异常;uid:".implode(',', $dbUid).";fun:".__FUNCTION__.';line:'.__LINE__);
                }

                if($dbData)
                {
                    $result  = (array) $result + (array) $dbData;
                    $event   = new EventManager;
                    $action  = EventManager::ACTION_ANCHOR_RESET_CACHE;

                    foreach ($dbData as $ek => $ev)
                    {
                        $event->trigger($action,['uid' => $ek]);
                    }
                    $event   = null;
                    $this->setFromDb(true);
                }
            }

        } else
        {
            $roomidDb = $this->getRoomidDb();
            $roomidDb->setMaster($this->getFromDbMaster());
            $result   = $roomidDb->getRoomidByUids($uid);
            if($result === false)
            {
                //do db error log
                $this->logError("mysql 服务异常;uid:".implode(',', $uid).";fun:".__FUNCTION__.';line:'.__LINE__);
                return 0;
            }
        }

        return !is_array($this->getUid()) ? (isset($result[$this->getUid()]) ? $result[$this->getUid()] : 0) :  $result;
    }

    /**
     * 通过uid 批量获取房间号
     * @return array|boolean
     */
    public function getRoomIdsByUids()
    {
        return $this->getRoomidByUid();
    }

    /**
     * 判断 acUid 是否为房管
     * @return boolean [description]
     */
    public function isRoomManager()
    {
        $anchorDao = $this->getRoomManagerDao();
        return $anchorDao->isRoomManager($this->getAcUid());
    }

    /**
     * 获取房管列表
     *
     *
     * @return array array(3,90,16,7887) 返回房管uid数组
     */
    public function getRoomManagerList()
    {

        //获取房管列表需要转入roomid
        $anchorDao = new Anchor($this->getUid());
        $managerList = $anchorDao->roomManagerList($this->getPage(), $this->getSize());
        return $managerList;
    }

    /**
     * 添加房间管理员
     */
    public function addRoomManager()
    {
        $logMsg = "uid:{$this->getUid()};managerId:{$this->getManagerUid()}";
        $this->logInfo("收到添加房间管理员;{$logMsg};fun:".__FUNCTION__.';line:'.__LINE__);
        if (!$this->getRoomManagerDao()->addRoomManager($this->getManagerUid()))
        {
            $this->logError("添加房间管理员失败; {$logMsg};fun:".__FUNCTION__.';line:'.__LINE__);
            return false;
        }

        $this->logInfo("添加房间管理员成功;{$logMsg};fun:".__FUNCTION__.';line:'.__LINE__);
        return true;
    }

    /**
     * 获取底层处理对象
     * @return anchor obj
     */
    public function getRoomManagerDao()
    {
        if (!$this->_roomManagerDao)
        {
            $this->_roomManagerDao = new Anchor($this->getUid());
        }

        return $this->_roomManagerDao;
    }

    /**
     * 获取访客禁言状态
     *  uid 主播uid
     *  targetUid 校验用户uid
     * @return boolean
     */
    public function isSilenced()
    {
        $auth = new UserAuthService();
        $auth->setUid($this->getTargetUid());
        $auth->setAnchorUid($this->getUid());

        if($auth->checkSilencedStatus() === false)
        {
            $result = $auth->getResult();
            $etime  = isset($result['silenced_etime']) ? $result['silenced_etime'] : 0;
            return $etime;
        }

        return true;
    }

    /**
     * 房间禁言
     */
    public function setSilence()
    {
        //操作者
        $acUid      = $this->getAcUid();
        //主播
        $anchorUid  = $this->getUid();
        //被禁言用户
        $targetUid  = $this->getTargetUid();
        //禁言时长
        $timeLength = $this->getTimeLength();
        $logMsg     = "acUid:{$acUid}; anchorUid:{$anchorUid}; targetUid:{$targetUid};timeLength:{$timeLength}";
        $this->logInfo("notice|收到设置禁言请求|{$logMsg};line:" . __LINE__ );

        $userDataService = new UserDataService();
        $userDataService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
        $userDataService->setUid([$targetUid, $acUid]);
        $usersInfo       = $userDataService->getUserInfo();
        $targetUserNick  = isset($usersInfo[$targetUid]['nick']) ? $usersInfo[$targetUid]['nick'] : '';
        $acUserNick      = isset($usersInfo[$acUid]['nick'])     ? $usersInfo[$acUid]['nick']     : '';

        $disableService  = new UserDisableStatusService();
        //被禁言用户
        $disableService->setUid($targetUid);
        $disableService->setType($disableService::USER_DISABLE_TYPE_SEND_MSG);
        //禁言房间主播uid
        $disableService->setScope($anchorUid);
        $disableService->setEtime($timeLength);
        $disableService->setPlatform(2);
        $disableService->setAcUid($acUid);

        try {

             //新版
            if(!$disableService->addDisable())
            {
                $msg = "error|禁言操作失败;targetUserNick:{$targetUserNick};{$logMsg};line:" . __LINE__;
                $this->logInfo($msg);
                $code   = self::ERROR_SET_SILENCED;
                $msg    = self::$errorMsg[$code];
                throw new Exception($msg, $code);
            }

            //直播间下发消息
            $result = $this->getLiveRoomDao()->setSilenced($targetUid, $targetUserNick, $acUid, $acUserNick, $timeLength);
            if($result !== true)
            {
                $msg = "warning|禁言下发直播间消息异常;targetUserNick:{$targetUserNick};{$logMsg};line:" . __LINE__ ;
                $this->logInfo($msg);
            }

            $msg = "success|禁言操作成功;targetUserNick:{$targetUserNick};{$logMsg};line:" . __LINE__ ;
            $this->logInfo($msg);

            return true;

        } catch (Exception $e) {

            $code    = $e->getCode();
            $msg     = $e->getMessage();
            $erroMsg = "error|error_code:{$code}; msg:{$msg}; nick:{$targetUserNick}; {$logMsg};line:" . __LINE__;
            $this->logInfo($erroMsg);
            throw new Exception($msg, $code);
        }

    }

    /**
     * 获取底层处理 数据库
     * @return db obj
     */
    public function getRoomManagerDb()
    {
        if (!$this->_roomManagerDb)
        {
            $this->_roomManagerDb = Anchor::getDB();
        }

        return $this->_roomManagerDb;
    }

    public function getLiveRoomDao()
    {
        if (!$this->_liveRoomDao)
        {
            $this->_liveRoomDao = new LiveRoom($this->getUid());
        }

        return $this->_liveRoomDao;
    }

    /**
     * 获取主播公告
     *
     * @return array|bool  status －1表示没有公告  0审核中  1审核通过 2未通过
     */
    public function getAnchorNotice()
    {
        $result = $this->getAnchorDao()->getAnchorNotice();

        if ($result === false)
        {
            $code = self::ERROR_CODE_ANCHOR_NOTICE;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};uid:{$this->getUid()}|class:" . __CLASS__ . ';func' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log);
            return false;
        }

        return $result;
    }

    public function getAnchorDb()
    {
        if (!$this->_anchorDB)
        {
            $this->_anchorDB = Anchor::getDB();
        }

        return $this->_anchorDB;
    }

    public function getAnchorDao()
    {
        if (!$this->_anchorDao || !$this->_anchorDB)
        {
            $this->_anchorDao = new Anchor($this->getUid());
        }

        return $this->_anchorDao;
    }

    public function getRoomidDb()
    {
        if(!$this->_roomidDb)
        {
            $this->_roomidDb = new Roomid();
        }

        return $this->_roomidDb;
    }

    public function getRoomidRedis()
    {
        if(!$this->_roomidRedis)
        {
            $this->_roomidRedis = new RoomidRedis();
        }

        return $this->_roomidRedis;
    }

    public function logInfo($msg)
    {
        write_log("info|{$msg};class:".__CLASS__.";caller:{$this->getCaller()}",$this->_infoLog);
    }

    public function logError($msg)
    {
        write_log("error|{$msg};class:".__CLASS__.";caller:{$this->getCaller()}",$this->_infoLog);
    }

}
