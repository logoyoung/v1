<?php
namespace lib\room;
use Exception;
use system\DbHelper;

class Roomid
{
    const DB_CONF = 'huanpeng';

    public static $fields = ['id','uid','roomid','ctime', 'utime'];

    public function getTable()
    {
        return 'roomid';
    }

    public function getDb()
    {
        return DbHelper::getInstance(self::DB_CONF);
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->getDb(), $method], $args);
    }

    /**
     * 获取所有直播房间数据
     * @return int | false
     */
    public function getTotalNum()
    {

        $db  = $this->getDb();
        $sql = "SELECT COUNT(*) AS total_num FROM `{$this->getTable()}`";

        try {

            $result = $db->query($sql);
            return isset($result[0]['total_num']) ? (int) $result[0]['total_num'] : 0;

        } catch (Exception $e) {
            return false;
        }

    }

    /**
     * 根据roomid 获取主播uid
     * @param  int $roomid [description]
     * @return int| false
     */
    public function getUidByRoomid($roomid)
    {
        if(!$roomid)
        {
            return false;
        }

        $db      = $this->getDb();
        $bdParam = ['roomid' => $roomid];
        $sql     = "SELECT `uid` FROM `{$this->getTable()}` WHERE `roomid` = :roomid LIMIT 1";
         try {

            $result = $db->query($sql,$bdParam);
            return isset($result[0]['uid']) ? (int) $result[0]['uid'] : 0;

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 根据uid 获取roomid
     * @param  int $uid [description]
     * @return int|false      [description]
     */
    public function getRoomidByUid($uid)
    {
        if(!$uid)
        {
            return false;
        }

        $db      = $this->getDb();
        $bdParam = ['uid' => $uid];
        $sql     = "SELECT `roomid` FROM `{$this->getTable()}` WHERE `uid` = :uid LIMIT 1";
        try {

            $result = $db->query($sql,$bdParam);
            return isset($result[0]['roomid']) ? (int) $result[0]['roomid'] : 0;

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 根据roomid 获取主播uid
     * @param  int $roomid [description]
     * @return int| false
     */
    public function getUidByRoomids($roomid)
    {
        if(!$roomid)
        {
            return false;
        }
        $roomid  = (array) $roomid;
        $num     = count($roomid);
        $db      = $this->getDb();
        $inStr   = $db->buildInPrepare($roomid);
        $sql     = "SELECT `uid`,`roomid` FROM `{$this->getTable()}` WHERE `roomid` IN($inStr) LIMIT {$num}";
        try {

            $result = $db->query($sql,$roomid);
            if(!$result)
            {
                return $result;
            }

            $uids = [];
            foreach ($result as $v)
            {
                $uids[$v['roomid']] = $v['uid'];
            }
            unset($result);
            return $uids;

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 根据uid 获取roomid
     * @param  int $uid [description]
     * @return int|false      [description]
     */
    public function getRoomidByUids($uid)
    {
        if(!$uid)
        {
            return false;
        }

        $uid   = (array) $uid;
        $num   = count($uid);
        $db    = $this->getDb();
        $inStr = $db->buildInPrepare($uid);
        $sql   = "SELECT `roomid`, `uid` FROM `{$this->getTable()}` WHERE `uid` IN($inStr) LIMIT {$num}";

        try {

            $result = $db->query($sql,$uid);
            if(!$result)
            {
                return $result;
            }

            $roomid = [];
            foreach ($result as $v)
            {
                $roomid[$v['uid']] = $v['roomid'];
            }

            unset($result);
            return $roomid;

        } catch (Exception $e) {
            return false;
        }
    }
}