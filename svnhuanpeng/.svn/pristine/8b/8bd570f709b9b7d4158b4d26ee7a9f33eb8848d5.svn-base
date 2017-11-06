ALTER TABLE giftrecord add `otid` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE giftrecordcoin add `otid` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE pickupHpbean add `otid` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE pickTreasure add `otid` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0;
###
ALTER TABLE pickTreasure add `tid` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0;
### 为通知表增加info字段
alter table live_pushmsg_list add `info` BLOB NOT NULL DEFAULT '';


update gift set giftname = "欢朋豆" where id=31;
update gift set money=100 where id=31;