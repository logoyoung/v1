/*
Navicat Premium Data Transfer

Source Server         : dev
Source Server Type    : MySQL
Source Server Version : 50554
Source Host           : 192.168.21.64
Source Database       : huanpeng

Target Server Type    : MySQL
Target Server Version : 50554
File Encoding         : latin1

Date: 06/02/2017 14:15:03 PM
*/

SET NAMES latin1;

/*资质认证表*/
CREATE TABLE IF NOT EXISTS `due_cert` (
  `id` int(11) unsigned NOT NULL,
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '认证用户ID',
  `game_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '认证游戏ID',
  `game_level_id` int(11) NOT NULL DEFAULT '0' COMMENT '认证游戏等级ID',
  `pic_urls` varchar(255) NOT NULL DEFAULT '' COMMENT '认证图片',
  `video_url` varchar(120) NOT NULL DEFAULT '' COMMENT '视频地址',
  `video_size` smallint(5) unsigned zerofill NOT NULL COMMENT '视频时长',
  `audio_url` varchar(120) NOT NULL DEFAULT '' COMMENT '音频地址',
  `audio_size` tinyint(4) unsigned zerofill NOT NULL COMMENT '音频时长',
  `info` varchar(200) NOT NULL DEFAULT '' COMMENT '认证说明',
  `status` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '审核状态 -1,审核中.1,机器审核通过.2,人工审核通过.3,机器审核未通过 4,人工审核未通过',
 `reason` varchar(200) NOT NULL DEFAULT '' COMMENT '审核失败原因',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `utime` timestamp NOT NULL DEFAULT '1971-01-01 01:01:01' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='陪玩资质认证表';

/*技能设置表*/
CREATE TABLE IF NOT EXISTS `due_skill` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '认证用户ID',
  `cert_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '资质ID',
  `game_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '认证游戏ID',
  `tag_ids` varchar(50) NOT NULL DEFAULT '' COMMENT '标签ID',
  `price` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '单价',
  `unit` varchar(10) NOT NULL DEFAULT '' COMMENT '价格单位 1、局 2、小时',
  `stime` MEDIUMINT(7) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `etime` MEDIUMINT(7) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `service_day` varchar(20) NOT NULL DEFAULT '' COMMENT '服务日期（1、周一 2、周二 3、周三 4、周四 5、周五 6、周六 7、周日）',
  `switch` tinyint(1) NOT NULL DEFAULT '-1' COMMENT ' 开关 -1不展示 1展示',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否推荐',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `utime` timestamp NOT NULL DEFAULT '1971-01-01 01:01:01' COMMENT '更新时间',
  `avg_score` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '技能平均分',
  `total_score` int(11) unsigned NOT NULL COMMENT '评论总分',
  `comment_num` int(11) unsigned NOT NULL COMMENT '评论次数',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `cert_id` (`cert_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='认证设置表';

/*约玩游戏表*/
CREATE TABLE IF NOT EXISTS `due_game` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `gameid` int(10) unsigned NOT NULL COMMENT '游戏id',
  `gametid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '游戏分类id',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '游戏名称',
  `icon` varchar(100) NOT NULL COMMENT '游戏图标',
  `poster` varchar(100) NOT NULL COMMENT '游戏封面',
  `ord` int(10) NOT NULL,
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `number` tinyint(2) NOT NULL DEFAULT '2' COMMENT '楼层控制量',
  PRIMARY KEY (`id`),
  KEY `gameid` (`gameid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='约玩游戏表';

/*约玩标签表*/
CREATE TABLE IF NOT EXISTS `due_tags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `game_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '认证游戏ID',
  `star` tinyint(4) unsigned NOT NULL COMMENT '星级',
  `tag` varchar(50) NOT NULL DEFAULT '' COMMENT '标签',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='约玩标签表';

/*约玩评论表*/
CREATE TABLE IF NOT EXISTS `due_comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '订单号',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '下单用户ID',
  `cert_uid` int(11) NOT NULL DEFAULT '0' COMMENT '资质认证主播id',
  `skill_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户技能id',
  `star` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '星级',
  `tag_ids` varchar(200) NOT NULL DEFAULT '' COMMENT '标签ID',
  `comment` varchar(200) NOT NULL DEFAULT '' COMMENT '评语',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `utime` timestamp NOT NULL DEFAULT '1971-01-01 01:01:01' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '评论状态',
  PRIMARY KEY (`id`),
  KEY `skill_id` (`skill_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='评价表';

/*约玩价格表*/
CREATE TABLE IF NOT EXISTS `due_price` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `price` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '单价',
  `price_name` varchar(200) NOT NULL DEFAULT '' COMMENT '价格名 xxx欢朋币/局',
  `unit` tinyint(4) NOT NULL DEFAULT '1' COMMENT '单位 1为局 2为小时',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `utime` timestamp NOT NULL DEFAULT '1971-01-01 01:01:01' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='陪玩价格表';


/*约玩订单表*/
CREATE TABLE IF NOT EXISTS `due_order` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`otid` bigint(20) NOT NULL DEFAULT '0' COMMENT '财务流水订单号',
`order_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '订单号',
`uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '下单用户ID',
`cert_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '认证用户ID',
`cert_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '资质ID',
`discount` BIGINT(20) NOT NULL DEFAULT '0' COMMENT '优惠政策',
`amount` BIGINT(20)  NOT NULL DEFAULT '0' COMMENT '总价',
`real_amount` BIGINT(20)  NOT NULL DEFAULT '0' COMMENT '实际付款',
`income` BIGINT(20)  NOT NULL DEFAULT '0' COMMENT '收益',
`memo` varchar(200) NOT NULL DEFAULT '' COMMENT '用户下单备注',
`status` int(10) NOT NULL DEFAULT '0' COMMENT '订单状态',
`comment` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否评论 0无评论',
`reason` varchar(200) NOT NULL DEFAULT '' COMMENT '取消、拒单原因',
`ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`otime` timestamp NOT NULL DEFAULT '1971-01-01 01:01:01' COMMENT '更新时间',
`stime` timestamp NOT NULL DEFAULT '1971-01-01 01:01:01' COMMENT '订单完成时间',
`rtime` timestamp NOT NULL DEFAULT '1971-01-01 01:01:01' COMMENT '取消时间',
PRIMARY KEY (`id`),
UNIQUE KEY `order_id` (`order_id`),
KEY `otime` (`otime`),
KEY `ctime` (`ctime`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='约玩订单表';


/*约玩订单详情表*/
CREATE TABLE IF NOT EXISTS `due_order_detail` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`order_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '订单id',
`cert_id` int(11) NOT NULL DEFAULT '0' COMMENT '认证id',
`skill_id` int(11) NOT NULL DEFAULT '0' COMMENT '技能ID',
`price` BIGINT(20) NOT NULL DEFAULT '0' COMMENT '单价',
`unit` tinyint(4) NOT NULL DEFAULT '0' COMMENT '计价方式',
`number` int(10) NOT NULL DEFAULT '0' COMMENT '购买数量',
`start_time` int(11) NOT NULL DEFAULT '0' COMMENT '开始时间',
`end_time` int(11) NOT NULL DEFAULT '0' COMMENT '结束时间',
`role_name` varchar(30) NOT NULL DEFAULT '' COMMENT '角色名',
`contact_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '联系方式',
`contact` varchar(30) NOT NULL DEFAULT '' COMMENT '联系号码',
`ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
PRIMARY KEY (`id`),
KEY `start_time` (`start_time`),
KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='订单表订单详情';

/*约玩申诉表*/
CREATE TABLE IF NOT EXISTS `due_order_appeal` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '认证用户ID',
`order_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '订单id',
`content` varchar(200) NOT NULL DEFAULT '' COMMENT '申诉描述',
`reply` varchar(200) NOT NULL DEFAULT '' COMMENT '回复',
`pic` varchar(300) NOT NULL DEFAULT '' COMMENT '图片路径',
`ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '申诉时间',
`utime` timestamp NOT NULL DEFAULT '1971-01-01 01:01:01' COMMENT '操作时间',
`status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '申诉状态',
`clear`  tinyint(4) NOT NULL DEFAULT '0' COMMENT '清算状态 0默认 1已清算',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='申诉表';

/*约玩订单日志表*/
CREATE TABLE IF NOT EXISTS `due_order_log` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`order_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '订单号',
`ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`status` int(10)  NOT NULL DEFAULT '0' COMMENT '订单状态',
`uid` int(11) NOT NULL DEFAULT '0' COMMENT '操作人',
`reason` varchar(200) NOT NULL DEFAULT '' COMMENT '修改理由',
`log` varchar(200) NOT NULL DEFAULT '' COMMENT '系统日志',
PRIMARY KEY (`id`),
KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='订单变更记录表';

 
/*约玩融云用户表*/
CREATE TABLE IF NOT EXISTS `due_rong_user` (
`uid` int(11) NOT NULL COMMENT 'uid',
`token` varchar(100) NOT NULL COMMENT 'token',
`ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='融云即时聊天';

/*约玩资质审核表*/
CREATE TABLE `admin_due_cert` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '认证用户ID',
  `game_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '认证游戏ID',
  `game_level_id` int(11) NOT NULL DEFAULT '0' COMMENT '认证游戏等级ID',
  `pic_urls` varchar(255) NOT NULL DEFAULT '' COMMENT '认证图片',
  `video_url` varchar(120) NOT NULL DEFAULT '' COMMENT '视频地址',
  `video_size` smallint(5) unsigned zerofill NOT NULL COMMENT '视频时长',
  `audio_url` varchar(120) NOT NULL DEFAULT '' COMMENT '音频地址',
  `audio_size` tinyint(4) unsigned zerofill NOT NULL COMMENT '音频时长',
  `info` varchar(200) NOT NULL DEFAULT '' COMMENT '认证说明',
  `status` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '审核状态 -1,审核中.1,机器审核通过.2,人工审核通过.3,机器审核未通过 4,人工审核未通过',
  `reason` varchar(200) NOT NULL DEFAULT '' COMMENT '审核失败原因',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `utime` timestamp NOT NULL DEFAULT '1971-01-01 01:01:01' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='约玩资质审核表';

/*主播 标签评论次数表*/
DROP TABLE IF EXISTS `due_user_tags`;
CREATE TABLE `due_user_tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `tagid` int(10) unsigned NOT NULL COMMENT '用户前四个频次最高的标签id',
  `nums` int(10) unsigned NOT NULL,
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag_index` (`uid`,`tagid`) USING BTREE,
  KEY `order_index` (`nums`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

/*标签导入*/
INSERT INTO `due_tags` VALUES ('1', '0', '0', '带节奏', '2017-06-26 10:03:11');
INSERT INTO `due_tags` VALUES ('2', '0', '0', '补刀大神', '2017-06-26 10:03:11');
INSERT INTO `due_tags` VALUES ('3', '0', '0', '王者', '2017-06-26 10:03:11');
INSERT INTO `due_tags` VALUES ('4', '0', '0', '超神', '2017-06-26 10:03:11');
INSERT INTO `due_tags` VALUES ('5', '0', '0', '治愈', '2017-06-26 10:03:11');
INSERT INTO `due_tags` VALUES ('6', '0', '0', '帅气', '2017-06-26 10:03:11');
INSERT INTO `due_tags` VALUES ('7', '0', '0', '可爱', '2017-06-26 10:03:11');
INSERT INTO `due_tags` VALUES ('8', '0', '0', '娇柔', '2017-06-26 10:03:11');
INSERT INTO `due_tags` VALUES ('9', '0', '0', '声甜', '2017-06-26 10:03:11');
INSERT INTO `due_tags` VALUES ('10', '0', '0', '给力辅助', '2017-06-26 10:03:11');
INSERT INTO `due_tags` VALUES ('11', '0', '0', '最强输出', '2017-06-26 10:03:11');
INSERT INTO `due_tags` VALUES ('12', '0', '0', '聊天达人', '2017-06-26 10:03:11');
INSERT INTO `due_tags` VALUES ('13', '0', '0', '萝莉', '2017-06-26 10:03:11');
INSERT INTO `due_tags` VALUES ('14', '0', '0', '老司机', '2017-06-26 10:03:11');
INSERT INTO `due_tags` VALUES ('15', '0', '0', '666', '2017-06-26 10:03:11');
INSERT INTO `due_tags` VALUES ('16', '0', '0', '走心', '2017-06-26 10:03:11');
INSERT INTO `due_tags` VALUES ('17', '0', '0', '呆萌', '2017-06-26 10:03:11');
INSERT INTO `due_tags` VALUES ('18', '0', '0', '幽默', '2017-06-26 10:03:11');
INSERT INTO `due_tags` VALUES ('19', '0', '0', '小鲜肉', '2017-06-26 10:03:11');
INSERT INTO `due_tags` VALUES ('20', '0', '0', 'ADC', '2017-06-26 10:03:11');
