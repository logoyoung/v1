<?php
require_once (dirname(__DIR__) .DIRECTORY_SEPARATOR ."BaseSql.php");

class zhimaCert  extends BaseSql{


    public $limitNum = 1;

    public function up() {

        $sqls = <<<EOF
CREATE TABLE IF NOT EXISTS `zhima_cert` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '10身份证,20',
  `cert_name` varchar(32) DEFAULT NULL COMMENT '真实姓名',
  `cert_no` varchar(32) DEFAULT NULL COMMENT '证件名',
  `transaction_id` char(23) NOT NULL DEFAULT '' COMMENT '欢朋本次认证的唯一标识',
  `biz_code` varchar(20) NOT NULL DEFAULT '' COMMENT '认证场景码,FACE,SMART_FACE,FACE_SDK',
  `biz_no` varchar(128) NOT NULL COMMENT '芝麻本次认证的唯一标识,biz_no有效期为23小时',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1（初始化）,2(认证成功),3（认证失败）',
  `error_msg` varchar(200) NOT NULL DEFAULT '',
  `biz_etime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '回调有效期',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `utime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sid`),
  UNIQUE KEY `uniq_transaction_id` (`transaction_id`),
  KEY `idx_status_biz_etime` (`status`,`biz_etime`),
  KEY `idx_uid_certno_status` (`uid`,`cert_no`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
EOF;
        return $this->dbexec($sqls);
    }

}