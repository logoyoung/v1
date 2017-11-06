<?php
namespace service\rank;
use service\common\AbstractService;
use lib\LiveRoom;
use service\user\UserDataService;
use lib\User;

/**
 * 直播间排行榜
 * @Date 2017-4-22 9:59
 * @version 1.01
 */

class LiveRoomRankService extends AbstractService
{
    private $_timeType;
    private $_luid;
    private $_size;
    private $_liveRoomDao;
    //日榜单
    const TIME_TYPE_DAY   = 1;
    //周榜单
    const TIME_TYPE_WEEK  = 2;
    //总榜单
    const TIME_TYPE_TOTAL = 3;

    //从redis里没有获取到日排行
    const ERROR_RANK_DAY   = -15201;
    //从redis里没有获取到取周排行
    const ERROR_RANK_WEEK  = -15202;
    //从redis里没有获取到取总排行
    const ERROR_RANK_ALL = -15203;

    public static $errorMsg = [
        self::ERROR_RANK_DAY   => 'redis里没有获取到日排行',
        self::ERROR_RANK_WEEK  => 'redis里没有获取到周排行',
        self::ERROR_RANK_ALL   => 'redis里没有获取到总排行',
    ];

    public function setLuid($luid)
    {
        $this->_luid        = $luid;
        $this->_liveRoomDao = null;
        return $this;
    }

    public function getLuid()
    {
        return $this->_luid;
    }

    public function setTimeType($timeType)
    {
        $this->_timeType = $timeType;
        return $this;
    }

    public function getTimeType()
    {
        return $this->_timeType ?:self::TIME_TYPE_TOTAL;
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

    public function getLiveRoomDao()
    {
        if(!$this->_liveRoomDao)
        {
            $this->_liveRoomDao = new LiveRoom($this->getLuid());
        }

        return $this->_liveRoomDao;
    }

    /**
     * 获取排行
     * @return array
     */
    public function getRankList()
    {
        $liveRoomDao = $this->getLiveRoomDao();
        $rankList    = [];
        $code        = 0;
        switch ($this->getTimeType())
        {
            //日
            case self::TIME_TYPE_DAY:
                $rankList = $liveRoomDao->getRoomDayRanking($this->getSize());
                if(!$rankList)
                {
                    $code = self::ERROR_RANK_DAY;
                }
                break;

            //周
            case self::TIME_TYPE_WEEK:
                $rankList = $liveRoomDao->getRoomWeekRanking($this->getSize());
                if(!$rankList)
                {
                    $code = self::ERROR_RANK_WEEK;
                }
                break;

            //总
            case self::ERROR_RANK_ALL:
            default:
                $rankList = $liveRoomDao->getRoomAllRanking($this->getSize());
                if(!$rankList)
                {
                    $code = self::ERROR_RANK_ALL;
                }

                break;
        }
        $result  = [];
        if($code != 0)
        {
            return $result;
            // $msg  = self::$errorMsg[$code];
            // $log  = "notice |error_code:{$code};msg:{$msg};主播uid:{$this->getLuid()}|class:".__CLASS__.';func:'.__FUNCTION__.';line:'.__LINE__.$this->getCaller();
            // write_log($log);
            // return $result;
        }

        $uids      = array_column($rankList, 'uid');
        if(!$uids)
        {
            $log  = "error |获直播间排行榜;返回用户uid为空，主播uid:{$this->getLuid()}|class:".__CLASS__.';func:'.__FUNCTION__.';line:'.__LINE__.$this->getCaller();
            write_log($log);
            return $result;
        }

        $userService = new UserDataService();
        $userService->setCaller('class:'.__CLASS__.';func:'.__FUNCTION__.';line:'.__LINE__);
        $userService->setUid($uids);
        $userService->setUserInfoDetail(UserDataService::USER_STATIC_ACTICE_BASE);
        $usersInfo = $userService->batchGetUserInfo();
        foreach ($rankList as $k => $v)
        {
            $result[$k]['uid']   = $v['uid'];
            $result[$k]['head']  = $usersInfo[$v['uid']]['pic'];
            $result[$k]['nick']  = $usersInfo[$v['uid']]['nick'];
            $result[$k]['money'] = $v['cost'];
            $result[$k]['level'] = $usersInfo[$v['uid']]['level'];
        }

        return $result;

    }

}
