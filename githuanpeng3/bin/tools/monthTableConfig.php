﻿<?php


return [
    'exchange_detail_' => 
      "CREATE TABLE `$$` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `otid` bigint(20) NOT NULL DEFAULT '0' COMMENT '映射ID',
          `tid` bigint(20) NOT NULL DEFAULT '0' COMMENT '财务返回ID',
          `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
          `beforefrom` bigint(20) NOT NULL DEFAULT '0' COMMENT '兑换前被兑换项数值',
          `beforeto` bigint(20) NOT NULL DEFAULT '0' COMMENT '兑换前兑换成的数值',
          `afterfrom` bigint(20) NOT NULL DEFAULT '0' COMMENT '兑换后被兑换项数值',
          `afterto` bigint(20) NOT NULL DEFAULT '0' COMMENT '兑换后兑换成的数值',
          `message` varchar(300) NOT NULL DEFAULT '' COMMENT '兑换描述',
          `number` bigint(20) NOT NULL DEFAULT '0' COMMENT '兑换数量',
          `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '兑换方式',
          `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '记录状态 0默认 1创建 2审核 3成功 4失败',
          `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `utime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
          PRIMARY KEY (`id`),
          KEY `uid` (`uid`)
      ) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1",
    'giftrecord_' => 
    "CREATE TABLE `$$` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录id',
		  `luid` int(10) unsigned NOT NULL COMMENT '主播id',
		  `liveid` int(10) unsigned NOT NULL COMMENT '直播id',
		  `uid` int(10) unsigned NOT NULL COMMENT '送礼人id',
		  `giftid` tinyint(3) unsigned NOT NULL COMMENT '礼物id',
		  `giftnum` int(10) unsigned NOT NULL,
		  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '送礼时间',
          `otid` bigint(20) unsigned NOT NULL DEFAULT '0',
          `income` float(14,2) NOT NULL DEFAULT '0.00',
          `cost` float(14,2) NOT NULL DEFAULT '0.00',
          PRIMARY KEY (`id`),
          KEY `luid` (`luid`),
          KEY `liveid` (`liveid`),
          KEY `uid` (`uid`),
          KEY `ctime` (`ctime`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1",
    'giftrecordcoin_' => 
    " CREATE TABLE `$$` (
          `id` varchar(20) NOT NULL DEFAULT '' COMMENT '记录id',
		  `luid` int(10) unsigned NOT NULL COMMENT '主播id',
		  `liveid` int(10) unsigned NOT NULL COMMENT '直播id',
		  `uid` int(10) unsigned NOT NULL COMMENT '送礼人id',
		  `giftid` tinyint(3) unsigned NOT NULL COMMENT '礼物id',
		  `giftnum` tinyint(3) unsigned NOT NULL COMMENT '礼物数',
		  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '送礼时间',
          `otid` bigint(20) unsigned NOT NULL DEFAULT '0',
          `income` float(14,2) NOT NULL DEFAULT '0.00',
          `cost` float(14,2) NOT NULL DEFAULT '0.00',
          `sendType` tinyint(3) unsigned not null DEFAULT 0 COMMENT '送礼类型 0:普通送礼，1:背包礼物',
          `packid` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '背包礼物记录ID',
          PRIMARY KEY (`id`),
          KEY `luid` (`luid`),
          KEY `liveid` (`liveid`),
          KEY `uid` (`uid`),
          KEY `ctime` (`ctime`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1",
    'hpf_beanToGBRecord_' => 
    "CREATE TABLE `$$` (
          `id` bigint(20) unsigned NOT NULL,
          `uid` int(10) unsigned NOT NULL DEFAULT '0',
          `gd` bigint(20) NOT NULL DEFAULT '0',
          `gb` bigint(20) NOT NULL DEFAULT '0',
          `desc` blob NOT NULL,
          `otid` bigint(20) unsigned NOT NULL DEFAULT '0',
          `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `uid` (`uid`),
          KEY `otid` (`otid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1",
    'hpf_changeRateRecord_' => 
    "CREATE TABLE `$$` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `uid` int(10) unsigned NOT NULL DEFAULT '0',
          `rate` float(10,3) unsigned NOT NULL DEFAULT '0.000',
          `rateNew` float(10,3) unsigned NOT NULL DEFAULT '0.000',
          `desc` blob NOT NULL,
          `type` int(20) unsigned NOT NULL DEFAULT '0',
          `ip` bigint(20) unsigned NOT NULL DEFAULT '0',
          `port` int(10) unsigned NOT NULL DEFAULT '0',
          `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `otid` bigint(20) NOT NULL DEFAULT '0',
          PRIMARY KEY (`id`),
          KEY `uid` (`uid`),
          KEY `otid` (`otid`),
          KEY `type` (`type`)
    ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1",
    'hpf_exchangeRecord_' => 
    "CREATE TABLE `$$` (
          `id` bigint(20) unsigned NOT NULL,
          `uid` int(10) unsigned NOT NULL DEFAULT '0',
          `gb` bigint(20) NOT NULL DEFAULT '0',
          `hb` bigint(20) NOT NULL DEFAULT '0',
          `desc` blob NOT NULL,
          `otid` bigint(20) unsigned NOT NULL DEFAULT '0',
          `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `uid` (`uid`),
          KEY `otid` (`otid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1",
    'hpf_getHDRecord_' => 
    "CREATE TABLE `$$` (
          `id` bigint(20) unsigned NOT NULL,
          `uid` int(10) unsigned NOT NULL DEFAULT '0',
          `hd` bigint(20) NOT NULL DEFAULT '0',
          `channel` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '获取渠道，1:到时领取，2:宝箱领取',
          `otid` bigint(20) unsigned NOT NULL DEFAULT '0',
          `desc` blob NOT NULL,
          `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `uid` (`uid`),
          KEY `channel` (`channel`),
          KEY `otid` (`otid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1",
    'hpf_guaranteeCronOrderLog_' => 
    "CREATE TABLE `$$` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `orderid` bigint(20) unsigned NOT NULL DEFAULT '0',
          `status` int(10) NOT NULL DEFAULT '0' COMMENT '目标状态',
          `action` int(10) NOT NULL DEFAULT '0' COMMENT '目标动作',
          `runtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
          `handleuid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '操作人ID',
          `handletid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '操作ID',
          `handlegroup` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '1:系统，10:后台,20:后台',
          `desc` blob NOT NULL COMMENT '日志描述',
          `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `orderid` (`orderid`),
          KEY `status` (`status`)
    ) ENGINE=InnoDB AUTO_INCREMENT=655 DEFAULT CHARSET=latin1",
    'hpf_guaranteeOrderLog_' => 
    "CREATE TABLE `$$` (
          `id` bigint(20) unsigned NOT NULL DEFAULT '0',
          `orderid` bigint(20) unsigned NOT NULL DEFAULT '0',
          `s_status` int(10) unsigned NOT NULL DEFAULT '0',
          `d_status` int(10) unsigned NOT NULL DEFAULT '0',
          `handleuid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '操作人ID',
          `handletid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '操作ID',
          `handlegroup` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '1:系统，10:后台,20:后台',
          `desc` blob NOT NULL COMMENT '日志描述',
          `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `orderid` (`orderid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 ",
    'hpf_guarantee_' => 
    "CREATE TABLE `$$` (
          `id` bigint(20) unsigned NOT NULL DEFAULT '0',
          `uid` int(10) unsigned NOT NULL DEFAULT '0',
          `tuid` int(10) unsigned NOT NULL DEFAULT '0',
          `real_pay` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '实际付款金额',
          `pay` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '订单付款金额',
          `pay_coin_type` int(10) unsigned NOT NULL DEFAULT '0',
          `income` bigint(20) unsigned NOT NULL DEFAULT '0',
          `income_coin_type` int(10) unsigned NOT NULL DEFAULT '0',
          `status` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '1:订单创建，',
          `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `etime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
          `desc` blob NOT NULL,
          `type` int(10) unsigned NOT NULL DEFAULT '0',
          `otid` bigint(20) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY (`id`),
          KEY `status` (`status`),
          KEY `uid` (`uid`,`tuid`),
          KEY `tuid` (`tuid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1",
    'hpf_innerCostHBRecord_' => 
    "CREATE TABLE `$$` (
          `id` bigint(20) unsigned NOT NULL,
          `uid` int(10) unsigned NOT NULL DEFAULT '0',
          `hb` bigint(20) NOT NULL DEFAULT '0',
          `channel` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '平台内部消费渠道，如改名：1',
          `otid` int(10) unsigned NOT NULL DEFAULT '0',
          `desc` blob NOT NULL,
          `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `uid` (`uid`),
          KEY `channel` (`channel`),
          KEY `otid` (`otid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1",
    'hpf_innerRechargeRecord_' => 
    " CREATE TABLE `$$` (
          `id` bigint(20) unsigned NOT NULL,
          `uid` int(10) unsigned NOT NULL DEFAULT '0',
          `hb` bigint(20) NOT NULL DEFAULT '0',
          `hd` bigint(20) NOT NULL DEFAULT '0',
          `gb` bigint(20) NOT NULL DEFAULT '0',
          `gd` bigint(20) NOT NULL DEFAULT '0',
          `channel` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内部发放渠道',
          `otid` bigint(20) unsigned NOT NULL DEFAULT '0',
          `desc` blob NOT NULL,
          `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `uid` (`uid`),
          KEY `channel` (`channel`),
          KEY `otid` (`otid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1",
    'hpf_rechargeRecord_' => 
    "CREATE TABLE `$$` (
          `id` bigint(20) unsigned NOT NULL,
          `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
          `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `paytime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
          `uid` int(10) unsigned NOT NULL DEFAULT '0',
          `rmb` bigint(20) NOT NULL DEFAULT '0',
          `hb` bigint(20) NOT NULL DEFAULT '0',
          `desc` blob NOT NULL,
          `client` varchar(10) NOT NULL DEFAULT '' COMMENT 'web/android/ios',
          `refer_url` varchar(100) NOT NULL DEFAULT '' COMMENT '订单来路',
          `ip` int(10) unsigned NOT NULL DEFAULT '0',
          `port` int(10) unsigned NOT NULL DEFAULT '0',
          `thrid_order_id` varchar(100) NOT NULL DEFAULT '',
          `thrid_buyer_id` varchar(100) NOT NULL DEFAULT '',
          `promotionID` int(10) unsigned NOT NULL DEFAULT '0',
          `channel` varchar(10) NOT NULL DEFAULT '' COMMENT 'wechat/alipay',
          `otid` bigint(20) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY (`id`),
          UNIQUE KEY `thrid_order_id` (`thrid_order_id`,`channel`),
          KEY `channel` (`channel`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1",
    'hpf_sendBeanRecord_' => 
    " CREATE TABLE `$$` (
          `id` bigint(20) unsigned NOT NULL,
          `suid` int(10) unsigned NOT NULL DEFAULT '0',
          `ruid` int(10) unsigned NOT NULL DEFAULT '0',
          `hd` bigint(20) NOT NULL DEFAULT '0',
          `gd` bigint(20) NOT NULL DEFAULT '0',
          `desc` blob NOT NULL,
          `otid` bigint(20) unsigned NOT NULL DEFAULT '0',
          `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `suid` (`suid`,`ruid`),
          KEY `otid` (`otid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1",
    'hpf_statement_' => 
    "CREATE TABLE `$$` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `uid` int(10) unsigned NOT NULL DEFAULT '0',
          `hb` bigint(20) NOT NULL DEFAULT '0' COMMENT '欢朋币余额',
          `gb` bigint(20) NOT NULL DEFAULT '0' COMMENT '金币余额',
          `hd` bigint(20) NOT NULL DEFAULT '0' COMMENT '欢朋豆余额',
          `gd` bigint(20) NOT NULL DEFAULT '0' COMMENT '金豆余额',
          `hbd` bigint(20) NOT NULL DEFAULT '0' COMMENT '欢朋币差值+-',
          `gbd` bigint(20) NOT NULL DEFAULT '0' COMMENT '金币差值+-',
          `hdd` bigint(20) NOT NULL DEFAULT '0' COMMENT '欢朋豆差值+-',
          `gdd` bigint(20) NOT NULL DEFAULT '0' COMMENT '金豆差值+-',
          `tid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '对账单ID',
          `type` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '账目类型 1:送礼，2:充值，3:金币兑换欢朋币，4:金豆兑换成金币，5:提现，6:内部发放',
          `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `tid` (`tid`,`type`),
          KEY `uid` (`uid`),
          KEY `ctime` (`ctime`)
    ) ENGINE=InnoDB AUTO_INCREMENT=3116 DEFAULT CHARSET=latin1",
    'hpf_withdrawRecord_' => 
    "CREATE TABLE `$$` (
          `id` bigint(20) unsigned NOT NULL,
          `uid` int(10) unsigned NOT NULL DEFAULT '0',
          `gb` bigint(20) NOT NULL DEFAULT '0',
          `rmb` bigint(20) NOT NULL DEFAULT '0',
          `otid` bigint(20) unsigned NOT NULL DEFAULT '0',
          `desc` blob NOT NULL,
          `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `type` int(10) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY (`id`),
          KEY `uid` (`uid`),
          KEY `otid` (`otid`),
          KEY `status` (`type`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1",
    'iap_record_' => 
    " CREATE TABLE `$$` (
          `id` bigint(20) unsigned NOT NULL,
          `uid` int(10) unsigned NOT NULL DEFAULT '0',
          `app_item_id` bigint(20) unsigned NOT NULL DEFAULT '0',
          `item_id` bigint(20) unsigned NOT NULL DEFAULT '0',
          `product_id` varchar(100) NOT NULL DEFAULT '',
          `quantity` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'dollar',
          `bvrs` varchar(20) NOT NULL DEFAULT '' COMMENT 'appversion',
          `bid` varchar(100) NOT NULL DEFAULT '' COMMENT 'APPBundleID',
          `version_external_identifier` varchar(32) NOT NULL DEFAULT '',
          `original_purchase_date_ms` bigint(20) unsigned NOT NULL DEFAULT '0',
          `purchase_date_ms` bigint(20) unsigned NOT NULL DEFAULT '0',
          `unique_vendor_identifier` varchar(100) NOT NULL DEFAULT '',
          `unique_identifier` varchar(100) NOT NULL DEFAULT '',
          `original_transaction_id` bigint(20) unsigned NOT NULL DEFAULT '0',
          `transaction_id` bigint(20) unsigned NOT NULL DEFAULT '0',
          `status` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '0:创建,10:验证成功,20:验证失败',
          `mix` char(32) NOT NULL DEFAULT '',
          `receipt` blob NOT NULL,
          `errorno` int(10) unsigned NOT NULL DEFAULT '0',
          `ftid` bigint(20) unsigned NOT NULL DEFAULT '0',
          `channelid` int(10) unsigned NOT NULL DEFAULT '0',
          `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          UNIQUE KEY `mix` (`mix`),
          KEY `ftid` (`ftid`),
          KEY `uid` (`uid`),
          KEY `channelid` (`channelid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1", 
    'hpf_sendGiftRecord_' =>
     "CREATE TABLE `$$` (
            `id` bigint(20) unsigned NOT NULL,
            `suid` int(10) unsigned NOT NULL DEFAULT '0',
            `ruid` int(10) unsigned NOT NULL DEFAULT '0',
            `hb` bigint(20) NOT NULL DEFAULT '0',
            `gb` bigint(20) NOT NULL DEFAULT '0',
            `desc` blob NOT NULL,
            `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `otid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '网站前台对照票据',
			`sendType` tinyint(3) unsigned not null DEFAULT 0 COMMENT '送礼类型 0:普通送礼，1:背包礼物',
            PRIMARY KEY (`id`),
            KEY `suid` (`suid`,`ruid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1"
]; 

 