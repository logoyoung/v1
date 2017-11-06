<?php

namespace lib\video;

use Exception;
use system\DbHelper;

class VideoFollow
{

    const DB_CONF = 'huanpeng';

    public static $fields = [
        'uid', //int(10) unsigned NOT NULL DEFAULT '0',
        'videoid', //int(10) unsigned NOT NULL DEFAULT '0',
        'tm', //timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ];
    public static $unUpdateFields = ['uid', 'videoid'];
    private $_master = false;

    /**
     * 获取收藏的录像id
     * @param int $uid 主播id
     * @param int $page
     * @param int $size
     * @return array|boolean
     */

    public function getFollowVideoList(int $uid,int $page = 1,int $size = 20)
    {
        
        $offset = ($page - 1) * $size;
        $pdParam = ['uid' => $uid,'offset' => $offset, 'size' => $size];
        $db = $this->getDb();
        $sql = "SELECT videoid FROM `{$this->getTable()}` WHERE uid=:uid ORDER BY tm DESC LIMIT :offset,:size";

        try
        {
            $res = $db->query($sql,$pdParam);
            if($res)
            {
                $data = [];
                foreach($res as $v)
                {
                    $data[] = $v['videoid'];
                }
                return $data;
            }
            return $res;
        } catch (Exception $e)
        {
            return false;
        }
    }

    public function getTable()
    {
        return 'videofollow';
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
