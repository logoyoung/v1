<?php

class backpack {

    /**
     * 此SQL语句可以执行的次数
     * @var type 
     */
    public $limitNum = 0;
    private $tool;

    public function __construct(DatabaseTool $tool) {
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

//ALTER TABLE giftrecordcoin_201709 ADD sendType tinyint(3) unsigned not null DEFAULT 0 COMMENT '送礼类型 0:普通送礼，1:背包礼物';
//ALTER TABLE giftrecordcoin_201709 ADD packid BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '背包礼物记录ID';​

        /**
         * @todo 执行需要执行的sql
         */
        $sqls = <<<EOF
CREATE TABLE IF NOT EXISTS `userpack` (
  `id` int(11)  NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT 'uid',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '背包类型',
  `size` int(11) NOT NULL DEFAULT '0' COMMENT '背包空间',
  `free` int(11) NOT NULL DEFAULT '0' COMMENT '剩余背包空间',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '背包状态',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`,`type`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `userpack_gift_template` (
  `id` int(11)  NOT NULL AUTO_INCREMENT,
  `otid` BIGINT(20) NOT NULL DEFAULT '0' COMMENT 'otid',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT 'uid',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '来源类型',
  `sourceid` VARCHAR(32)  NULL DEFAULT '' COMMENT '来源标识',
  `goodsid` int(11) NOT NULL DEFAULT '0' COMMENT '获得物品',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '礼物状态状态',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `stime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '有效开始时间',
  `etime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '有效结束时间',
  `utime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `otid` (`otid`),
  KEY `uid` (`uid`),
  KEY `stime` (`stime`),
  KEY `etime` (`etime`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

        
                
                
    
CREATE TABLE IF NOT EXISTS `activity_goods_log` (
  `id` int(11)  NOT NULL AUTO_INCREMENT,
  `otid` BIGINT(20) NOT NULL DEFAULT '0' COMMENT 'otid',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT 'uid',
  `memo` varchar(100) NOT NULL DEFAULT '' COMMENT '说明',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '来源类型',
  `sourceid` VARCHAR(32)  NULL DEFAULT '' COMMENT '来源标识',
  `goodsid` int(11) NOT NULL DEFAULT '0' COMMENT '获得物品',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '数量',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '领取状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `otid` (`otid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


EOF;
        $res = $this->dbexec($sqls);
        if ($res) {
            $sql = "SHOW CREATE TABLE userpack_gift_template ";
            $result = $this->tool->getDb()->query($sql);
            for ($i = 1; $i < 30; $i++) {
                $newTable = sprintf(" IF NOT EXISTS `userpack_gift_%04d` ", $i);
                $sql = str_replace('`userpack_gift_template`', $newTable, $result[0]['Create Table']);
                $row = $this->tool->getDb()->execute($sql);
            }
        }
        return $res;
    }

}
