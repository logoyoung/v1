ALTER TABLE giftrecordcoin_201709 ADD sendType tinyint(3) unsigned not null DEFAULT 0 COMMENT '送礼类型 0:普通送礼，1:背包礼物';
ALTER TABLE giftrecordcoin_201709 ADD packid BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '背包礼物记录ID';