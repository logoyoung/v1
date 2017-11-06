<?php
namespace lib\anchor;
use Exception;
use system\DbHelper;

class Anchor
{
    const DB_CONF = 'huanpeng';

    public static $fields = [
      'uid','level','income','integral','bean','coin','videolimit','cid','cert_status','ctime','utime','rate',
    ];

    public function getTable()
    {
        return 'anchor';
    }

    public function getDb()
    {
        return DbHelper::getInstance(self::DB_CONF);
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->getDb(), $method], $args);
    }

    public function getAnchorDataByUid($uid, array $fields = [])
    {
        if(!$uid)
        {
            return false;
        }

        $uid = (array) $uid;
        $num = count($uid);
        if($fields)
        {
            $fields[] = 'uid';
            $fields   = array_unique($fields);
        } else {
            $fields = self::$fields;
        }

        $db      = $this->getDb();
        $fields  = $db->buildFieldsParam($fields);
        $inStr   = $db->buildInPrepare($uid);
        $sql     = "SELECT {$fields} FROM `{$this->getTable()}` WHERE `uid` IN($inStr) LIMIT {$num}";

        try {

            $result = $db->query($sql,$uid);
            if(!$result)
            {
                return $result;
            }

            $data = [];
            foreach ($result as $v)
            {
                $data[$v['uid']] = $v;
            }

            return $data;

        } catch (Exception $e) {
            return false;
        }
    }

     /**
     * 供校验脚本使用
     * @return init
     */
    public function getUserTotalNum()
    {
        $sql = "SELECT COUNT(*) AS total_num FROM `{$this->getTable()}`";

        try {
            $db     = $this->getDb();
            $result = $db->query($sql);
            if(!$result)
            {
                return 0;
            }

            return (int) $result[0]['total_num'];

        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * 供校验脚本使用
     * @param  [type] $page   [description]
     * @param  [type] $size   [description]
     * @param  array  $fields [description]
     * @return [type]         [description]
     */
    public function getAnchorList($page, $size, array $fields = [])
    {
        if($fields)
        {
            $fields[] = 'uid';
            $fields   = array_unique($fields);
        } else {
            $fields = self::$fields;
        }
        $db      = $this->getDb();
        $fields  = $db->buildFieldsParam($fields);
        $page    = (int) $page;
        $size    = $size > 1000 ? 1000 : $size;
        $offset  = ($page - 1) * $size;
        $bdParam = ['offset' => $offset, 'size' => $size];
        $sql     = "SELECT {$fields} FROM `{$this->getTable()}` ORDER BY `uid` ASC LIMIT :offset,:size";

        try {

            $result = $db->query($sql,$bdParam);
            if(!$result)
            {
                return $result;
            }

            $data = [];
            foreach ($result as $v)
            {
                $data[$v['uid']] = $v;
            }

            return $data;

        } catch (Exception $e) {
            return false;
        }

    }
}