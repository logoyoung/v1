<?php

namespace lib;

use DBHelperi_huanpeng;
use RedisHelp;

class Message {

    public $uid = null; //uid
    private $_db = null; //数据库对象
    private $_redis = null; //Redis对象

    const USER_MESSAGE_STATUS_00 = 0;
    const USER_MESSAGE_STATUS_01 = 1;

    /**
     * User constructor. 类初始化
     *
     * @param int    $uid 用户uid
     * @param object $db  数据库对象
     */
    public function __construct($uid = '', $db = '', $redis = '') {
        if ($uid) {
            $this->uid = (int) $uid;
        }
        if ($db) {
            $this->_db = $db;
        } else {
            $this->_db = new DBHelperi_huanpeng();
        }
        if ($redis) {
            $this->_redis = $redis;
        } else {
            $this->_redis = new RedisHelp();
        }
    }

    /**
     * 设置Uid
     * @param type $uid
     * @return type
     */
    public function setUid($uid) {
        $this->uid = $uid;
    }

    /**
     * 获取用户的消息
     * @param type $id    uid
     * @param type $limit  获取条数
     * @param type $page   页码
     * @param type $where  附加条件
     * @return array
     */
    public function getUserMessageByWhere($uid, $limit = 10, $page = 1, $where = []) {
        $uid = intval($uid) ? $uid : $this->uid;

        if ($uid > 0) {
            $page = $page >= 1 ? $page : 1;
            $limit = $limit >= 1 ? $limit : 10;
            $sqlWhere['uid'] = $uid;
            if (is_array($where) && !empty($where)) {
                $sqlWhere = array_merge($sqlWhere, $where);
            }
            $result = $this->_db->where($sqlWhere)->limit($page, $limit)->select('usermessage');
        } else {
            $result = [];
        }
        return $result;
    }

    /**
     * 获取用户信息的总数
     * 
     * @param type $id  uid
     * @param type $where  附件条件
     * @return int
     */
    public function getTotalById($uid, $where = []) {
        $uid = intval($uid) ? $uid : $this->uid;
        if ($uid > 0) {
            $sqlWhere['uid'] = $uid;
            if (is_array($where) && !empty($where)) {
                $sqlWhere = array_merge($sqlWhere, $where);
            }
            $result = $this->_db->field('count(*) as  total')->where($sqlWhere)->select('usermessage');
        }
        if ($result[0]['total'] > 0) {
            return $result[0]['total'];
        } else {
            return 0;
        }
    }

    /**
     * 获取消息的内容
     * @param type $ids
     * @return type
     */
    public function getMessageContentByIds($ids) {
        if (empty($ids)) {
            return [];
        }
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $result = [];
        if (count($ids) > 0) {
            $wherString = " `id` IN (" . implode(',', $ids) . ")";
            $result = $this->_db->where($wherString)->select('sysmessage');
        }
        return $result;
    }

}
