<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace lib\pack;

use Exception;
use system\DbHelper;
use lib\pack\Backpack;

class BackpackLog {

    //活动类型
    const ACTIVITY_TYPE_DAY_EXCHANGE = 1;
    const ACTIVITY_TYPE_MONTH_EXCHANGE = 2;
    const ACTIVITY_TYPE_INVITE_ACTIVITY = 3;

    ### 物品 值
    const GOODS_HUANPENG_BEAN_100 = 1;
    const GOODS_HUANPENG_BEAN_200 = 2;
    const GOODS_STARTS = 3;
    const GOODS_WAVE = 4;
    const GOODS_SIX_SIX_SIX = 5;

    public $table = 'activity_goods_log';
    public $tableFiled = ' `id`, `otid`, `uid`,  `memo`,  `type`,  `sourceid`,  `goodsid`,  `num`,  `ctime` ';
    public $tablePrimary = '`id`';
    public $_dbConfig = 'huanpeng';
    public $_db = NULL;

    public function __construct() {
        
    }

    public function getDb(): \system\MysqlConnection {
        if (is_null($this->_db)) {
            $this->_db = DbHelper::getInstance($this->_dbConfig);
        }
        return $this->_db;
    }

    /**
     * 
     * @param type $data
     * @return boolean
     */
    public function addlog($data) {
        $sql = "INSERT INTO {$this->table}({$this->tableFiled})VALUE(NULL,:otid,:uid,:memo,:type, :sourceid, :goodsid, :num, :ctime);";
        $bindParam = [
            'otid'     => getOtid(),
            'uid'      => $data['uid'],
            'memo'     => $data['memo'],
            'type'     => $data['type'],
            'sourceid' => $data['sourceid'],
            'goodsid'  => $data['goodsid'],
            'num'      => $data['num'],
            'ctime'    => date("Y-m-d H:i:s"),
        ];
        foreach ($bindParam as $value) {
            if (empty($value)) {
                return FALSE;
            }
        }
        $res = $this->getDb()->execute($sql, $bindParam);
//        return $res;
        if ($res) {
            return $bindParam['otid'];
        }
    }

    /**
     * 
     * @param type $data
     * @return boolean
     */
    public function updateLogStatusByOtid($data) {
        $sql = "UPDATE  {$this->table}  SET `status`=:status WHERE `otid`=:otid LIMIT 1;";
        $bindParam = [
            'otid'   => $data['otid'],
            'status' => $data['status'],
        ];
        foreach ($bindParam as $value) {
            if (empty($value)) {
                return FALSE;
            }
        }
        return $this->getDb()->execute($sql, $bindParam);
    }

    /**
     * 
     * @param type $data
     * @return boolean
     */
    public function getRowDataByOtid($otid) {
        $sql = "SELECT {$this->tableFiled} FROM {$this->table} WHERE `otid` = :otid LIMIT 1 ";
        $bindParam = [
            'otid' => $otid
        ];
        return $this->getDb()->query($sql, $bindParam);
    }

    /**
     * 
     * @param type $data
     * @return boolean
     */
    public function getDataBySourceid($sourceid) {
        $sql = "SELECT {$this->tableFiled} FROM {$this->table} WHERE `sourceid` = :sourceid LIMIT 1 ";
        $bindParam = [
            'sourceid' => $sourceid
        ];
        return $this->getDb()->query($sql, $bindParam);
    }

}
