<?php

namespace lib\user;

use Exception;
use system\DbHelper;

class UserFollow
{

    const DB_CONF = 'huanpeng';

    public static $fields = [
        'uid1', //int(10) unsigned NOT NULL DEFAULT '0',
        'uid2', //int(10) unsigned NOT NULL DEFAULT '0',
        'tm', //timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ];
    private $_master = false;

    public function getUserFollowByUid(int $uid,int $page = 1, int $size = 10)
    {
        $offset = ($page - 1) * $size;
        $db = $this->getDb();
        $pdParams = ['offset' => $offset,'size' => $size];
        $sql = "SELECT uid2 FROM `{$this->getTable()}` WHERE uid1=$uid LIMIT :offset,:size";

        try
        {

            $res = $db->query($sql,$pdParams);
            if (!$res)
            {
                return false;
            }

            $luids = [];

            foreach ($res as $v)
            {
                $luids[] = $v['uid2'];
            }

            return $luids;
        } catch (Exception $e)
        {
            return false;
        }
    }

    public function getUserFollowCountByUid(int $uid)
    {
        $db = $this->getDb();
        $sql = "SELECT COUNT(*) as total FROM `{$this->getTable()}` WHERE uid1=$uid";

        try
        {

            $res = $db->query($sql);
            return isset($res[0]['total']) ? $res[0]['total'] : 0;
        } catch (Exception $e)
        {
            return false;
        }
    }

    public function getTable()
    {
        return 'userfollow';
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
