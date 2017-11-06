<?php

namespace service\live;

use service\common\AbstractService;
use lib\live\LiveLength;
use lib\anchor\AnchorMostPopular;
use service\live\helper\LiveLengthRedis;

/**
 * 直播时长数据服务
 * @author longgang@6.cn
 * @date 2017-08-10 18:16:45
 * @copyright (c) 2017, 6.cn
 * @version 1.0.1
 */
class LiveLengthService extends AbstractService
{

    //每月直播任务时长  单位秒
    const PER_TASK_LIVE_TIME = 100 * 3600;
    //直播有效天数最小每天直播时间 单位秒
    const PER_MIN_EFFECTIVE_TIME = 3600;
    //从数据库获取主播月直播时长异常
    const ERROR_ANCHOR_MONTH_LIVE_LENGTH = -650001;
    //从数据库获取主播直播有效天数异常
    const ERROR_LIVE_EFFECTIVE_DAYS = -650002;
    //从数据库获取直播峰值异常
    const ERROR_LIVE_POPULARY_PEAK = -650003;
    //从数据库获取主播日直播时长异常
    const ERROR_ANCHOR_DAY_LIVE_LENGTH = -650004;
    //获取当日主播直播异常
    const ERROR_ALL_ANCHOR_DAY_LIVE_LENGTH = -650014;
    //更新当日主播奖励状态异常
    const ERROR_UPDATE_ANCHOR_REWARD_STATUS = -650024;
    //主播直播时长信息异常
    const ERROR_ANCHOR_LIVE_LENGTH_INFO = -650005;

    public static $errorMsg = [
        self::ERROR_ANCHOR_MONTH_LIVE_LENGTH => '从数据库获取主播直播时长异常',
        self::ERROR_LIVE_EFFECTIVE_DAYS => '从数据库获取主播直播有效天数异常',
        self::ERROR_LIVE_POPULARY_PEAK => '从数据库获取直播峰值异常',
        self::ERROR_ANCHOR_DAY_LIVE_LENGTH => '从数据库获取主播日直播时长异常',
        self::ERROR_ANCHOR_LIVE_LENGTH_INFO => '主播直播时长信息异常',
        self::ERROR_ALL_ANCHOR_DAY_LIVE_LENGTH =>'数据库获取当日所有主播直播时长异常',
        self::ERROR_UPDATE_ANCHOR_REWARD_STATUS =>'数据库更新当日主播奖励状态失败',
    ];
    private $_luid;
    private $_fromDb = false;

    public function setLuid($luid)
    {
        $this->_luid = $luid;
        return $this;
    }

    public function getLuid()
    {
        return $this->_luid;
    }

    public function setFromDb($fromDb)
    {
        $this->_fromDb = $fromDb;
        return $this;
    }

    public function getFromDb()
    {
        return $this->_fromDb;
    }

    /**
     * 获取主播时间内直播时长
     * @param string $smonth
     * @param string $emonth
     * @return boolean|int
     */
    public function getAnchorLiveLength($smonth, $emonth)
    {
        $liveLengthDao = $this->getLiveLengthDao();
        $liveLength = $liveLengthDao->getAnchorLiveLength($this->_luid, $smonth, $emonth);
        if (!$liveLength)
        {
            $code = self::ERROR_ANCHOR_DAY_LIVE_LENGTH;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};luid:{$this->getLuid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . $this->getCaller();
            write_log($log);
        }

        return $liveLength;
    }

    /**
     * 获取主播时间内直播有效天数
     * @param string $smonth
     * @param string $emonth
     * @return boolean|int
     */
    public function getLiveEfeeDays($smonth, $emonth)
    {
        $liveLengthDao = $this->getLiveLengthDao();
        $liveEfeeDays = $liveLengthDao->getLiveEfeeDays($this->_luid, $smonth, $emonth, self::PER_MIN_EFFECTIVE_TIME);
        if (!$liveEfeeDays)
        {
            $code = self::ERROR_LIVE_EFFECTIVE_DAYS;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};luid:{$this->getLuid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . $this->getCaller();
            write_log($log);
        }

        return $liveEfeeDays;
    }

    /**
     * 获取时间段内主播直播时人气峰值
     * @param string $smonth
     * @param string $emonth
     * @return boolean|int
     */
    public function getLivePopularyPeak($smonth, $emonth)
    {
        $anchorMostPopularDao = $this->getAnchorMostPopularDao();
        $livePopularyPeak = $anchorMostPopularDao->getLivePopularyPeak($this->_luid, $smonth, $emonth);
        if (!$livePopularyPeak)
        {
            $code = self::ERROR_LIVE_POPULARY_PEAK;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};luid:{$this->getLuid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . $this->getCaller();
            write_log($log);
        }

        return $livePopularyPeak;
    }

    /**
     * 获取某天主播直播时长
     * @param string $day
     * @return boolean|int
     */
    public function getAnchorDayLiveLength($day)
    {
        $liveLengthDao = $this->getLiveLengthDao();
        $dayLiveLength = $liveLengthDao->getAnchorDayLiveLength($this->_luid, $day);
        if (!$dayLiveLength)
        {
            $code = self::ERROR_ANCHOR_DAY_LIVE_LENGTH;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};luid:{$this->getLuid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . $this->getCaller();
            write_log($log);
        }

        return $dayLiveLength;
    }

    /**
     * 更新主播奖励状态
     * @param $day
     * @param $status 1 1档奖励  5 2档奖励 10 3档奖励目前最高档
     * @return bool|mixed
     */
    public function updateAnchorRewardStatus($day,$status)
    {
        $liveLengthDao = $this->getLiveLengthDao();
        $res = $liveLengthDao->updateRewardStatus($this->_luid, $day,$status);
        if($res === false)
        {
            $code = self::ERROR_UPDATE_ANCHOR_REWARD_STATUS;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};luid:{$this->getLuid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . $this->getCaller();
            write_log($log);
            return false;
        }
        return true;

    }

    /**
     * 获取当天所有主播直播时长
     * @param $day
     * @return array|bool|false
     */
    public function getAllAnchorDayLiveLength($day)
    {
        $liveLengthDao = $this->getLiveLengthDao();
        $dayLiveLength = $liveLengthDao->getAllAnchorDayLiveLength( $day);
        if (!$dayLiveLength)
        {
            $code = self::ERROR_ANCHOR_DAY_LIVE_LENGTH;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};luid:{$this->getLuid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . $this->getCaller();
            write_log($log);
        }

        return $dayLiveLength;
    }

    /**
     * 从数据库获取主播直播时长信息
     * @param string $smonth
     * @param string $emonth
     * @param string $day
     * @return array|boolean
     */
    public function getAnchorLiveLengthInfoFromDb($smonth, $emonth, $day = '')
    {
        $anchorLiveLength = [];

        $anchorLiveLength['monthLength'] = $this->getAnchorLiveLength($smonth, $emonth);
        $anchorLiveLength['noLiveLength'] = self::PER_TASK_LIVE_TIME - $anchorLiveLength['monthLength'] >= 0 ? self::PER_TASK_LIVE_TIME - $anchorLiveLength['monthLength'] : 0;
        if ($day)
        {
            $anchorLiveLength['dayLength'] = $this->getAnchorDayLiveLength($day);
        } else
        {
            $anchorLiveLength['dayLength'] = 0;
        }

        $anchorLiveLength['effectiveDays'] = $this->getLiveEfeeDays($smonth, $emonth);
        $anchorLiveLength['popuPeak'] = $this->getLivePopularyPeak($smonth, $emonth);

        if (!$anchorLiveLength)
        {
            $code = self::ERROR_ANCHOR_LIVE_LENGTH_INFO;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};luid:{$this->getLuid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . $this->getCaller();
            write_log($log);
            return FALSE;
        }

        return $anchorLiveLength;
    }

    /**
     * 获取主播直播时长信息
     * @param string $smonth
     * @param string $emonth
     * @param string $day
     * @return array|boolean
     */
    public function getAnchorLiveLengthInfo($smonth, $emonth, $day = '')
    {
        if ($this->getFromDb())
        {
            return $this->getAnchorLiveLengthInfoFromDb($smonth, $emonth, $day);
        }

        $liveLengthRedis = new LiveLengthRedis();
        $res = $liveLengthRedis->getAnchorLiveLength($smonth, $this->_luid);

        if (!$res || !($res = array_filter($res)))
        {
            return $this->getAnchorLiveLengthInfoFromDb($smonth, $emonth, $day);
        }

        $anchorLiveLength = [];

        foreach ($res as $v)
        {
            $anchorLiveLength = json_decode($v,true);
        }

        if (!$anchorLiveLength)
        {
            $code = self::ERROR_ANCHOR_LIVE_LENGTH_INFO;
            $msg = self::$errorMsg[$code];
            $log = "error |error_code:{$code};msg:{$msg};uid:{$this->getLuid()}|class:" . __CLASS__ . ';func:' . __FUNCTION__ . $this->getCaller();
            write_log($log);
            return FALSE;
        }

        return $anchorLiveLength;
    }

    public function getLiveLengthDao()
    {
        return new LiveLength();
    }

    public function getAnchorMostPopularDao()
    {
        return new AnchorMostPopular();
    }

}
