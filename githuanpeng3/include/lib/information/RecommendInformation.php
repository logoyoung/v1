<?php

namespace lib\information;

use Exception;
use system\DbHelper;

class RecommendInformation
{

    const DB_CONF = 'huanpeng';

    public static $fields = [
        'id', //tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1焦点推荐 2 列表推荐',
        'client', //tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1app端 2 web端',
        'list', //varchar(64) NOT NULL DEFAULT '' COMMENT '推荐资讯id列表',
        'ctime', //timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
        'utime', //timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
    ];
    public static $unUpdateFields = ['ctime'];
    private $_master = false;

    public function getCarouselListIds(int $client)
    {
        $db = $this->getDb();
        $sql = "SELECT list FROM `{$this->getTable()}` WHERE id=1 AND `client`=:client";
        $pdParam = ['client' => $client];
        try
        {
            $res = $db->query($sql,$pdParam);

            return isset($res[0]['list']) ? $res[0]['list'] : false;
        } catch (Exception $e)
        {
            return false;
        }
    }

    public function getTable()
    {
        return 'recommend_information';
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
