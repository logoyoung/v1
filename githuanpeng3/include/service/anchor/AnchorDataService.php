<?php

namespace service\anchor;

use service\common\AbstractService;
use lib\Anchor;
use RedisHelp;

class AnchorDataService extends AbstractService
{

    //黑名单
    const ERROR_CODE_BLACK = -5030;
    //没有绑定手机号
    const ERROR_CODE_N5017 = -5017;
    const ERROR_CODE_N5028 = -5028;
    //从数据库获取主播财产异常
    const ERROR_CODE_PROPERTY = -8001;
    //从数据库获取主播等级异常
    const ERROR_CODE_ANCHORLEVEL = -8002;
    //从数据库获取等级list异常
    const ERROR_CODE_LEVEL_LIST = -8003;
    //从数据库获取房管列表list异常
    const ERROR_CODE_MANAGER_LIST = -8004;
    //从数据库获取最近直播的五场游戏名称异常
    const ERROR_CODE_GAME_LIST = -8005;
    //获取房管列表时roomid不能为空
    const ERROR_CODE_MANAGER_PARAM = -8006;
    //主播不显示公告
    const ANCHOR_NOTICE_TYPE_0 = 0;
    //主播显示公告
    const ANCHOR_NOTICE_TYPE_1 = 1;
    //审核中
    const ANCHOR_NOTICE_STATUS_W = 0;
    //审核通过
    const ANCHOR_NOTICE_STATUS_F = 1;
    //未通过
    const ANCHOR_NOTICE_STATUS_R = 2;
    //未填写
    const ANCHOR_NOTICE_STATUS_N = -1;

    private $_anchorDao = false;
    private $_uid = '';
    private $_status;
    private $_roomid;
    private $_anchorDB;
    private $_cookieUid;
    private $_property;
    private $_anchorLevel;
    private $_anchorLevelList;
    public static $errorMsg = [
        self::ERROR_CODE_BLACK => '黑名单主播',
        self::ERROR_CODE_N5017 => '主播没有绑定手机号',
        self::ERROR_CODE_N5028 => '主播没有实名认证',
        self::ERROR_CODE_PROPERTY => '从数据获取主播财产异常',
        self::ERROR_CODE_ANCHORLEVEL => '从数据库获取主播等级异常',
        self::ERROR_CODE_LEVEL_LIST => '从数据库获等级list异常',
        self::ERROR_CODE_MANAGER_LIST => '从数据库获取获取房管列表list异常',
        self::ERROR_CODE_GAME_LIST => '从数据库获取最近直播的五场游戏名称异常',
        self::ERROR_CODE_MANAGER_PARAM => '获取房管列表时roomid不能为空',
    ];

    /**
     * 主播uid
     * @param int $uid
     */
    public function setUid($uid)
    {
        $this->_uid = $uid;
        $this->_property = [];
        $this->_anchorLevel = '';
        $this->_anchorDao = '';
        $this->_anchorDB = '';
        $this->_anchorLevelList = '';
        return $this;
    }

    public function getUid()
    {
        return $this->_uid;
    }

    public function setRoomid($roomid)
    {
        $this->_roomid = $roomid;
        return $this;
    }

    public function getAnchorDao()
    {
        if (!$this->_anchorDao || !$this->_anchorDB)
        {
            $this->_anchorDao = new Anchor($this->getUid());
        }

        return $this->_anchorDao;
    }

    /**
     * 是否是主播
     * @return boolean
     */
    public function isAnchor()
    {
        $this->_status = $this->getAnchorStatus();
        return $this->_status === true;
    }

    /**
     * 获取主播 状态
     * @return true | error code
     *
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * 获取主播信息状态
     * @return array
     */
    public function getAnchorStatus()
    {
        return Anchor::isAnchor($this->getUid(), Anchor::getDB());
    }

    /**
     * 获所有财产
     * @return array
     */
    public function getProperty()
    {
        if (!$this->_property)
        {
            $anchorDao = $this->getAnchorDao();
            $this->_property = $anchorDao->getAnchorProperty();
            if (!$this->_property)
            {
                $code = self::ERROR_CODE_PROPERTY;
                $msg = self::$errorMsg[$code];
                $log = "error |error_code:{$code};msg:{$msg};uid:{$this->getUid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
                write_log($log);
            }
        }

        return $this->_property;
    }

    /**
     * 获取主播金币数
     *
     * @return int
     */
    public function getCoin()
    {
        $property = $this->getProperty();
        return isset($property['coin']) ? $property['coin'] : 0;
    }

    /**
     * 获取主播金豆数
     *
     * @return int
     */
    public function getBean()
    {
        $property = $this->getProperty();
        return isset($property['bean']) ? round($property['bean'], 1) : 0;
    }

    /**
     * 获取主播等级｜经验值
     *
     * @return array  array(
     * 'level' => '',//等级
     * 'integral' => ''//经验值
     * )
     */
    public function getAnchorLevel()
    {
        if (!$this->_anchorLevel)
        {
            $anchorDao = $this->getAnchorDao();
            $this->_anchorLevel = $anchorDao->getAnchorLevel();
            if (!$this->_anchorLevel)
            {
                $code = self::ERROR_CODE_ANCHORLEVEL;
                $msg = self::$errorMsg[$code];
                $log = "error |error_code:{$code};msg:{$msg};uid:{$this->getUid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . $this->getCaller();
                write_log($log);
            }
        }

        return $this->_anchorLevel;
    }

    /**
     *  获取所有等级列表
     * @return array
     */
    public function getAnchorLevelList()
    {
        return get_anchor_integral_by_level();
    }

    /**
     *  获取主播等级 对应的 积分数
     * @return int
     */
    public function getIntegralByLevel()
    {
        $anchorDao = $this->getAnchorDao();
        $level = $this->getAnchorLevel();
        return get_anchor_integral_by_level($level['level']);
    }

    /**
     * 获取关注主播的用户数
     * @return int
     */
    public function getFollowNumber()
    {
        $followDataService = new \service\follow\FollowDataService;
        return $followDataService->setUid($this->getUid())->getFansTotalNum();
    }

    /**
     * 获取最近直播的五场游戏名称
     * @return  array |false
     */
    public function getHistoryGameName()
    {
        $anchorDao = $this->getAnchorDao();
        $list = $anchorDao->getHistoryGameName();
        if (!$list)
        {
            $code = self::ERROR_CODE_GAME_LIST;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};uid:{$this->getUid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . $this->getCaller();
            write_log($log);
        }

        return $list;
    }

    public function getAnchorDb()
    {
        if (!$this->_anchorDB)
        {
            $this->_anchorDB = Anchor::getDB();
        }

        return $this->_anchorDB;
    }

    public function getRedis()
    {
        return new RedisHelp();
    }

}
