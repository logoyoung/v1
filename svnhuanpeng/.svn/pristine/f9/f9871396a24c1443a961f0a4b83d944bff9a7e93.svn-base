<?php

namespace lib\information;

use Exception;
use system\DbHelper;

class AdminInformation
{

    const DB_CONF = 'huanpeng';

    public static $fields = [
        'id', //int(10) unsigned NOT NULL AUTO_INCREMENT,
        'tid', //tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '分类id',
        'title', //varchar(100) NOT NULL DEFAULT '' COMMENT '资讯标题',
        'content', //blob COMMENT '内容',
        'poster', //varchar(100) NOT NULL DEFAULT '' COMMENT '封面图',
        'ctime', //timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
        'utime', //timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
        'adminid', //int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建者id',
        'click', //int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点击数量',
        'status', //tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0关闭 1开启 2删除',
        'isrecommend', //tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0不推荐 1焦点推荐 2列表推荐',
        'url', //varchar(100) NOT NULL DEFAULT '' COMMENT '链接',
        'client', //tinyint(3) unsigned NOT NULL DEFAULT '2' COMMENT '1app端 2 web端',
        'is_login', //tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0不需要登录 1需要登录',
        'show_type', //tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1公告形式 2新页面 3跳转到App直播间 4跳转到陪玩',
        'stime', //timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        'etime', //timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        'ispublish', //tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0未发布 1已发布',
        'type', //tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1首页 2直播间活动 3首页&直播间 4指定直播间',
        'luids', //blob COMMENT '直播间id',
        'skillid', //int(10) unsigned NOT NULL DEFAULT '0' COMMENT '技能id',
        'certid', //int(10) unsigned NOT NULL DEFAULT '0' COMMENT '资质id',
    ];
    
    public function getCarouselInfo($client,$recommendType)
    {
        $sql = "SELECT id,title,url,is_login AS isLogin,poster,show_type AS showType,luids as luid,certid,skillid FROM `{$this->getTable()}` WHERE client=$client AND isrecommend=$recommendType AND ispublish=1 ORDER BY utime DESC,id DESC";
        
        try
        {
            return $this->getDb()->query($sql);
        } catch (Exception $e)
        {
            return false;
        }
        
    }

    public function getTable()
    {
        return 'admin_information';
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
