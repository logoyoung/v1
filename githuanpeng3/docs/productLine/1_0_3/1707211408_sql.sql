### 个人充值次数 判断是否为首充
### UPDATE userstatic as us, (SELECT count(uid) as recharge_number,uid FROM hpf_rechargeRecord_201703 GROUP BY uid) as r SET us.recharge_number = us.recharge_number + r.recharge_number WHERE us.uid=r.uid;
### UPDATE userstatic as us, (SELECT count(uid) as recharge_number,uid FROM hpf_rechargeRecord_201704 GROUP BY uid) as r SET us.recharge_number = us.recharge_number + r.recharge_number WHERE us.uid=r.uid;
### UPDATE userstatic as us, (SELECT count(uid) as recharge_number,uid FROM hpf_rechargeRecord_201705 GROUP BY uid) as r SET us.recharge_number = us.recharge_number + r.recharge_number WHERE us.uid=r.uid;
### UPDATE userstatic as us, (SELECT count(uid) as recharge_number,uid FROM hpf_rechargeRecord_201706 GROUP BY uid) as r SET us.recharge_number = us.recharge_number + r.recharge_number WHERE us.uid=r.uid;
### UPDATE userstatic as us, (SELECT count(uid) as recharge_number,uid FROM hpf_rechargeRecord_201707 GROUP BY uid) as r SET us.recharge_number = us.recharge_number + r.recharge_number WHERE us.uid=r.uid;

### 为陪玩增加比率
INSERT INTO hpf_rate (uid,`type`,rate) VALUES (0,10,0.6);

###
INSERT INTO hpf_rate (uid,`type`,rate) SELECT uid,10,0.7 FROM anchor where cid !=0 and cid !=15 and anchor.cid NOT IN (SELECT id FROM company WHERE type=2);

CREATE TABLE IF NOT EXISTS hpf_guarantee_template (
  `id`               BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `uid`              INT UNSIGNED    NOT NULL DEFAULT 0,
  `tuid`             INT UNSIGNED    NOT NULL DEFAULT 0,
  `real_pay`         BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '实际付款金额',
  `pay`              BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单付款金额',
  `pay_coin_type`    INT UNSIGNED    NOT NULL DEFAULT 0,
  `income`           BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `income_coin_type` INT UNSIGNED    NOT NULL DEFAULT 0,
  `status`           INT UNSIGNED    NOT NULL DEFAULT 0
  COMMENT '1:订单创建，',
  `ctime`            TIMESTAMP       NOT NULL DEFAULT current_timestamp,
  `etime`            TIMESTAMP       NOT NULL DEFAULT '0000-00-00 00:00:00',
  `desc`             BLOB            NOT NULL DEFAULT '',
  `type` INT UNSIGNED NOT NULL DEFAULT 0,
  `otid`             BIGINT UNSIGNED NOT NULL DEFAULT 0,

  PRIMARY KEY (id),
  INDEX (`status`),
  INDEX (`uid`,`tuid`),
  INDEX (`tuid`)
);

CREATE TABLE IF NOT EXISTS hpf_guaranteeOrderLog_template
(
  `id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `orderid` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `s_status` INT UNSIGNED NOT NULL DEFAULT 0,
  `d_status` INT UNSIGNED NOT NULL DEFAULT 0,
  `handleuid` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作人ID',
  `handletid` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作ID',
  `handlegroup` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '1:系统，10:后台,20:后台',
  `desc` BLOB NOT NULL DEFAULT '' COMMENT '日志描述',
  `ctime` TIMESTAMP NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`),
  INDEX (`orderid`)
);

###订单状态越迁记录表
CREATE TABLE IF NOT EXISTS hpf_guaranteeCronOrderLog_template(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `orderid` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `status` INT(10) NOT NULL DEFAULT 0 COMMENT '目标状态',
  `action` INT(10) NOT NULL DEFAULT 0 COMMENT '目标动作',
  `runtime` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  `handleuid` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作人ID',
  `handletid` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作ID',
  `handlegroup` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '1:系统，10:后台,20:后台',
  `desc` BLOB NOT NULL DEFAULT '' COMMENT '日志描述',
  `ctime` TIMESTAMP NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`),
  INDEX (`orderid`),
  INDEX (`status`)
);

###担保订单处理表
CREATE TABLE IF NOT EXISTS hpf_guaranteeCronOrderHandle(
  `orderid` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `action` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '10:到账,20:退款',
  `ctime` TIMESTAMP NOT NULL DEFAULT current_timestamp,
  `runtime` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` INT NOT NULL DEFAULT 0 COMMENT '1:未完成，2:已完成',
  PRIMARY KEY (`orderid`),
  INDEX (`action`),
  INDEX (`status`)
);