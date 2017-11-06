<?php

require_once (dirname(__DIR__) .DIRECTORY_SEPARATOR ."BaseSql.php");

class addGiftField  extends BaseSql{


    public $limitNum = 1;
   
    public function up() {
       
        $sqls = <<<EOF
                ALTER TABLE hpf_sendGiftRecord_template ADD sendType tinyint(3) unsigned not null DEFAULT 0 COMMENT '送礼类型 0:普通送礼，1:背包礼物';
ALTER TABLE hpf_sendGiftRecord_201709 ADD sendType tinyint(3) unsigned not null DEFAULT 0 COMMENT '送礼类型 0:普通送礼，1:背包礼物';
              
ALTER TABLE giftrecordcoin_201709 ADD sendType tinyint(3) unsigned not null DEFAULT 0 COMMENT '送礼类型 0:普通送礼，1:背包礼物';
ALTER TABLE giftrecordcoin_201709 ADD packid BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '背包礼物记录ID';​
alter table gift add thumb_poster varchar(255) not null default '' comment'送礼礼物展示图片';
alter table gift add thumb_poster_3x varchar(255) not null default '' comment'送礼礼物展示图片';
EOF;

        return $this->dbexec($sqls);
    }

}
