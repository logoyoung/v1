### 优惠卷表
CREATE TABLE IF NOT EXISTS  `due_coupon` (
`cid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '优惠券id',
`aid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '活动ID',
`name` varchar(50) NOT NULL DEFAULT '' COMMENT '优惠券名称',
`type` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '优惠券类型',
`price` decimal(10,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '优惠券金额',
`luid`  int(10) unsigned NOT NULL DEFAULT '0' COMMENT '主播限制0为不限制',
`game_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '游戏限制0为不限制',
`max_number` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最大发放数量',
`send_number` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已发放数量',
`condition`  TEXT NOT NULL DEFAULT '' COMMENT '使用条件',
`expire` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '过期时间',
`status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
`ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`utime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
`stime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开始发放时间',
`etime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '结束发放时间',
PRIMARY KEY(cid)
)ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='优惠券定义表';
### 活动表配置表
CREATE TABLE IF NOT EXISTS  `due_activity` (
`aid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '活动id',
`type` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '活动类型',
`name` varchar(50) NOT NULL DEFAULT '' COMMENT '活动名称',
`content` varchar(500) NOT NULL DEFAULT '' COMMENT '活动内容',
`pic` varchar(300) NOT NULL DEFAULT '' COMMENT '活动分享图片',
`configure` TEXT NOT NULL DEFAULT '' COMMENT '配置',
`send_number` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已发放数量',
`status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
`ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`expire` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '过期时间',
`stime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开始发放时间',
`etime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '结束发放时间',
`utime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新放时间',
PRIMARY KEY(aid)
)ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='活动表配置表';
### 分发记录表
CREATE TABLE IF NOT EXISTS  `due_share_record` (
`rid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录id',
`aid` int(10) unsigned NOT NULL COMMENT '活动id',
`type` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '活动类型',
`source_id` BIGINT(20) unsigned NOT NULL DEFAULT '0' COMMENT '源id',
`share_uuid` varchar(40)  NOT NULL DEFAULT '' COMMENT '唯一ID',
`uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分享用户id',
`configure` TEXT NOT NULL DEFAULT '' COMMENT '配置',
`share_number` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已发放数量',
`receive_number` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已领取数',
`status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
`ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`stime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '发放开始时间',
`etime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '发放结束时间',
PRIMARY KEY(rid),
KEY `uid` (uid),
KEY `aid` (aid),
KEY `share_uuid` (share_uuid)
)ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='分发记录表';
### 优惠卷领取、使用记录表
CREATE TABLE IF NOT EXISTS  `due_user_coupon` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录id', 
`code`  bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '优惠券码',
`uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
`phone` char(20) NOT NULL DEFAULT '' COMMENT '用户手机号',
`promocode` varchar(100) NOT NULL DEFAULT '' COMMENT  '推广码',
`price` decimal(10,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '红包金额',
`status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
`orderid`  bigint(20) NOT NULL DEFAULT '0' COMMENT '订单id',
`share_uuid` varchar(40)  NOT NULL DEFAULT '' COMMENT '唯一ID',
`activity_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '活动id',
`coupon_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '优惠券id',
`type` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '活动类型',
`channel` int(10) NOT NULL DEFAULT '0' COMMENT '发放渠道',
`ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`stime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '有效期开始时间',
`etime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '有效期结束时间',
`utime` timestamp NOT NULL DEFAULT '0000-00-00' COMMENT '更新时间',
PRIMARY KEY(id),
KEY `coupon_id` (coupon_id),
KEY `uid` (`uid`),
KEY `phone` (`phone`),
KEY `share_uuid` (`share_uuid`)
)ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='优惠卷领取使用记录表';
 
### 优惠券活动类型
CREATE TABLE IF NOT EXISTS  `due_activity_category`(
     `id` int(11) unsigned not null auto_increment comment '活动类型id',
     `name` varchar(50) NOT NULL DEFAULT '' COMMENT '活动类型',
     `desc` TEXT NOT NULL DEFAULT '' COMMENT '类型描述',
     `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '类型状态：1此类型活动运营中；0此类型所有活动停用；',
     `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',

PRIMARY KEY(id)
)ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='优惠卷活动类型'; 

###主播 标签评论次数表 
CREATE TABLE IF NOT EXISTS `due_user_tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `tagid` int(10) unsigned NOT NULL COMMENT '用户前四个频次最高的标签id',
  `nums` int(10) unsigned NOT NULL,
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag_index` (`uid`,`tagid`) USING BTREE,
  KEY `order_index` (`nums`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

###优惠券配置表
CREATE TABLE IF NOT EXISTS `due_coupon_config` (
  `id` int(11) unsigned NOT NULL DEFAULT '1',
  `config` text NOT NULL DEFAULT '' COMMENT '优惠券配置参数',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `utime` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='优惠券配置表';
###初始化数据
INSERT INTO `due_activity_category` (`id`, `name`, `desc`, `status`, `ctime`)
VALUES
	(1, '登录活动类型', '只领取一次', 1, '2017-07-20 11:20:13'),
	(2, '用户下单类型', '', 1, '2017-07-20 11:20:08'),
	(3, '资质审核通过类型', '', 1, '2017-07-20 11:20:05'),
	(4, '内部发放', '审核人员或系统发放', 1, '2017-07-20 11:20:06');



         
