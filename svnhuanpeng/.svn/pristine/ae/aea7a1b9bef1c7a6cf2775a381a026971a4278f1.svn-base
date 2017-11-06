###表一
CREATE TABLE IF NOT EXISTS `usersilence` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL COMMENT '管理员id',
  `luid` int(10) UNSIGNED NOT NULL COMMENT '主播id',
  `roomid` int(10) UNSIGNED NOT NULL COMMENT '房间id',
  `reason` varchar(50) NOT NULL COMMENT '禁言原因',
  `fromto` tinyint(4) NOT NULL COMMENT '1后台2前台',
  `stime` datetime NOT NULL COMMENT '禁言开始时间',
  `etime` datetime NOT NULL COMMENT '禁言结束时间',
  `type` smallint(5) UNSIGNED NOT NULL COMMENT '类型 1 禁言 2解禁',
  `uuid` int(10) UNSIGNED NOT NULL COMMENT '解禁管理员id',
  `utime` datetime NOT NULL COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='禁言解禁流水表';

### Indexes for table `usersilence`

ALTER TABLE `usersilence` ADD PRIMARY KEY (`id`), ADD KEY `luid` (`luid`), ADD KEY `roomid` (`roomid`);

###使用表AUTO_INCREMENT `usersilence`

ALTER TABLE `usersilence` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;