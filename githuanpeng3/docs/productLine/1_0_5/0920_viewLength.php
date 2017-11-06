<?php

require_once (dirname(__DIR__) . DIRECTORY_SEPARATOR . "BaseSql.php");

class viewLength extends BaseSql
{

    public $limitNum = 1;

    public function up()
    {

        $sqls = <<<EOF
CREATE TABLE IF NOT EXISTS `view_length` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '观看用户id',
  `record_date` date NOT NULL DEFAULT '0000-00-00' COMMENT '记录日期',
  `reward_status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '奖励状态 1、未奖励 2、已奖励第一档次3、已奖励第二档次4、已奖励第三档次',
  `view_length` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '当天观看时长',
  `ctime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `utime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `duid` (`record_date`,`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COMMENT='观看直播时长表';
EOF;
        return $this->dbexec($sqls);
    }

}
