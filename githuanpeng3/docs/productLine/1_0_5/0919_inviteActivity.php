<?php

/**
 * 使用规则
 * 1 类名 要和文件名去除日期后一致,如:0825_test.php   类名就是 test。
 * 2 设置可以执行的次数,设置$limitNum, 0是无限制
 * 3 $sqls 中添加sql语句
 */
class inviteActivity {

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
CREATE TABLE IF NOT EXISTS `invite_activity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '分享用户uid',
  `invite_code` varchar(40) NOT NULL COMMENT '邀请码 生成规则（uid+时间戳）',
  `channer_id` int(10) unsigned NOT NULL COMMENT '渠道id 预留',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `nums` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '活动链接 领取次数',
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_uid` (`uid`) USING BTREE,
  KEY `index_invite_code` (`invite_code`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
                
CREATE TABLE IF NOT EXISTS `invite_receive` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '领取用户uid ',
  `phone` bigint(11) unsigned NOT NULL,
  `package_id` int(10) unsigned NOT NULL COMMENT '礼包id',
  `beans` int(10) unsigned NOT NULL COMMENT '欢朋豆',
  `invite_code` varchar(40) NOT NULL COMMENT '邀请码',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `utime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `index_code` (`invite_code`),
  KEY `index_phone` (`phone`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
EOF;

        return $this->dbexec($sqls);
    }

}
