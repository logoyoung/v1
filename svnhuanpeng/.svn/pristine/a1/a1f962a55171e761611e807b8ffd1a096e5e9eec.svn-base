<?php

namespace lib\live;

use Exception;
use system\DbHelper;

class AdminRecommendLive
{

    const DB_CONF = 'huanpeng';

    const RECOMMEND_LIVE = 0;
    
    public static $fields = [
        'uid', //int(10) unsigned NOT NULL COMMENT '主播id',
        'nick', //varchar(50) NOT NULL DEFAULT '' COMMENT '主播昵称',
        'head', //varchar(100) NOT NULL DEFAULT '' COMMENT '主播头像',
        'poster', //varchar(100) NOT NULL DEFAULT '' COMMENT '海报',
        'ctime', //timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
        'utime', //timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
        'status', //tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0已推荐 1未推荐',
        'adminid', //int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理者id',
    ];
    private $_master = false;

    public function getRecommendLiveLuser(int $page = 1 ,int $size = 20)
    {
        $offset = ($page - 1) * $size;
        $db = $this->getDb();
        $pdParams = ['offset' => $offset, 'size' => $size];
        $sql = "SELECT uid FROM `{$this->getTable()}` ORDER BY `status` DESC LIMIT :offset,:size";

        try
        {
            $res = $db->query($sql,$pdParams);
            if($res)
            {
                $datas = [];
                foreach ($res as $v)
                {
                   $datas[] = $v['uid']; 
                }
                return $datas;
            }
            return false;
        } catch (Exception $e)
        {
            return false;
        }
    }
    
    public function getRecommendLiveLuserCount()
    {
        $db = $this->getDb();
        $sql = "SELECT COUNT(*) as total FROM `{$this->getTable()}` where `status`=" . self::RECOMMEND_LIVE;

        try
        {
            $res = $db->query($sql);
            return isset($res[0]['total']) ? $res[0]['total'] : 0;
        } catch (Exception $e)
        {
            return false;
        }
    }

    public function getTable()
    {
        return 'admin_recommend_live';
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
