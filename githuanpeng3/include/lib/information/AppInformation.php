<?php
namespace lib\information;
use Exception;
use system\DbHelper;

class AppInformation
{
    const DB_CONF = 'huanpeng';

    public static $fields = [
        'id',
        'info_id', //'活动id',
        'thumbnail', //'缩略图',
        'adminid',
        'status', // '1发布 2下架',
        'ctime',
        'utime',
    ];

    public function getTable()
    {
        return 'app_information';
    }

    public function getDb()
    {
        return DbHelper::getInstance(self::DB_CONF);
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->getDb(), $method], $args);
    }

    public function getDataByStatus($status = 1, array $fields = ['thumbnail','status','info_id'])
    {
        $db      = $this->getDb();
        $fields  = $db->buildFieldsParam(($fields ? $fields :self::$fields));
        $bdParam = ['status' => (int) $status];
        $sql     = "SELECT {$fields} FROM `{$this->getTable()}` WHERE `status` = :status LIMIT 1";

        try {

            $result = $db->query($sql,$bdParam);
            if(!$result)
            {
                return [];
            }

            return $result[0];

        } catch (Exception $e) {
            return false;
        }
    }
}