<?php

namespace service\home;

use service\common\AbstractService;
use lib\information\AdminInformation;
use lib\information\RecommendInformation;
use lib\game\AdminRecommendGame;
use lib\live\AdminRecommendLive;
use service\game\GameService;
use service\live\LiveService;
use service\room\LiveRoomService;
use service\user\UserAuthService;
use service\user\UserDataService;

/**
 * APP首页服务类
 * @author longgang@6.cn
 * @date 2017-07-31 15:49:19
 * @copyright (c) 2017, 6.cn
 * @version 1.0.1
 */
class IndexForAppService extends AbstractService
{

    //默认页数
    const DEFAULT_PAGE = 1;
    //默认取值数
    const DEFAULT_SIZE = 20;
    //直播预取出数量
    const LIVE_RESERVE_NUM = 200;
    //获取轮播项id异常
    const ERROR_CAROUSEL_LIST_IDS = -750001;
    //获取轮播信息列表异常
    const ERROR_CAROUSEL_LIST = -750002;
    //获取热门推荐配置异常
    const ERROR_HOT_RECOMMEND_OPT = -750003;
    //获取游戏信息异常
    const ERROR_GAME_INFO = -750004;
    //获取主播id异常
    const ERROR_LUIDS = -750005;
    //获取直播信息异常
    const ERROR_LIVE_INFO = -750006;
    //获取游戏推荐配置异常
    const ERROR_GAME_RECOMMEND_OPT = -750007;
    //app首页直播每行数
    const COL_NUM = 2;
    //APP首页热门推荐楼层数,暂时写死
    const FLOOR_NUM = 2;
    const HOT_RECOMMEND_TEXT = "热门推荐";
    const HOT_RECOMMEND_ID = -99;
    //APP端
    const CLIENT_APP = 1;
    //pc端
    const CLIENT_PC = 2;
    //H5端
    const CLIENT_H5 = 3;
    //焦点推荐
    const RECOMMEND_TYPE_01 = 1;
    //列表推荐
    const RECOMMEND_TYPE_02 = 2;

    public static $errorMsg = [
        self::ERROR_CAROUSEL_LIST_IDS => '获取轮播项id异常',
        self::ERROR_CAROUSEL_LIST => '获取轮播信息列表异常',
        self::ERROR_HOT_RECOMMEND_OPT => '获取热门推荐配置异常',
        self::ERROR_GAME_INFO => '获取游戏信息异常',
        self::ERROR_LUIDS => '获取主播id异常',
        self::ERROR_LIVE_INFO => '获取直播信息异常',
        self::ERROR_GAME_RECOMMEND_OPT => '获取游戏推荐配置异常',
    ];
    private $_client = self::CLIENT_APP;
    private $_fromDb = true;
    private $_recommendType = self::RECOMMEND_TYPE_01;
    private $_a;
    private $_black;

    public function setClient($client)
    {
        $this->_client = $client;
        return $this;
    }

    public function getClient()
    {
        return $this->_client;
    }

    public function setRecommendType($recommendType)
    {
        $this->_recommendType = $recommendType;
        return $this;
    }

    public function getRecommendType()
    {
        return $this->_recommendType;
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

    public function setA($a)
    {
        $this->_a = $a;
        return $this;
    }

    public function setBlack($black)
    {
        $this->_black = $black;
        return $this;
    }

    public function getCarouselList()
    {
        if ($this->getFromDb())
        {
            $carouselList = $this->getAdminInformationDao()->getCarouselInfo($this->getClient(), $this->getRecommendType());

            if (!$carouselList)
            {
                $code = self::ERROR_CAROUSEL_LIST;
                $msg = self::$errorMsg[$code];
                $log = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
                write_log($log);
                return false;
            }

            $liveService = new LiveService();
            $liveService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            $conf = $this->getConf();

            foreach ($carouselList as $k => $v)
            {
                $carouselList[$k]['poster'] = !empty($v['poster']) ? DOMAIN_PROTOCOL . $conf['domain-img'] . $v['poster'] : '';
                if ($v['showType'] != 3)
                {
                    continue;
                }
                $liveService->setLuid($v['luid']);
                $liveInfo = $liveService->getLastLive();
                $carouselList[$k]['screenPic'] = isset($liveInfo['poster']) ? $conf['domain-lposter'] . '/' . $liveInfo['poster'] : '';
            }
            return $carouselList;
        }
    }

    public function getRecommendInformationDao()
    {
        return new RecommendInformation();
    }

    public function getAdminInformationDao()
    {
        return new AdminInformation();
    }

    public function getConf()
    {
        return $GLOBALS['env-def'][$GLOBALS['env']];
    }

    public function getLiveList()
    {
        //用于iOS检测
        if ($this->_a == 10)
        {
            //用于处理iOS检测
            if ($GLOBALS['env'] !== "DEV")
            {
                $liveid = [2045, 2050];
            } else
            {
                if ($this->_black)
                {
                    $array = array('1815' => 5, '1870' => 10);
                    $bres = explode(",", $this->_black);

                    for ($i = 0, $k = count($bres); $i < $k; $i++)
                    {
                        unset($array[$bres[$i]]);
                    }
                    if (empty($array))
                    {
                        $liveid = [0];
                    } else
                    {
                        $liveid = $array;
                    }
                } else
                {
                    $liveid = [5, 10];
                }
            }
            $liveService = new LiveService();
            $liveService->setCaller('class:' . __CLASS__ . 'func:' . __FUNCTION__ . ';line:' . __LINE__);

            $liveInfos = $liveService->getLiveInfosByLiveIds($liveid);
            if (!$liveInfos)
            {
                $code = self::ERROR_LIVE_INFO;
                $msg = self::$errorMsg[$code];
                $log = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
                write_log($log);
                return [];
            }
            
            $luids = array_unique(array_column($liveInfos, 'uid'));

            $liveRoomService = new LiveRoomService();
            $liveRoomService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            $liveRoomService->setLuid($luids);
            $viewCount = $liveRoomService->batchGetLiveUserCountFictitious();


            $conf = $this->getConf();
            $datas = [];
            $datas['gameName'] = self::HOT_RECOMMEND_TEXT;
            $datas['gameId'] = self::HOT_RECOMMEND_ID;
            //$datas['icon'] = !empty($gameInfo[0]['icon']) ? DOMAIN_PROTOCOL . $conf['domain-img'] . $gameInfo[0]['icon'] : '';
            $datas['icon'] = DOMAIN_PROTOCOL . $conf['domain'] . '/static/img/hot_recommend/icon_hot@3x.png';

            $authService = new UserAuthService();
            $authService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);

            $userDataService = new UserDataService();
            $userDataService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            $userDataService->setUid($luids);
            $userInfo = $userDataService->getUserInfo();

            $data = [];
            foreach ($liveInfos as $v)
            {
                $liveInfo = [];
                $liveInfo['liveid'] = $v['liveid'];
                $liveInfo['uid'] = $v['uid'];
                $liveInfo['nick'] = $userInfo[$v['uid']]['nick'];
                $liveInfo['head'] = $userInfo[$v['uid']]['pic'];
                $liveInfo['gameID'] = $v['gameid'];
                $liveInfo['poster'] = !empty($v['poster']) ? stripos($v['poster'], 'http') === false ? LiveService::getPosterUrl($v['poster']) : $v['poster'] : '';
                $liveInfo['title'] = $v['title'];
                $liveInfo['orientation'] = $v['orientation'];
                $liveInfo['stime'] = $v['stime'];
                $liveInfo['gameName'] = $v['gamename'];
                $liveInfo['viewCount'] = $viewCount[$v['uid']];
                $authService->setUid($v['uid']);
                $liveInfo['isCert'] = $authService->checkIsDueAnchor() ? 1 : 2;
                $data[] = $liveInfo;
            }
            $datas['liveList'] = $data;

            return [$datas];
        }
        
        $hotRecommendLiveList = $this->_getHotRecommendLiveList();
        $floorGameLiveList = $this->_getFloorGameLiveList();

        return array_merge($hotRecommendLiveList, $floorGameLiveList);
    }

    private function _getHotRecommendLiveList()
    {
        $adminRecommendLive = new AdminRecommendLive();

        $adminRecommendGame = new AdminRecommendGame();
        $opt = $adminRecommendGame->getGameIdByRecommendType(AdminRecommendGame::HOT_RECOMMEND_LIVE);
        if (!$opt)
        {
            $code = self::ERROR_HOT_RECOMMEND_OPT;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log);
            return [];
        }
        //TODO 目前暂时将热门推荐图标写死
        /*
          $gameService = new GameService();
          $gameService->setCaller('class:' . __CLASS__ . 'func:' . __FUNCTION__ . ';line:' . __LINE__);
          $gameInfo = isset($opt[0]['gameid']) ? $gameService->getGameInfoById([$opt[0]['gameid']]) : [];
          if (!$gameInfo)
          {
          $code = self::ERROR_GAME_INFO;
          $msg = self::$errorMsg[$code];
          $log = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
          write_log($log);
          return [];
          }

         */

        $floorNum = isset($opt['number']) ? (int) $opt['number'] : self::FLOOR_NUM;

        $liveService = new LiveService();
        $liveService->setCaller('class:' . __CLASS__ . 'func:' . __FUNCTION__ . ';line:' . __LINE__);

        /*         * 过滤未在直播的主播----------start------------* */
        $total = $adminRecommendLive->getRecommendLiveLuserCount();
        $totalPage = ceil($total / self::LIVE_RESERVE_NUM);

        $showNum = $floorNum * self::COL_NUM;
        $getNum = self::DEFAULT_SIZE;
        $page = self::DEFAULT_PAGE;
        $liveInfos = [];
        $luids = [];

        while ($getNum)
        {
            if ($page > $totalPage)
            {
                break;
            }

            //推荐主播
            $luid = $adminRecommendLive->getRecommendLiveLuser($page, self::LIVE_RESERVE_NUM);
            $page++;

            if (!$luid)
            {
                $code = self::ERROR_LUIDS;
                $msg = self::$errorMsg[$code];
                $log = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
                write_log($log);
                return [];
            }

            $liveService->setLuid($luid);
            $res = $liveService->getLiveInfosByLuids();

            if (!$res)
            {
                continue;
            }

            foreach ($res as $v)
            {
                $liveService->setLuid($v['uid']);

                if ($liveService->isLiving())
                {
                    $liveInfos[] = $v;
                    $luids[] = $v['uid'];
                    $getNum--;
                }
                if ($getNum <= 0)
                {
                    break;
                }
            }
        }

        if (!$liveInfos)
        {
            $code = self::ERROR_LIVE_INFO;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log);
            return [];
        }

        //随机取出指定数量展示
        shuffle($liveInfos);
        $liveInfos = array_slice($liveInfos, 0, $showNum, true);

        $liveRoomService = new LiveRoomService();
        $liveRoomService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
        $liveRoomService->setLuid($luids);
        $viewCount = $liveRoomService->batchGetLiveUserCountFictitious();


        $conf = $this->getConf();
        $datas = [];
        $datas['gameName'] = self::HOT_RECOMMEND_TEXT;
        $datas['gameId'] = self::HOT_RECOMMEND_ID;
        //$datas['icon'] = !empty($gameInfo[0]['icon']) ? DOMAIN_PROTOCOL . $conf['domain-img'] . $gameInfo[0]['icon'] : '';
        $datas['icon'] = DOMAIN_PROTOCOL . $conf['domain'] . '/static/img/hot_recommend/icon_hot@3x.png';

        $authService = new UserAuthService();
        $authService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);

        $userDataService = new UserDataService();
        $userDataService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
        $userDataService->setUid($luids);
        $userInfo = $userDataService->getUserInfo();

        $data = [];
        foreach ($liveInfos as $v)
        {
            $liveInfo = [];
            $liveInfo['liveid'] = $v['liveid'];
            $liveInfo['uid'] = $v['uid'];
            $liveInfo['nick'] = $userInfo[$v['uid']]['nick'];
            $liveInfo['head'] = $userInfo[$v['uid']]['pic'];
            $liveInfo['gameID'] = $v['gameid'];
            $liveInfo['poster'] = !empty($v['poster']) ? stripos($v['poster'], 'http') === false ? LiveService::getPosterUrl($v['poster']) : $v['poster'] : '';
            $liveInfo['title'] = $v['title'];
            $liveInfo['orientation'] = $v['orientation'];
            $liveInfo['stime'] = $v['stime'];
            $liveInfo['gameName'] = $v['gamename'];
            $liveInfo['viewCount'] = $viewCount[$v['uid']];
            $authService->setUid($v['uid']);
            $liveInfo['isCert'] = $authService->checkIsDueAnchor() ? 1 : 2;
            $data[] = $liveInfo;
        }
        $datas['liveList'] = $data;

        return [$datas];
    }

    private function _getFloorGameLiveList()
    {
        $adminRecommendGame = new AdminRecommendGame();
        $opt = $adminRecommendGame->getGameIdByRecommendType(AdminRecommendGame::APP_FLOOR_GAME);
        $gameids = !empty($opt['gameid']) ? explode(',', $opt['gameid']) : '';
        if (!$opt || !$gameids)
        {
            $code = self::ERROR_GAME_RECOMMEND_OPT;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log);
            return [];
        }

        $gameService = new GameService();
        $gameService->setCaller('class:' . __CLASS__ . 'func:' . __FUNCTION__ . ';line:' . __LINE__);
        $gameInfo = $gameService->getGameInfoById($gameids);

        if (!$gameInfo)
        {
            $code = self::ERROR_GAME_INFO;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log);
            return [];
        }

        $showNums = [];
        $numbers = !empty($opt['number']) ? explode(',', $opt['number']) : [];
        foreach ($gameids as $k => $v)
        {
            $showNums[$v] = isset($numbers[$k]) ? $numbers[$k] * self::COL_NUM : self::FLOOR_NUM * self::COL_NUM;
        }

        $liveService = new LiveService();
        $liveService->setCaller('class:' . __CLASS__ . 'func:' . __FUNCTION__ . ';line:' . __LINE__);
        $liveService->setLiveType(GameService::LIVE_TYPE_HOT);
        $liveService->setPage(self::DEFAULT_PAGE);
        $liveService->setSize(self::DEFAULT_SIZE);

        $authService = new UserAuthService();
        $authService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);

        $liveRoomService = new LiveRoomService();
        $liveRoomService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);

        $datas = [];
        foreach ($gameids as $gameid)
        {
            $data = [];
            $liveService->setGameId($gameid);
            $res = $liveService->getLiveListByLiveTypeAndGameId();

            if (!$res || $res['total'] <= 0)
            {
                $code = self::ERROR_LIVE_INFO;
                $msg = self::$errorMsg[$code];
                $log = "error_code:{$code};msg:{$msg};gameid:{$gameid}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
                write_log($log);
                continue;
            }

            $data['gameName'] = $gameInfo[$gameid]['name'];
            $data['gameId'] = $gameid;
            $data['icon'] = $gameInfo[$gameid]['icon'];
            shuffle($res['list']);
            $res['list'] = array_slice($res['list'], 0, $showNums[$gameid]);

            $luids = array_column($res['list'], 'uid');

            $liveRoomService->setLuid($luids);
            $viewCount = $liveRoomService->batchGetLiveUserCountFictitious();

            $liveInfos = [];
            foreach ($res['list'] as $k => $v)
            {
                $liveInfo = [];
                $liveInfo['liveid'] = $v['liveid'];
                $liveInfo['uid'] = $v['uid'];
                $liveInfo['nick'] = $v['nick'];
                $liveInfo['head'] = $v['head'];
                $liveInfo['gameID'] = $gameid;
                $liveInfo['poster'] = $v['poster'];
                $liveInfo['title'] = $v['title'];
                $liveInfo['orientation'] = $v['orientation'];
                $liveInfo['stime'] = $v['stime'];
                $liveInfo['gameName'] = $v['gamename'];
                $liveInfo['viewCount'] = $viewCount[$v['uid']];
                $authService->setUid($v['uid']);
                $liveInfo['isCert'] = $authService->checkIsDueAnchor() ? 1 : 2;
                $liveInfos[] = $liveInfo;
            }

            $data['liveList'] = $liveInfos;
            $datas[] = $data;
        }
        return $datas;
    }

}
