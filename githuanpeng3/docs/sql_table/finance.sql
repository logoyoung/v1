###用户余额表
CREATE TABLE IF NOT EXISTS `hpf_balance` (
  `uid`   INT(10) UNSIGNED  NOT NULL DEFAULT 0,
  `hb`    BIGINT(20) SIGNED NOT NULL DEFAULT 0,
  `gb`    BIGINT(20) SIGNED NOT NULL DEFAULT 0,
  `hd`    BIGINT(20) SIGNED NOT NULL DEFAULT 0,
  `gd`    BIGINT(20) SIGNED NOT NULL DEFAULT 0,
  `ctime` TIMESTAMP         NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`uid`)
);

###流水表（余额表）
CREATE TABLE IF NOT EXISTS `hpf_statement_template` (
  `id`    BIGINT(20) UNSIGNED NOT NULL            AUTO_INCREMENT,
  `uid`   INT(10) UNSIGNED    NOT NULL            DEFAULT 0,
  `hb`    BIGINT(20) SIGNED   NOT NULL            DEFAULT 0
  COMMENT '欢朋币余额',
  `gb`    BIGINT(20) SIGNED   NOT NULL            DEFAULT 0
  COMMENT '金币余额',
  `hd`    BIGINT(20) SIGNED   NOT NULL            DEFAULT 0
  COMMENT '欢朋豆余额',
  `gd`    BIGINT(20) SIGNED   NOT NULL            DEFAULT 0
  COMMENT '金豆余额',
  `hbd`   BIGINT(20) SIGNED   NOT NULL            DEFAULT 0
  COMMENT '欢朋币差值+-',
  `gbd`   BIGINT(20) SIGNED   NOT NULL            DEFAULT 0
  COMMENT '金币差值+-',
  `hdd`   BIGINT(20) SIGNED   NOT NULL            DEFAULT 0
  COMMENT '欢朋豆差值+-',
  `gdd`   BIGINT(20) SIGNED   NOT NULL            DEFAULT 0
  COMMENT '金豆差值+-',
  `tid`   BIGINT(20) UNSIGNED NOT NULL            DEFAULT 0
  COMMENT '对账单ID',
  `type`  INT(10) UNSIGNED    NOT NULL            DEFAULT 0
  COMMENT '账目类型 1:送礼，2:充值，3:金币兑换欢朋币，4:金豆兑换成金币，5:提现，6:内部发放',
  `ctime` TIMESTAMP           NOT NULL            DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY (`tid`, `type`),
  KEY (`uid`),
  KEY (`ctime`)
);

###汇率转换表
CREATE TABLE IF NOT EXISTS `hpf_rate` (
  `uid`   INT(10) UNSIGNED      NOT NULL       DEFAULT 0,
  `type`  INT(10) UNSIGNED      NOT NULL       DEFAULT 0
  COMMENT '转换类型 1:欢朋币=>金币,2:人民币=>欢朋币,3:金币=>欢朋币,4:金豆=>金币,5:金币=>人民币, 6:欢朋币=>欢朋币',
  `rate`  FLOAT(10, 3) UNSIGNED NOT NULL       DEFAULT 0.000,
  `ctime` TIMESTAMP             NOT NULL       DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (uid, type)
);

INSERT INTO hpf_rate (uid, type, rate)
VALUES (0, 1, 0.6), (0, 2, 10), (0, 3, 10), (0, 4, 1), (0, 5, 1), (0, 6, 1), (0, 7, 0.001), (0, 8, 1), (0, 9, 0.5);

###送礼记录表
CREATE TABLE IF NOT EXISTS hpf_sendGiftRecord_template (
  `id`    BIGINT(20) UNSIGNED NOT NULL,
  `suid`  INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `ruid`  INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `hb`    BIGINT(20) SIGNED   NOT NULL DEFAULT 0,
  `gb`    BIGINT(20) SIGNED   NOT NULL DEFAULT 0,
  `desc`  BLOB                NOT NULL DEFAULT '',
  `ctime` TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `otid`  BIGINT(20) UNSIGNED NOT NULL DEFAULT 0
  COMMENT '网站前台对照票据',
  PRIMARY KEY (`id`),
  KEY (`suid`, `ruid`)
);

###送豆记录表
CREATE TABLE IF NOT EXISTS hpf_sendBeanRecord_template (
  `id`    BIGINT(20) UNSIGNED NOT NULL,
  `suid`  INT(10) UNSIGNED    NOT NULL  DEFAULT 0,
  `ruid`  INT(10) UNSIGNED    NOT NULL  DEFAULT 0,
  `hd`    BIGINT(20) SIGNED   NOT NULL  DEFAULT 0,
  `gd`    BIGINT(20) SIGNED   NOT NULL  DEFAULT 0,
  `desc`  BLOB                NOT NULL  DEFAULT '',
  `otid`  BIGINT(20) UNSIGNED NOT NULL  DEFAULT 0,
  `ctime` TIMESTAMP           NOT NULL  DEFAULT current_timestamp,
  PRIMARY KEY (`id`),
  KEY (`suid`, `ruid`),
  KEY (`otid`)
);

###充值记录表
CREATE TABLE IF NOT EXISTS hpf_rechargeRecord_template (
  `id`             BIGINT(20) UNSIGNED NOT NULL,
  `status`         TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `ctime`          TIMESTAMP           NOT NULL DEFAULT current_timestamp,
  `paytime`        TIMESTAMP           NOT NULL DEFAULT '0000-00-00 00:00:00',
  `uid`            INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `rmb`            BIGINT(20) SIGNED   NOT NULL DEFAULT 0,
  `hb`             BIGINT(20) SIGNED   NOT NULL DEFAULT 0,
  `desc`           BLOB                NOT NULL DEFAULT '',
  `client`         VARCHAR(10)         NOT NULL DEFAULT ''
  COMMENT 'web/android/ios',
  `refer_url`      VARCHAR(100)        NOT NULL DEFAULT ''
  COMMENT '订单来路',
  `ip`             INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `port`           INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `thrid_order_id` VARCHAR(100)        NOT NULL DEFAULT '',
  `thrid_buyer_id` VARCHAR(100)        NOT NULL DEFAULT '',
  `promotionID`    INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `channel`        VARCHAR(10)         NOT NULL DEFAULT ''
  COMMENT 'wechat/alipay',
  `otid`           BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE (`thrid_order_id`, `channel`),
  KEY (`channel`)
);

###金币兑换欢朋币
CREATE TABLE IF NOT EXISTS hpf_exchangeRecord_template (
  `id`    BIGINT(20) UNSIGNED NOT NULL,
  `uid`   INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `gb`    BIGINT(20) SIGNED   NOT NULL DEFAULT 0,
  `hb`    BIGINT(20) SIGNED   NOT NULL DEFAULT 0,
  `desc`  BLOB                NOT NULL DEFAULT '',
  `otid`  BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
  `ctime` TIMESTAMP           NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`),
  KEY (`uid`),
  KEY (`otid`)
);

###金豆兑换金币
CREATE TABLE IF NOT EXISTS hpf_beanToGBRecord_template (
  `id`    BIGINT(20) UNSIGNED NOT NULL,
  `uid`   INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `gd`    BIGINT(20) SIGNED   NOT NULL DEFAULT 0,
  `gb`    BIGINT(20) SIGNED   NOT NULL DEFAULT 0,
  `desc`  BLOB                NOT NULL DEFAULT '',
  `otid`  BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
  `ctime` TIMESTAMP           NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`),
  KEY (`uid`),
  KEY (`otid`)
);

###提现
CREATE TABLE IF NOT EXISTS hpf_withdrawRecord_template (
  `id`     BIGINT(20) UNSIGNED NOT NULL,
  `uid`    INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `gb`     BIGINT(20) SIGNED   NOT NULL DEFAULT 0,
  `rmb`    BIGINT(20) SIGNED   NOT NULL DEFAULT 0,
  `otid`   BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
  `desc`   BLOB                NOT NULL DEFAULT '',
  `ctime`  TIMESTAMP           NOT NULL DEFAULT current_timestamp,
  `type` INT(10) UNSIGNED    NOT NULL DEFAULT 0 COMMENT '1:提现，5:退款',
  PRIMARY KEY (`id`),
  KEY (`uid`),
  KEY (`otid`),
  KEY (`status`)
);

###ALTER TABLE hpf_withdrawRecord_template ADD `utime`  TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00';

###修改体现状态记录表
###CREATE TABLE IF NOT EXISTS hpf_changeWithdrawStatusRecord_template (
###  `id`         BIGINT(20) UNSIGNED NOT NULL,
###  `withdrawID` INT(10) UNSIGNED    NOT NULL DEFAULT 0,
###   `status`     INT(10) UNSIGNED    NOT NULL DEFAULT 0,
###   `otid`       BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
###   `result`     INT(10) SIGNED      NOT NULL DEFAULT 0,
###   `ctime`      TIMESTAMP           NOT NULL DEFAULT current_timestamp,
###   PRIMARY KEY (`id`),
###   KEY (`status`),
###   KEY (`otid`)
### );

###内部充值表
CREATE TABLE IF NOT EXISTS hpf_innerRechargeRecord_template (
  `id`      BIGINT(20) UNSIGNED NOT NULL,
  `uid`     INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `hb`      BIGINT(20) SIGNED   NOT NULL DEFAULT 0,
  `hd`      BIGINT(20) SIGNED   NOT NULL DEFAULT 0,
  `gb`      BIGINT(20) SIGNED   NOT NULL DEFAULT 0,
  `gd`      BIGINT(20) SIGNED   NOT NULL DEFAULT 0,
  `channel` INT(10) UNSIGNED    NOT NULL DEFAULT 0
  COMMENT '内部发放渠道',
  `otid`    BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
  `desc`    BLOB                NOT NULL DEFAULT '',
  `ctime`   TIMESTAMP           NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`),
  KEY (`uid`),
  KEY (`channel`),
  KEY (`otid`)
);

###内部消耗欢朋币记录表
CREATE TABLE IF NOT EXISTS hpf_innerCostHBRecord_template (
  `id`      BIGINT(20) UNSIGNED NOT NULL,
  `uid`     INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `hb`      BIGINT(20) SIGNED   NOT NULL DEFAULT 0,
  `channel` INT(10) UNSIGNED    NOT NULL DEFAULT 0
  COMMENT '平台内部消费渠道，如改名：1',
  `otid`    INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `desc`    BLOB                NOT NULL DEFAULT '',
  `ctime`   TIMESTAMP           NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`),
  KEY (`uid`),
  KEY (`channel`),
  KEY (`otid`)
);

###用户获取欢朋豆记录表
CREATE TABLE IF NOT EXISTS hpf_getHDRecord_template (
  `id`      BIGINT(20) UNSIGNED NOT NULL,
  `uid`     INT(10) UNSIGNED    NOT NULL      DEFAULT 0,
  `hd`      BIGINT(20) SIGNED   NOT NULL      DEFAULT 0,
  `channel` INT(10) UNSIGNED    NOT NULL      DEFAULT 0
  COMMENT '获取渠道，1:到时领取，2:宝箱领取',
  `otid`    BIGINT(20) UNSIGNED NOT NULL      DEFAULT 0,
  `desc`    BLOB                NOT NULL      DEFAULT '',
  `ctime`   TIMESTAMP           NOT NULL      DEFAULT current_timestamp,
  PRIMARY KEY (`id`),
  KEY (`uid`),
  KEY (`channel`),
  KEY (`otid`)
);

CREATE TABLE IF NOT EXISTS hpf_changeRateRecord_template (
  `id`      BIGINT(20) UNSIGNED   NOT NULL AUTO_INCREMENT,
  `uid`     INT(10) UNSIGNED      NOT NULL DEFAULT 0,
  `rate`    FLOAT(10, 3) UNSIGNED NOT NULL DEFAULT 0.000,
  `rateNew` FLOAT(10, 3) UNSIGNED NOT NULL DEFAULT 0.000,
  `desc`    BLOB                  NOT NULL DEFAULT '',
  `type`    INT(20) UNSIGNED      NOT NULL DEFAULT 0,
  `ip`      BIGINT(20) UNSIGNED   NOT NULL DEFAULT 0,
  `port`    INT(10) UNSIGNED      NOT NULL DEFAULT 0,
  `ctime`   TIMESTAMP             NOT NULL DEFAULT current_timestamp,
  `otid`    BIGINT(20)            NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY (`uid`),
  KEY (`otid`),
  KEY (`type`)
);

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

CREATE TABLE IF NOT EXISTS tmp_user_balance (
  `uid`  INT(10) NOT NULL DEFAULT 0,
  `bean` INT(10) NOT NULL DEFAULT 0,
  `coin` INT(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`uid`)
);

CREATE TABLE IF NOT EXISTS tmp_anchor_balance (
  `uid`  INT(10) NOT NULL DEFAULT 0,
  `bean` INT(10) NOT NULL DEFAULT 0,
  `coin` INT(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`uid`)
);

CREATE TABLE IF NOT EXISTS iap_product_info (
  `item_id`       BIGINT(20) UNSIGNED     NOT NULL DEFAULT 0,
  `app_item_id`   BIGINT(20) UNSIGNED     NOT NULL DEFAULT 0,
  `product_id`    VARCHAR(100)            NOT NULL DEFAULT '',
  `hpcoin_amount` INT(10) UNSIGNED        NOT NULL DEFAULT '',
  `cash_amount`   NUMERIC(13, 3) UNSIGNED NOT NULL DEFAULT '',
  `bid`           VARCHAR(100)            NOT NULL DEFAULT '',
  `channel_id`    INT(10) UNSIGNED        NOT NULL DEFAULT 0,
  PRIMARY KEY (`item_id`)
);

CREATE TABLE IF NOT EXISTS iap_record_template (
  `id`                          BIGINT(20) UNSIGNED NOT NULL,
  `uid`                         INT(10) UNSIGNED    NOT NULL         DEFAULT 0,
  `app_item_id`                 BIGINT(20) UNSIGNED NOT NULL         DEFAULT 0,
  `item_id`                     BIGINT(20) UNSIGNED NOT NULL         DEFAULT 0,
  `product_id`                  VARCHAR(100)        NOT NULL         DEFAULT '',
  `quantity`                    BIGINT(20) UNSIGNED NOT NULL         DEFAULT 0
  COMMENT 'dollar',
  `bvrs`                        VARCHAR(20)         NOT NULL         DEFAULT ''
  COMMENT 'appversion',
  `bid`                         VARCHAR(100)        NOT NULL         DEFAULT ''
  COMMENT 'APPBundleID',
  `version_external_identifier` VARCHAR(32)         NOT NULL         DEFAULT '',
  `original_purchase_date_ms`   BIGINT(20) UNSIGNED NOT NULL         DEFAULT 0,
  `purchase_date_ms`            BIGINT(20) UNSIGNED NOT NULL         DEFAULT 0,
  `unique_vendor_identifier`    VARCHAR(100)        NOT NULL         DEFAULT '',
  `unique_identifier`           VARCHAR(100)        NOT NULL         DEFAULT '',
  `original_transaction_id`     BIGINT(20) UNSIGNED NOT NULL         DEFAULT 0,
  `transaction_id`              BIGINT(20) UNSIGNED NOT NULL         DEFAULT 0,
  `status`                      INT(10) UNSIGNED    NOT NULL         DEFAULT 0
  COMMENT '0:创建,10:验证成功,20:验证失败',
  `mix`                         CHAR(32)            NOT NULL         DEFAULT '',
  `receipt`                     BLOB                NOT NULL         DEFAULT '',
  `errorno`                     INT(10) UNSIGNED    NOT NULL         DEFAULT 0,
  `ftid`                        BIGINT(20) UNSIGNED NOT NULL         DEFAULT 0,
  `channelid`                   INT(10) UNSIGNED    NOT NULL         DEFAULT 0,
  `ctime`                       TIMESTAMP           NOT NULL         DEFAULT current_timestamp,
  PRIMARY KEY (`id`),
  UNIQUE (`mix`),
  KEY (`ftid`),
  KEY (`uid`),
  KEY (`channelid`)
);

CREATE TABLE IF NOT EXISTS iap_handle_table_record (
  `name`   VARCHAR(20) NOT NULL DEFAULT '',
  `status` INT(10)     NOT NULL DEFAULT 0,
  PRIMARY KEY (`name`),
  KEY (`status`)
);


