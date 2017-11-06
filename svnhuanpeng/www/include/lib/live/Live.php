<?php

namespace lib\live;

use Exception;
use system\DbHelper;

class Live
{

    const DB_CONF = 'huanpeng';

    const CUTTING_NUMBER = 200;
    
    public static $fields = [
        'liveid', //int(10) unsigned NOT NULL AUTO_INCREMENT,
        'stream', //varchar(100) NOT NULL,
        'status', //tinyint(3) unsigned NOT NULL DEFAULT '0',
        'uid', //int(10) unsigned NOT NULL DEFAULT '0',
        'gametid', //int(10) unsigned NOT NULL DEFAULT '0',
        'gameid', //int(10) unsigned DEFAULT '0',
        'gamename', //varchar(100) DEFAULT '',
        'title', //varchar(100) DEFAULT '',
        'ctime', //timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        'poster', //varchar(100) NOT NULL DEFAULT '',
        'upcount', //int(10) unsigned NOT NULL DEFAULT '0',
        'ip', //int(10) unsigned NOT NULL DEFAULT '0',
        'port', //int(10) unsigned NOT NULL DEFAULT '0',
        'deviceid', //varchar(100) NOT NULL DEFAULT '',
        'orientation', //tinyint(3) unsigned NOT NULL,
        'server', //varchar(100) NOT NULL,
        'etime', //timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        'antopublish', //tinyint(3) unsigned NOT NULL DEFAULT '0',
        'quality', //tinyint(3) unsigned NOT NULL DEFAULT '0',
        'stop_reason', //tinyint(3) unsigned NOT NULL DEFAULT '0',
        'livetype', //tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '直播类型 0录屏直播  1摄像头直播',
        'longitude', //varchar(30) NOT NULL DEFAULT '' COMMENT '经度',
        'latitude', //varchar(30) NOT NULL DEFAULT '' COMMENT '纬度',
        'stime', //timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        'utime', //timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        'titlestatus', //tinyint(3) unsigned NOT NULL DEFAULT '0',
    ];
    public static $unUpdateFields = ['liveid', 'ctime'];
    private $_master = false;

    public function getLiveList(array $fields = [])
    {
        if ($fields)
        {
            $fields[] = 'liveid';
            $fields = array_unique($fields);
        } else
        {
            $fields = self::$fields;
        }

        $db = $this->getDb();
        $fields = $db->buildFieldsParam($fields);
        $sql = "SELECT {$fields} FROM `{$this->getTable()}` WHERE `status`=" . LIVE;

        try
        {

            return $db->query($sql);
        } catch (Exception $e)
        {
            return false;
        }
    }

    //会扫描全表,数据量大会hang住,建议仅做校验使用
    public function getLastLiveList(array $fields = [])
    {
        if ($fields)
        {
            $fields[] = 'liveid';
            $fields = array_unique($fields);
        } else
        {
            $fields = self::$fields;
        }

        $db = $this->getDb();
        $fields = $db->buildFieldsParam($fields);

        try
        {
            $sql = "SELECT DISTINCT(uid) as luid FROM `{$this->getTable()}`";
            $res = $db->query($sql);
            if(!$res)
            {
                return false;
            }
            $luids = [];
            foreach ($res as $v)
            {
                $luids[] = $v['luid'];
            }
            $total = count($luids);
            $totalNum   = ceil($total / self::CUTTING_NUMBER);
            $liveList = [];
            $num = 0;
            while($totalNum)
            {
                $num++;
                $index = ($num - 1) * self::CUTTING_NUMBER;
                $cutLuids = array_slice($luids, $index, self::CUTTING_NUMBER);
                $cutLuids = implode(',', $cutLuids);
                $sql = "SELECT {$fields} FROM (SELECT {$fields} FROM `{$this->getTable()}` WHERE uid IN($cutLuids) ORDER BY liveid DESC) live GROUP BY uid ORDER BY LIVEID DESC ";

                $res = $db->query($sql);
                $liveList = array_merge($liveList, $res);
                sleep(1);
                $totalNum --;
            }
            return $liveList;
        } catch (Exception $e)
        {
            return false;
        }
    }
    
    public function getGameLiveCount()
    {
        $sql = "SELECT gameid,COUNT(*) AS count FROM `{$this->getTable()}` WHERE `status`=" . LIVE . " GROUP BY gameid";
        try
        {
            $res = $this->getDb()->query($sql);
            if($res)
            {
                $gameLiveCount = [];
                foreach ($res as $v)
                {
                    $gameLiveCount[$v['gameid']] = $v['count'];
                }
                return $gameLiveCount;
            } else
            {
                return false;
            }
        } catch (Exception $e)
        {
            return false;
        }
    }

    public function getLiveTotalNum()
    {
        $sql = "SELECT COUNT(*) AS total_num FROM `{$this->getTable()}` WHERE `status`=" . LIVE;
        try
        {
            $db = $this->getDb();
            $result = $db->query($sql);
            if (!$result)
            {
                return 0;
            }

            return (int) $result[0]['total_num'];
        } catch (Exception $e)
        {
            return 0;
        }
    }

    public function getTable()
    {
        return 'live';
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
