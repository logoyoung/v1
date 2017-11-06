<?php

namespace lib\user;

use Exception;
use system\DbHelper;

class History
{

    const DB_CONF = 'huanpeng';

    public static $fields = [
        'uid', //int(10) unsigned NOT NULL DEFAULT '0',
        'luid', //int(10) unsigned NOT NULL DEFAULT '0',
        'stime', //timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        'status', //tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1正常 0删除',
    ];
    private $_master = false;

    public function getHistoryByUid(int $uid, int $page = 1, int $size = 20)
    {
        $offset = ($page - 1) * $size;
        $db = $this->getDb();
        $pdParams = ['offset' => $offset, 'size' => $size,'uid' => $uid];
        $sql = "SELECT luid FROM `{$this->getTable()}` WHERE uid=:uid AND `status`=1 ORDER BY stime DESC LIMIT :offset,:size";

        try
        {

            $res = $db->query($sql, $pdParams);
            if (!$res)
            {
                return false;
            }

            $luids = [];

            foreach ($res as $v)
            {
                $luids[] = $v['luid'];
            }

            return $luids;
        } catch (Exception $e)
        {
            return false;
        }
    }

    public function getHistoryCountByUid(int $uid)
    {
        $db = $this->getDb();
        $pdParam = ['uid' => $uid];
        $sql = "SELECT count(*) as total FROM `{$this->getTable()}` WHERE uid=:uid AND `status`=1";

        try
        {

            $res = $db->query($sql,$pdParam);
            

            return isset($res[0]['total']) ? $res[0]['total'] : 0;
        } catch (Exception $e)
        {
            return false;
        }
    }
    
    public function getTable()
    {
        return 'history';
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
