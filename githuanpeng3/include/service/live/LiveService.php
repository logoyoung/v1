<?php

namespace service\live;

use service\common\AbstractService;
use lib\Live;
use service\live\helper\LiveRedis;
use lib\live\LiveHelper;
use service\user\UserAuthService;
use Exception;
use service\event\EventManager;
use service\user\FollowService;
use service\user\UserDataService;
use service\room\LiveRoomService;
use service\room\RoomManagerService;

/**
 * 直播数据服务
 * @author longgang@6.cn
 * @date 2017-04-13 10:47:32
 * @copyright (c) 2017, 6.cn
 * @version 1.0.1
 */
class LiveService extends AbstractService
{

    //默认页数
    const DEFAULT_PAGE = 1;
    //默认pc端输出数据条数
    const DEFAULT_PC_NUM = 24;
    //默认移动端出数据条数
    const DEFAULT_MB_NUM = 24;
    //从底层数据获直播流服务地址异常
    const ERROR_PALY_RTMP_URL = -21001;
    //获取最后一场直播数据库异常
    const ERROR_CODE_LAST_LIVE = -21002;
    //用户不是主播
    const ERROR_IS_ANCHOR = -21003;
    //主播没有在直播
    const ERROR_IS_LIVING = -21004;
    //根据gameID获取直播列表异常
    const ERROR_GET_LIVE_LIST_BY_GAMEID = -21005;
    //从数据库获取所有游戏ID异常
    const ERROR_GAME_IDS = -21006;
    //从数据库获取直播ID对应直播信息异常
    const ERROR_LIVE_INFO = -21007;
    //获取直播主播id异常
    const ERROR_LIVING_LUID = -21008;
    //主播有事暂时离开
    const PLAY_TYPE_01 = 0;
    //正常直播
    const PLAY_TYPE_02 = 1;
    //结束直播
    const PLAY_TYPE_03 = 3;

    private $_page;
    private $_size;
    private $_liveType;
    private $_gameId;
    private $_luid;
    private $_liveId;
    private $_livePoster;
    private $_fromDb = false;
    //底层数据服务
    private $_liveDao;
    private $_lastLive;
    public static $errorMsg = [
        self::ERROR_PALY_RTMP_URL => '获直播流服务地址异常',
        self::ERROR_CODE_LAST_LIVE => '获取最后一场直播数据库异常',
        self::ERROR_IS_ANCHOR => '用户不是主播',
        self::ERROR_IS_LIVING => '主播没有在直播',
        self::ERROR_GET_LIVE_LIST_BY_GAMEID => '根据gameID获取直播列表失败',
        self::ERROR_GAME_IDS => '从数据库获取所有游戏ID异常',
        self::ERROR_LIVE_INFO => '从数据库获取直播ID对应直播信息异常',
        self::ERROR_LIVING_LUID => '获取直播主播id异常',
    ];
    private static $_liveLog = 'live_service';

    public function setLuid($luid)
    {
        $this->_luid = $luid;
        $this->_liveDao = null;
        $this->_lastLive = null;
        return $this;
    }

    public function getLuid()
    {
        return $this->_luid;
    }

    public function setPage($page)
    {
        $this->_page = $page;
        return $this;
    }

    public function getPage()
    {
        return $this->_page ? $this->_page : self::DEFAULT_PAGE;
    }

    public function setSize($size)
    {
        $this->_size = $size;
        return $this;
    }

    public function getSize()
    {
        return $this->_size ? $this->_size : self::DEFAULT_PC_NUM;
    }

    public function setLiveType($liveType)
    {
        $this->_liveType = $liveType;
        return $this;
    }

    public function setGameId($gameId)
    {
        $this->_gameId = $gameId;
        return $this;
    }

    /**
     * 传入原始直播封面图
     * @author xuyong <[<email address>]>
     * @param string
     */
    public function setLivePoster($poster)
    {
        $this->_livePoster = ltrim($poster, '/');
        return $this;
    }

    /**
     * 获取直播封面图
     * @author xuyong <[<email address>]>
     * @return string
     */
    public function getLivePoster()
    {
        return self::getPosterUrl($this->_livePoster);
    }

    public static function getPosterUrl($poster)
    {
        $conf = self::getConf();
        $poster = ltrim($poster, '/');
        return isset($conf['domain-lposter']) ? rtrim($conf['domain-lposter'], '/') . '/' . $poster : $poster;
    }

    /**
     * 设置直播id
     * @param int $liveId [description]
     */
    public function setLiveId($liveId)
    {
        $this->_liveId = $liveId;
        return $this;
    }

    /**
     *  获取直播id
     * @return int
     */
    public function getLiveId()
    {
        return $this->_liveId;
    }

    /**
     * db开关
     * @return boolean
     */
    public function setFromDb($fromDb = true)
    {
        $this->_fromDb = $fromDb;
        return $this;
    }

    public function getFromDb()
    {
        return $this->_fromDb;
    }

    /**
     * 获取正在直播的所有主播uid
     * @return array|boolean
     */
    public static function getLivingLuidByType($page = 1, $size = 0)
    {

        $liveRedis = new LiveRedis();

        $liveType = LiveListDataService::LIVE_TYPE_HOT;

        $luids = $liveRedis->getLiveList($liveType, $page, $size);

        if (!$luids)
        {
            $code = self::ERROR_LIVING_LUID;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__;
            write_log($log);
            return [];
        }

        return $luids;
    }

    /**
     * 获取最后一场直播，如果在直播的，也就是直播中的信息
     * @return array |false
     * @author xuyong <[<email address>]>
     */
    public function getLastLive()
    {
        if (!$this->_lastLive)
        {
            $this->_lastLive = Live::getLastLive($this->getLuid(), $this->getLiveDb());
            if (!$this->_lastLive)
            {
                $code = self::ERROR_CODE_LAST_LIVE;
                $msg = self::$errorMsg[$code];
                $log = "error |error_code:{$code};msg:{$msg};luid:{$this->getLuid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
                write_log($log);
                return false;
            }
        }

        return $this->_lastLive;
    }

    /**
     * 获直播流服务地址
     * @return array | null
     * @author xuyong <[<email address>]>
     */
    public function getLivePlayRtmpUrl()
    {
        $liveDao = $this->getLiveDao();
        $playUrl = $liveDao->getLivePlayRtmpUrl();
        if (!$playUrl)
        {
            $code = self::ERROR_PALY_RTMP_URL;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};luid:{$this->getLuid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log, 'live_service_access');
        }

        return $playUrl;
    }

    /**
     * 获取直播流信息
     * @return array | null
     * @author xuyong <[<email address>]>
     */
    public function getStreamList()
    {

        $auth = new UserAuthService();
        $auth->setUid($this->getLuid());
        if (!$auth->checkAnchorCertStatus())
        {
            $code = self::ERROR_IS_ANCHOR;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};luid:{$this->getLuid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log, 'live_service_access');
            return false;
        }

        $stream = [];
        //是否在直播中
        if ($this->isLiving())
        {
            $stream = $this->getLivePlayRtmpUrl();
        } else
        {
            $code = self::ERROR_IS_LIVING;
            $msg = self::$errorMsg[$code];
            $log = "notice |主播没有在直播 luid:{$this->getLuid()} {$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log, 'live_service_access');
        }

        $result = [];
        $result['orientation'] = '0';
        $liveInfo = $this->getLastLive();
        //直播信息
        if ($liveInfo)
        {
            $result['orientation'] = $liveInfo['orientation'];
            $result['liveID'] = $liveInfo['liveid'];
        }

        $result['streamList'] = isset($stream['rtmpServer']) ? (array) $stream['rtmpServer'] : [];
        $result['stream'] = isset($stream['stream']) ? $stream['stream'] : '';
        return $result;
    }

    public function getLiveDao()
    {
        if (!$this->_liveDao)
        {
            $this->_liveDao = new Live($this->getLuid());
        }

        return $this->_liveDao;
    }

    /**
     * 获取直播DB
     * @return
     */
    public function getLiveDb()
    {
        return Live::getDB();
    }

    public static function getConf()
    {
        return $GLOBALS['env-def'][$GLOBALS['env']];
    }

    /**
     * 是否在直播
     * @return int
     */
    public function isLiving()
    {

        $lastLive = $this->getLastLive();
        if (!$lastLive)
        {
            return 0;
        }

        return $lastLive['status'] == LIVE ? 1 : 0;
    }

    public function getLiveByLiveID()
    {
        $liveInfo = Live::getLiveByLiveID($this->getLiveId(), $this->getLiveDb());
        if (!$liveInfo)
        {
            $code = self::ERROR_LIVE_INFO;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};liveid:{$this->getLiveId()}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log);
        }

        return $liveInfo;
    }

    public function getLiveInfosByLuids()
    {
        $luids = (array) $this->getLuid();

        $liveRedis = new LiveRedis();

        $liveInfos = $liveRedis->getLiveInfoByUid($luids);

        $liveRoomService = new LiveRoomService();
        $liveRoomService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
        $liveRoomService->setLuid($luids);
        $viewCount = $liveRoomService->batchGetLiveUserCountFictitious();

        $followService = new FollowService();
        $followService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
        $fansCount = $followService->getUserCount($luids);

        if (!$liveInfos || !($liveInfos = array_filter($liveInfos)) || count($liveInfos) != count($luids))
        {

            $luids = implode(',', $luids);

            $res = $this->getLiveDao()->getLiveInfosByLuids($luids);
            if (!$res)
            {
                return false;
            }

            $luids = explode(',', $luids);

            $userDataService = new UserDataService();
            $userDataService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            $userDataService->setUid($luids);
            $userInfo = $userDataService->getUserInfo();

            $roomManagerService = new RoomManagerService();
            $roomManagerService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            $roomManagerService->setUid($luids);
            $room = $roomManagerService->getRoomIdsByUids();

            $liveids = array_unique(array_column($res, 'liveid'));

            $subPoster = LiveService::getSlaveDataByLiveId($liveids);

            $data = [];
            foreach ($res as $v)
            {
                $liveInfo = [];
                $liveInfo['liveid'] = $v['liveid'];
                $liveInfo['status'] = $v['status'];
                $liveInfo['uid'] = $v['uid'];
                $liveInfo['nick'] = $userInfo[$v['uid']]['nick'];
                $liveInfo['head'] = $userInfo[$v['uid']]['pic'];
                $liveInfo['gameID'] = $v['gameid'];
                $liveInfo['gameid'] = $v['gameid'];
                $liveInfo['poster'] = !empty($v['poster']) ? stripos($v['poster'], 'http') === false ? LiveService::getPosterUrl($v['poster']) : $v['poster'] : '';

                if (self::slaveIsLiving($v['uid']) == self::PLAY_TYPE_02)
                {
                    $liveInfo['subPoster'] = isset($subPoster[$v['liveid']]['poster']) ? $subPoster[$v['liveid']]['poster'] : '';
                } else
                {
                    $liveInfo['subPoster'] = '';
                }

                $liveInfo['title'] = $v['title'];
                $liveInfo['orientation'] = $v['orientation'];
                $liveInfo['stime'] = $v['stime'];
                $liveInfo['ctime'] = $v['stime'];
                $liveInfo['gameName'] = $v['gamename'];
                $liveInfo['gamename'] = $v['gamename'];
                $liveInfo['viewCount'] = $viewCount[$v['uid']];
                $liveInfo['fansCount'] = $fansCount[$v['uid']];
                $liveInfo['roomID'] = $room[$v['uid']];
                $data[] = $liveInfo;
            }
            //按照传进来的luid顺序排序
            $datas = [];
            foreach ($luids as $luid)
            {
                foreach ($data as $k => $v)
                {
                    if ($v['uid'] == $luid)
                    {
                        $datas[] = $v;
                        unset($data[$k]);
                    }
                }
            }

            return $datas;
        }

        foreach ($liveInfos as $k => $v)
        {
            $liveInfos[$k] = json_decode($v, true);
        }

        foreach ($liveInfos as &$v)
        {
            $v['viewCount'] = $viewCount[$v['uid']];
            $v['fansCount'] = $fansCount[$v['uid']];
        }

        if (!$liveInfos)
        {
            $code = self::ERROR_LIVE_INFO;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log);
        }

        return $liveInfos;
    }

    public function getLiveInfosByLiveIds($liveids)
    {
        $liveids = (array) $liveids;

        $liveRedis = new LiveRedis();

        $liveInfos = $liveRedis->getLiveInfo($liveids);

        if (!$liveInfos || !($liveInfos = array_filter($liveInfos)))
        {

            $liveids = implode(',', $liveids);

            return $this->getLiveDao()->getLiveInfosByLiveIds($liveids);
        }

        foreach ($liveInfos as $k => $v)
        {
            $liveInfos[$k] = json_decode($v, true);
        }

        if (!$liveInfos)
        {
            $code = self::ERROR_LIVE_INFO;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log);
        }

        return $liveInfos;
    }

    /**
     * 获取 web socket server
     * @return
     */
    public static function getWebSocketServer()
    {
        $conf = self::getConf();
        return isset($conf['web-socket']) ? $conf['web-socket'] : [];
    }

    /**
     * 获取双屏直播流(包含主屏与摄像头流信息)
     *    logoyang说以后只维护这个方法了，建义使用这个
     * @param  int $uid 主播uid
     * @return array
     */
    public static function getMultiPlayUrlByAnchorUid($uid)
    {
        try
        {

            $stream = LiveHelper::getplayurl($uid);
            return $stream ? (array) $stream : [];
        } catch (Exception $e)
        {

            write_log("error|获取直播流，数据库异常,anchorUid:{$uid}; error_msg:{$e->getMessage()};line:" . __LINE__, 'live_service_access');
            return [];
        }
    }

    public static function slaveIsLiving($uid)
    {
        $lastSlaveLiving = self::getMultiPlayUrlByAnchorUid($uid);

        return isset($lastSlaveLiving['slave']['playtype']) ? (int) $lastSlaveLiving['slave']['playtype'] : 0;
    }

    /**
     * 获取摄像头直播信息
     * @param  array $liveId  直播id
     * @param  array  $fields 需要获取的字段
     * @return         array | false
     */
    public static function getSlaveDataByLiveId($liveId, array $fields = ['poster'])
    {
        if (!$liveId)
        {
            return false;
        }
        $liveId = (array) $liveId;
        $liveId = array_values(array_unique(array_filter($liveId)));

        try
        {

            $slaveLive = LiveHelper::getlivebyid($liveId, $fields);
            if (!$slaveLive)
            {
                return [];
            }
        } catch (Exception $e)
        {
            write_log("error|获取摄像头直播信息，数据库异常,liveId:" . implode(',', $liveId) . ";msg:{$e->getMessage()}" . __LINE__ . ';class:' . __CLASS__, self::$_liveLog);
            return false;
        }

        foreach ($slaveLive as &$v)
        {
            if (isset($v['poster']))
            {
                $v['poster'] = self::getPosterUrl($v['poster']);
            }
        }

        return $slaveLive;
    }

    /**
     * 更新直播截图缓存
     * @param int $liveId
     * @param string $poster
     * @param boolean $master
     * @return boolean
     */
    public function updateLivePosterRedis($liveId, $poster, $master = true)
    {
        $liveInfo = $this->getLiveInfosByLiveIds($liveId);

        if (!$liveInfo)
        {
            return false;
        }

        $liveRedis = new LiveRedis();

        if ($master)
        {
            $liveInfo[$liveId]['poster'] = LiveService::getPosterUrl($poster);
            $liveInfo[$liveId]['ispic'] = '1';
        } else
        {
            $liveInfo[$liveId]['subPoster'] = LiveService::getPosterUrl($poster);
        }

        $params['liveinfo'] = $liveInfo;
        $event = new EventManager();

        $event->trigger(EventManager::ACTION_UPDATE_LIVE_INFO, $params);

        $event = null;
        return true;
    }

    public function createLiveRedis($liveId)
    {
        $liveInfo = Live::getLiveByLiveID($liveId, $this->getLiveDb());
        
        if (!$liveInfo)
        {
            $code = self::ERROR_LIVE_INFO;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};liveid:{$liveId}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log);
            return false;
        } 
        
        $params = [];
        $time = time();

        $userService = new UserDataService();
        $userService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
        $userService->setUid($liveInfo['uid']);
        $userInfo = $userService->getUserInfo();

        $roomManagerService = new RoomManagerService();
        $roomManagerService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
        $roomManagerService->setUid($liveInfo['uid']); 
        
        $followService = new FollowService();
        $followService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
        $fansCount = $followService->getUserCount($liveInfo['uid']);
        
        $liveInfo['head'] = isset($userInfo['pic']) ? $userInfo['pic'] : '';
        $liveInfo['nick'] = isset($userInfo['nick']) ? $userInfo['nick'] : '';
        $liveInfo['roomID'] = $roomManagerService->getRoomidByUid();
        $liveInfo['subPoster'] = '';
        $liveInfo['ispic'] = '0';
        $liveInfo['gameName'] = $liveInfo['gamename']; //兼容不同端取字段大小写不同
        $liveInfo['viewCount'] = 0;
        $liveInfo['fansCount'] = $fansCount;

        $params['liveinfo'] = [$liveInfo];
        
        $params['livestatus'][0]['liveid'] = $liveInfo['liveid'];
        $params['livestatus'][0]['status'] = $liveInfo['status'];

        $params['gamelivecount']['gameid'] = $liveInfo['gameid'];

        $params['liveid'] = $liveInfo['liveid'];
        $params['uid'] = $liveInfo['uid'];

        $types = LiveRedis::$sortType;
        foreach ($types as $type)
        {
            if ($type == LiveRedis::LIVE_LIST_BY_VIEW_COUNT)
            {
                $params['livelist'][$type]['score'] = 0;
                $params['gamelivelist'][$type]['score'] = 0;
            } elseif ($type == LiveRedis::LIVE_LIST_BY_CTIME)
            {
                $params['livelist'][$type]['score'] = $time;
                $params['gamelivelist'][$type]['score'] = $time;
            } elseif ($type == LiveRedis::LIVE_LIST_BY_FOLLOW_COUNT)
            {
                $params['livelist'][$type]['score'] = $fansCount;
                $params['gamelivelist'][$type]['score'] = $fansCount;
            }
            $params['livelist'][$type]['uid'] = $liveInfo['uid'];
            $params['gamelivelist'][$type]['uid'] = $liveInfo['uid'];
            $params['gamelivelist'][$type]['gameid'] = $liveInfo['gameid'];
        }
        unset($types,$liveInfo);
        
        $event = new EventManager();

        $event->trigger(EventManager::ACTION_LIVE_START, $params);

        $event = null;
        unset($params);
        
        return true;
    }

}
