###内部发放纪录表
CREATE TABLE IF NOT EXISTS  `internal_distribution_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL  COMMENT '收益者id',
  `adminid` int(10) unsigned NOT NULL COMMENT '发放者id',
  `hpcoin` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '欢朋币',
  `hpbean` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '欢朋豆',
  `coin` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '金币',
  `bean` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '金豆',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '101内币发放，110活动发放',
  `desc` blob NOT NULL COMMENT '下发描述',
  `activeid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '活动id',
  `ftid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '交易id',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


INSERT INTO `internal_distribution_record` VALUES
(1,2275,0,50000,500000,0,0,101,'给运营同学内币发放',1001,0,'2017-02-08 06:11:20'),
(2,2040,0,20000,200000,0,0,101,'给运营同学内币发放',1001,0,'2017-02-08 06:11:20'),
(3,2105,0,30000,300000,0,0,101,'给运营同学内币发放',1001,0,'2017-02-08 06:11:20'),
(4,2055,0,20000,200000,0,0,101,'给运营同学内币发放',1001,0,'2017-02-08 06:11:20'),
(5,2095,0,20000,200000,0,0,101,'给运营同学内币发放',1001,0,'2017-02-08 06:11:20'),
(6,2100,0,30000,300000,0,0,101,'给运营同学内币发放',1001,0,'2017-02-08 06:11:20'),
(7,2225,0,30000,300000,0,0,101,'给运营同学内币发放',1001,0,'2017-02-08 06:11:20'),
(8,2075,0,50000,500000,0,0,101,'给运营同学内币发放',1001,0,'2017-02-08 06:11:20'),
(9,2070,0,50000,500000,0,0,101,'给运营同学内币发放',1001,0,'2017-02-08 06:11:20'),
(10,3445,0,5000,30000,0,0,101,'给运营同学内币发放',1001,0,'2017-03-03 10:09:20'),
(11,3580,0,3000,0,0,0,101,'给运营同学内币发放',1001,0,'2017-03-09 02:29:20'),
(12,3905,0,3000,0,0,0,101,'给运营同学内币发放',1001,0,'2017-03-09 02:29:20'),
(13,3640,0,3000,0,0,0,101,'给运营同学内币发放',1001,0,'2017-03-09 02:29:20'),
(14,3900,0,3000,0,0,0,101,'给运营同学内币发放',1001,0,'2017-03-09 02:29:20'),
(15,3915,0,3000,0,0,0,101,'给运营同学内币发放',1001,0,'2017-03-09 02:29:20'),
(16,2175,0,3000,0,0,0,101,'给运营同学内币发放',1001,0,'2017-03-09 02:29:20'),
(17,14035,0,3000,0,0,0,101,'给运营同学内币发放',1001,0,'2017-03-09 02:29:20'),
(18,13325,0,1000,0,0,0,101,'给运营同学内币发放',1001,0,'2017-03-09 02:29:20'),
(19,13345,0,1000,0,0,0,101,'给运营同学内币发放',1001,0,'2017-03-09 02:29:20'),
(20,13795,0,1000,0,0,0,101,'给运营同学内币发放',1001,0,'2017-03-09 02:29:20'),
(21,14570,0,1000,0,0,0,101,'给运营同学内币发放',1001,0,'2017-03-09 02:29:20'),
(22,15035,0,1000,0,0,0,101,'给运营同学内币发放',1001,0,'2017-03-09 02:29:20'),
(23,15040,0,1000,0,0,0,101,'给运营同学内币发放',1001,0,'2017-03-09 02:29:20'),
(24,15045,0,1000,0,0,0,101,'给运营同学内币发放',1001,0,'2017-03-09 02:29:20'),
(25,15050,0,1000,0,0,0,101,'给运营同学内币发放',1001,0,'2017-03-09 02:29:20'),
(26,15060,0,1000,0,0,0,101,'给运营同学内币发放',1001,0,'2017-03-09 02:29:20'),
(27,1860,0,420,0,0,0,101,'内币发放',1001,0,'2017-02-22 11:02:30'),
(28,26260,0,10000,10000,0,0,101,'给运营同学内币发放',1001,0,'2017-03-24 01:40:20'),
(29,19510,0,30000,30000,0,0,101,'给运营同学内币发放',1001,0,'2017-03-24 01:40:20'),
(30,3445,0,20000,20000,0,0,101,'给运营同学内币发放',1001,0,'2017-03-24 01:40:20'),
(31,15460,0,10000,10000,0,0,101,'给运营同学内币发放',1001,0,'2017-03-24 01:40:20'),
(32,18955,0,100,0,0,0,110,'4月11日王者荣耀solo奖励发放',2002,0,'2017-04-12 08:26:20'),
(33,4410,0,100,0,0,0,110,'4月11日王者荣耀solo奖励发放',2002,0,'2017-04-12 08:26:20'),
(34,18575,0,100,1000,0,0,110,'4月11日王者荣耀solo奖励发放',2002,0,'2017-04-12 08:26:20'),
(35,16385,0,100,1000,0,0,110,'4月11日王者荣耀solo奖励发放',2002,0,'2017-04-12 08:26:20'),
(36,8815,0,0,1000,0,0,110,'4月11日王者荣耀solo奖励发放',2002,0,'2017-04-12 08:26:20'),
(37,18075,0,0,1000,0,0,110,'4月11日王者荣耀solo奖励发放',2002,0,'2017-04-12 08:26:20'),
(38,10990,0,0,1000,0,0,110,'4月11日王者荣耀solo奖励发放',2002,0,'2017-04-12 08:26:20'),
(39,5215,0,0,1000,0,0,110,'4月11日王者荣耀solo奖励发放',2002,0,'2017-04-12 08:26:20'),
(40,4565,0,100,0,0,0,110,'3月31日王者荣耀solo奖励发放',2002,0,'2017-04-11 01:55:20'),
(41,3490,0,100,0,0,0,110,'3月31日王者荣耀solo奖励发放',2002,0,'2017-04-11 01:55:20'),
(42,21905,0,100,1000,0,0,110,'3月31日王者荣耀solo奖励发放',2002,0,'2017-04-11 01:55:20'),
(43,4380,0,100,1000,0,0,110,'3月31日王者荣耀solo奖励发放',2002,0,'2017-04-11 01:55:20'),
(44,13460,0,0,1000,0,0,110,'3月31日王者荣耀solo奖励发放',2002,0,'2017-04-11 01:55:20'),
(45,3415,0,0,1000,0,0,110,'3月31日王者荣耀solo奖励发放',2002,0,'2017-04-11 01:55:20'),
(46,17065,0,0,1000,0,0,110,'3月31日王者荣耀solo奖励发放',2002,0,'2017-04-11 01:55:20'),
(47,22060,0,0,1000,0,0,110,'3月31日王者荣耀solo奖励发放',2002,0,'2017-04-11 01:55:20'),
(48,22060,0,100,0,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-31 06:22:43'),
(49,13460,0,100,0,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-31 06:22:43'),
(50,2290,0,100,1000,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-31 06:22:43'),
(51,4000,0,100,1000,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-31 06:22:43'),
(52,24895,0,0,1000,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-31 06:22:43'),
(53,24505,0,0,1000,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-31 06:22:43'),
(54,25055,0,0,1000,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-31 06:22:43'),
(55,24745,0,0,1000,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-31 06:22:43'),
(56,17065,0,100,0,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-30 06:37:20'),
(57,3415,0,100,0,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-30 06:37:20'),
(58,18075,0,100,1000,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-30 06:37:20'),
(59,11735,0,100,1000,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-30 06:37:20'),
(60,4675,0,0,1000,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-30 06:37:20'),
(61,12515,0,0,1000,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-30 06:37:20'),
(62,13565,0,0,1000,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-30 06:37:20'),
(63,5215,0,0,1000,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-30 06:37:20'),
(64,21905,0,100,0,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-29 03:06:20'),
(65,4565,0,100,0,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-29 03:06:20'),
(66,9460,0,100,1000,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-29 03:06:20'),
(67,14445,0,100,1000,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-29 03:06:20'),
(68,3630,0,0,1000,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-29 03:06:20'),
(69,3635,0,0,1000,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-29 03:06:20'),
(70,8815,0,0,1000,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-29 03:06:20'),
(71,4410,0,0,1000,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-29 03:06:20'),
(72,3490,0,100,0,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-29 03:06:20'),
(73,4380,0,100,0,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-29 03:06:20'),
(74,3630,0,100,1000,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-29 03:06:20'),
(75,4465,0,100,1000,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-29 03:06:20'),
(76,2290,0,0,1000,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-29 03:06:20'),
(77,4675,0,0,1000,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-29 03:06:20'),
(78,3415,0,0,1000,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-29 03:06:20'),
(79,3635,0,0,1000,0,0,110,'王者荣耀solo奖励发放',2002,0,'2017-03-29 03:06:20'),
(80,9100,0,200,0,0,0,110,'反馈问题全面',2001,0,'2017-03-13 06:08:20'),
(81,2625,0,500,0,0,0,110,'封测活动－精彩视频数量榜奖励发放',2001,0,'2017-03-03 10:09:20'),
(82,2290,0,200,0,0,0,110,'封测活动－精彩视频数量榜奖励发放',2001,0,'2017-03-03 10:09:20'),
(83,3055,0,200,0,0,0,110,'封测活动－精彩视频数量榜奖励发放',2001,0,'2017-03-03 10:09:20'),
(84,3490,0,100,0,0,0,110,'封测活动－精彩视频数量榜奖励发放',2001,0,'2017-03-03 10:09:20'),
(85,3430,0,100,0,0,0,110,'封测活动－精彩视频数量榜奖励发放',2001,0,'2017-03-03 10:09:20'),
(86,2290,0,500,0,0,0,110,'封测活动－有效时长榜奖励发放',2001,0,'2017-03-03 10:09:20'),
(87,2625,0,200,0,0,0,110,'封测活动－有效时长榜奖励发放',2001,0,'2017-03-03 10:09:20'),
(88,3490,0,200,0,0,0,110,'封测活动－有效时长榜奖励发放',2001,0,'2017-03-03 10:09:20'),
(89,3055,0,100,0,0,0,110,'封测活动－有效时长榜奖励发放',2001,0,'2017-03-03 10:09:20'),
(90,5285,0,100,0,0,0,110,'封测活动－有效时长榜奖励发放',2001,0,'2017-03-03 10:09:20'),
(91,2290,0,0,6000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(92,2625,0,0,6000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(93,3490,0,0,6000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(94,3055,0,0,6000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(95,5285,0,0,6000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(96,3700,0,0,6000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(97,4245,0,0,4000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(98,4260,0,0,4000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(99,4380,0,0,4000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(100,3415,0,0,4000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(101,2780,0,0,4000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(102,4465,0,0,4000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(103,4410,0,0,4000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(104,8485,0,0,4000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(105,7930,0,0,4000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(106,3250,0,0,4000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(107,3630,0,0,4000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(108,3635,0,0,4000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(109,3710,0,0,2000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(110,2225,0,0,2000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(111,3430,0,0,2000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(112,3100,0,0,2000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(113,7945,0,0,2000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(114,4565,0,0,2000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(115,4240,0,0,2000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(116,4655,0,0,2000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(117,2105,0,0,2000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(118,1860,0,0,2000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(119,4530,0,0,2000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(120,4295,0,0,2000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(121,3070,0,0,2000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(122,3505,0,0,2000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(123,3685,0,0,2000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(124,4310,0,0,2000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(125,4140,0,0,2000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(126,4160,0,0,2000,0,0,110,'封测活动－阳光普照奖励发放',2001,0,'2017-03-03 10:09:20'),
(127,24480,0,100,0,0,0,110,'王者荣耀solo比赛下发',2002,0,'2017-04-14 02:13:40'),
(128,39830,0,100,0,0,0,110,'王者荣耀solo比赛下发',2002,0,'2017-04-14 02:13:40'),
(129,25035,0,100,1000,0,0,110,'王者荣耀solo比赛下发',2002,0,'2017-04-14 02:13:40'),
(130,15920,0,100,1000,0,0,110,'王者荣耀solo比赛下发',2002,0,'2017-04-14 02:13:40'),
(131,3415,0,0,1000,0,0,110,'王者荣耀solo比赛下发',2002,0,'2017-04-14 02:13:40'),
(132,39585,0,0,1000,0,0,110,'王者荣耀solo比赛下发',2002,0,'2017-04-14 02:13:40'),
(133,39945,0,0,1000,0,0,110,'王者荣耀solo比赛下发',2002,0,'2017-04-14 02:13:40'),
(134,30000,0,0,1000,0,0,110,'王者荣耀solo比赛下发',2002,0,'2017-04-14 02:13:40'),
(135,31995,0,100,0,0,0,110,'王者荣耀solo比赛下发',2002,0,'2017-04-14 08:02:30'),
(136,31320,0,100,0,0,0,110,'王者荣耀solo比赛下发',2002,0,'2017-04-14 08:02:30'),
(137,12525,0,100,1000,0,0,110,'王者荣耀solo比赛下发',2002,0,'2017-04-14 08:02:30'),
(138,4000,0,100,1000,0,0,110,'王者荣耀solo比赛下发',2002,0,'2017-04-14 08:02:30'),
(139,3430,0,0,1000,0,0,110,'王者荣耀solo比赛下发',2002,0,'2017-04-14 08:02:30'),
(140,13565,0,0,1000,0,0,110,'王者荣耀solo比赛下发',2002,0,'2017-04-14 08:02:30'),
(141,2715,0,0,1000,0,0,110,'王者荣耀solo比赛下发',2002,0,'2017-04-14 08:02:30'),
(142,35030,0,0,1000,0,0,110,'王者荣耀solo比赛下发',2002,0,'2017-04-14 08:02:30'),
(143,2625,0,5000,50000,0,0,110,'反馈问题全面',2001,0,'2017-02-23 11:02:30'),
(144,31995,0,100,0,0,0,110,'王者荣耀solo比赛下发',2002,0,'2017-04-17 03:01:30'),
(145,24480,0,100,0,0,0,110,'王者荣耀solo比赛下发',2002,0,'2017-04-17 03:01:30'),
(146,18955,0,100,1000,0,0,110,'王者荣耀solo比赛下发',2002,0,'2017-04-17 03:01:30'),
(147,3630,0,100,1000,0,0,110,'王者荣耀solo比赛下发',2002,0,'2017-04-17 03:01:30'),
(148,3570,0,0,1000,0,0,110,'王者荣耀solo比赛下发',2002,0,'2017-04-17 03:01:30'),
(149,39830,0,0,1000,0,0,110,'王者荣耀solo比赛下发',2002,0,'2017-04-17 03:01:30'),
(150,4410,0,0,1000,0,0,110,'王者荣耀solo比赛下发',2002,0,'2017-04-17 03:01:30'),
(151,31320,0,0,1000,0,0,110,'王者荣耀solo比赛下发',2002,0,'2017-04-17 03:01:30'),
(152,3570,2,100,0,0,0,110,'王者荣耀solo比赛，奖励下发',2002,0,'2017-04-19 09:12:30'),
(153,3630,2,100,0,0,0,110,'王者荣耀solo比赛,活动下发',2002,0,'2017-04-19 09:12:30'),
(154,14445,2,100,1000,0,0,110,'王者荣耀solo比赛,活动下发',2002,0,'2017-04-19 09:12:30'),
(155,12665,2,100,1000,0,0,110,'王者荣耀solo比赛,活动下发',2002,0,'2017-04-19 09:12:30'),
(156,2290,2,0,1000,0,0,110,'王者荣耀solo比赛奖励',2002,0,'2017-04-19 09:12:30'),
(157,3055,2,0,1000,0,0,110,'王者荣耀solo 比赛下发',2002,0,'2017-04-19 09:12:30'),
(158,12515,2,0,1000,0,0,110,'王者荣耀solo比赛下发',2002,0,'2017-04-19 09:12:30'),
(159,11735,2,0,1000,0,0,110,'王者荣耀solo比赛下发',2002,0,'2017-04-19 09:12:30'),
(160,39810,2,10000,200000,0,0,101,'运营内币发放',1001,0,'2017-04-19 09:12:30'),
(161,19230,2,10000,200000,0,0,101,'运营内币发放',1001,0,'2017-04-19 09:12:30'),
(162,47315,2,10000,200000,0,0,101,'运营内币发放',1001,0,'2017-04-19 09:12:30'),
(163,50310,2,200,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-02 17:47:50'),
(164,55610,2,200,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-02 17:47:50'),
(165,11885,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-02 17:47:50'),
(166,8705,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-02 17:47:50'),
(167,4410,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-02 17:47:50'),
(168,4380,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-02 17:47:50'),
(169,17065,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-02 17:47:50'),
(170,46400,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-02 17:47:50'),
(171,39945,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-02 17:47:50'),
(172,2290,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-02 17:47:50'),
(173,62956,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-04 14:53:40'),
(174,12515,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-04 14:53:40'),
(175,17405,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-04 14:53:40'),
(176,46700,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-04 14:53:40'),
(177,18955,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-04 14:53:40'),
(178,64140,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-04 14:53:40'),
(179,68632,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-04 14:53:40'),
(180,68612,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-04 14:53:40'),
(181,50310,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-04 14:53:40'),
(182,9460,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-04 14:53:40'),
(183,57295,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-04 14:53:40'),
(184,4380,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-04 14:53:40'),
(185,12515,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-04 14:53:40'),
(186,56505,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-04 14:53:40'),
(187,3055,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-04 14:53:40'),
(188,23365,2,0,1000,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-04 14:53:40'),
(189,55610,2,0,1000,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-04 14:53:40'),
(190,19090,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-09 16:46:40'),
(191,39585,2,0,1000,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-09 16:46:40'),
(192,39830,2,0,1000,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-09 16:46:40'),
(193,67968,2,0,1000,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-09 16:46:40'),
(194,17065,2,0,1000,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-10 10:10:40'),
(195,57295,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-10 10:10:40'),
(196,53475,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-10 10:10:40'),
(197,58525,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-10 10:10:40'),
(198,45205,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-10 10:10:40'),
(199,55610,2,0,1000,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-10 10:10:40'),
(200,64140,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-10 10:10:40'),
(201,68632,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-10 10:10:40'),
(202,68612,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-10 10:10:40'),
(203,46275,2,0,1000,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-10 10:10:40'),
(204,62956,2,0,1000,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-10 10:10:40'),
(205,12515,2,0,1000,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-10 10:10:40'),
(206,17405,2,0,1000,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-10 10:10:40'),
(207,46700,2,0,1000,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-10 10:10:40'),
(208,64140,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-10 10:10:40'),
(209,68632,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-10 10:10:40'),
(210,68612,2,100,0,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-10 10:10:40'),
(211,18955,2,0,1000,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-10 10:10:40'),
(212,19090,2,0,1000,0,0,110,'王者荣耀活动发放',2002,0,'2017-05-10 10:10:40'),
(213,15460,2,0,50000,0,0,101,'运营内币发放',1001,0,'2017-05-19 18:07:30');

###修改昵称纪录表
CREATE TABLE IF NOT EXISTS  `update_nick_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL  COMMENT '用户id',
  `before_name` varchar(100) NOT NULL DEFAULT '' COMMENT '修改之前的昵称',
  `after_name` varchar(100) NOT NULL DEFAULT '' COMMENT '修改之后的昵称',
  `type` tinyint(1) unsigned NOT NULL  COMMENT '1花钱修改，2免费修改',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

###修改分成比率纪录表
CREATE TABLE IF NOT EXISTS  `rate_change_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL  COMMENT '用户id',
  `before_rate` varchar(100) NOT NULL DEFAULT '' COMMENT '修改之前的比率',
  `after_rate` varchar(100) NOT NULL DEFAULT '' COMMENT '修改之后的比率',
  `adminid` int(10) unsigned NOT NULL COMMENT '管理员id',
  `type` tinyint(1) unsigned NOT NULL  COMMENT '1主播角色变更影响比率变更，2主播角色未变更但比率变更',
  `role_change_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '对应主播角色表里的id',
  `desc` blob NOT NULL COMMENT '修改描述',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否通知到财务系统,0没有通知 1已通知到',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

###主播角色变更纪录表
CREATE TABLE IF NOT EXISTS  `anchor_change_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL  COMMENT '用户id',
  `before_cid` int(10) NOT NULL COMMENT '变更前所属cid',
  `after_cid` int(10) NOT NULL  COMMENT '当前所属cid',
  `adminid` int(10) unsigned NOT NULL COMMENT '管理员id',
  `desc` blob NOT NULL COMMENT '修改描述',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

###财务系统返回,后台后续操作失败日志
CREATE TABLE IF NOT EXISTS `unsuccess_log_for_financeBack`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '标题',
  `desc` blob NOT NULL COMMENT '描述',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


alter table anchor add `rate` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '公司汇率 如:70表示3:7分';

alter table game add `gamepic` blob COMMENT '游戏图片';
alter table game add `bgpic` varchar(100) NOT NULL DEFAULT '' COMMENT '背景图片';
alter table game add `description` blob NOT NULL COMMENT '游戏描述';

update game set `poster`='/game/c/6/c6ef5edd9b12b19a37c9a53a66987c83.png',`bgpic`='/game/3/c/3c2676fe52d98bdfa8912ab4d8a77b97.jpg' where gameid=2;
update game set `poster`='/game/4/e/4e6863e6b0b043b6c36e1fd85a1405e0.png' where gameid=8;
update game set `poster`='/game/6/9/69ba657ef22ae5273574a7de95d67f4e.png' where gameid=14;
update game set `poster`='/game/8/d/8d41d6ae97898903542214605a8864e8.png' where gameid=20;
update game set `poster`='/game/f/1/f1a667a3d80447995a6fc46fc20d408e.png' where gameid=26;
update game set `poster`='/game/f/5/f594af80a1b6cf86896e55af24017276.png' where gameid=32;
update game set `poster`='/game/f/8/f84dc1afd37689973ab2194dc5d330d7.png' where gameid=38;
update game set `poster`='/game/0/4/0451a2d5d42fac9f3eb8db6bc55fde1a.png' where gameid=44;
update game set `poster`='/game/e/2/e2df71c10977256ce17931ac5bdf8654.png' where gameid=50;
update game set `poster`='/game/f/5/f57180a982ce34914bb2026b112f2cba.png' where gameid=56;
update game set `poster`='/game/7/8/78e93b3227449b0b16d5cb1ca3d3a444.png',`bgpic`='/game/1/6/16d376d2629c04c0487a69a3f357dc5e.png' where gameid=62;
update game set `poster`='/game/5/1/5195aebcfc367f8c75b40691f685ba7b.png' where gameid=68;
update game set `poster`='/game/b/4/b49739b410c800b98a085cad9b2e8854.png' where gameid=74;
update game set `poster`='/game/5/4/54c66de70769fe183c58d0f34dc0187d.png' where gameid=80;
update game set `poster`='/game/5/9/5981ccfa0e208624c0d832a0b8c696be.png' where gameid=86;
update game set `poster`='/game/b/0/b0283cb4086e812c6bc0d28686d559b2.png' where gameid=92;
update game set `poster`='/game/6/b/6bc7a45c4ade825a04c2fa02017554ff.png' where gameid=98;
update game set `poster`='/game/5/b/5be3e645778c1925a0c76ba48ad531b5.png' where gameid=104;
update game set `poster`='/game/a/9/a99c5df5c9cb6a64762a770e4f3aaf0d.png' where gameid=110;
update game set `poster`='/game/1/2/125ff6a191796e2c0193bc9bd5da3d3c.png' where gameid=116;
update game set `poster`='/game/5/9/593ce9b65cc29b68b65119ac8b7087c9.png' where gameid=122;
update game set `poster`='/game/f/1/f19334f4029c7c64f397c557ab1f2518.png' where gameid=125;
update game set `poster`='/game/3/9/39a45f6ad201950ec9cc92077a56c041.png' where gameid=130;
update game set `poster`='/game/1/c/1c7e111f8165bd50ffbae1032ee2325c.png' where gameid=135;
update game set `poster`='/game/1/4/140422c8e455d252fbed1a951f63e64d.png' where gameid=145;
update game set `poster`='/game/4/1/41b15dbc24a932222bd9a752733f8a1e.png',`bgpic`='/game/b/9/b90d383c33f78cd53218a8afbad23e13.png' where gameid=150;
update game set `poster`='/game/f/a/fa17cbb91bfb869a40b98b34110cabea.png' where gameid=155;
update game set `poster`='/game/f/1/f120771a693dfaec424034fe4c823593.png' where gameid=160;
update game set `poster`='/game/1/5/15689b2a46ecfac7829661c55aded86c.png' where gameid=165;
update game set `poster`='/game/8/8/882030ff81581eb7e085181e8a89252c.png' where gameid=170;
update game set `poster`='/game/e/f/ef2c94f923b5eb51402cfbce2dea943c.png' where gameid=175;
update game set `poster`='/game/0/7/078bc55ba07d6d510c5171039f65ef49.png' where gameid=180;
update game set `poster`='/game/e/1/e19564914b0fac568090d7b7f7c89ccd.png' where gameid=185;
update game set `poster`='/game/1/2/12bce020c4babfa9401ae3ddbed8b334.png',`bgpic`='/game/9/0/90ee33793dcf5b7f754bcb9400d6cedf.png' where gameid=190;
update game set `poster`='/game/a/8/a8c0423ca9817df92d543f5feee45cc8.png' where gameid=195;
update game set `poster`='/game/9/1/91c9ea8d7cfda1d9d6ec620269f95ad2.png' where gameid=200;
update game set `poster`='/game/f/8/f860783d800d26e300dee2d05690bef9.png' where gameid=205;
update game set `poster`='/game/8/f/8fbbd99b613028887253638e4c167dc2.png' where gameid=210;
update game set `poster`='/game/0/0/00de825a96e4f65220530c8b49369037.png',`bgpic`='/game/a/a/aa92fd496e7cfe0ea0a49a1156c94877.png' where gameid=215;
update game set `poster`='/game/6/b/6bb8f7950fb705c5a53ee2f4249ec214.png' where gameid=401;
update game set `poster`='/game/d/4/d4b0279d1c63c9865960ef678729fdf8.png' where gameid=420;
update game set `poster`='/game/b/2/b2696c3dc8aca91ae6429978e72d4aa5.png' where gameid=425;
update game set `poster`='/game/0/4/04adece8d7c5394f20423de5275432ff.png' where gameid=430;
update game set `poster`='/game/7/6/76da7a6e23d2dc5afdffae8fabee0811.png' where gameid=435;
update game set `poster`='/game/0/3/032efac30e8199b39a7c024b4f3e4ad7.png' where gameid=440;
update game set `poster`='/game/9/3/933f876784eeba9f0731021703d60f20.png' where gameid=445;
update game set `poster`='/game/9/4/94142b315ccd01c820d14f26c50a0da9.png' where gameid=450;
update game set `poster`='/game/0/0/00471f562ff37d20e2619fd33ff5c485.png' where gameid=455;
update game set `poster`='/game/0/1/01adb0137275982798f8e592322f296c.png' where gameid=460;
update game set `poster`='/game/8/6/868451eef8bc05cec8d24367e898746f.png' where gameid=465;
update game set `poster`='/game/e/4/e4419761e79e022f79b64a08b4552b39.png' where gameid=470;
update game set `poster`='/game/1/8/18e9c802ac11d9ed10e6b9e61079539a.png' where gameid=475;
update game set `poster`='/game/0/a/0acbcc72997a015e891bdd86d5e05014.png' where gameid=480;
update game set `poster`='/game/d/b/db1b4ba017c2251631e6a4750a338d09.png' where gameid=485;
update game set `poster`='/game/d/3/d3d368f5cac9146615b4d52546b7fced.png' where gameid=490;
update game set `poster`='/game/8/8/889c9608f12a0d95756ffde4484e4274.png' where gameid=495;
update game set `poster`='/game/c/e/ce023acd588ca90c699be8d777f7bed1.png' where gameid=500;
update game set `poster`='/game/2/f/2fddbe0ed4213232e86e6ee122e30116.png' where gameid=505;
update game set `poster`='/game/8/0/80dd4d7c4313c9e2482fe972c4a37936.png' where gameid=510;
update game set `poster`='/game/6/4/6434c064cbba9430664b68ce09d6716f.png' where gameid=515;
update game set `poster`='/game/c/f/cfe391fc317b91d56431da14fc1e605d.png' where gameid=520;

alter table anchor  modify column `coin` float(14,3) NOT NULL DEFAULT '0.000';
alter table anchor  modify column `bean` float(14,3) NOT NULL DEFAULT '0.000';
alter table anchor  modify column `integral` float(14,3) NOT NULL DEFAULT '0.000';
alter table useractive  modify column `integral` float(14,3) NOT NULL DEFAULT '0.000';
INSERT INTO `taskinfo` VALUES (37,0,0,0,1,'2017-05-08 07:28:00','0000-00-00 00:00:00','0000-00-00 00:00:00',1,'首充');

update gift set money=100 where id=31;

alter table useractive  modify column `hpcoin` float(13,3) NOT NULL DEFAULT '0.000';
alter table useractive  modify column `hpbean` float(13,3) NOT NULL DEFAULT '0.000';