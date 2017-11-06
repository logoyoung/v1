<?php

namespace lib\statistics;

use Exception;
use system\DbHelper;

class ViewLength
{

    const DB_CONF = 'huanpeng';

    const REWARD_STATUS_01 = 1;
    const REWARD_STATUS_02 = 2;
    const REWARD_STATUS_03 = 3;
    const REWARD_STATUS_04 = 4;

    public static $fields = [
        'id', //int(11) unsigned NOT NULL AUTO_INCREMENT,
        'uid', //int(10) unsigned NOT NULL DEFAULT '0' COMMENT '观看用户id',
        'record_date' ,//date NOT NULL DEFAULT '0000-00-00' COMMENT '记录日期',
        'reward_status', //tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '奖励状态 1、未奖励 2、已奖励第一档次3、已奖励第二档次4、已奖励第三档次',
        'view_length', //smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '当天观看时长',
        'ctime', //datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
        'utime', //timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    ];
    public static $unUpdateFields = ['id','uid','ctime'];
    private $_master = false;

    /**
     * 插入观看时长
     * @param int $uid
     * @param int $viewLength
     * @return boolean
     */
    public function createViewLength(int $uid, int $viewLength,$date)
    {
        $ctime = date('Y-m-d H:i:s');
        $pdParam = ['uid' => $uid,'viewLength' => $viewLength,'ctime' => $ctime,'record_date' => $date];
        $sql = "INSERT INTO `{$this->getTable()}` (`uid`,`view_length`,`ctime`,`record_date`)  VALUES (:uid,:viewLength,:ctime,:record_date)";

        try
        {
            return $this->getDb()->execute($sql,$pdParam);
        } catch (Exception $exc)
        {
            return false;
        }
    }

    /**
     * 更新观看时长
     * @param int $uid
     * @param date $record_date
     * @param int $viewLength
     * @return boolean
     */
    public function updateViewLength(int $uid,$record_date, int $viewLength)
    {
        $sql = "UPDATE `{$this->getTable()}` SET `view_length`=:viewLength WHERE `uid`=:uid AND record_date=:record_date";
        $pdParam = ['uid' => $uid,'viewLength' => $viewLength,'record_date'=>$record_date];

        try
        {
            return $this->getDb()->execute($sql, $pdParam);
        } catch (Exception $exc)
        {
            return false;
        }
    }

    /**
     * 更新奖励状态
     * @param int $uid
     * @param int $reward_status
     * @return boolean
     */
    public function updateRewardStatus(int $uid,int $reward_status,$record_date)
    {
        $sql = "UPDATE `{$this->getTable()}` SET reward_status=:reward_status WHERE uid=:uid AND record_date=:record_date";
        $pdParam = ['uid' => $uid,'reward_status' => $reward_status,'record_date'=>$record_date];
               
        try
        {
            return $this->getDb()->execute($sql, $pdParam);
        } catch (Exception $exc)
        {
            return false;
        }
    }
    
    /**
     * 检测某天uid用户观看时长
     * @param int $uid
     * @param date $record_date
     * @return int|boolean
     */
    public function checkUserLiveViewLengthByUidDate(int $uid,$record_date)
    {
        $sql = "SELECT view_length FROM `{$this->getTable()}` WHERE uid=:uid AND record_date=:record_date";
        $pdParam =['uid' => $uid,'record_date'=>$record_date];
               
        try
        {
            $res = $this->getDb()->query($sql,$pdParam);
            
            return isset($res[0]['view_length']) ? (int)$res[0]['view_length'] : false;
        } catch (Exception $exc)
        {
            return false;
        }
    }
    
    /**
     * 获取用户某天观看时长信息
     * @param int $uid
     * @param date $record_date
     * @return array|boolean
     */
    public function getUserLiveViewLengthByUidDate(int $uid,$record_date)
    {
        $sql = "SELECT view_length,utime FROM `{$this->getTable()}` WHERE uid=:uid AND record_date=:record_date";
        $pdParam =['uid' => $uid,'record_date'=>$record_date];
               
        try
        {
            return $this->getDb()->query($sql,$pdParam);
        } catch (Exception $exc)
        {
            return false;
        }
    }

    /**
     * 获取某天观看用户(不包含已经全部奖励完的)总数
     * @param date $date
     * @return int|boolean
     */
    public function getUserLiveViewCountByDate($date)
    {
        $sql = "SELECT COUNT(*) as num FROM `{$this->getTable()}` WHERE record_date=:record_date AND reward_status<" . self::REWARD_STATUS_04;
        $pdParam =['record_date'=>$date];
               
        try
        {
            $res = $this->getDb()->query($sql,$pdParam);
            
            return isset($res[0]['num']) ? (int)$res[0]['num'] : false;
        } catch (Exception $exc)
        {
            return false;
        }
    }
    
    public function getUserLiveViewDataByDate($date,int $page = 1,int $size = 20)
    {
        $offset = ($page - 1) * $size;
        $sql = "SELECT uid,view_length,reward_status FROM `{$this->getTable()}` WHERE record_date=:record_date AND reward_status<" . self::REWARD_STATUS_04 . " LIMIT :offset,:size";
        $pdParam =['record_date' => $date,'offset' => $offset, 'size' => $size];
               
        try
        {
            return $this->getDb()->query($sql,$pdParam);
            
        } catch (Exception $exc)
        {
            return false;
        }
    }

    public function getTable()
    {
        return 'view_length';
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
