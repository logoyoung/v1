<?php

namespace service\home;

use lib\BaseInfo;
use lib\Rank;
use service\rank\RankService;
use service\game\GameService;
use service\room\RoomManagerService;
use service\live\LiveService;
use service\live\LiveListDataService;
use service\user\UserDataService;
use service\room\LiveRoomService;
use service\user\FollowService;
use service\common\AbstractService;
use RedisHelp;
use service\live\helper\LiveRedis;
use service\game\helper\GameRedis;
use service\home\IndexForAppService;
use lib\information\AdminInformation;

/**
 * 首页服务类
 * @author longgang@6.cn
 * @date 2017-04-13 17:19:25
 * @copyright (c) 2017, 6.cn
 * @version 1.0.1
 */
class IndexService extends AbstractService
{

    //默认页数
    const DEFAULT_PAGE = 1;
    //pc首页轮播数
    const INDEX_FLASH_PC_NUM = 6;
    //pc首页猜你喜欢数
    const INDEX_GUESS_PC_NUM = 6;
    //pc首页排行榜数
    const INDEX_RANK_PC_NUM = 5;
    //pc首页游戏分类数
    const INDEX_GAME_TYPE_PC_NUM = 5;
    //pc首页新闻中心数
    const INDEX_INFORMATION_PC_NUM = 5;
    //pc首页推荐游戏数
    const INDEX_GAME_LIVE_PC_NUM = 4;
    //王者荣耀
    const WZRY = 'wzry';
    //球球大作战
    const QQDZZ = 'qqdzz';
    //穿越火线
    const CYHX = 'cyhx';
    //获取轮播推荐视频失败
    const ERROR_RECOMMEND_LIVE_LIST = 740001;
    //获取猜你喜欢列表失败
    const ERROR_GUESS_YOU_LIKE_LIST = 740002;
    //获取主播人气排行榜列表失败
    const ERROR_RANK_ANCHOR_LIST = 740003;
    //获取财富排行榜列表失败
    const ERROR_RANK_MONEY_LIST = 740004;
    //获取游戏分类列表失败
    const ERROR_RECOMMEND_GAME_LIST = 740005;
    //获取新闻中心列表失败
    const ERROR_INFORMATION_LIST = 740006;
    //获取推荐游戏视频列表失败
    const ERROR_RECOMMEND_GAME_LIVE_LIST = 740007;
    //首页推荐直播前缀
    const RECOMMEND_LIVE_LIST = 'recommend_live_list_index';
    //首页资讯
    const INFOEMATION_LIST = 'hp_information_list_index';
    //app首页热门直播前缀
    const HOT_LIVE_LIST = 'hp_hot_live_list_app_index';
    //app首页热门视频前缀
    const HOT_VIDEO_LIST = 'hp_hot_video_list_app_index';
    //首页最新直播前缀
    const NEW_LIVE_LIST = 'hp_new_live_list_app_index';
    //首页最新视频前缀
    const NEW_VIDEO_LIST = 'hp_new_video_list_app_index';

    public static $errorMsg = [
        self::ERROR_RECOMMEND_LIVE_LIST => '获取轮播推荐视频失败',
        self::ERROR_GUESS_YOU_LIKE_LIST => '获取猜你喜欢列表失败',
        self::ERROR_RANK_ANCHOR_LIST => '获取主播人气排行榜列表失败',
        self::ERROR_RANK_MONEY_LIST => '获取财富排行榜列表失败',
        self::ERROR_RECOMMEND_GAME_LIST => '获取游戏分类列表失败',
        self::ERROR_INFORMATION_LIST => '获取新闻中心列表失败',
        self::ERROR_RECOMMEND_GAME_LIVE_LIST => '获取推荐游戏视频列表失败',
    ];
    //推荐游戏ID
    public static $gameIds = [
        self::WZRY => 190,
        self::QQDZZ => 150,
        self::CYHX => 215
    ];
    //所有直播推荐类型
    public static $liveTypeAll = [
        LiveListDataService::LIVE_TYPE_HOT,
        LiveListDataService::LIVE_TYPE_NEW,
        LiveListDataService::LIVE_TYPE_FOLLOW
    ];
    private $_uid;
    private $_size;
    private $_client;
    private $_informationType;

    public function setUid($uid)
    {
        $this->_uid = $uid;
        return $this;
    }

    public function setSize($size)
    {
        $this->_size = $size;
        return $this;
    }

    public function getSize()
    {
        return $this->_size ? $this->_size : self::INDEX_GUESS_PC_NUM;
    }

    public function setClient($client)
    {
        $this->_client = $client;
        return $this;
    }

    public function getClient()
    {
        return $this->_client ? $this->_client : BaseInfo::INFORMATION_CLIENT_WEB;
    }

    public function setInformationType($informationType)
    {
        $this->_informationType = $informationType;
        return $this;
    }

    public function getInformationType()
    {
        return $this->_informationType ? $this->_informationType : BaseInfo::INFORMATION_TYPE_All;
    }

    /**
     * 获取首页轮播视频列表数据
     * @return array
     */
    public function recommendLiveListsData()
    {
        $baseInfoData = new BaseInfo();
        $res = $baseInfoData->RecommendLiveLists();

        //如果推荐表中主播开播数不够,从待推荐列表里组合
        $num = $res ? count($res) : 0;

        //待推荐主播id
        $luids = $baseInfoData->getWaitForRecommend();
        if ($num < BaseInfo::INDEX_CAROUSEL_LIVE_COUNT && $luids)
        {
            $liveService = new LiveService();
            $liveService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);

            $recommLuids = [];
            $count = BaseInfo::INDEX_CAROUSEL_LIVE_COUNT - $num;

            $i = 0;
            foreach ($luids as $luid)
            {
                $liveService->setLuid($luid['uid']);
                if ($liveService->isLiving() && !array_key_exists($luid['uid'], $res))
                {
                    $i++;
                    $recommLuids[] = $luid['uid'];
                }

                if ($i >= $count)
                {
                    break;
                }
            }

            //如果待推荐中直播都不够
            if ($i < $count)
            {
                foreach ($luids as $luid)
                {
                    $liveService->setLuid($luid['uid']);
                    if (!$liveService->isLiving() && !array_key_exists($luid['uid'], $res) && $baseInfoData->hasLive($luid['uid']))
                    {
                        $i++;
                        $recommLuids[] = $luid['uid'];
                    }

                    if ($i >= $count)
                    {
                        break;
                    }
                }
            }

            if ($recommLuids)
            {
                $recommLuids = implode(',', $recommLuids);
                $livelist = $baseInfoData->getAnchorLive($recommLuids);
                $res = array_merge($res, $livelist);
            }
        }

        if (!$res)
        {
            return false;
        }

        $recommendList = [];
        $conf = $GLOBALS['env-def'][$GLOBALS['env']];
        foreach ($res as $v)
        {
            $arr = [];

            $arr['uid'] = $v['uid'];
            $roomManager = new RoomManagerService();
            $roomManager->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            $roomManager->setUid($v['uid']);
            $roomid = $roomManager->getRoomidByUid();
            $arr['roomID'] = $roomid ? $roomid : 0;
            $arr['liveID'] = $v['liveid'];
            $arr['stream'] = $v['stream'];
            $arr['server'] = $v['server'];
            if ($v['status'] == 100)
            {
                $arr['isLiving'] = 1;
            } else
            {
                $arr['isLiving'] = 0;
            }

            $arr['poster'] = !empty($v['poster']) ? $conf['domain-lposter'] . '/' . $v['poster'] : '';


            $arr['orientation'] = $v['orientation'];

            //获取直播流地址
            $liveService = new LiveService();
            $liveService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            $liveService->setLuid($v['uid']);
            $streamList = $liveService->getLivePlayRtmpUrl();
            $arr['streamlist'] = isset($streamList['rtmpServer']) ? $streamList['rtmpServer'] : '';
            array_push($recommendList, $arr);
        }

        return $recommendList;
    }

    /**
     * 首页轮播flash
     * @return array
     */
    public function getStreamList()
    {
        $liveRedis = new LiveRedis();
        $res = $liveRedis->getRecommendLiveList();
        $recommendLiveLists = json_decode($res, TRUE);

        if (!$recommendLiveLists)
        {
            $recommendLiveLists = $this->recommendLiveListsData();
        }

        if (!$recommendLiveLists)
        {
            $code = self::ERROR_RECOMMEND_LIVE_LIST;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log);
            return [];
        } else
        {
            return $recommendLiveLists;
        }
    }

    /**
     * 获取首页猜你喜欢直播列表数据
     * @return array
     */
    public function _getGuessYouLikeData()
    {
        $baseInfoData = new BaseInfo();

        $conf = $GLOBALS['env-def'][$GLOBALS['env']];
        $rows = $baseInfoData->getGuessLiveLists($this->_uid, $this->getSize());

        if (empty($rows))
        {
            return false;
        }
        $arr = $guessList = [];

        foreach ($rows as $rv)
        {
            $arr['uid'] = $rv['uid'];
            $arr['livestream'] = $rv['stream'];
            $arr['title'] = $rv['title'];
            $arr['gameName'] = $rv['gamename'];
            $arr['orientation'] = $rv['orientation'];
            if ($rv['poster'])
            {
                $arr['poster'] = $conf['domain-lposter'] . "/" . $rv['poster'];
                $arr['ispic'] = '1';
            } else
            {
                $arr['poster'] = CROSS;
                $arr['ispic'] = '0';
            }


            $roomManager = new RoomManagerService();
            $roomManager->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            $roomManager->setUid($rv['uid']);
            $roomid = $roomManager->getRoomidByUid();
            $arr['roomID'] = $roomid ? $roomid : 0;

            $gameDataService = new GameService();
            $gameDataService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            $gameDataService->setGameId($rv['gametid']);
            $arr['gameType'] = $gameDataService->getGameTypeNameByGameId();

            $userDataService = new UserDataService();
            $userDataService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            $userDataService->setUid($rv['uid']);
            $author = $userDataService->getUserInfo();
            $arr['nick'] = $author['nick'];
            $arr['head'] = $author['pic'] ? $author['pic'] : DEFAULT_PIC;

            $liveRoomService = new LiveRoomService();
            $liveRoomService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            $liveRoomService->setLuid($rv['uid']);
            $arr['userCount'] = $liveRoomService->getLiveUserCountFictitious();

            $followService = new FollowService();
            $followService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            $arr['fansCount'] = $followService->getFansCount($rv['uid']);

            array_push($guessList, $arr);
        }

        if ($guessList)
        {
            return ['list' => $guessList];
        } else
        {
            return false;
        }
    }

    /**
     * 猜你喜欢
     * @return array
     */
    public function getGuessYouLike()
    {
        $guessYouLike = $this->_getGuessYouLikeData();
        if (!$guessYouLike)
        {
            $code = self::ERROR_GUESS_YOU_LIKE_LIST;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            return [];
        } else
        {
            return $guessYouLike;
        }
    }

    /**
     * 排行榜
     * @return array
     */
    public function getHomeRanking()
    {
        $data = [];
        $rankDataService = new RankService();
        $rankDataService->setGetAll(true);
        $rankDataService->setSize(self::INDEX_RANK_PC_NUM);
        //获取主播收人气排行数据
        $rankDataService->setUserType(Rank::USER_TYPE_A);
        $rankDataService->setOrderType(Rank::ORDER_TYPE_2);
        $data['anchorList'] = $rankDataService->formatPcRankList($rankDataService->getRankFromDataService());
        if (!$data['anchorList'])
        {
            $code = self::ERROR_RANK_ANCHOR_LIST;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
        }

        //财富排行榜
        $rankDataService->setUserType(Rank::USER_TYPE_U);
        $rankDataService->setOrderType(Rank::ORDER_TYPE_1);
        //获取观众贡献榜数据
        $data['moneyList'] = $rankDataService->formatPcRankList($rankDataService->getRankFromDataService());
        if (!$data['moneyList'])
        {
            $code = self::ERROR_RANK_MONEY_LIST;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
        }

        return $data;
    }

    /**
     * 游戏分类
     * @return array
     */
    public function getGameInfoList()
    {
        $gameDataService = new GameService();

        $recommendGameList = $gameDataService->getRecommendGameList();
        if (!$recommendGameList)
        {
            $code = self::ERROR_RECOMMEND_GAME_LIST;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            return [];
        } else
        {
            return $recommendGameList;
        }
    }

    /**
     * 获取首页资讯列表
     *
     * @param int    $type   1轮播 2列表
     * @param int    $client 1app  2web
     * @param object $db
     *
     * @return array|bool
     */
    public function getRecommendInformationListData($type, $client)
    {
        if (!in_array($type, array(BaseInfo::INFORMATION_TYPE_All, BaseInfo::INFORMATION_TYPE_PICTUR, BaseInfo::INFORMATION_TYPE_LIST)) || !in_array($client, array(BaseInfo::INFORMATION_CLIENT_APP, BaseInfo::INFORMATION_CLIENT_WEB)))
        {
            return false;
        }
        $baseInfoData = new BaseInfo();

        $conf = $GLOBALS['env-def'][$GLOBALS['env']];

        $Itype = $baseInfoData->getInformationType();
        $recommendType = [];
        switch ($type)
        {
            case BaseInfo::INFORMATION_TYPE_PICTUR:
                $recommendType = [IndexForAppService::RECOMMEND_TYPE_01];
                break;
            case BaseInfo::INFORMATION_TYPE_LIST:
                $recommendType = [IndexForAppService::RECOMMEND_TYPE_02];
                break;
            case BaseInfo::INFORMATION_TYPE_All:
            default :
                $recommendType = [IndexForAppService::RECOMMEND_TYPE_01, IndexForAppService::RECOMMEND_TYPE_02];
        }
        $adminInformationDao = new AdminInformation();
        $informationList = $adminInformationDao->getCarouselInfo($client, $recommendType);

        $plist = $tlist = [];
        foreach ($informationList as $v)
        {

            if ($v['isrecommend'] == IndexForAppService::RECOMMEND_TYPE_01)
            {
                $v['poster'] = !empty($v['poster']) ? DOMAIN_PROTOCOL . $conf['domain-img'] . '/' . $v['poster'] : '';
                $v['thumbnail'] = !empty($v['thumbnail']) ? DOMAIN_PROTOCOL . $conf['domain-img'] . '/' . $v['thumbnail'] : '';
                $plist[] = $v;
            } elseif ($v['isrecommend'] == IndexForAppService::RECOMMEND_TYPE_02)
            {
                $v['type'] = isset($Itype[$v['tid']]) ? $Itype[$v['tid']] : '';
                $v['poster'] = !empty($v['poster']) ? DOMAIN_PROTOCOL . $conf['domain-img'] . '/' . $v['poster'] : '';
                $v['thumbnail'] = !empty($v['thumbnail']) ? DOMAIN_PROTOCOL . $conf['domain-img'] . '/' . $v['thumbnail'] : '';
                $tlist[] = $v;
            }
        }
        return ['plist' => $plist, 'tlist' => $tlist];
    }

    /**
     * 新闻中心
     * @return array
     */
    public function getInformationList()
    {
        $liveRedis = new LiveRedis();
        $res = $liveRedis->getInformationList($this->getInformationType(), $this->getClient());
        $informationList = json_decode($res, TRUE);

        if (!$informationList)
        {
            $informationList = $this->getRecommendInformationListData($this->getInformationType(), $this->getClient());
        }

        if (!$informationList)
        {
            $code = self::ERROR_INFORMATION_LIST;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            return [];
        } 
        return $informationList;
    }

    /**
     * 推荐游戏直播
     * @return array
     */
    public function getHomePageGameList()
    {
        $data = [];
        $liveTypeKey = [
            LiveListDataService::LIVE_TYPE_HOT => 'hot',
            LiveListDataService::LIVE_TYPE_NEW => 'new',
            LiveListDataService::LIVE_TYPE_FOLLOW => 'maxfollow'
        ];

        $gameRedis = new GameRedis();
        $liveRedis = new LiveRedis();

        $gameLiveCount = $gameRedis->getGameLiveCount();
        $liveService = new LiveService();
        $liveService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);

        foreach (self::$gameIds as $game => $gameId)
        {
            foreach (self::$liveTypeAll as $liveType)
            {
                $luids = $gameRedis->getGameLiveList($liveType, $gameId, self::DEFAULT_PAGE, self::INDEX_GAME_LIVE_PC_NUM);

                $liveInfos = [];
                if ($luids && ($luids = array_filter($luids)))
                {
                    $liveService->setLuid($luids);
                    $liveInfos = $liveService->getLiveInfosByLuids();
                }

                $data[$game][$liveTypeKey[$liveType]]['list'] = $liveInfos;
                $data[$game][$liveTypeKey[$liveType]]['total'] = isset($gameLiveCount[$gameId]) ? $gameLiveCount[$gameId] : 0;
                $res = $gameRedis->getGameListDataByGameId([$gameId]);
                if ($res && ($res = array_filter($res)))
                {
                    $gameInfo = json_decode($res[$gameId], true);
                    $data[$game][$liveTypeKey[$liveType]]['ref'] = $gameInfo['name'];
                }
                if (!$data[$game][$liveTypeKey[$liveType]])
                {
                    $code = self::ERROR_RECOMMEND_GAME_LIVE_LIST;
                    $msg = self::$errorMsg[$code];
                    $log = "error_code:{$code};msg:{$msg};game:{$game};live_type:{$liveType}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
                    write_log($log);
                }
            }
        }

        if (!$data)
        {
            $liveListDataService = new LiveListDataService();
            $liveListDataService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            $liveListDataService->setPage(self::DEFAULT_PAGE);
            $liveListDataService->setSize(self::INDEX_GAME_LIVE_PC_NUM);

            foreach (self::$gameIds as $game => $gameId)
            {
                foreach (self::$liveTypeAll as $liveType)
                {
                    $liveListDataService->setLiveType($liveType);
                    $liveListDataService->setGameId($gameId);
                    $data[$game][$liveTypeKey[$liveType]] = $liveListDataService->getLiveListByLiveTypeAndGameId();

                    if (!$data[$game][$liveTypeKey[$liveType]])
                    {
                        $code = self::ERROR_RECOMMEND_GAME_LIVE_LIST;
                        $msg = self::$errorMsg[$code];
                        $log = "error_code:{$code};msg:{$msg};game:{$game};live_type:{$liveType}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
                        write_log($log);
                    }
                }
            }
        }
        return $data;
    }

    /**
     * 获取首页所有数据
     * @return array
     */
    public function getAll()
    {
        $data = [];
        $data['getStreamList'] = $this->getStreamList();
        $data['guessYouLike'] = $this->getGuessYouLike();
        $data['homeRanking'] = $this->getHomeRanking();
        $data['gameInfoList'] = $this->getGameInfoList();
        $data['getInformation'] = $this->getInformationList();
        $data['homePageGameList'] = $this->getHomePageGameList();

        return $data;
    }

    public function getRedis()
    {
        return new RedisHelp();
    }

}
