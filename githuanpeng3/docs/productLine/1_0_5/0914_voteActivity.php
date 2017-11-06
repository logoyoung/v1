<?php

/**
 * 使用规则
 * 1 类名 要和文件名去除日期后一致,如:0825_test.php   类名就是 test。
 * 2 设置可以执行的次数,设置$limitNum, 0是无限制
 * 3 $sqls 中添加sql语句
 */
class voteActivity {

    /**
     * 此SQL语句可以执行的次数
     * @var type 
     */
    public $limitNum = 10;
    private $tool;

    public function __construct($tool) {
        $this->tool = $tool;
    }

    private function dbexec($sql) {
        return $this->tool->execSql($sql);
    }

    /**
     * 待执行方法
     * @example $sqls中填写SQL语句,每句以分号分隔。
     * @return boolean
     */
    public function up() { 
        /**
         * @todo 执行需要执行的sql
         */
        $sqls = <<<EOF
CREATE TABLE IF NOT EXISTS `vote_activity` (
  `activity_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '投票ID',
  `game_id` int(11) unsigned NOT NULL COMMENT '游戏id',
  `activity` varchar(100) NOT NULL COMMENT '投票活动标题',
  `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '活动描述',
  `stime` timestamp NOT NULL DEFAULT '1971-01-01 01:01:01' COMMENT '活动开始时间',
  `etime` timestamp NOT NULL DEFAULT '1971-01-01 01:01:01' COMMENT '活动结束时间',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  KEY `activity_id` (`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='投票活动';
                
 CREATE TABLE IF NOT EXISTS `vote_nums` (
  `hero_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '英雄ID',
  `activity_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '投票ID',
  `hero` char(20) NOT NULL DEFAULT '0' COMMENT '投票活动标题',
  `img` varchar(100) NOT NULL COMMENT '英雄图片',
  `bgImg` varchar(100) NOT NULL COMMENT '预留活动背景图',
  `nums` int(10) NOT NULL DEFAULT '1',
  UNIQUE KEY `vote_nums_index` (`activity_id`,`hero_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='英雄投票表';
                
CREATE TABLE IF NOT EXISTS `vote_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '投票用户id',
  `hero_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '英雄ID',
  `activity_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '投票ID',
  `utime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '投票时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `vote_log_index` (`uid`,`activity_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 COMMENT='英雄投票表';
                
 CREATE TABLE IF NOT EXISTS `enroll`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `activity_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '投票ID',
  `game_id` int(10) unsigned NOT NULL,
  `game_nick` varchar(50) NOT NULL COMMENT '游戏昵称',
  `qq` bigint(15) unsigned NOT NULL DEFAULT '0' COMMENT 'qq号',
  `level` varchar(20) NOT NULL DEFAULT '' COMMENT '王者荣耀游戏段位',
  `img` varchar(100) NOT NULL DEFAULT '' COMMENT '段位截图',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '报名时间',
  PRIMARY KEY (`id`),
  KEY `enroll_index` (`uid`,`game_id`,`activity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COMMENT='活动报名表';
EOF;

        return $this->dbexec($sqls);
    }

}
