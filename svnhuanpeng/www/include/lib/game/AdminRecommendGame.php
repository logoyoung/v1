<?php

namespace lib\game;

use Exception;
use system\DbHelper;

class AdminRecommendGame
{

    const DB_CONF = 'huanpeng';
    const CLASSIFY_GAME = 1; //导航分类推荐游戏
    const RECOMMEND_GAME = 2; //游戏分类推荐游戏
    const FLOOR_GAME = 3; //楼层游戏
    const APP_FLOOR_GAME = 4; //APP楼层游戏
    const HOT_RECOMMEND_LIVE = 5; //APP热门推荐直播

    public static $fields = [
        'type', //tinyint(3) unsigned NOT NULL COMMENT '1 导航栏 2游戏推荐 3楼层推荐',
        'gameid', //varchar(100) NOT NULL DEFAULT '' COMMENT '游戏id列表',
        'number', //varchar(100) NOT NULL DEFAULT '' COMMENT '数量',
        'ctime', //timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
        'utime', //timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
    ];
    private $_master = false;

    public function getRecommendGameId()
    {

        $db = $this->getDb();
        $sql = "SELECT gameid FROM `{$this->getTable()}` where type=" . self::RECOMMEND_GAME;

        try
        {
            $res = $db->query($sql);
                
            return isset($res[0]['gameid']) ? $res[0]['gameid'] : false;
        } catch (Exception $e)
        {
            return false;
        }
    }

    public function getGameIdByRecommendType(int $type = 1)
    {
        $db = $this->getDb();
        $sql = "SELECT gameid,number FROM `{$this->getTable()}` where type=$type";

        try
        {
            $res = $db->query($sql);
                
            return isset($res[0]) ? $res[0] : false;
        } catch (Exception $e)
        {
            return false;
        }
    }

    public function getTable()
    {
        return 'admin_recommend_game';
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
