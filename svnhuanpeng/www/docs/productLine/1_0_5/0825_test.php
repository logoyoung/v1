<?php

/**
 * 使用规则
 * 1 类名 要和文件名去除日期后一致,如:0825_test.php   类名就是 test。
 * 2 设置可以执行的次数,设置$limitNum, 0是无限制
 * 3 $sqls 中添加sql语句
 */
class test {

    /**
     * 此SQL语句可以执行的次数
     * @var type 
     */
    public $limitNum = 3;
    private $tool;

    public function __construct($tool) {
        $this->tool = $tool;
    }

    private function dbexec($sql) {
        return $this->tool->execSql($sql);
    }

    /**
     * 待执行方法
     * @example $sqls中填写SQL语句,每句以分号分隔。
     * @return boolean
     */
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
INSERT INTO `database_test`(`testname`)VALUES('测试数据');
EOF;

        return $this->dbexec($sqls);
    }

}
