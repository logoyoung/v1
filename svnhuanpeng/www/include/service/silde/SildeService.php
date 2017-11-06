<?php

namespace service\silde;

use lib\video\Video;
use lib\video\VideoFollow;
use lib\live\Live;
use service\common\AbstractService;
use service\live\LiveService;
use lib\live\AdminRecommendLive;
use lib\user\UserFollow;
use service\game\GameService;
use lib\user\History;
use lib\game\AdminRecommendGame;
use service\home\IndexForAppService;

/**
 * 滑动列表服务
 * @author longgang <longgang@6.cn>
 * @date 2017-07-27 10:52:22
 * @version 1.0.0
 */
class SildeService extends AbstractService
{

    //视频列表获取异常
    const ERROR_VIDEO_LIST = -30001;
    //直播列表获取异常
    const ERROR_LIVE_LIST = -30002;
    //获取直播信息异常
    const ERROR_LUIDS = -30003;
    //获取直播信息异常
    const ERROR_LIVE_INFO = -30004;
    //获取游戏推荐配置异常
    const ERROR_GAME_RECOMMEND_OPT = -30005;
    
    //滑动列表size
    const SILDE_SIZE = 50;
    const SILDE_INDEX_SIZE = 10;
    //默认页
    const DEFAULT_PAGE = 1;
    //直播每次预取出数据数量
    const LIVE_RESERVE_NUM = 200;
    //首页请求滑动直播列表
    const INDEX_LIVE_REQUEST_TYPE = 1;
    //关注列表请求滑动直播列表
    const FOLLOW_LIVE_REQUEST_TYPE = 2;
    //游戏分类下请求滑动直播列表
    const GAME_LIVE_REQUEST_TYPE = 3;
    //历史列表请求滑动直播列表
    const HISTORY_LIVE_REQUEST_TYPE = 4;
    //游戏分类下请求滑动视频列表
    const GAME_VIDEO_REQUEST_TYPE = 1;
    //主播主页视频请求滑动视频列表
    const ANCHOR_INDEX_VIDEO_REQUEST_TYPE = 2;
    //陪玩详情页请求滑动视频列表
    const CERT_VIDEO_REQUEST_TYPE = 3;
    //我的空间请求滑动视频列表
    const SPACE_VIDEO_REQUEST_TYPE = 4;
    //我的收藏请求视频滑动列表
    const COLLECTION_VIDEO_REQUEST_TYPE = 5;
    
    public static $errorMsg = [
        self::ERROR_VIDEO_LIST => '视频列表获取异常',
        self::ERROR_LIVE_LIST => '直播列表获取异常',
        self::ERROR_LUIDS => '获取直播信息异常',
        self::ERROR_LIVE_INFO => '获取直播信息异常',
        self::ERROR_GAME_RECOMMEND_OPT => '获取游戏推荐配置异常',
    ];
    private $_uid;
    private $_luid;
    private $_gameId;
    private $_params;
    private $_size = self::SILDE_SIZE;

    public function setUid($uid)
    {
        $this->_uid = $uid;
        return $this;
    }

    public function getUid()
    {
        return $this->_uid;
    }

    public function setLuid($luid)
    {
        $this->_luid = $luid;
        return $this;
    }

    public function getLuid()
    {
        return $this->_luid;
    }

    public function setGameId($gameId)
    {
        $this->_gameId = $gameId;
        return $this;
    }

    public function getGameId()
    {
        return $this->_gameId;
    }

    public function setParams($params)
    {
        $this->_params = $params;
        return $this;
    }

    public function getParams()
    {
        return $this->_params;
    }

    public function setSize($size)
    {
        $this->_size = $size;
        return $this;
    }

    public function getSize()
    {
        return $this->_size;
    }

    public function getSildeVideoList()
    {
        $res = [];
        switch ($this->_params['type'])
        {
            case self::GAME_VIDEO_REQUEST_TYPE:
                $res = $this->getVideoListByGameId($this->_params['gameId']);
                break;
            case self::ANCHOR_INDEX_VIDEO_REQUEST_TYPE:
            case self::CERT_VIDEO_REQUEST_TYPE:
            case self::SPACE_VIDEO_REQUEST_TYPE:
                $res = $this->getVideoListByLuid($this->_params['uid'], $this->_params['status']);
                break;
            case self::COLLECTION_VIDEO_REQUEST_TYPE:
                $res = $this->getFollowVideoList($this->_params['uid']);
                break;
            default :
        }

        return $this->formattedVideoData($res);
    }

    public function getVideoListByLuid($uid, $status)
    {
        return $this->getVideoDao()->getAnchorVideoid($uid, $status);
    }

    public function getVideoListByGameId($gameId)
    {
        return $this->getVideoDao()->getVideoListByGameId($gameId);
    }

    public function getFollowVideoList($uid)
    {
        $videoFollow = new VideoFollow();
        $videoIds = $videoFollow->getFollowVideoList($uid);
        if ($videoIds)
        {
            $videoIds = implode(',', $videoIds);
            return $this->getVideoDao()->getVideoInfoByVideoid($videoIds);
        }
        return [];
    }

    public function formattedVideoData($data)
    {
        $fromVideo = $this->getVideoDao()->getVideoInfoByVideoid($this->_params['videoId']);
        if (!$fromVideo)
        {
            $code = self::ERROR_VIDEO_LIST;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log);
            return [];
        }

        $handle = false;
        foreach ($data as $v)
        {
            if ($fromVideo[0]['uid'] == $v['uid'])
            {
                $handle = true;
                break;
            }
        }

        if (!$handle)
        {
            //当前视频插入列表
            array_unshift($data, $fromVideo);
        }

        foreach ($data as $k => $v)
        {
            $data[$k]['videoID'] = $v['videoid'];
            $data[$k]['poster'] = !empty($v['poster']) ? sposter($v['poster']) : CROSS;
            $data[$k]['videoUrl'] = !empty($v['vfile']) ? sfile($v['vfile']) : '';
            unset($data[$k]['vfile'], $data[$k]['videoid']);
        }
        return $data;
    }

    public function getVideoDao()
    {
        return new Video();
    }

    public function getLiveDao()
    {
        return new Live();
    }

    public function getSildeLiveList()
    {

        $res = [];
        switch ($this->_params['type'])
        {
            case self::INDEX_LIVE_REQUEST_TYPE:
                $res = $this->getIndexLiveList();
                break;
            case self::FOLLOW_LIVE_REQUEST_TYPE:
                $res = $this->getFollowLiveList($this->_params['uid']);
                break;
            case self::GAME_LIVE_REQUEST_TYPE:
                $res = $this->getLiveListByGameId($this->_params['gameId']);
                break;
            case self::HISTORY_LIVE_REQUEST_TYPE:
                $res = $this->getHistoryLiveList($this->_params['uid']);
                break;
            default :
        }

        return $this->formattedLiveData($res);
    }

    public function getHotRecommendLiveList()
    {
        $adminRecommendLive = new AdminRecommendLive();


        $liveService = new LiveService();
        $liveService->setCaller('class:' . __CLASS__ . 'func:' . __FUNCTION__ . ';line:' . __LINE__);

        $total = $adminRecommendLive->getRecommendLiveLuserCount();

        $totalPage = ceil($total / self::LIVE_RESERVE_NUM);

        $showNum = $this->getSize();
        $page = self::DEFAULT_PAGE;
        $liveInfos = [];

        while ($showNum)
        {
            if ($page > $totalPage)
            {
                break;
            }

            //推荐主播
            $luids = $adminRecommendLive->getRecommendLiveLuser($page, self::LIVE_RESERVE_NUM);
            $page++;

            if (!$luids)
            {
                $code = self::ERROR_LUIDS;
                $msg = self::$errorMsg[$code];
                $log = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
                write_log($log);
                return [];
            }

            $liveService->setLuid($luids);
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
                    $showNum--;
                }
                if ($showNum <= 0)
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

        return $liveInfos;
    }

    public function getFollowLiveList()
    {
        $userFollow = new UserFollow();

        $liveService = new LiveService();
        $liveService->setCaller('class:' . __CLASS__ . 'func:' . __FUNCTION__ . ';line:' . __LINE__);

        $total = $userFollow->getUserFollowCountByUid($this->_params['uid']);

        $totalPage = ceil($total / self::LIVE_RESERVE_NUM);

        $showNum = $this->getSize();
        $page = self::DEFAULT_PAGE;
        $liveInfos = [];

        while ($showNum)
        {
            if ($page > $totalPage)
            {
                break;
            }

            $luids = $userFollow->getUserFollowByUid($this->_params['uid'], $page, self::LIVE_RESERVE_NUM);

            $page++;

            if (!$luids)
            {
                $code = self::ERROR_LUIDS;
                $msg = self::$errorMsg[$code];
                $log = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
                write_log($log);
                return [];
            }

            $liveService->setLuid($luids);
            $res = $liveService->getLiveInfosByLuids();

            if (!$res)
            {
                continue;
            }

            foreach ($res as $v)
            {
                if ($v['status'] == LIVE)
                {
                    $liveInfos[] = $v;
                    $showNum--;
                }
                if ($showNum <= 0)
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

        return $liveInfos;
    }

    public function getLiveListByGameId()
    {
        $liveService = new LiveService();
        $liveService->setCaller('class:' . __CLASS__ . 'func:' . __FUNCTION__ . ';line:' . __LINE__);
        $liveService->setLiveType(GameService::LIVE_TYPE_HOT);
        $liveService->setPage(self::DEFAULT_PAGE);
        $liveService->setSize($this->getSize());
        $liveService->setGameId($this->_params['gameId']);
        $liveInfos = $liveService->getLiveListByLiveTypeAndGameId();

        if (!$liveInfos || $liveInfos['total'] <= 0)
        {
            $code = self::ERROR_LIVE_INFO;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log);
            return [];
        }

        return $liveInfos['list'];
    }

    public function getHistoryLiveList()
    {
        $history = new History();

        $liveService = new LiveService();
        $liveService->setCaller('class:' . __CLASS__ . 'func:' . __FUNCTION__ . ';line:' . __LINE__);

        $total = $history->getHistoryCountByUid($this->_params['uid']);

        $totalPage = ceil($total / self::LIVE_RESERVE_NUM);

        $showNum = $this->getSize();
        $page = self::DEFAULT_PAGE;
        $liveInfos = [];

        while ($showNum)
        {
            if ($page > $totalPage)
            {
                break;
            }

            $luids = $history->getHistoryByUid($this->_params['uid'], $page, self::LIVE_RESERVE_NUM);

            $page++;

            if (!$luids)
            {
                $code = self::ERROR_LUIDS;
                $msg = self::$errorMsg[$code];
                $log = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
                write_log($log);
                return [];
            }

            $liveService->setLuid($luids);
            $res = $liveService->getLiveInfosByLuids();

            if (!$res)
            {
                continue;
            }

            foreach ($res as $v)
            {
                if ($v['status'] == LIVE)
                {
                    $liveInfos[] = $v;
                    $showNum--;
                }
                if ($showNum <= 0)
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

        return $liveInfos;
    }

    public function getIndexLiveList()
    {
        $this->setSize(self::SILDE_INDEX_SIZE);

        $hotRecommendLiveList = $this->getHotRecommendLiveList();

        $adminRecommendGame = new AdminRecommendGame();
        $opt = $adminRecommendGame->getGameIdByRecommendType(AdminRecommendGame::APP_FLOOR_GAME);
        $gameids = !empty($opt['gameid']) ? explode(',', $opt['gameid']) : '';
        if (!$opt || !$gameids)
        {
            $code = self::ERROR_GAME_RECOMMEND_OPT;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log);
            return $hotRecommendLiveList;
        }

        $floorGameLiveList = [];
        foreach ($gameids as $gameid)
        {
            $this->_params['gameId'] = $gameid;
            $res = $this->getLiveListByGameId();
            $floorGameLiveList = array_merge($floorGameLiveList, $res);
        }

        return array_merge($hotRecommendLiveList, $floorGameLiveList);
    }

    public function formattedLiveData($liveInfos)
    {
        $liveService = new LiveService();
        $liveService->setCaller('class:' . __CLASS__ . 'func:' . __FUNCTION__ . ';line:' . __LINE__);
        $liveService->setLuid($this->_params['luid']);
        $res[0] = $liveService->getLastLive();

        if (!$res)
        {
            $code = self::ERROR_LIVE_INFO;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log);
            return [];
        }

        foreach ($liveInfos as $k => $v)
        {
            if ($res[0]['uid'] == $v['uid'])
            {
                unset($liveInfos[$k]);
                break;
            }
        }

        //当前直播插入列表
        array_push($liveInfos, $res[0]);
        
        //去重
        $liveInfos = $this->_array_unique_fb($liveInfos);
        
        $datas = [];
        foreach ($liveInfos as $v)
        {
            $data = [];
            $data['uid'] = $v['uid'];
            $data['liveID'] = $v['liveid'];
            $data['poster'] = !empty($v['poster']) ? stripos($v['poster'], 'http') === false ? LiveService::getPosterUrl($v['poster']) : $v['poster'] : CROSS;
            $datas[] = $data;
        }

        return $datas;
    }
    
    /**
     * 对二维数组去重,并保留第二维索引
     * @param array $arr
     * @return array
     */
    private function _array_unique_fb(array $arr)
    {
        if(!$arr)
        {
            return false;
        }
        $arr_after = [];
        
        foreach ($arr as $v)
        {
            $id = $v['uid'];
            $arr_after[$id] = isset($arr_after[$id]) ? ($arr_after[$id]['ctime'] > $v['ctime'] ? $arr_after[$id] : $v) : $v;
        }
        return $arr_after;
    }

}
