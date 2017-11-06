<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace lib\activity;

use system\DbHelper;

class RegisterActivityTaskLib {
    ## status

    const REGISTER_ACTIVITY_TASK_LIB_STATUS_DEFAULT = '0';
    const REGISTER_ACTIVITY_TASK_LIB_STATUS_IN_QUEUE = '1';
    const REGISTER_ACTIVITY_TASK_LIB_STATUS_DONE = '2';

    public $db = null;

    public function getDb(): \system\MysqlConnection {
        if (is_null($this->db)) {
            $this->db = DbHelper::getInstance('huanpeng');
        }
        return $this->db;
    }

    public $table = 'activity_register_task';
    public $field = '`id` ,`uid` ,`todotype` ,`ctime` ,`utime` ,`status` ';

    /**
     * 数据写入
     * @param type $data
     *  $data = [
     *  'uid' => $data['uid'],<br />
     *  'todo' => $data['todo'],<br />
     *  'ctime' => $data['ctime'] ?? date("Y-m-d H:i:s"),<br />
     *  'status' => $data['status'] ?? 0,<br />
      ];
     */
    public function insertRowData($data) {
        $bind = [
            'uid'      => $data['uid'],
            'todotype' => $data['todotype'],
            'ctime'    => $data['ctime'] ?? date("Y-m-d H:i:s"),
            'status'   => $data['status'] ?? self::REGISTER_ACTIVITY_TASK_LIB_STATUS_DEFAULT,
        ];

        $sql = "INSERT INTO {$this->table}({$this->field})VALUE(NULL,:uid,:todotype,:ctime,'0000-00-00 00:00:00',:status) ";
        return $this->getDb()->execute($sql, $bind);
    }

    /**
     * 更新数据
     * @param array $id
     * @return type
     */
    public function updateDataById(array $id, $status = self::REGISTER_ACTIVITY_TASK_LIB_STATUS_IN_QUEUE) {
        $in = $this->getDb()->buildInPrepare($id);
        $bind = $id;
        $utime = date("Y-m-d H:i:s");
        $sql = "UPDATE {$this->table} SET `status`= {$status} ,`utime`= '{$utime}' WHERE `id` IN({$in}) ";
        return $this->getDb()->execute($sql, $bind);
    }

    /**
     * 更新数据
     * @param array $id
     * @return type
     */
    public function updateDataByUidAndType($uid, $type, $status = self::REGISTER_ACTIVITY_TASK_LIB_STATUS_IN_QUEUE) {
        $utime = date("Y-m-d H:i:s");
        $bind = [
            'uid'      => $uid,
            'todotype' => $type,
            'status'   => $status,
            'utime'    => $utime,
        ];

        $sql = "UPDATE {$this->table} SET `status`= :status ,`utime`= :utime WHERE `uid`=:uid AND `todotype`=:todotype ";
        return $this->getDb()->execute($sql, $bind);
    }

    public function getRowDataByStatus($status, $num = 30, $utimelimit = '') {
        $bind = [
            'status' => $status,
            'num'    => $num
        ];
        $where = '';
        if (!empty($utimelimit)) {
            $where = " AND `utime` < :utime ";
            $bind['utime'] = $utimelimit;
        }

        $sql = "SELECT {$this->field} FROM {$this->table} WHERE `status` = :status  {$where}  ORDER BY id ASC LIMIT :num  ";
        return $this->getDb()->query($sql, $bind);
    }

}
