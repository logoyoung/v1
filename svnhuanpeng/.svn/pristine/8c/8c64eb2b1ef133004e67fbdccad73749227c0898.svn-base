<?php

namespace service\user;

use service\common\AbstractService;
use lib\User;
use service\anchor\AnchorDataService;
use service\room\LiveRoomService;
use lib\Anchor;
use service\live\LiveService;
use service\user\UserDataService;
use service\room\RoomManagerService;

/**
 * 用户服务
 */
class HistoryService extends AbstractService
{

    //从底层获取历史记录失败
    const ERROR_HISTORY_LIST = 7008;
    //从底层没有获取到主播LUID
    const ERROR_LUIDS = -7009;
    //从底层没有获取到主播信息失败
    const ERROR_ANCHOR_INFO = -7010;
    //从底层获取主播房间ID失败(批量获取)
    const ERROR_ROOM_IDS = -7011;
    //获取直播信息失败
    const ERROR_LIVE_INFO = -7012;

    public static $errorMsg = [
        self::ERROR_HISTORY_LIST => '从底层获取历史记录失败',
        self::ERROR_LUIDS => '从底层没有获取到主播LUID',
        self::ERROR_ANCHOR_INFO => '从底层没有获取到主播信息失败',
        self::ERROR_ROOM_IDS => '从底层获取主播房间ID失败(批量获取)',
        self::ERROR_LIVE_INFO => '获取直播信息失败',
    ];
    private $_uid;
    private $_userDao;
    private $_cache = false;
    private $_enc;
    private $_page;
    private $_size;

    public function setUid($uid)
    {
        $this->_uid = $uid;
        $this->_userDao = false;
        $this->_cache = false;
        $this->_enc = '';
        return $this;
    }

    public function getUid()
    {
        return is_array($this->_uid) ? array_unique($this->_uid) : $this->_uid;
    }

    public function setEnc($enc)
    {
        $this->_enc = $enc;
        return $this;
    }

    public function getEnc()
    {
        return $this->_enc;
    }

    public function setCache($cache = true)
    {
        $this->_cache = $cache;
        return $this;
    }

    public function getCache()
    {
        return $this->_cache;
    }

    public function setPage($page)
    {
        $this->_page = $page;
        return $this;
    }

    public function getPage()
    {
        return $this->_page;
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

    public function getUserDao()
    {
        if (!$this->_userDao)
        {
            $this->_userDao = new User($this->getUid());
        }

        return $this->_userDao;
    }

    /**
     * 获取历史观看记录
     * @author longgang <[<email address>]>
     * @return array
     */
    public function getHistoryList()
    {

        $res = $datas = [];
        $userDao = $this->getUserDao();
        $historyList = $userDao->getHistoryList($this->getPage(), $this->getSize());
        if (!$historyList)
        {
            $code = self::ERROR_HISTORY_LIST;
            $msg = self::$errorMsg[$code];
            $log = "error_code:{$code};msg:{$msg};uid:{$this->getUid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
            write_log($log);
            return [];
        }
        if (isset($historyList['list']) && !empty($historyList['list']))
        {
            $luids = array_column($historyList['list'], 'luid');
            if (!$luids)
            {
                $code = self::ERROR_LUIDS;
                $msg = self::$errorMsg[$code];
                $log = "error_code:{$code};msg:{$msg};uid:{$this->getUid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
                write_log($log);
                return [];
            }
            //uid 为数组
            $userDataService = new UserDataService();
            $userDataService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            $userDataService->setUid($luids);
            $userDataService->setUserInfoDetail(User::USER_INFO_BASE);
            $anchorInfos = $userDataService->batchGetUserInfo();
            if (!$anchorInfos)
            {
                $code = self::ERROR_ANCHOR_INFO;
                $msg = self::$errorMsg[$code];
                $log = "error_code:{$code};msg:{$msg};uid:{$this->getUid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
                write_log($log);
                return [];
            }

            $roomManagerService = new RoomManagerService();
            $roomManagerService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            $roomManagerService->setUid($luids);
            $roomIds = $roomManagerService->getRoomIdsByUids();
            if (!$roomIds)
            {
                $code = self::ERROR_ROOM_IDS;
                $msg = self::$errorMsg[$code];
                $log = "error_code:{$code};msg:{$msg};uid:{$this->getUid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
                write_log($log);
                return [];
            }

            $liveRoomService = new LiveRoomService();
            $liveRoomService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            $liveRoomService->setLuid($luids);
            $viewCount = $liveRoomService->batchGetLiveUserCountFictitious();

            $liveService = new LiveService();
            $liveService->setCaller('class:' . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__);
            foreach ($anchorInfos as $anchorInfo)
            {
                $data = [];
                $data['uid'] = isset($anchorInfo['uid']) ? $anchorInfo['uid'] : 0;
                $data['head'] = isset($anchorInfo['pic']) ? $anchorInfo['pic'] : '';
                $data['nick'] = isset($anchorInfo['nick']) ? $anchorInfo['nick'] : '';
                $data['roomID'] = isset($roomIds[$anchorInfo['uid']]) ? $roomIds[$anchorInfo['uid']] : 0;
                $liveRoomService = new LiveRoomService();
                $liveRoomService->setCaller('class:' . __CLASS__ . 'func:' . __FUNCTION__ . ';line:' . __LINE__);
                $liveRoomService->setLuid($anchorInfo['uid']);
                $liveService->setCaller('class:' . __CLASS__ . 'func:' . __FUNCTION__ . ';line:' . __LINE__);
                $liveService->setLuid($anchorInfo['uid']);
                $data['isLiving'] = $liveService->isLiving();
                $liveInfo = $liveService->getLastLive();
                if (!$liveInfo)
                {
                    $code = self::ERROR_LIVE_INFO;
                    $msg = self::$errorMsg[$code];
                    $log = "error_code:{$code};msg:{$msg};uid:{$this->getUid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . ';line:' . __LINE__ . $this->getCaller();
                    write_log($log);
                }
                $data['orientation'] = isset($liveInfo['orientation']) ? $liveInfo['orientation'] : '';
                $data['title'] = isset($liveInfo['title']) ? $liveInfo['title'] : '';
                $data['gameName'] = isset($liveInfo['gamename']) ? $liveInfo['gamename'] : '';
                $data['ctime'] = isset($liveInfo['ctime']) ? strtotime($liveInfo['ctime']) : 1483200000;

                $data['poster'] = isset($liveInfo['poster']) ? LiveService::getPosterUrl($liveInfo['poster']) : '';

                foreach ($historyList['list'] as $history)
                {
                    if ($anchorInfo['uid'] == $history['luid'])
                    {
                        $data['stime'] = isset($history['stime']) ? strtotime($history['stime']) : 1483200000;
                        $data['viewTime'] = isset($history['stime']) ? time() - (strtotime($history['stime'])) : 1483200000;
                    }
                }
                $data['viewCount'] = isset($viewCount[$anchorInfo['uid']]) ? $viewCount[$anchorInfo['uid']] : 0;
                $datas[] = $data;
            }

            $datas = dyadicArray($datas, 'stime');

            $res['list'] = $datas;
        } else
        {
            $res['list'] = $historyList['list'];
        }
        $res['total'] = isset($historyList['total'][0]['total']) ? $historyList['total'][0]['total'] : 0;

        return $res;
    }

}
