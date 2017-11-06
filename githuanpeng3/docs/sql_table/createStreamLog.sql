###创建直播流日志表
CREATE TABLE IF NOT EXISTS `liveStreamLog` (
  `id`      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `liveid`  INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `stream`  VARCHAR(100)     NOT NULL,
  `lstatus` TINYINT(3) UNSIGNED COMMENT '直播状态',
  `sstatus` TINYINT(3) UNSIGNED COMMENT '直播流状态', #
  `server`  VARCHAR(100)     NOT NULL,
  `ref`     TINYINT(3) UNSIGNED COMMENT '更改来源',
  `ctime`   TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);