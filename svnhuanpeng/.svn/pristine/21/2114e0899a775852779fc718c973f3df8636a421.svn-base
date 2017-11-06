<?php

/**
 * 申诉表操作
 * @encoding utf-8
 * @author liupeng@6.cn
 * @since
 * @version 1.0
 * @Description
 */

namespace lib\due;

use system\DbHelper;

/**
 * 约玩评论表类
 * Class DueComment
 * @package lib\due
 */
class DueAppeal {

    const APPEAL_STATUS_00_DEFAULT = 0;
    const APPEAL_STATUS_01_AGREE = 1; //同意 用户申请
    const APPEAL_STATUS_02_DISAGREE = 2; // 拒绝 用户申请
    const APPEAL_CLEAR_00_DEFAULT = 0; //默认还没有清算 
    const APPEAL_CLEAR_01_CLEAR = 1; //已经清算

    //db 配置文件的key

    public static $dbConfName = 'huanpeng';
    private $uid = null;
    private $_db = null;
    public $param = [];

    /**
     * 初始化类
     * @param $uid
     * @param string $db
     */
    public function __construct($uid = '', $db = '') {
        if ($uid) {
            $this->uid = (int) $uid;
        }
        if ($db) {
            $this->_db = $db;
        } else {
            $this->_db = DbHelper::getInstance(self::$dbConfName);
        }
        return true;
    }

    /**
     * 定义表名
     * @return string
     */
    public function tableName() {
        return 'due_order_appeal';
    }

    /**
     * 增加评论
     * @return bool
     */
    public function addAppeal($data) {
        $this->param = $data;
        $table = $this->tableName();
        $sql = "INSERT INTO `{$table}`( `uid`,`order_id`, `content`, `pic`) VALUES(:uid,:order_id,:content,:pic)";
        //参数绑定
        $bdParam = [
            'uid' => $this->param['uid'],
            'order_id' => $this->param['order_id'],
            'content' => $this->param['content'],
            'pic' => $this->param['pic'],
        ];
        try {

            $result = $this->_db->execute($sql, $bdParam);
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 增加评论
     * @return bool
     */
    public function updateAppealByOrderId($data) {
        $table = $this->tableName();
        $sql = "UPDATE  `{$table}`  SET `reply` = :reply  ,`status` =:status WHERE `order_id` =:order_id LIMIT 1 ";
        //参数绑定
        $bdParam = [
            'reply' => $data['reply'],
            'status' => $data['status'],
            'order_id' => $data['order_id'],
        ];
        try {

            $result = $this->_db->execute($sql, $bdParam);
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * get
     * @return array|bool
     */
    public function getAppealByOrderId($orderId) {
        $table = $this->tableName();
        $field = '`id`, `uid`,`order_id`, `content`,`reply`, `pic`, `ctime`, `utime`, `status`';
        $bindParam = ['order_id' => $orderId];



        $sql = "SELECT {$field} FROM `{$table}` WHERE `order_id`=:order_id ";
        try {
            $res = $this->_db->query($sql, $bindParam);
            return $res[0];
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 获取审核数据
     * @param type $status
     * @param type $clear
     * @return boolean
     */
    public function getAppealOrders($status = null, $clear = null ,$limit =100) {
        $table = $this->tableName();
        $field = '`id`, `uid`,`order_id`, `content`,`reply`, `pic`, `ctime`, `utime`, `status` ,`clear` ';
        $whereStr = " WHERE  1 ";
        $bindParam = [];
        if (!is_null($status)) {
            $where[] = " AND `status`=:status ";
            $bindParam['status'] = $status;
        }
        if (!is_null($clear)) {
            $where[] = " AND  `clear`=:clear ";
            $bindParam['clear'] = $clear;
        }
        if (!empty($where)) {
            $whereStr .= implode('', $where);
        }
        
        $sql = "SELECT {$field} FROM `{$table}` {$whereStr}   LIMIT {$limit}";
        try {
            $res = $this->_db->query($sql, $bindParam);
            return $res;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * 更新清算状态
     * @param type $orderIds
     * @return boolean
     */
    public function updateAppealOrders($orderIds) {
        if(empty($orderIds)){
            return false;
        }
        if (!is_array($orderIds)) {
            $orderIds = [intval($orderIds)];
        }
        $table = $this->tableName();
        //绑定占位符
        $in = $this->_db->buildInPrepare($orderIds);
        $count = count($orderIds);
        //查询主播约玩资质
        $sql = "UPDATE {$table}  SET `clear` = 1 WHERE `order_id` IN({$in}) LIMIT {$count} ";
        $bdParam = $orderIds;
        try {
            return $this->_db->query($sql, $bdParam);
        } catch (Exception $e) {
            return false;
        }
    }

}
