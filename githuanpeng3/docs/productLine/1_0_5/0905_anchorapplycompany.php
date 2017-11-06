<?php

/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/9/5
 * Time: 15:05
 */
class anchorapplycompany
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
ALTER TABLE `anchorapplycompany` ADD  `gameid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '游戏id';
ALTER TABLE `anchorapplycompany` ADD  `gamelevel` varchar(255) DEFAULT NULL COMMENT '游戏等级';
ALTER TABLE `anchorapplycompany` ADD  `qq` bigint(16) unsigned DEFAULT NULL COMMENT 'qq号';
ALTER TABLE `anchorapplycompany` ADD  `showface` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否露脸直播 1为露脸 0为不露脸';
EOF;
        return $this->dbexec($sqls);
    }

}