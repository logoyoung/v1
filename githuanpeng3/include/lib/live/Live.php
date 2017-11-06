<?php

namespace lib\live;

use Exception;
use system\DbHelper;

class Live
{

    const DB_CONF = 'huanpeng';

    const CUTTING_NUMBER = 50;
    
    const LIVE_TYPE_01 = 0;//录屏直播
    const LIVE_TYPE_02 = 1;//摄像头直播
    const LIVE_TYPE_03 = 2;//pc直播
    const LIVE_TYPE_04 = 3;//双屏主
    const LIVE_TYPE_05 = 4;//双屏从
    
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
        'livetype', //tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '直播类型 0录屏直播 1摄像头直播 2pc直播 3双屏主 4双屏从',
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
    
    public function getLiveListByLiveType(int $liveType,int $page = 1,int $size = 20,array $fields = [])
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
        $offset = ($page - 1) * $size;
        $pdParam = ['liveType' => $liveType,'offset' => $offset, 'size' => $size];
        $sql = "SELECT {$fields} FROM `{$this->getTable()}` WHERE `status`=" . LIVE . " AND livetype=:liveType LIMIT :offset,:size";

        try
        {
            return $db->query($sql,$pdParam);
        } catch (Exception $e)
        {
            return false;
        }
    }

    public function getLiveListCountByLiveType(int $liveType)
    {
        $db = $this->getDb();
        $pdParam = ['liveType' => $liveType];
        $sql = "SELECT COUNT(*) as num FROM `{$this->getTable()}` WHERE `status`=" . LIVE . " AND livetype=:liveType";

        try
        {
            $res = $db->query($sql,$pdParam);
            return isset($res[0]['num']) ? (int) $res[0]['num'] : 0;
        } catch (Exception $e)
        {
            return false;
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
