<?php

namespace service\live;

use service\common\AbstractService;
use lib\Live;
use service\anchor\AnchorDataService;
use service\game\GameService;
use service\live\helper\LiveRedis;
use service\game\helper\GameRedis;

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

    //所有直播推荐类型
    public static $liveTypeAll = [
        GameService::LIVE_TYPE_HOT,
        GameService::LIVE_TYPE_NEW,
        GameService::LIVE_TYPE_FOLLOW,
    ];
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
     * 直播大厅视频列表
     * @return array
     */
    public function getLiveList()
    {
        $data = [];
        $liveTypeKey = [
            GameService::LIVE_TYPE_HOT => 'hotList',
            GameService::LIVE_TYPE_NEW => 'newList',
            GameService::LIVE_TYPE_FOLLOW => 'maxfollowList'
        ];

        if ($this->getFromDb())
        {
            foreach (self::$liveTypeAll as $liveType)
            {
                $gameService = new GameService();
                $gameService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
                $gameService->setFromDb($this->getFromDb());
                $data[$liveTypeKey[$liveType]] = $gameService->getLiveListByGameId($liveType, $this->getPage(), $this->getSize());
            }
            return $data;
        }
        $liveRedis = new LiveRedis();
        foreach (self::$liveTypeAll as $liveType)
        {
            $luids = $liveRedis->getLiveList($liveType, $this->getPage(), $this->getSize());
            $liveInfos = [];
            if ($luids)
            {
                $res = $liveRedis->getLiveInfoByUid($luids);
                if ($res)
                {
                    foreach ($res as $v)
                    {
                        $liveInfos[] = json_decode($v, true);
                    }
                    switch ($liveType)
                    {
                        case GameService::LIVE_TYPE_HOT:
                            $liveInfos = multiArraySort($liveInfos, 'viewCount', 'liveid');
                            break;
                        case GameService::LIVE_TYPE_NEW:
                            $liveInfos = multiArraySort($liveInfos, 'stime', 'liveid');
                            break;
                        case GameService::LIVE_TYPE_FOLLOW:
                            $liveInfos = multiArraySort($liveInfos, 'fansCount', 'liveid');
                            break;
                    }
                }
            }



            $data[$liveTypeKey[$liveType]]['list'] = $liveInfos;
            $data[$liveTypeKey[$liveType]]['total'] = $liveRedis->getLiveCount($liveType);
            $data[$liveTypeKey[$liveType]]['ref'] = '全部直播';
        }

        return $data;
    }

    /**
     * 获取live总数
     */
    public function getLiveTotal()
    {
        $liveRedis = new LiveRedis();
        return $liveRedis->getLiveCount($this->_liveType);
    }

    /**
     * 根据type获取直播列表
     * @return array
     */
    public function getLiveListByType()
    {

        $liveRedis = new LiveRedis();

        $luids = $liveRedis->getLiveList($this->_liveType, $this->getPage(), $this->getSize());
        $liveInfos = [];
        if ($luids)
        {
            $res = $liveRedis->getLiveInfoByUid($luids);
            if ($res)
            {
                foreach ($res as $v)
                {
                    $liveInfos[] = json_decode($v, true);
                }

                switch ($this->_liveType)
                {
                    case GameService::LIVE_TYPE_HOT:
                        $liveInfos = multiArraySort($liveInfos, 'viewCount', 'liveid');
                        break;
                    case GameService::LIVE_TYPE_NEW:
                        $liveInfos = multiArraySort($liveInfos, 'stime', 'liveid');
                        break;
                    case GameService::LIVE_TYPE_FOLLOW:
                        $liveInfos = multiArraySort($liveInfos, 'fansCount', 'liveid');
                        break;
                }
            }
        }
        return $liveInfos;
    }

    /**
     * 获取正在直播的所有主播uid
     * @return array|boolean
     */
    public static function getLivingLuidByType($page = 1, $size = 0)
    {

        $liveRedis = new LiveRedis();

        $liveType = GameService::LIVE_TYPE_HOT;

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
     * 根据type和gameID获取直播列表
     * @return array
     */
    public function getLiveListByLiveTypeAndGameId()
    {
        $data = [];


        if ($this->getFromDb())
        {
            $gameService = new GameService();
            $gameService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);

            $data = $gameService->getLiveListByGameId($this->_liveType, $this->getPage(), $this->getSize(), $this->_gameId);
            return $data;
        }

        $gameRedis = new GameRedis();
        $liveRedis = new LiveRedis();
        $luids = $gameRedis->getGameLiveList($this->_liveType, $this->_gameId, $this->getPage(), $this->getSize());
        $liveInfos = [];
        if ($luids)
        {
            $res = $liveRedis->getLiveInfoByUid($luids);
            if ($res)
            {
                foreach ($res as $v)
                {
                    $liveInfos[] = json_decode($v, true);
                }
                switch ($this->_liveType)
                {
                    case GameService::LIVE_TYPE_HOT:
                        $liveInfos = multiArraySort($liveInfos, 'viewCount', 'liveid');
                        break;
                    case GameService::LIVE_TYPE_NEW:
                        $liveInfos = multiArraySort($liveInfos, 'stime', 'liveid');
                        break;
                    case GameService::LIVE_TYPE_FOLLOW:
                        $liveInfos = multiArraySort($liveInfos, 'fansCount', 'liveid');
                        break;
                }
            }
        }

        if (!$liveInfos)
        {
            $gameService = new GameService();
            $gameService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);

            $data = $gameService->getLiveListByGameId($this->_liveType, $this->getPage(), $this->getSize(), $this->_gameId);
            return $data;
        }

        $data['list'] = $liveInfos;
        $gameLiveCount = $gameRedis->getGameLiveCount();
        $data['total'] = isset($gameLiveCount[$this->_gameId]) ? $gameLiveCount[$this->_gameId] : 0;
        $res = $gameRedis->getGameListDataByGameId([$this->_gameId]);
        if ($res)
        {
            $gameInfo = json_decode($res[$this->_gameId], true);
            $data['ref'] = $gameInfo['name'];
        }

        return $data;
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
            write_log($log);
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

        $anchorService = new AnchorDataService();
        $anchorService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
        $anchorService->setUid($this->getLuid());
        //是否是主播
        if (!$anchorService->isAnchor())
        {
            $code = self::ERROR_IS_ANCHOR;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};luid:{$this->getLuid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log);
            return false;
        }

        $stream = null;
        //是否在直播中
        if ($this->isLiving())
        {
            $stream = $this->getLivePlayRtmpUrl();
        } else
        {
            $code = self::ERROR_IS_LIVING;
            $msg = self::$errorMsg[$code];
            $log = "notice |主播没有在直播 luid:{$this->getLuid()} {$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log);
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

    /**
     * 根据gameID获取直播列表
     * @return array|boolean
     */
    public function getLiveListsByGameId()
    {
        $liveObj = new Live();
        $list = $liveObj->getLiveListsByGid($this->_gameId, $this->getLiveDb());
        if (!$list)
        {
            $code = self::ERROR_GET_LIVE_LIST_BY_GAMEID;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};luid:{$this->getLuid()};gameid:{$this->_gameId}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log);
            return false;
        }
        return $list;
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

        if (!$liveInfos)
        {

            $luids = implode(',', $luids);

            return $this->getLiveDao()->getLiveInfosByLuids($luids);
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

    public function getLiveInfosByLiveIds($liveids)
    {
        $liveids = (array) $liveids;

        $liveRedis = new LiveRedis();

        $liveInfos = $liveRedis->getLiveInfo($liveids);

        $liveInfos = array_filter($liveInfos);
        
        if (!$liveInfos)
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

}
