<?php

require_once (dirname(__DIR__) .DIRECTORY_SEPARATOR ."BaseSql.php");

class test  extends BaseSql{


    public $limitNum = 3;

    public function up() {
        return TRUE;
        
        /**
         * @todo 执行需要执行的sql
         */
        $sqls = <<<EOF
CREATE TABLE IF NOT EXISTS `database_test` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `testname` varchar(60) NOT NULL DEFAULT '' COMMENT '测试数据',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
INSERT INTO `database_test`(`testname`)VALUES('测试数据');
EOF;

        return $this->dbexec($sqls);
    }

}
