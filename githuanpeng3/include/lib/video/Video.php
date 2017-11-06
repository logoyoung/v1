<?php

namespace lib\video;

use Exception;
use system\DbHelper;

class Video
{

    const DB_CONF = 'huanpeng';

    public static $fields = [
        'videoid', //int(10) unsigned NOT NULL AUTO_INCREMENT,
        'uid', //int(10) unsigned NOT NULL DEFAULT '0',
        'gametid', //int(10) unsigned NOT NULL DEFAULT '0',
        'gameid', //int(10) unsigned DEFAULT '0',
        'gamename', //varchar(100) DEFAULT '',
        'title', //varchar(100) DEFAULT '',
        'ctime', //timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        'length', //int(10) unsigned NOT NULL,
        'poster', //varchar(100) NOT NULL DEFAULT '',
        'ip', //int(10) unsigned NOT NULL DEFAULT '0',
        'port', //int(10) unsigned NOT NULL DEFAULT '0',
        'viewcount', //int(10) unsigned NOT NULL,
        'vfile', //varchar(100) NOT NULL DEFAULT '',
        'orientation', //tinyint(3) unsigned NOT NULL,
        'publish', //tinyint(3) unsigned NOT NULL DEFAULT '0',
        'status', //tinyint(3) unsigned NOT NULL DEFAULT '0',
        'upcount', //int(10) unsigned NOT NULL DEFAULT '0',
        'stop_reason', //tinyint(3) unsigned NOT NULL DEFAULT '0',
        'liveid', //int(10) unsigned NOT NULL DEFAULT '0',
        'is_save', //tinyint(3) unsigned NOT NULL DEFAULT '0',
        'utime', //timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
    ];
    public static $unUpdateFields = ['videoid', 'ctime'];
    public static $orderByFields = ['videoid desc','videoid asc','videoid', 'viewcount desc','viewcount asc','viewcount'];


    private $_master = false;

    public function getVideoListForApp($date, $orderBy = 'videoid DESC', array $fields = [])
    {
        if(!in_array(strtolower($orderBy), self::$orderByFields))
        {
            return false;
        }
        
        if ($fields)
        {
            $fields[] = 'videoid';
            $fields = array_unique($fields);
        } else
        {
            $fields = self::$fields;
        }

        $db = $this->getDb();
        $fields = $db->buildFieldsParam($fields);
        $pdParam = ['date' => "$date"];
        $sql = "SELECT {$fields} FROM `{$this->getTable()}` WHERE `status`=" . VIDEO . " and ctime>=:date group by uid order by $orderBy";

        try
        {

            return $db->query($sql,$pdParam,true);
        } catch (Exception $e)
        {
            return false;
        }
    }

    /** 获取主播录像
     * @param int $uid 主播id
     * @param int $status 发布状态
     * @param int $page 
     * @param int $size 
     * @return array
     */
    public function getAnchorVideoid($uid, $status, int $page = 1, int $size = 20)
    {
        if($status == 1)
        {
            $status = 0;
            $orderBy = "ORDER BY videoid DESC";
        } else
        {
            $orderBy = "ORDER BY viewcount DESC,videoid DESC";
        }
        $offset = ($page - 1) * $size;
        $pdParam = [ 'uid' => $uid , 'status' => $status, 'offset' => $offset, 'size' => $size];
        $sql = "SELECT videoid,uid,poster,vfile FROM `{$this->getTable()}` WHERE uid=:uid AND status = :status $orderBy  LIMIT :offset,:size";

        try
        {
            return $this->getDb()->query($sql, $pdParam);
        } catch (Exception $e)
        {
            return false;
        }
    }

    /**
     * 根据游戏id获取对应的录像列表
     * @param int $gameId 游戏id
     * @param int $page
     * @param int $size
     * @return array|boolean
     */
    public function getVideoListByGameId($gameId, int $page = 1, int $size = 20)
    {
        $offset = ($page - 1) * $size;
        $pdParam = ['gameid' => $gameId,'offset' => $offset, 'size' => $size];
        $sql = "SELECT uid,videoid,poster,vfile FROM `{$this->getTable()}` WHERE gameid=:gameid AND status=" . VIDEO . " ORDER BY viewcount DESC, videoid DESC LIMIT :offset,:size";

        try
        {
            return $this->getDb()->query($sql, $pdParam);
        } catch (Exception $e)
        {
            return false;
        }
    }

    /**
     * 根据videoid获取录像详情
     * @param string $videoId
     * @return array|boolean
     */
    public function getVideoInfoByVideoid($videoId)
    {
        if (empty($videoId))
        {
            return false;
        }
        $videoId = (array) $videoId;
        $inStr = $this->getDb()->buildInPrepare($videoId);

        $sql = "SELECT videoid,uid,poster,vfile FROM `{$this->getTable()}` WHERE videoid IN ($inStr)";

        try
        {
            return $this->getDb()->query($sql,$videoId);
            
        } catch (Exception $e)
        {
            return false;
        }
    }

    public function getTable()
    {
        return 'video';
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
