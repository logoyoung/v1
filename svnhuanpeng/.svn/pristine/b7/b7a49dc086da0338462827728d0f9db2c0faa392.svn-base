<?php

// +----------------------------------------------------------------------
//  自定义文件类
// +----------------------------------------------------------------------

namespace HP\File;

class Dao
{
    static public function gettable($str)
    {
        if($str){
            return D('fileBucket')->setTableNum($str);
        }
    }
    static public function inittable($str)
    {
        $sql = "CREATE TABLE IF NOT EXISTS `file`.`file_%s` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `name` varchar(50) NOT NULL DEFAULT '',
                    `mime` varchar(20) NOT NULL DEFAULT '',
                    `size` int(10) unsigned NOT NULL DEFAULT '0',
                    `md5` char(32) NOT NULL DEFAULT '',
                    `referer` varchar(100) NOT NULL DEFAULT '',
                    `uaid` int(10) unsigned NOT NULL DEFAULT '0',
                    `ip` int(10) unsigned NOT NULL DEFAULT '0',
                    `msg` varchar(255) NOT NULL DEFAULT '',
                    PRIMARY KEY (`id`)
                )ENGINE=InnoDB CHARSET=utf8;";
        D('fileIndex')->execute(sprintf($sql,$str));
    }
}
