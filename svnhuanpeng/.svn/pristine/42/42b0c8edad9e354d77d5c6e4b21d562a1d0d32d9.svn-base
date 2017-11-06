CREATE TABLE IF NOT EXISTS `system_log` (
  `mid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) unsigned NOT NULL,
  `type` smallint(5) unsigned NOT NULL DEFAULT '200',
  `ac_uid` bigint(20) unsigned NOT NULL DEFAULT '1',
  `content` text,
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mid`),
  KEY `idx_uid_type_ctime` (`uid`,`type`,`ctime`),
  KEY `idx_type_ctime` (`type`,`ctime`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;