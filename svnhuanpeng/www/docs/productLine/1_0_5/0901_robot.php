<?php

/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/9/4
 * Time: 11:33
 */

/**
 * 降低房间机器人等级SQL 随机降为1-3级
 * Class robot
 */
class robot
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
    public function up() {
        return TRUE;

        /**
         * @todo 执行需要执行的sql
         */
        $sqls = <<<EOF
UPDATE useractive SET `level` = floor(1+RAND()*3) WHERE uid IN (SELECT uid FROM userstatic WHERE username = 'hpRobot');
EOF;

        return $this->dbexec($sqls);
    }
}