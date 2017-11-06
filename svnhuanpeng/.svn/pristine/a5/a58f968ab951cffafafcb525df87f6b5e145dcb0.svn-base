###用户兑换记录表
CREATE TABLE IF NOT EXISTS `exchange_detail_template` (
  `id`          int(11)                 NOT NULL AUTO_INCREMENT,
  `uid`         int(10)      UNSIGNED   NOT NULL DEFAULT '0' COMMENT '用户ID',
  `beforefrom`  BIGINT(20)              NOT NULL DEFAULT '0' COMMENT '兑换前被兑换项数值',
  `beforeto`    BIGINT(20)              NOT NULL DEFAULT '0' COMMENT '兑换前兑换成的数值',
  `afterfrom`   BIGINT(20)              NOT NULL DEFAULT '0' COMMENT '兑换后被兑换项数值',
  `afterto`     BIGINT(20)              NOT NULL DEFAULT '0' COMMENT '兑换后兑换成的数值',
  `message`     varchar(300)            NOT NULL DEFAULT ''  COMMENT '兑换描述',
  `number`      BIGINT(20)              NOT NULL DEFAULT '0' COMMENT '兑换数量',
  `type`        tinyint(3)   UNSIGNED   NOT NULL DEFAULT '0' COMMENT '兑换方式',
  `status`      tinyint(3)   UNSIGNED   NOT NULL DEFAULT '0' COMMENT '记录状态 0默认 1创建 2审核 3成功 4失败',
  `ctime`       timestamp               NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `utime`       timestamp               NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

###修改主播表 金豆和金币数据类型
###ALTER TABLE `anchor` CHANGE `bean` `bean` FLOAT(10,3)  NOT NULL DEFAULT '0' ;
###ALTER TABLE `anchor` CHANGE `coin` `coin` FLOAT(10,3)  NOT NULL DEFAULT '0' ;
###修改用户表 欢豆和欢朋币数据类型
###ALTER TABLE `useractive` CHANGE `hpbean` `hpbean` FLOAT(10,3) NOT NULL DEFAULT '0';
###ALTER TABLE `useractive` CHANGE `hpcoin` `hpcoin` FLOAT(10,3) NOT NULL DEFAULT '0';
###
ALTER TABLE `exchange_detail_template` ADD `otid` BIGINT(20) NOT NULL DEFAULT '0' COMMENT '映射ID' AFTER `id`;
ALTER TABLE `exchange_detail_template` ADD `tid` BIGINT(20) NOT NULL DEFAULT '0' COMMENT '财务返回ID' AFTER `otid`;