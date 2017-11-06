<?php

namespace lib\anchor;

use Exception;
use system\DbHelper;

class AnchorMostPopular
{

    const DB_CONF = 'huanpeng';

    public static $fields = [
        'id', //int(10) unsigned NOT NULL AUTO_INCREMENT,
        'liveid', //int(10) unsigned NOT NULL DEFAULT '0' COMMENT '直播id',
        'uid', //int(10) unsigned NOT NULL DEFAULT '0' COMMENT '主播id',
        'popular', //int(10) unsigned NOT NULL DEFAULT '0' COMMENT '人气',
        'ctime', //timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    ];
    public static $unUpdateFields = ['id','ctime'];
    private $_master = false;

    /**
     * 获取时间段内主播直播时人气峰值
     * @param int $uid
     * @param string $smonth
     * @param string $emonth
     * @return boolean|int
     */
    public function getLivePopularyPeak($uid, $smonth, $emonth)
    {
        $sql = "SELECT MAX(popular) AS popu_peak FROM `{$this->getTable()}` WHERE uid=:uid AND ctime>=:smonth AND ctime<:emonth";
        $pdParam = ['uid' => $uid,'smonth' => "$smonth 00:00:00",'emonth' => "$emonth 00:00:00"];
        try
        {
            $res = $this->getDb()->query($sql,$pdParam);
            return isset($res[0]['popu_peak']) ? $res[0]['popu_peak'] : 0;
        } catch (Exception $exc)
        {
            return false;
        }
    }

    public function getTable()
    {
        return 'anchor_most_popular';
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
