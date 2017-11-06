<?php
namespace lib\anchor;
use Exception;
use system\DbHelper;

class AnchorLevel
{
    const DB_CONF = 'huanpeng';

    public static $fields = [
        'level', //int(10)
        'integral', //int
    ];

    private $_master = false;

    public function getLevelData()
    {

        $fields  = self::$fields;
        $db      = $this->getDb();
        $fields  = $db->buildFieldsParam($fields);
        $sql     = "SELECT {$fields} FROM `{$this->getTable()}`";

        try {

            $level = $db->query($sql);
            if(!$level)
            {
                return $level;
            }

            $levelData = [];

            foreach ($level as $v)
            {
                $levelData[$v['level']] = $v['integral'];
            }

            return $levelData;

        } catch (Exception $e) {
            return false;
        }
    }

    public function getTable()
    {
        return 'anchorlevel';
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