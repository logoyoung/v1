<?php

namespace service\user;

use service\common\AbstractService;
use lib\User;
use lib\LiveActivity;
use lib\Anchor;
use service\user\UserDataService;
use service\room\LiveRoomService;
use service\live\LiveService;
use lib\LiveRoom;

/**
 * 关注服务
 * @date 2017-4-23
 * @version 1.0.1
 */
class FollowService extends AbstractService
{

    //关注主播时底层服务异常
    const ERROR_CODE_FOLLOW = -12701;
    //取消关注主播时底层服务异常
    const ERROR_CODE_UN_FOLLOW = -12702;
    //从底层获取关注列表失败
    const ERROR_FOLLOW_LIST = 70008;
    //从底层没有获取到主播LUID
    const ERROR_LUIDS = 70009;
    //从底层没有获取到主播信息失败
    const ERROR_ANCHOR_INFO = 70010;
    //从底层获取主播房间ID失败(批量获取)
    const ERROR_ROOM_IDS = 70011;
    //获取主播粉丝数失败
    const ERROR_USER_COUNTS = 70012;
    //获取最新直播信息失败
    const ERROR_LAST_LIVE = 70013;
    //获取房间观众数量失败
    const ERROR_VIEW_COUNTS = 70014;
    //已超出要获取的数据数量
    const ERROR_GET_NUM = 70015;
    //筛选luids出错
    const ERROR_FILTER_LUIDS = 70016;

    private $_db;
    private $_uid;
    private $_luid;
    private $_followDao;
    private $_page;
    private $_size;
    private $_client;
    public static $errorMsg = [
        self::ERROR_CODE_FOLLOW => '关注主播时底层服务异常',
        self::ERROR_CODE_UN_FOLLOW => '取消关注主播时底层服务异常',
        self::ERROR_FOLLOW_LIST => '从底层获取关注列表失败',
        self::ERROR_LUIDS => '从底层没有获取到主播LUID',
        self::ERROR_ANCHOR_INFO => '从底层没有获取到主播信息失败',
        self::ERROR_ROOM_IDS => '从底层获取主播房间ID失败(批量获取)',
        self::ERROR_USER_COUNTS => '获取主播粉丝数失败',
        self::ERROR_LAST_LIVE => '获取最新直播信息失败',
        self::ERROR_VIEW_COUNTS => '获取房间观众数量失败',
        self::ERROR_GET_NUM => '已超出要获取的数据数量',
        self::ERROR_FILTER_LUIDS => '筛选luids出错',
    ];

    /**
     * 用户uid
     * @param [type] $uid [description]
     */
    public function setUid($uid)
    {
        $this->_uid = $uid;
        $this->_followDao = null;
        return $this;
    }

    public function getUid()
    {
        return $this->_uid;
    }

    /**
     * 需要注意的用户uid 数组支持批量
     * @param array $luids
     */
    public function setLuid($luid)
    {
        $this->_luid = $luid;
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
        return $this->_page ? $this->_page : 0;
    }

    public function setSize($size)
    {
        $this->_size = $size;
        return $this;
    }

    public function getSize()
    {
        return $this->_size ? $this->_size : 10;
    }

    public function setClient($client)
    {
        $this->_client = $client;
        return $this;
    }

    /**
     * 关注主播
     * @return boolean
     */
    public function followAnchor()
    {
        $followDao = $this->getFollowDao();
        $result = $followDao->followAnchor($this->getLuid());
        $logMsg = "uid:{$this->getUid()};luid:{$this->getLuid()};";
        if ($result === false)
        {
            $code = self::ERROR_CODE_FOLLOW;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};{$logMsg}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log);
            return false;
        }

        $liveRoom = new LiveRoom($this->getLuid());
        //关注主播下发消息
        if (!$liveRoom->followMsg($this->getUid()))
        {
            write_log("warning|关注主播下发消息异常;{$logMsg}|line:" . __LINE__ . $this->getCaller());
        }

        write_log("notice |关注成功;{$logMsg}line:" . __LINE__ . $this->getCaller());
        return true;
    }

    /**
     * 取消关注主播
     * @return boolean
     */
    public function unFollowAnchor()
    {
        $followDao = $this->getFollowDao();
        $result = $followDao->removeFollowedAnchor($this->getLuid());
        if ($result === false)
        {
            $code = self::ERROR_CODE_UN_FOLLOW;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};uid:{$this->getUid()};luid:{$this->getLuid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log);

            return false;
        }

        write_log("notice |取消关注成功 uid:{$this->getUid()};luid:{$this->getLuid()};line:" . __LINE__ . $this->getCaller());
        return true;
    }

    public function getFollowDao()
    {
        if (!$this->_followDao)
        {

            $this->_followDao = new User($this->getUid());
        }

        return $this->_followDao;
    }

    /**
     * 从底层获取关注主播id
     * @return array
     * @author longgang
     */
    private function _getFollowList()
    {
        $followDao = $this->getFollowDao();
        $followList = $followDao->getFollowList();

        if (!$followList)
        {
            $code = self::ERROR_FOLLOW_LIST;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg};uid:{$this->getUid()};luid:{$this->getLuid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            return [];
        }

        return $followList;
    }

    /**
     * 批量获取主播直播信息
     * @param array $followList
     * @author longgang
     */
    private function _getLastLives($followList)
    {
        $lastLives = [];
        //如果关注主播数数据大于200分批获取
        if (count($followList) > 200)
        {
            $num = count($followList);
            $len = ceil($num / 200);
            for ($i = 0; $i < $len; $i++)
            {
                $temp = array_slice($followList, $i * 200, 200);
                $tempLastLives = LiveActivity::getFollowLivesByUids($temp, LiveActivity::getDB());
                $lastLives = array_merge($lastLives, $tempLastLives);
            }
        } else
        {
            $lastLives = LiveActivity::getFollowLivesByUids($followList, LiveActivity::getDB());
        }

        if (!$lastLives)
        {
            $code = self::ERROR_LAST_LIVE;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg};uid:{$this->getUid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            return [];
        }

        return $lastLives;
    }

    /**
     * 筛选出符合条件的luids
     * @param int $start
     * @param array $followList
     * @param array $lastLives
     * @author longgang
     */
    private function _getLuids($start, $followList, $lastLives)
    {
        $luids = [];
        if (!$lastLives)
        {
            //如果没有直播中的主播,直接获取
            $luids = array_slice($followList, $start, $this->getSize());
        } else
        {
            $totalNum = count($lastLives);

            if ($start > $totalNum)
            {
                //如果获取信息的起始位置超过有直播的总数
                $followListRemaining = $this->_array_delete($followList, $lastLives, 'uid');
                $luids = array_slice($followListRemaining, $start - $totalNum, $this->getSize());
            } else
            {
                $lastLives = dyadicArray($lastLives, 'stime');
                $getLastLives = array_slice($lastLives, $start, $this->getSize());

                $luids = array_column($getLastLives, 'uid');

                $size = $this->getSize();
                $num = count($getLastLives);

                //如果直播中信息不够,从剩余主播中截取
                if ($size > $num)
                {
                    $followListRemaining = $this->_array_delete($followList, $lastLives, 'uid');
                    $luids = array_merge($luids, array_slice($followListRemaining, 0, $size - $num));
                }
            }
        }
        if (!$luids)
        {
            $code = self::ERROR_FILTER_LUIDS;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg};uid:{$this->getUid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            return [];
        }
        return $luids;
    }

    /**
     * 批量获取主播信息
     * @param array $luids
     * @author longgang
     * @return array
     */
    private function _getAnchorInfos($luids)
    {
        $userDataService = new UserDataService();
        $userDataService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
        $userDataService->setUid($luids);
        $userDataService->setUserInfoDetail(User::USER_INFO_BASE);
        $anchorInfos = $userDataService->batchGetUserInfo();
        if (!$anchorInfos)
        {
            $code = self::ERROR_ANCHOR_INFO;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg};uid:{$this->getUid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            return [];
        }

        return $anchorInfos;
    }

    /**
     * 批量获取主播房间ID
     * @param array $luids
     * @return array
     * @author longgang
     */
    private function _getRoomIds($luids)
    {
        $roomIds = Anchor::getRoomIDs($luids, Anchor::getDB());
        if (!$roomIds)
        {
            $code = self::ERROR_ROOM_IDS;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg};uid:{$this->getUid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            return [];
        }

        return $roomIds;
    }

    /**
     * 获取主播粉丝数
     * @return array
     */
    public function getFansCount($luid)
    {
        $anchor = new Anchor($luid);

        $fans = $anchor->getFollowNumber();
        if (!$fans)
        {
            $code = self::ERROR_USER_COUNTS;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg};uid:{$this->getUid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            return [];
        }

        return $fans;
    }

    /**
     * 批量获取主播粉丝数
     * @param array $luids
     * @return array
     */
    public function getUserCount($luids)
    {
        $anchor = new Anchor();
        $userCounts = $anchor->getMornAnchorFans($luids, Anchor::getDB());
        if (!$userCounts)
        {
            $code = self::ERROR_USER_COUNTS;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg};uid:{$this->getUid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            return [];
        }

        return $userCounts;
    }

    /**
     * 批量获取观众数量
     * @param array $luids
     * @author longgang
     * @return array
     */
    private function _getViewCount($luids)
    {
        $liveRoomService = new LiveRoomService();
        $liveRoomService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
        $liveRoomService->setLuid($luids);
        $viewCounts = $liveRoomService->batchGetLiveUserCountFictitious();
        if (!$viewCounts)
        {
            $code = self::ERROR_VIEW_COUNTS;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg};uid:{$this->getUid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            return [];
        }

        return $viewCounts;
    }

    /**
     * @author longgang
     * @param  [type] $anchorInfos [description]
     * @param  [type] $roomIds     [description]
     * @param  [type] $viewCounts  [description]
     * @param  [type] $userCounts  [description]
     * @param  [type] $lastLives   [description]
     * @return [type]              [description]
     */
    private function _makeData($anchorInfos, $roomIds, $viewCounts, $userCounts, $lastLives)
    {
        $datas = [];
        $followDao = $this->getFollowDao();
        $liveTotal = $followDao->getAnchorLiveListInFollow();
        $datas['liveTotal'] = $datas['liveCount'] = isset($liveTotal['total']) ? $liveTotal['total'] : 0;
        $datas['total'] = $followDao->getCountForFollow();

        $liveids = array_unique(array_column($lastLives, 'liveid'));
        $subPoster = LiveService::getSlaveDataByLiveId($liveids);

        foreach ($anchorInfos as $anchorInfo)
        {
            $data = [];
            $data['head'] = isset($anchorInfo['pic']) ? $anchorInfo['pic'] : '';
            $data['nick'] = isset($anchorInfo['nick']) ? $anchorInfo['nick'] : '';
            $data['uid'] = isset($anchorInfo['uid']) ? $anchorInfo['uid'] : '';
            $data['roomID'] = isset($roomIds[$anchorInfo['uid']]) ? $roomIds[$anchorInfo['uid']] : 0;
            $data['userCount'] = $data['viewCount'] = isset($viewCounts[$anchorInfo['uid']]) ? $viewCounts[$anchorInfo['uid']] : 0;
            //$data['userCount'] = isset($userCounts[$anchorInfo['uid']]) ? $userCounts[$anchorInfo['uid']] : 0;
            $data['isLiving'] = 0;
            $data['stime'] = 1483200000;
            $data['title'] = '';
            $data['gameName'] = '';
            $data['poster'] = '';
            $data['orientation'] = '';
            foreach ($lastLives as $lastLive)
            {
                if (isset($lastLive['uid']) && $anchorInfo['uid'] == $lastLive['uid'])
                {
                    $data['isLiving'] = 1;
                    $data['stime'] = isset($lastLive['ctime']) ? strtotime($lastLive['ctime']) : 1483200000;
                    $data['title'] = isset($lastLive['title']) ? $lastLive['title'] : '';
                    $data['gameName'] = isset($lastLive['gamename']) ? $lastLive['gamename'] : '';
                    $data['poster'] = isset($lastLive['poster']) ? LiveService::getPosterUrl($lastLive['poster']) : '';
                    
                    if (LiveService::slaveIsLiving($lastLive['uid']) == LiveService::PLAY_TYPE_02)
                    {
                        $data['subPoster'] = isset($subPoster[$lastLive['liveid']]['poster']) ? $subPoster[$lastLive['liveid']]['poster'] : '';
                    } else
                    {
                        $data['subPoster'] = '';
                    }
                    
                    $data['orientation'] = isset($lastLive['orientation']) ? $lastLive['orientation'] : '';
                }
            }
            $datas['list'][] = $data;
        }
        $datas['list'] = isset($datas['list']) && count($datas['list']) > 0 ? dyadicArray($datas['list'], 'stime') : [];
        return $datas;
    }

    /**
     * 获取关注列表
     * @author longgang
     * @return array
     */
    public function getFollowList()
    {
        $followList = $this->_getFollowList();

        if (!$followList)
        {
            return [];
        }

        $followNum = count($followList);
        $start = $this->getPage() * $this->getSize();
        //判断是否获取的数据在范围内
        if ($start > $followNum)
        {
            $code = self::ERROR_GET_NUM;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg};uid:{$this->getUid()};luid:{$this->getLuid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            return [];
        }

        $lastLives = $this->_getLastLives($followList);

        $luids = $this->_getLuids($start, $followList, $lastLives);
        if (!$luids)
        {
            return [];
        }

        $anchorInfos = $this->_getAnchorInfos($luids);
        if (!$anchorInfos)
        {
            return [];
        }

        $roomIds = $this->_getRoomIds($luids);
        if (!$roomIds)
        {
            return [];
        }

        $userCounts = $this->getUserCount($luids);
        if (!$userCounts)
        {
            return [];
        }

        $viewCounts = $this->_getViewCount($luids);
        if (!$viewCounts)
        {
            return [];
        }

        $datas = $this->_makeData($anchorInfos, $roomIds, $viewCounts, $userCounts, $lastLives);

        return $datas;
    }

    /**
     * @author longgang
     * @return [type] [description]
     */
    public function getAppFollowList()
    {
        $followList = $this->_getFollowList();

        if (!$followList)
        {
            return [];
        }

        $followNum = count($followList);
        $start = $this->getPage() * $this->getSize();
        //判断是否获取的数据在范围内
        if ($start > $followNum)
        {
            $code = self::ERROR_GET_NUM;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg};uid:{$this->getUid()};luid:{$this->getLuid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__;
            write_log($log);
            return [];
        }

        $lastLives = $this->_getLastLives($followList);
        if (!$lastLives)
        {
            return [];
        }

        $lastLives = dyadicArray($lastLives, 'stime');
        $getLastLives = array_slice($lastLives, $start, $this->getSize());

        $luids = array_column($getLastLives, 'uid');

        $anchorInfos = $this->_getAnchorInfos($luids);
        if (!$anchorInfos)
        {
            return [];
        }

        $roomIds = $this->_getRoomIds($luids);
        if (!$roomIds)
        {
            return [];
        }

        $userCounts = $this->getUserCount($luids);
        if (!$userCounts)
        {
            return [];
        }

        $viewCounts = $this->_getViewCount($luids);
        if (!$viewCounts)
        {
            return [];
        }

        $datas = $this->_makeData($anchorInfos, $roomIds, $viewCounts, $userCounts, $lastLives);

        return $datas;
    }

    /**
     * 去除数组中某字段相同的数据
     * @param array $arr1 被比较数组(一维)
     * @param array $arr2 和这个数组进行对比(二维)
     * @param string $col 要比较的字段($arr2中的某个字段)
     * @author longgang
     *
     */
    private function _array_delete($arr1, $arr2, $col)
    {
        foreach ($arr1 as $key => $value)
        {
            foreach ($arr2 as $v)
            {
                if ($v[$col] == $value)
                {
                    unset($arr1[$key]);
                }
            }
        }

        return array_values($arr1);
    }

}
