<?php

namespace lib\due;

use system\DbHelper;

/**
 * 
 * @encoding utf-8
 * @author liupeng@6.cn
 * @since 2017年06月02日11:58:34
 * @version 1.0
 * @Description 订单处理类
 */
class DueActivityConfig {

    public $db = null;
    public $redis = null;
    public static $dbConfig = 'huanpeng';
    public static $moneyField = ['discount', 'amount', 'real_amount', 'price', 'income'];

    const MULTIPLE = 1000;

    public function __construct() {
        $this->getDb();
    }

    /**
     * 获取数据库资源
     * @return type
     */
    public function getDb() {
        if (is_null($this->db)) {
            $this->db = DbHelper::getInstance(self::$dbConfig);
        }
        return $this->db;
    }

    private function _getDueActivityConfigTable() {
        return 'due_coupon_config';
    }

    /** 
     * 按活动Id获取活动配置
     * @param type $activityId
     * @return type
     */
    public function getConfig($id = 1) {
        $table = $this->_getDueActivityConfigTable();
        $fields = ' `config` ';
        $sql = "SELECT {$fields} FROM  {$table}   WHERE `id`=:id DESC LIMIT 1";
        $rows = $this->db->query($sql, ['id' => $id]);
        return isset($rows[0]) ? $rows[0] : [];
    }

    /**
     * 设置活动配置
     * @param type $activityId
     * @return type
     */
    public function setConfig($paramConfig, $paramId = 1) {
        $config = json_encode($paramConfig);
        $table = $this->_getDueActivityConfigTable();
        $fields = '`id`,`config`,`utime`';
        $sql = "INSERT INTO {$table} ({$fields}) VALUES(:id, :config,:utime) ON DUPLICATE KEY UPDATE `config`=:config2,`utime`=:utime2";
        $paramData = ['config' => $config, 'config2' => $config, 'utime' => date("Y-m-d H:i:s"), 'utime2' => date("Y-m-d H:i:s"), 'id' => $paramId];
        $res = $this->db->execute($sql, $paramData);
        return $res;
    }
    /**
     * 设置优惠券使用配置
     * @param type $activityId
     * @return type
     */
    public function setUseCouponConfig($paramConfig, $paramId = 2) {
        $config = json_encode($paramConfig);
        $table = $this->_getDueActivityConfigTable();
        $fields = '`id`,`config`,`utime`';
        $sql = "INSERT INTO {$table} ({$fields}) VALUES(:id, :config,:utime) ON DUPLICATE KEY UPDATE `config`=:config2,`utime`=:utime2";
        $paramData = ['config' => $config, 'config2' => $config, 'utime' => date("Y-m-d H:i:s"), 'utime2' => date("Y-m-d H:i:s"), 'id' => $paramId];
        $res = $this->db->execute($sql, $paramData);
        return $res;
    }

    /**
     * 进数据格式化
     * @param type $data
     */
    public static function dataInFormat(&$data) {
        $field = self::$moneyField;
        foreach ($data as $key => &$value) {
            if (is_array($value)) {
                self::dataInFormat($value);
            } else {
                if (in_array($key, $field)) {
                    $value = intval($value * self::MULTIPLE);
                }
            }
        }
    }

    /**
     * 出数据格式化
     * @param type $data
     */
    public static function dataOutFormat(&$data) {
        $field = self::$moneyField;
        foreach ($data as $key => &$value) {
            if (is_array($value)) {
                self::dataOutFormat($value);
            } else {
                if (in_array($key, $field)) {
                    $value = $value / self::MULTIPLE;
                }
            }
        }
    }

}
