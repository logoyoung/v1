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

/*后台配置主播机器人观众数表*/
CREATE TABLE IF NOT EXISTS `admin_robot_viewer` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '主播uid',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '基础观众人数',
  `time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '增长时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT ' 开关 1为开启 -1为关闭',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `utime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='主播机器人观众数表';

/*机器人聊天消息表*/
CREATE TABLE IF NOT EXISTS `robot_chat_msg` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `msg` varchar(255) NOT NULL DEFAULT '' COMMENT '聊天消息',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `utime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='机器人聊天消息';



INSERT INTO `robot_chat_msg` VALUES ('1', '666666666', '2017-08-09 17:26:08', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('2', '666', '2017-08-09 17:26:49', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('3', '233333333', '2017-08-09 17:27:07', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('4', '233', '2017-08-09 17:27:14', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('5', 'FFFFFFFFFF', '2017-08-09 17:27:22', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('6', '主播，好有爱！', '2017-08-09 17:27:32', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('7', '求露脸～', '2017-08-09 17:27:42', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('8', '不要逗！', '2017-08-09 17:27:52', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('9', '主播，求BGM', '2017-08-09 17:28:04', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('10', '约吗？', '2017-08-09 17:28:12', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('11', '吓死宝宝了！', '2017-08-09 17:28:21', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('12', '我的内心几乎是崩溃的', '2017-08-09 17:28:34', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('13', '笑尿了', '2017-08-09 17:28:43', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('14', '演的不错', '2017-08-09 17:28:49', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('15', '漂亮', '2017-08-09 17:28:56', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('16', '啊啊啊', '2017-08-09 17:29:00', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('17', '哈哈哈啊啊', '2017-08-09 17:29:06', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('18', '哈哈', '2017-08-09 17:29:11', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('19', '呵呵', '2017-08-09 17:29:16', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('20', '完美', '2017-08-09 17:29:21', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('21', '套路', '2017-08-09 17:29:33', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('22', '٩( \'ω\' )', '2017-08-09 17:29:41', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('23', '0.0', '2017-08-09 17:29:50', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('24', '？？？', '2017-08-09 17:30:01', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('25', '。。。', '2017-08-09 17:30:08', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('26', '太简单了吧', '2017-08-09 17:30:16', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('27', '←_←', '2017-08-09 17:30:23', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('28', '→_→', '2017-08-09 17:30:31', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('29', '厉害了!', '2017-08-09 17:30:39', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('30', '那你很棒哦！', '2017-08-09 17:30:46', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('31', '尴尬', '2017-08-09 17:30:53', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('32', '还能这么玩', '2017-08-09 17:30:59', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('33', '困=_=\"', '2017-08-09 17:31:08', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('34', '看不懂啊', '2017-08-09 17:31:14', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('35', '好冷', '2017-08-09 17:31:21', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('36', '欢迎', '2017-08-09 17:31:27', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('37', '我服了', '2017-08-09 17:31:33', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('38', '服了', '2017-08-09 17:31:38', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('39', '没办法', '2017-08-09 17:31:44', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('40', '别闹了', '2017-08-09 17:31:52', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('41', '醉了', '2017-08-09 17:31:58', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('42', '来晚了。。', '2017-08-09 17:32:05', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('43', '给主播来波豆', '2017-08-09 17:32:12', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('44', '可以的', '2017-08-09 17:32:17', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('45', '关注了', '2017-08-09 17:32:22', '0000-00-00 00:00:00');
INSERT INTO `robot_chat_msg` VALUES ('46', '你好意思吗？', '2017-08-09 17:32:30', '0000-00-00 00:00:00');
