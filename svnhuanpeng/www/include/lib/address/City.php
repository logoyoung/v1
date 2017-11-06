<?php
namespace lib\address;
use Exception;
use system\DbHelper;

class City
{
    const DB_CONF = 'huanpeng';

    public static $fields = ['id','pid','name',];

    public function getAllCityData(array $fields = [])
    {
        $fields  = $fields ? $fields : self::$fields;
        $db      = $this->getDb();
        $fields  = $db->buildFieldsParam($fields);
        $sql     = "SELECT {$fields} FROM `{$this->getTable()}`";

        try {

            $result = $db->query($sql);
            if(!$result)
            {
                return $result;
            }

            $cityData = [];
            foreach ($result as $v)
            {
                $uk = "{$v['id']}_{$v['pid']}";
                $cityData[$uk] = $v;
            }

            ksort($cityData);
            return $cityData;

        } catch (Exception $e) {
            return false;
        }
    }

    public function getTable()
    {
        return 'city';
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