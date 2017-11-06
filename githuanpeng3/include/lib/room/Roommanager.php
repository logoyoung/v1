<?php
namespace lib\room;
use Exception;
use system\DbHelper;

class Roommanager
{
    const DB_CONF = 'huanpeng';

    public static $fields = ['luid','uid','ctime','level'];

    public function getTable()
    {
        return 'roommanager';
    }

    public function getDb()
    {
        return DbHelper::getInstance(self::DB_CONF);
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->getDb(), $method], $args);
    }

    public function add($anchorUid,$managerUid,$ctime = 0,$level = 0)
    {
        if(!$anchorUid || !$managerUid)
        {
            return false;
        }

        $bdParam = [
            'luid'  => $anchorUid,
            'uid'   => $managerUid,
            'ctime' => (($ctime != 0) ? $ctime : date('Y-m-d H:i:s') ),
            'level' => $level,
        ];

        $db  = $this->getDb();
        $sql = "INSERT INTO `{$this->getTable()}` (`luid`,`uid`,`ctime`,`level`) VALUES(:luid,:uid,:ctime,:level)";

        try {

            return $db->execute($sql,$bdParam,true);

        } catch (Exception $e) {
            return false;
        }
    }

    public function delete($anchorUid,$managerUid)
    {
        if(!$anchorUid || !$managerUid)
        {
            return false;
        }

        $db      = $this->getDb();
        $bdParam = [
           'luid'  => $anchorUid,
           'uid'   => $managerUid,
        ];

        $sql     = "DELETE FROM `{$this->getTable()}` WHERE `luid` = :luid AND `uid` = :uid LIMIT 1";

        try {

            return $db->execute($sql,$bdParam,true);

        } catch (Exception $e) {
            return false;
        }

    }

    public function getDataByAnchorUid($anchorUid, array $fields = [], $limit = 1000)
    {
        if(!$anchorUid)
        {
            return false;
        }

        $db      = $this->getDb();

        $bdParam = [
           'luid'  => $anchorUid,
        ];

        $fields  = $db->buildFieldsParam(($fields ?  $fields : self::$fields));

        $sql     = "SELECT {$fields} FROM `{$this->getTable()}` WHERE `luid` = :luid";

        try {

            return $db->query($sql,$bdParam);

        } catch (Exception $e) {
            return false;
        }
    }

    public function getDataByAnchorUidManagerUid($anchorUid, $managerUid, array $fields = [])
    {
        if(!$anchorUid || !$managerUid)
        {
            return false;
        }

        $bdParam = [
           'luid'  => $anchorUid,
           'uid'   => $managerUid,
        ];

        $db      = $this->getDb();
        $fields  = $db->buildFieldsParam(($fields ?  $fields : self::$fields));

        $sql     = "SELECT {$fields} FROM `{$this->getTable()}` WHERE `luid` = :luid AND `uid` = :uid";

        try {

            return $db->query($sql,$bdParam);

        } catch (Exception $e) {
            return false;
        }
    }

    public function getTotalNumByAnchorUid($anchorUid)
    {
        if(!$anchorUid)
        {
            return 0;
        }

        $bdParam = [
            'luid' => $anchorUid,
        ];

        try {

            $db     = $this->getDb();
            $sql = "SELECT COUNT(*) AS total_num FROM `{$this->getTable()}` WHERE `luid` = :luid";
            $result = $db->query($sql,$bdParam);
            if(!$result)
            {
                return 0;
            }

            return (int) $result[0]['total_num'];

        } catch (Exception $e) {
            return 0;
        }
    }
}