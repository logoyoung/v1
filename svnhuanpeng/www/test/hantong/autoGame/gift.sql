CREATE TABLE `gift` (
  `id`             INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `money`          INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `giftname`       VARCHAR(30)      NOT NULL DEFAULT '',
  `type`           INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `conversionrate` INT(10)          NOT NULL DEFAULT '0',
  `exp`            INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `bg`             VARCHAR(255)     NOT NULL DEFAULT '' COMMENT '移动端礼物连击背景图',
  `bg_3x`          VARCHAR(255)     NOT NULL DEFAULT '' COMMENT '移动端礼物连击背景图3x',
  `poster`         VARCHAR(255)     NOT NULL DEFAULT '' COMMENT '礼物图标',
  `poster_3x`      VARCHAR(255)     NOT NULL DEFAULT '' COMMENT '礼物图标3x',
  `desc`           VARCHAR(255)     NOT NULL DEFAULT ''
  COMMENT '礼物描述',
  `font_color`     VARCHAR(20)      NOT NULL DEFAULT ''
  COMMENT '移动端礼物连击文字颜色',
  `web_preview`    VARCHAR(255)     NOT NULL DEFAULT ''
  COMMENT '网站礼物预览图',
  `web_bg`         VARCHAR(255)     NOT NULL DEFAULT ''
  COMMENT '网站礼物连击背景图',
  `web_font_color` VARCHAR(20)      NOT NULL DEFAULT ''
  COMMENT '网站礼物连击文字颜色',
  `all_site_notify` TINYINT(3) NOT NULL DEFAULT 0 COMMENT'是否开启全站通知，0:关闭，1:开启',
  `combo_show_time` INT(10) NOT NULL DEFAULT 0 COMMENT '每条礼物的连击展示时间',
  PRIMARY KEY (`id`)
);


#功能上，模板类型不予添加，需要后台添加，只允许配置里面的规则
CREATE TABLE `gift_template`
(
  `id`          INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `config_id`   INT(10) UNSIGNED    NOT NULL DEFAULT 0,
  `type`        INT(10) UNSIGNED    NOT NULL DEFAULT 0
  COMMENT '模型模板类型',
  `name` VARCHAR(20) NOT NULL DEFAULT '',
  `conver_rule` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0
  COMMENT '覆盖策略，1:保留特殊策略配置，2:以此模板为基准，不读取子配置',
  PRIMARY KEY (`id`),
  UNIQUE KEY (`type`)
);


#如果当前配置在模板内，则不允许删除
CREATE TABLE gift_config (
  `id`         INT UNSIGNED        NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(255)        NOT NULL DEFAULT ''
  COMMENT '配置名称',
  `ctime`      TIMESTAMP           NOT NULL DEFAULT current_timestamp,
  `parent_type`  INT(10) UNSIGNED    NOT NULL DEFAULT '0' COMMENT '当前配置父类型',
  `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1:使用中，100:删除',
  PRIMARY KEY (`id`)
);

CREATE TABLE gift_config_detail
(
  `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `gift_id`   INT UNSIGNED NOT NULL DEFAULT 0,
  `config_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `num`       INT UNSIGNED NOT NULL DEFAULT 0,
  `ctime`     TIMESTAMP    NOT NULL DEFAULT current_timestamp,
  `order`     INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`config_id`, `gift_id`, `num`)
);


ALTER TABLE gift ADD `bg` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '移动端礼物礼物combo背景图';
ALTER TABLE gift ADD `bg_3x` VARCHAR(255) NOT NULL DEFAULT '移动端礼物连击背景图';
ALTER TABLE gift ADD `poster` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '礼物图片';
ALTER TABLE gift ADD `poster_3x` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE gift ADD `desc` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '礼物描述';
ALTER TABLE gift ADD `poster_3x` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE gift ADD `font_color` VARCHAR(20) NOT NULL DEFAULT '';
ALTER TABLE gift ADD `web_preview` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '网站礼物预览图';
ALTER TABLE gift ADD `web_bg` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '网站礼物combo背景图';
ALTER TABLE gift ADD `web_font_color` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '网站送礼连击字体颜色';

ALTER TABLE gift ADD `all_site_notify` TINYINT(3) NOT NULL DEFAULT 0 COMMENT '是否开启全站通知';
ALTER TABLE gift ADD `combo_show_time` INT(10) NOT NULL DEFAULT 0 COMMENT '每条连击展示时间';


INSERT INTO gift_template(`name`,`type`,`config_id`,`conver_rule`) VALUE('游戏直播基准模板','1','1','1');

INSERT INTO gift_config(`id`,`name`,`status`) VALUE(1,'默认配置',1);

INSERT INTO gift_config_detail(`gift_id`,config_id,num,`order`) VALUES(35,1,1,0),(34,1,1,1),(33,1,1,2),(36,1,1,3),(32,1,1,4),(31,1,520,5),(31,1,200,6),(31,1,100,7);