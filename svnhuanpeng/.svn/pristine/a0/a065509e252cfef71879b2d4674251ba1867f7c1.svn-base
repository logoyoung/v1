<?php
namespace lib\address;
use Exception;
use system\DbHelper;

class Province
{
    const DB_CONF = 'huanpeng';

    public static $fields = ['id','name',];

    public function getAllProvinceData(array $fields = [])
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

            $provinceData = [];
            foreach ($result as $v)
            {
                $provinceData[$v['id']] = $v;
            }

            return $provinceData;

        } catch (Exception $e) {
            return false;
        }
    }

    public function getTable()
    {
        return 'province';
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