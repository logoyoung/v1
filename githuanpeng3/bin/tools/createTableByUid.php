<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include __DIR__ . "/../../include/init.php";

use system\DbHelper;
use lib\pack\Backpack;

class createTableByUid {

    public $db = null;

    public function getDB() {
        if (is_null($this->db)) {
            $this->db = DbHelper::getInstance('huanpeng');
        }
        return $this->db;
    }

    public $maxUid = 0;
    public $tableMap = [
        #tableTemplate => limitNum     模板表 => 分隔大小
        'userpack_gift_template' => Backpack::TABLE_LIMIT_NUM,
    ];

    #1 获取用户id

    public function getMaxUid() {

        if ($this->maxUid > 0) {
            
        } else {

            $sql = "SELECT uid from `userstatic` order by uid desc limit 1; ";
            $res = $this->getDB()->query($sql);
            $this->maxUid = $res[0]['uid'] ?? 0;
        }
        return $this->maxUid;
    }

    #2 判断是否需要建表

    public function run() {
        $maxUid = $this->getMaxUid();
        $tableMap = $this->tableMap;
        $tables = array_keys($tableMap);
        $templates = $this->getTableTemplate();
        foreach ($templates as $value) {
            if (in_array($value, $tables)) {
                if ($tableMap[$value] - ( $maxUid % $tableMap[$value] ) > 6000) { // 希望有一天这里出错,那日注册量就很高了
                    continue;
                }
                $newTable = $this->getTable($this->maxUid, $value);
                $haveTables = $this->getTableTemplateSon($value);
                if (!in_array($newTable, $haveTables)) {
                    $this->createTable($value, $newTable);
                } else {
                    $this->createTable($value, $newTable);
                }
            }
        }
    }

    #3 判断表是否存在

    public function getTableTemplate() {
        $sql = "  show tables like '%template%' ";
        $list = $this->getDB()->query($sql);
        $res = [];
        foreach ($list as $one) {
            foreach ($one as $table) {
                $res[] = $table;
            }
        }
        return $res;
    }

    public function getTableTemplateSon($templateTableName) {

        $table = str_replace('template', '%', $templateTableName);
        $sql = "  show tables like '{$table}' ";
        $list = $this->getDB()->query($sql);
        $res = [];
        foreach ($list as $one) {
            foreach ($one as $table) {
                $res[] = $table;
            }
        }
        return $res;
    }

    public function getTable($uid, $templateName, $limit = 500000) {
        $name = str_replace('_template', '', $templateName);
        $table = sprintf("%s_%04d", $name, ceil($uid / $limit) + 1);
        return $table;
    }

    # 4 建表

    public function createTable($templateTable, $newTable) {
        $sql = "show create table {$templateTable}";
        $res = $this->getDB()->query($sql);
        $newSql = str_replace($templateTable, $newTable, $res[0]["Create Table"]);
        $newSql = str_replace("CREATE TABLE", "CREATE TABLE IF NOT EXISTS ", $newSql);
        return $this->getDB()->execute($newSql);
        write_log("建表:".$newTable , "createTableByUid");
    }

}

$m = new createTableByUid();
$m->run();
