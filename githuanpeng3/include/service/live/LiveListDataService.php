<?php

namespace service\live;

use service\common\AbstractService;
use lib\Live;
use service\game\GameService;
use service\live\helper\LiveRedis;
use service\game\helper\GameRedis;
use service\user\FollowService;
use service\user\UserDataService;
use service\room\RoomManagerService;
use service\room\LiveRoomService;
use service\live\LiveService;

/**
 * 直播列表数据服务
 * @author longgang@6.cn
 * @date 2017-08-30 17:27:10
 * @copyright (c) 2017, 6.cn
 * @version 1.0.1
 */
class LiveListDataService extends AbstractService
{

    //默认页数
    const DEFAULT_PAGE = 1;
    //默认pc端输出数据条数
    const DEFAULT_PC_NUM = 24;
    //默认移动端出数据条数
    const DEFAULT_MB_NUM = 24;
    //根据gameID获取直播列表异常
    const ERROR_GET_LIVE_LIST_BY_GAMEID = -22001;
    //最热直播
    const LIVE_TYPE_HOT = 1;
    //最新直播
    const LIVE_TYPE_NEW = 2;
    //最多关注直播
    const LIVE_TYPE_FOLLOW = 3;
    const DOUBLE_SCREEN_ID = -100;

    //所有直播推荐类型
    public static $liveTypeAll = [
        self::LIVE_TYPE_HOT,
        self::LIVE_TYPE_NEW,
        self::LIVE_TYPE_FOLLOW,
    ];
    private $_page;
    private $_size;
    private $_liveType;
    private $_gameId;
    private $_fromDb = false;
    //底层数据服务
    private $_liveDao;
    public static $errorMsg = [
        self::ERROR_GET_LIVE_LIST_BY_GAMEID => '根据gameID获取直播列表失败',
    ];

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
            self::LIVE_TYPE_HOT => 'hotList',
            self::LIVE_TYPE_NEW => 'newList',
            self::LIVE_TYPE_FOLLOW => 'maxfollowList'
        ];

        if ($this->getFromDb())
        {
            foreach (self::$liveTypeAll as $liveType)
            {
                $this->setLiveType($liveType);
                $data[$liveTypeKey[$liveType]] = $this->getLiveListByGameIdFromDb();
            }
            return $data;
        }
        $liveRedis = new LiveRedis();

        $liveService = new LiveService();

        $liveService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);

        foreach (self::$liveTypeAll as $liveType)
        {
            $luids = $liveRedis->getLiveList($liveType, $this->getPage(), $this->getSize());
            $liveInfos = [];
            if ($luids && ($luids = array_filter($luids)))
            {
                $liveService->setLuid($luids);
                $liveInfos = $liveService->getLiveInfosByLuids();
                if ($liveInfos)
                {

                    switch ($liveType)
                    {
                        case self::LIVE_TYPE_HOT:
                            $liveInfos = multiArraySort($liveInfos, 'viewCount', 'liveid');
                            break;
                        case self::LIVE_TYPE_NEW:
                            $liveInfos = multiArraySort($liveInfos, 'stime', 'liveid');
                            break;
                        case self::LIVE_TYPE_FOLLOW:
                            $liveInfos = multiArraySort($liveInfos, 'fansCount', 'liveid');
                            break;
                    }
                }
            }

            $data[$liveTypeKey[$liveType]]['list'] = $liveInfos;
            $data[$liveTypeKey[$liveType]]['total'] = $liveRedis->getLiveCount($liveType);
            $data[$liveTypeKey[$liveType]]['ref'] = '全部直播';
        }

        if (!$data)
        {
            foreach (self::$liveTypeAll as $liveType)
            {
                $this->setLiveType($liveType);
                $data[$liveTypeKey[$liveType]] = $this->getLiveListByGameIdFromDb();
            }
            return $data;
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
        if ($luids && ($luids = array_filter($luids)))
        {
            $liveService = new LiveService();
            $liveService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            $liveService->setLuid($luids);
            $liveInfos = $liveService->getLiveInfosByLuids();
            if ($liveInfos)
            {

                switch ($this->_liveType)
                {
                    case self::LIVE_TYPE_HOT:
                        $liveInfos = multiArraySort($liveInfos, 'viewCount', 'liveid');
                        break;
                    case self::LIVE_TYPE_NEW:
                        $liveInfos = multiArraySort($liveInfos, 'stime', 'liveid');
                        break;
                    case self::LIVE_TYPE_FOLLOW:
                        $liveInfos = multiArraySort($liveInfos, 'fansCount', 'liveid');
                        break;
                }
            }
        }
        return $liveInfos;
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
            $data = $this->getLiveListByGameIdFromDb();
            return $data;
        }

        $gameRedis = new GameRedis();

        $luids = $gameRedis->getGameLiveList($this->_liveType, $this->_gameId, $this->getPage(), $this->getSize());

        $liveInfos = [];
        if ($luids  && ($luids = array_filter($luids)))
        {

            $liveService = new LiveService();
            $liveService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            $liveService->setLuid($luids);
            $liveInfos = $liveService->getLiveInfosByLuids();

            if ($liveInfos)
            {
                switch ($this->_liveType)
                {
                    case self::LIVE_TYPE_HOT:
                        $liveInfos = multiArraySort($liveInfos, 'viewCount', 'liveid');
                        break;
                    case self::LIVE_TYPE_NEW:
                        $liveInfos = multiArraySort($liveInfos, 'stime', 'liveid');
                        break;
                    case self::LIVE_TYPE_FOLLOW:
                        $liveInfos = multiArraySort($liveInfos, 'fansCount', 'liveid');
                        break;
                }
            }
        }

        if (!$liveInfos)
        {
            $data = $this->getLiveListByGameIdFromDb();
            return $data;
        }

        $data['list'] = $liveInfos;
        $gameLiveCount = $gameRedis->getGameLiveCount();
        $data['total'] = isset($gameLiveCount[$this->_gameId]) ? $gameLiveCount[$this->_gameId] : 0;
        $res = $gameRedis->getGameListDataByGameId([$this->_gameId]);

        if ($res && ($res = array_filter($res)))
        {
            $gameInfo = json_decode($res[$this->_gameId], true);
            $data['ref'] = $gameInfo['name'];
        }

        return $data;
    }

    public function getLiveDao()
    {
        if (!$this->_liveDao)
        {
            $this->_liveDao = new Live();
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
     * 根据gameID获取直播列表
     * @return array|boolean
     */
    public function getLiveListsByGameId()
    {
        $liveDao = $this->getLiveDao();
        $list = $liveDao->getLiveListsByGid($this->_gameId, $this->getLiveDb());
        if (!$list)
        {
            $code = self::ERROR_GET_LIVE_LIST_BY_GAMEID;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};gameid:{$this->_gameId}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log);
            return false;
        }
        return $list;
    }

    /**
     * 根据类型从数据库获取gameId对应的直播列表
     *
     * @return array|bool
     */
    public function getLiveListByGameIdFromDb()
    {
        if (!in_array($this->_liveType, array(self::LIVE_TYPE_NEW, self::LIVE_TYPE_HOT, self::LIVE_TYPE_FOLLOW)))
        {
            return false;
        }
        $result = $this->makeLiveListsByTypeOrGameId($this->getSize(), $this->getPage(), $this->_liveType);

        if ($this->_gameId)
        {
            if ($this->_gameId == OTHER_GAME)
            {
                $ref = '其他游戏';
            } else
            {
                if (empty($result['count']))
                {
                    $gameObj = new GameService();
                    $gameObj->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
                    $gameObj->setGameId($this->_gameId);
                    $ref = $gameObj->getGameTypeNameByGameId();
                } else
                {
                    $ref = $result['list'][0]['gameName'];
                }
            }
        } else
        {
            $ref = '全部直播';
        }
        if ($result['list'])
        {
            return array('list' => $result['list'], 'ref' => $ref, 'total' => $result['count']);
        } else
        {
            return array('list' => [], 'ref' => $ref, 'total' => "0");
        }
    }

    /**
     *  组合最热、最新、最多关注直播数据
     *
     * @param int    $size     数量
     * @param int    $page     页数
     * @param int    $type     类型
     *
     * @return array|bool
     */
    private function makeLiveListsByTypeOrGameId($size, $page, $type)
    {
        if (!in_array($type, array(self::LIVE_TYPE_NEW, self::LIVE_TYPE_HOT, self::LIVE_TYPE_FOLLOW)))
        {
            return false;
        }
        if ($type == self::LIVE_TYPE_HOT)
        {
            $sort = 'viewCount';
            $tow_sort = 'stime';
        }
        if ($type == self::LIVE_TYPE_NEW)
        {
            $sort = 'stime';
            $tow_sort = 'viewCount';
        }
        if ($type == self::LIVE_TYPE_FOLLOW)
        {
            $sort = 'fansCount';
            $tow_sort = 'stime';
        }

        $res = $this->DataLists();
        if ($res)
        {
            $afterSort = multiArraySort($res, $sort, $tow_sort);
        } else
        {
            $afterSort = [];
        }

        $afterSortLength = count($afterSort);

        $offect = ( $page - 1 ) * $size;
        $finallyLiveLists = array_slice($afterSort, $offect, $size);
        return array('list' => $finallyLiveLists, 'count' => $afterSortLength);
    }

    private function DataLists()
    {
        $gamelist = $luides = array();

        $liveLists = $this->getLiveListsByGameId();

        $conf = $this->getConf();

        if ($liveLists)
        {
            $luides = array_unique(array_column($liveLists, 'uid'));

            $followService = new FollowService();
            $followService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            $fansCount = $followService->getUserCount($luides);

            $userDataService = new UserDataService();
            $userDataService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            $userDataService->setUid($luides);
            $autherInfo = $userDataService->batchGetUserInfo();

            $roomManagerService = new RoomManagerService();
            $roomManagerService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            $roomManagerService->setUid($luides);
            $room = $roomManagerService->getRoomIdsByUids();

            $liveRoomService = new LiveRoomService();
            $liveRoomService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            $liveRoomService->setLuid($luides);
            $viewCount = $liveRoomService->batchGetLiveUserCountFictitious();

            foreach ($liveLists as $v)
            {
                $list['uid'] = $v['uid'];
                $list['head'] = array_key_exists($v['uid'], $autherInfo) ? $autherInfo[$v['uid']]['pic'] : '';
                $list['roomID'] = array_key_exists($v['uid'], $room) ? $room[$v['uid']] : 0;
                $list['gameName'] = $v['gamename'];
                $list['nick'] = array_key_exists($v['uid'], $autherInfo) ? $autherInfo[$v['uid']]['nick'] : '';
                $list['title'] = $v['title'];
                $list['stime'] = strtotime($v['ctime']);
                $list['orientation'] = $v['orientation'];
                if ($v['poster'])
                {
                    $list['poster'] = $conf['domain-lposter'] . "/" . $v['poster'];
                    $list['ispic'] = '1';
                } else
                {
                    $list['poster'] = CROSS;
                    $list['ispic'] = '0';
                }
                $list['viewCount'] = isset($viewCount[$v['uid']]) ? $viewCount[$v['uid']] : 0;
                $list['fansCount'] = array_key_exists($v['uid'], $fansCount) ? $fansCount[$v['uid']] : 0;
                array_push($gamelist, $list);
            }
        }
        return $gamelist;
    }

    /**
     * 获取双屏直播列表
     * @return array|boolean
     */
    public function getDoubleScreenLiveList()
    {
        //获取双屏直播列表
        $liveDao = new \lib\live\Live();
        $total = $liveDao->getLiveListCountByLiveType(Live::LIVE_TYPE_04);
        if (!$total)
        {
            return false;
        }
        $liveList = $liveDao->getLiveListByLiveType(Live::LIVE_TYPE_04, $this->getPage(), $this->getSize());

        $luids = array_unique(array_column($liveList, 'uid'));

        $liveRoomService = new LiveRoomService();
        $liveRoomService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
        $liveRoomService->setLuid($luids);
        $viewCount = $liveRoomService->batchGetLiveUserCountFictitious();

        $followService = new FollowService();
        $followService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
        $fansCount = $followService->getUserCount($luids);

        $datas = [];

        $userDataService = new UserDataService();
        $userDataService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
        $userDataService->setUid($luids);
        $userInfo = $userDataService->getUserInfo();

        $liveids = array_unique(array_column($liveList, 'liveid'));

        $subPoster = LiveService::getSlaveDataByLiveId($liveids);

        $data = [];
        foreach ($liveList as $v)
        {
            $liveInfo = [];
            $liveInfo['liveid'] = $v['liveid'];
            $liveInfo['uid'] = $v['uid'];
            $liveInfo['nick'] = $userInfo[$v['uid']]['nick'];
            $liveInfo['head'] = $userInfo[$v['uid']]['pic'];
            $liveInfo['gameID'] = $v['gameid'];
            $liveInfo['poster'] = !empty($v['poster']) ? stripos($v['poster'], 'http') === false ? LiveService::getPosterUrl($v['poster']) : $v['poster'] : '';

            if (LiveService::slaveIsLiving($v['uid']) == LiveService::PLAY_TYPE_02)
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
            $liveInfo['viewCount'] = $viewCount[$v['uid']];
            $liveInfo['fansCount'] = $fansCount[$v['uid']];
            $data[] = $liveInfo;
        }
        $datas['list'] = $data;
        $datas['total'] = $total;
        return $datas;
    }

}
