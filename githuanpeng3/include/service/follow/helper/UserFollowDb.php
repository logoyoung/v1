<?php
namespace service\follow\helper;
use system\DbHelper;
use Exception;

class UserFollowDb
{

    const DB_CONF = 'huanpeng';

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

    public function getFansTotalNumByUid($uid)
    {

        try {

            $db      = $this->getDb();
            $bdParam = ['uid2' => $uid];
            $sql     = "SELECT COUNT(*) AS total_num FROM `{$this->getTable()}` WHERE `uid2` = :uid2";
            $result  = $db->query($sql,$bdParam);
            if(!$result)
            {
                return 0;
            }

            return (int) $result[0]['total_num'];

        } catch (Exception $e) {
            return false;
        }
    }

    public function getFollowStatusByUidObjectUid($uid,$objectUId)
    {

        try {

            $db      = $this->getDb();
            $bdParam = ['uid1' => $uid, 'uid2' => $objectUId];
            $sql     = "SELECT `uid1` FROM `{$this->getTable()}` WHERE `uid1` = :uid1 AND `uid2` = :uid2 LIMIT 1";
            return  $db->query($sql,$bdParam) ? 1 : 0;

        } catch (Exception $e) {
            return false;
        }
    }

}