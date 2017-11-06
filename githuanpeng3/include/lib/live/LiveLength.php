<?php

namespace lib\live;

use Exception;
use system\DbHelper;

class LiveLength
{

    const DB_CONF = 'huanpeng';

    public static $fields = [
        'cid', //int(11) NOT NULL DEFAULT '0' COMMENT '公司ID',
        'uid', //int(10) unsigned NOT NULL DEFAULT '0' COMMENT '主播ID',
        'date', //date NOT NULL DEFAULT '0000-00-00' COMMENT '日期',
        'length', //int(10) unsigned NOT NULL DEFAULT '0' COMMENT '直播时长',
        'bean', //float(14,2) NOT NULL DEFAULT '0.00' COMMENT '金豆收入',
        'coin', //float(14,2) NOT NULL DEFAULT '0.00' COMMENT '金币收入',
        'ctime', //timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        'utime', //timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
    ];
    public static $unUpdateFields = ['ctime'];
    private $_master = false;

    /**
     * 获取时间段内所有直播过的主播
     * @param string $smonth
     * @param string $emonth
     * @param int $page
     * @param int $size
     * @return array|boolean
     */
    public function getAllUid($smonth,$emonth,int $page = 1 , int $size = 20)
    {
        $offset = ($page - 1) * $size;
        $pdParam = ['offset' => $offset, 'size' => $size ,'smonth' => "$smonth", 'emonth' => "$emonth"];

        $sql = "SELECT DISTINCT(uid) AS uid FROM `{$this->getTable()}` WHERE date >=:smonth AND date <:emonth LIMIT :offset,:size";
        try
        {
            $res = $this->getDb()->query($sql,$pdParam);

            $luids = [];
            if($res)
            {
                foreach ($res as $v)
                {
                    $luids[] = $v['uid'];
                }
            }
            return $luids;
        } catch (Exception $exc)
        {
            return false;
        }
    }

    public function getAllUidCount($smonth,$emonth)
    {
        $sql = "SELECT COUNT(DISTINCT(uid)) AS num FROM `{$this->getTable()}` WHERE date >=:smonth AND date <:emonth";
        $pdParam = ['smonth' => "$smonth",'emonth' => "$emonth"];
        try
        {
            $res = $this->getDb()->query($sql,$pdParam);
            return isset($res[0]['num']) ? (int) $res[0]['num'] : 0;
        } catch (Exception $exc)
        {
            return false;
        }
    }

    public function getAnchorLiveLengths($uid,$smonth,$emonth)
    {
        $sql = "SELECT `length` FROM `{$this->getTable()}` WHERE uid=:uid AND date>=:smonth AND date<:emonth";
        $pdParam = ['uid' => $uid , 'smonth' => "$smonth", 'emonth' => "$emonth'"];
        try
        {
            $res = $this->getDb()->query($sql,$pdParam);
            $length = [];
            if($res)
            {
                foreach ($res as $v)
                {
                    $length[] = $v['length'];
                }
            }
            return $length;
        } catch (Exception $exc)
        {
            return false;
        }
    }

    /**
     * 获取主播时间内直播总时长
     * @param int $uid
     * @param string $smonth
     * @param string $emonth
     * @param int $page
     * @param int $size
     * @return boolean|int
     */
    public function getAnchorLiveLength($uid, $smonth, $emonth)
    {
        $sql = "SELECT SUM(length) AS len FROM `{$this->getTable()}` WHERE uid=:uid AND date>=:smonth AND date<:emonth";
        $pdParam = ['uid' => $uid,'smonth' => "$smonth",'emonth' => "$emonth"];
        try
        {
            $res = $this->getDb()->query($sql,$pdParam);
            return isset($res[0]['len']) ? $res[0]['len'] : 0;
        } catch (Exception $exc)
        {
            return false;
        }
    }

    /**
     * 获取主播时间内直播有效天数
     * @param int $uid
     * @param string $smonth
     * @param string $emonth
     * @param int $mixLength
     * @return boolean|int
     */
    public function getLiveEfeeDays($uid, $smonth, $emonth, $mixLength)
    {
        $sql = "SELECT COUNT(date) AS num FROM `{$this->getTable()}` WHERE uid=:uid AND date>=:smonth AND date<:emonth AND length >=:mixLength";
        $pdParam = ['uid' => $uid,'smonth' => "$smonth",'emonth' => "$emonth" ,'mixLength' => $mixLength];
        try
        {
            $res = $this->getDb()->query($sql,$pdParam);
            return isset($res[0]['num']) ? $res[0]['num'] : 0;
        } catch (Exception $exc)
        {
            return false;
        }
    }

    /**
     * 获取某天所有主播直播时长
     * @param string $day
     */
    public function getDayUid( $day , int $page = 1 , int $size = 20)
    {
        $offset = ($page - 1) * $size;
        $pdParam = ['day' => "$day",'offset' => $offset, 'size' => $size];
        $sql = "SELECT DISTINCT(uid) AS uid FROM `{$this->getTable()}` WHERE date=:day LIMIT :offset,:size";
        try
        {
            $res = $this->getDb()->query($sql,$pdParam);
            $luids = [];
            if($res)
            {
                foreach($res as $v)
                {
                    $luids[] = $v['uid'];
                }
            }
            return $luids;
        } catch (Exception $exc)
        {
            return false;
        }
    }

    /**
     * 获取某天所有主播直播时长
     * @param string $day
     */
    public function getAnchorDayLiveLengthsCount($day)
    {
        $sql = "SELECT COUNT(DISTINCT(uid)) as num FROM `{$this->getTable()}` WHERE date=:day";
        $pdParam = ['day' => "$day"];
        try
        {
            $res = $this->getDb()->query($sql,$pdParam);
            return isset($res[0]['num']) ? $res[0]['num'] : 0;
        } catch (Exception $exc)
        {
            return false;
        }
    }

    /**
     * 获取某天某主播直播时长
     * @param int $uid
     * @param string $day
     * @return boolean|int
     */
    public function getAnchorDayLiveLength($uid, $day)
    {
        $sql = "SELECT `length` FROM `{$this->getTable()}` WHERE uid=:uid AND date=:day";
        $pdParam = ['uid' => $uid,'day' => "$day"];
        try
        {
            $res = $this->getDb()->query($sql,$pdParam);
            return isset($res[0]['length']) ? $res[0]['length'] : 0;
        } catch (Exception $exc)
        {
            return false;
        }

    }
    public function updateRewardStatus($uid,$date,$rewardStatus)
    {
        $table = $this->getTable();
        $sql   = "UPDATE `{$table}` SET  `reward_status` = :reward_status WHERE `uid` = :uid and `date` = :date LIMIT 1";
        //参数绑定
        $bdParam = [
            'uid'   => $uid,
            'date'       => $date,
            'reward_status'    =>  $rewardStatus,
        ];
        try {

            return $this->getDb()->execute($sql,$bdParam);

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 获取当日所有主播播放时长
     * @param $day
     * @return array|bool|false
     */
    public function getAllAnchorDayLiveLength($day)
    {
        $sql = "SELECT `uid`,`length`,`reward_status` FROM `{$this->getTable()}` WHERE date =:day";
        $pdParam = ['day' => "$day"];
        try
        {
            $res = $this->getDb()->query($sql,$pdParam);
            return $res;
        } catch (Exception $exc)
        {
            return false;
        }
    }

    public function getTable()
    {
        return 'live_length';
    }

    public function getDb()
    {
        return DbHelper::getInstance(self::DB_CONF);
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->getDb(), $method], $args);
    }

}
