<?php

/**
 * Created by PhpStorm.
 * User: xingwei
 * Date: 2017/6/6
 * Time: 11:56
 */

namespace lib\due;

use system\DbHelper;

/**
 * 约玩评论表类
 * Class DueComment
 * @package lib\due
 */
class DueComment {

    //db 配置文件的key
    public static $dbConfName = 'huanpeng';
    private $uid = null;
    private $_db = null;
    public $param = [];

    /**
     * 审核方式  1 先审核后展示,2先展示后审核
     */
    const COMMENT_AUDIT_MODEL_CHOICE = 2;
    /**
     * 审核方式:先审核后展示
     */
    const COMMENT_AUDIT_MODEL_AUDIT_FIRST = 1;
     /**
     * 审核方式:先展示后审核
     */
    const COMMENT_AUDIT_MODEL_SHOW_FIRST = 2;

    public static $showStatus = [
        self::COMMENT_AUDIT_MODEL_AUDIT_FIRST => [
            self::ADD_COMMENT_STATUS_MACHINE_AUDIT_PASS,
            self::ADD_COMMENT_STATUS_MANUAL_APPROVAL_PASS,
        ],
        self::COMMENT_AUDIT_MODEL_SHOW_FIRST => [
            self::ADD_COMMENT_STATUS_TO_AUDIT,
            self::ADD_COMMENT_STATUS_MACHINE_AUDIT_PASS,
            self::ADD_COMMENT_STATUS_MANUAL_APPROVAL_PASS,
        ]
    ];

    //定义新增或更新资质审核状态 审核状态 -1,审核中.1,机器审核通过.2,人工审核通过.3,机器审核未通过 4,人工审核未通过
    const ADD_COMMENT_STATUS_TO_AUDIT = '-1';
    const ADD_COMMENT_STATUS_MACHINE_AUDIT_PASS = '1';
    const ADD_COMMENT_STATUS_MANUAL_APPROVAL_PASS = '2';
    const ADD_COMMENT_STATUS_MACHINE_AUDIT_NOT_PASS = '3';
    const ADD_COMMENT_STATUS_MANUAL_APPROVAL_NOT_PASS = '4';

    /**
     * 初始化类
     * @param $uid
     * @param string $db
     */
    public function __construct($uid = 0, $db = '') {
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
        return 'due_comment';
    }
    
    
    public function getShowStatus() {
       return self::$showStatus[self::COMMENT_AUDIT_MODEL_CHOICE];
    }
    

    public function fiterStatus($AND = true) {
        $status = implode(',', $this->getShowStatus());
        if ($AND) {
            //机器审核通过1 人工审核通过2
            $fiter = " AND `status` IN ({$status})";
        } else {
            $fiter = " WHERE  `status` IN ({$status})";
        }

        return $fiter;
    }

    /**
     * 增加评论
     * @param $data $data['order_id'],$data['cert_uid'],$data['skill_id'],$data['star'],$data['tag_ids'],$data['comment'] 必须
     * @return bool
     */
    public function addComment($data) {
        $this->param = $data;
        $table = $this->tableName();
        $sql = "INSERT INTO `{$table}`(`order_id`,`uid`,`cert_uid`,`skill_id`,`star`,`tag_ids`,`comment`,`status`) VALUES(:order_id,:uid,:cert_uid,:skill_id,:star,:tag_ids,:comment,:status)";
        //参数绑定
        $bdParam = [
            'order_id' => $this->param['order_id'],
            'uid' => $this->param['uid'],
            'cert_uid' => $this->param['cert_uid'],
            'skill_id' => $this->param['skill_id'],
            'star' => $this->param['star'],
            'tag_ids' => $this->param['tag_ids'],
            'comment' => $this->param['comment'],
            'status' => self::ADD_COMMENT_STATUS_TO_AUDIT,
        ];
        try {

            $result = $this->_db->execute($sql, $bdParam);
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 查询用户评论的内容
     * @param $uid @主播id
     * @param int $orderId @订单
     * @return array|\PDOStatement
     */
    public function getUserCommentByUid($uid, $orderId = 0) {
        $table = $this->tableName();
        //参数绑定
        $bdParam = [
            'uid' => $uid,
        ];
        //查询主播约玩资质
        if ($orderId > 0) {
            $addwhere = 'AND  `order_id`=:order_id ';
            $bdParam['order_id'] = $orderId;
        }
        $sql = "SELECT   `id`,`order_id`,`uid`,`cert_uid`,`skill_id`,`star`,`tag_ids`,`comment`,`ctime`,`status` FROM `{$table}` WHERE `uid` = :uid {$this->fiterStatus()}  {$addwhere} ORDER BY id";
        try {
            return $this->_db->query($sql, $bdParam);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * 查询主播所有评论按skill id
     * @param $data $data['skill_id']
     * @return bool|\PDOStatement
     */
    public function getCommentBySkillId($data) {
        $skill_id = $data['skillId'];
        $page = isset($data['page']) ? $data['page'] : '1';
        $pageSize = isset($data['pageSize']) ? $data['pageSize'] : '5';
        $page = ($page - 1) * $pageSize;
        $table = $this->tableName();
        //查询主播约玩资质
        $sql = "SELECT   `id`,`order_id`,`uid`,`cert_uid`,`skill_id`,`star`,`tag_ids`,`comment`,`ctime`,`status` FROM `{$table}` WHERE `skill_id` = :skill_id {$this->fiterStatus()} ORDER BY id DESC LIMIT :page ,:pageSize";
        //参数绑定
        $bdParam = [
            'skill_id' => $skill_id,
            'page' => $page,
            'pageSize' => $pageSize,
        ];

        try {
            return $this->_db->query($sql, $bdParam);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 获取评论数 星级总数 按skill_id 算平均分用
     * @return array|bool
     */
    public function getTotal($skill_id) {
        $table = $this->tableName();
        //查询主播约玩资质
        $sql = "SELECT  count(`id`) as num,sum(`star`) as total FROM `{$table}` WHERE `skill_id` = :skill_id {$this->fiterStatus()} ORDER BY id";
        //参数绑定
        $bdParam = [
            'skill_id' => $skill_id,
        ];
        try {
            $res = $this->_db->query($sql, $bdParam);
            return isset($res[0]) ? $res[0] : ['num' => 0, 'total' => 0];
        } catch (Exception $e) {

            return false;
        }
    }

    /**
     * 获取评论总数 分页用
     * @param $postData  $postData['skillId']
     * @return bool|\PDOStatement
     */
    public function getCommnetTotal($postData) {
        $table = $this->tableName();
        $skill_id = $postData['skillId'];
        //查询主播约玩资质
        $sql = "SELECT  count(`id`) as total  FROM `{$table}` WHERE `skill_id` = :skill_id {$this->fiterStatus()} ORDER BY id";
        //参数绑定
        $bdParam = [
            'skill_id' => $skill_id,
        ];
        try {
            $res = $this->_db->query($sql, $bdParam);
            return $res;
        } catch (Exception $e) {

            return false;
        }
    }

    /**
     * 获取所有被评论过得主播uids
     * --------------------
     * @author yalongSun<yalong2017@6.cn>
     * @param $page
     * @return array
     */
    public function getUserIdsByPage($page, $num) {
        $limit = $page * $num;
        $sql = "select DISTINCT `cert_uid` from `due_comment` {$this->fiterStatus(false)} limit {$limit},{$num}";
        $result = $this->_db->query($sql);
        return $result;
    }

    /**
     * 通过主播uids拉去评论信息
     * ------------------
     * @author yalongSun<yalong2017@6.cn>
     * @param $uids 主播uids 索引数组
     * @return array
     */
    public function getCommentsByUids(array $uids) {
        $uids = implode(",", $uids);
        $sql = "select `id`,`order_id`,`uid`,`cert_uid`,`skill_id`,`star`,`tag_ids`,`comment`,`ctime`,`utime`,`status` from `due_comment` where cert_uid in ({$uids}) {$this->fiterStatus()}";
        $result = $this->_db->query($sql);
        return $result;
    }

    /**
     * redis 将平均分更新到skill（临时处理，以后做脚本 redis维护）
     * ----------------------------------------------
     * @return bool
     */
    public function updateAvg($skill_id, $avg, $total_score, $num) {
        $sql = "update `due_skill` set `avg_score` = '" . $avg . "' , `total_score`='" . $total_score . "' , `comment_num`='" . $num . "' where id='" . $skill_id . "'";
        $result = $this->_db->query($sql);
        return $result;
    }

}
