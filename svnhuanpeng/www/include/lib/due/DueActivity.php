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
class DueActivity {

    public $db = null;
    public $redis = null;
    public static $dbConfig = 'huanpeng';
    public static $moneyField = ['discount', 'amount', 'real_amount', 'price', 'income'];

    const MULTIPLE = 1000;
    const ACTIVITY_STATUS_00_UNUSE = 0;
    const ACTIVITY_STATUS_01_USE = 1;

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

    private function _getDueActivityTable() {
        return 'due_activity';
    }

    private function _getDueShareRecordTable() {
        return 'due_share_record';
    }

    /**
     * 检测是否存在活动
     * @param type $typeId  活动类型id
     */
    public function searchActivity($type, $time, $status = self::ACTIVITY_STATUS_01_USE) {
        $table = $this->_getDueActivityTable();
        $fields = '`aid`, `type`, `name`, `content`,`pic`, `configure`, `send_number`, `status`, `ctime`, `expire`, `stime`, `etime`, `utime`';
        $sql = "SELECT {$fields} FROM  {$table} WHERE type =:type  AND `status` =:status AND  `stime` <=:time1 AND `etime` >= :time2 ";
        $bindParam = [
            'type' => $type,
            'status' => $status,
            'time1' => $time,
            'time2' => $time
        ];
        $rows = $this->db->query($sql, $bindParam);
        return $rows;
    }

    /**
     * 获取活动信息
     * @param type $typeId  活动类型id
     */
    public function getActivityById($id, $status = self::ACTIVITY_STATUS_01_USE) {
        $table = $this->_getDueActivityTable();
        $fields = '`aid`, `type`, `name`, `content`,`pic`, `configure`, `send_number`, `status`, `ctime`, `expire`, `stime`, `etime`, `utime`';
        $sql = "SELECT {$fields} FROM  {$table} WHERE aid =:aid  AND `status` =:status ";
        $bindParam = [
            'aid' => $id,
            'status' => $status,
        ];
        $rows = $this->db->query($sql, $bindParam);
        return $rows;
    }

    /**
     * 获取数据
     * @param type $typeId
     * @param type $uid
     * @param type $sourceId
     * @param type $startTime
     * @param type $endTime
     * @return type
     */
    public function getShareRecord($typeId, $uid, $sourceId, $startTime, $endTime) {
        $table = $this->_getDueShareRecordTable();
        $fields = '`rid`, `aid`, `type`, `source_id`, `share_uuid`, `uid`, `configure`, `share_number`, `receive_number`, `status`, `ctime`, `stime`, `etime`';
        $sql = "SELECT {$fields} FROM  {$table} WHERE type =:type  AND `uid`=:uid AND `source_id` = :source_id AND `ctime` BETWEEN  :startTime  AND :endTime ";
        $bindParam = [
            'type' => $typeId,
            'uid' => $uid,
            'source_id' => $sourceId,
            'startTime' => $startTime,
            'endTime' => $endTime,
        ];
        $rows = $this->db->query($sql, $bindParam);
        return $rows;
    }

    /**
     * 获取分享记录数据
     * @param type $shareUuid
     * @return type
     */
    public function getShareRecordByUuid($shareUuid) {
        $table = $this->_getDueShareRecordTable();
        $fields = '`rid`, `aid`, `type`, `source_id`, `share_uuid`, `uid`, `configure`, `share_number`, `receive_number`, `status`, `ctime`, `stime`, `etime`';
        $sql = "SELECT {$fields} FROM  {$table} WHERE share_uuid =:share_uuid ";
        $bindParam = [
            'share_uuid' => $shareUuid
        ];
        $rows = $this->db->query($sql, $bindParam);
        return $rows;
    }

    /**
     * 获取数据
     * @param type $typeId
     * @param type $uid
     * @param type $sourceId
     * @param type $startTime
     * @param type $endTime
     * @return type
     */
    public function getShareRecordByTypeAndSourceId($typeId, $sourceId) {
        $table = $this->_getDueShareRecordTable();
        $fields = '`rid`, `aid`, `type`, `source_id`, `share_uuid`, `uid`, `configure`, `share_number`, `receive_number`, `status`, `ctime`, `stime`, `etime`';
        $sql = "SELECT {$fields} FROM  {$table} WHERE type =:type AND `source_id` = :source_id ";
        $bindParam = [
            'type' => $typeId,
            'source_id' => $sourceId,
        ];
        $rows = $this->db->query($sql, $bindParam);
        return $rows;
    }

    /**
     * 插入数据
     * @param type $data
     * @return boolean
     */
    public function insertShareRecord($data) {
        try {
            $field = "`aid`, `type`, `source_id`, `share_uuid`, `uid`, `configure`, `share_number`, `receive_number`, `status`, `stime`, `etime`";
            $result = $this->formatInsertData($data, $field);
            $table = $this->_getDueShareRecordTable();
            $sql = "INSERT INTO {$table}({$result['field']})VALUES({$result['value']})";
            $res = $this->db->execute($sql, $result['bind']);
            return $res;
        } catch (Exception $e) {
            return FALSE;
        }
    }

    /**
     * 插入数据
     * @param type $data
     * @return boolean
     */
    public function updateShareRecord($aid, $shareuid) {
        try {
            $table = $this->_getDueShareRecordTable();
            $sql = "UPDATE  {$table} SET `receive_number` = `receive_number` +1 WHERE aid=:aid AND share_uuid=:share_uuid LIMIT 1";
            $bindParam = [
                'aid' => $aid,
                'share_uuid' => $shareuid,
            ];
            $res = $this->db->execute($sql, $bindParam);
            return $res;
        } catch (Exception $e) {
            return FALSE;
        }
    }

    /**
     * 格式化
     * @param type $data
     * @return type
     */
    public function formatInsertData($dataParam, $fieldParam) {
        $fields = explode(',', str_replace('`', '', $fieldParam));

        foreach ($fields as $key) {
            $key = trim($key);
            $field[] = "`{$key}`";
            $value[] = ":{$key}";
            $bind[$key] = $dataParam[$key];
        }
        return ['field' => implode(',', $field), 'value' => implode(',', $value), 'bind' => $bind];
    }

}
