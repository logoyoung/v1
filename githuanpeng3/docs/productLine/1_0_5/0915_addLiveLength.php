<?php

/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/9/15
 * Time: 17:08
 */
class addLiveLength
{
    /**
     * 此SQL语句可以执行的次数
     * @var type
     */
    public $limitNum = 1;
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
    public function up()
    {

        /**
         * @todo 执行需要执行的sql
         */
        $sqls = <<<EOF
ALTER TABLE `live_length` ADD  `reward_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '奖励状态 0未奖励 1 1档奖励 5 2档奖励 10 3档奖励';
EOF;
        return $this->dbexec($sqls);
    }

}