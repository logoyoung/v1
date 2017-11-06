<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once (dirname(__DIR__) . DIRECTORY_SEPARATOR . "BaseSql.php");

class activityTask extends BaseSql {

    public $limitNum = 3;

    public function up() {

        $sqls = <<<EOF
CREATE TABLE IF NOT EXISTS `activity_register_task` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11)  NOT NULL DEFAULT '0' COMMENT '用户id',
  `todotype` int(11) NOT NULL DEFAULT '0' COMMENT '操作类型',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `utime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `status` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '是否加入队列,0未处理,1已加入队列,2已处理',  
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `todotype` (`todotype`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COMMENT '活动任务记录表';
EOF;

        return $this->dbexec($sqls);
    }

}
