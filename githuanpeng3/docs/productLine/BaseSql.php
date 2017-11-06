<?php

/**
 * 使用规则(详细请看此文件):
 * 1 类名 要和文件名去除日期后一致,如:0825_test.php   类名就是 test。
 * 2 设置可以执行的次数,设置$limitNum, 0是无限制
 * 3 $sqls 中添加sql语句
 */
class baseSql
{
    /**
     * 此SQL语句可以执行的次数
     * @var type
     */
    public $limitNum = 1;
    public $tool;

    public function __construct($tool) {
        $this->tool = $tool;
    }

    public function dbexec($sql) {
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

                
                
                
EOF;

        return $this->dbexec($sqls);
    }
}