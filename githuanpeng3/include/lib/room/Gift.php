<?php
namespace lib\room;
use Exception;
use system\DbHelper;

class Gift
{
    const DB_CONF = 'huanpeng';

    public static $fields = [
          'id',
          'money',
          'giftname',
          'type',
          'conversionrate',
          'exp',
          'bg',
          'bg_3x',
          'poster',
          'poster_3x',
          'desc',
          'font_color',
          'web_preview',
          'web_bg',
          'web_font_color',
          'all_site_notify',
          'combo_show_time',
          'thumb_poster',
          'thumb_poster_3x',
    ];

    public function getTable()
    {
        return 'gift';
    }

    public function getDb()
    {
        return DbHelper::getInstance(self::DB_CONF);
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->getDb(), $method], $args);
    }

    public function getAllData(array $fields = [])
    {
        $db      = $this->getDb();
        $fields  = $db->buildFieldsParam(($fields ?  $fields : self::$fields));

        try {
            $sql    = "SELECT {$fields} FROM `{$this->getTable()}` ORDER BY `id` DESC";
            $result = $db->query($sql);
            if(!$result)
            {
                return [];
            }
            $data = [];
            foreach ($result as $v)
            {
                $data[$v['id']] = $v;
            }

            return $data;
        } catch (Exception $e) {
            return false;
        }
    }
}