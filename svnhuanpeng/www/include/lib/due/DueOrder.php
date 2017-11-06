<?php

namespace lib\due;

use system\DbHelper;
use service\due\DueCouponService;

/**
 * 
 * @encoding utf-8
 * @author liupeng@6.cn
 * @since 2017年06月02日11:58:34
 * @version 1.0
 * @Description 订单处理类
 */
class DueOrder {

    //订单正常状态
    const ORDER_STATUS_NEGATIVE_001_PAY_EXCEPTION = -1;
    const ORDER_STATUS_000_DEFAULT = 0;
    const ORDER_STATUS_010_CREATE_ORDER = 10;
    const ORDER_STATUS_020_TIMEOUT_CANCEL = 20;
    const ORDER_STATUS_030_USER_CANCEL = 30;
    const ORDER_STATUS_040_ANCHOR_REJECT_ORDER = 40;
    const ORDER_STATUS_050_ANCHOR_ACCEPTING_ORDER = 50;
    const ORDER_STATUS_060_ANCHOR_CANCEL = 60;
    const ORDER_STATUS_070_TIMEOUT_FINISHED = 70;
    const ORDER_STATUS_080_USER_FINISHED = 80;
    //订单纠纷状态
    const ORDER_STATUS_090_USER_BACK_ORDER = 90;
    const ORDER_STATUS_100_ANCHOR_AGREE_BACK = 100;
    const ORDER_STATUS_110_ANCHOR_DISAGREE_BACK = 110;
    const ORDER_STATUS_120_USER_APPEAL_CUSTOMER_SERVICE = 120;
    const ORDER_STATUS_130_CUSTOMER_SERVICE_AGREE = 130;
    const ORDER_STATUS_140_CUSTOMER_SERVICE_DISAGREE = 140;
    const ORDER_STATUS_150_TIMEOUT_OVER_FINISHED_ORDER = 150;
    const ORDER_STATUS_160_TIMEOUT_USER_BACK_ORDER = 160;
    const ORDER_STATUS_170_TIMEOUT_USER_NOT_APPEAL_CUSTOMER_SERVICE = 170;
    //订单结束不可再更改
    const ORDER_STATUS_1000_ORDER_END_AND_SETTLEMENT = 1000;
    const ORDER_STATUS_1010_ORDER_CANCELLED = 1010;
    const ORDER_STATUS_1020_ORDER_BACK = 1020;

    /**
     * 订单各状态详细描述(用户看的)【聊天顶部 用户查看的文案,接口字段 statusDesc】
     * @var type 
     */
    public static $order_status = [
        self::ORDER_STATUS_000_DEFAULT => '未知状态',
        self::ORDER_STATUS_010_CREATE_ORDER => '待接单',
        self::ORDER_STATUS_020_TIMEOUT_CANCEL => '主播未接单',
        self::ORDER_STATUS_030_USER_CANCEL => '用户取消订单',
        self::ORDER_STATUS_040_ANCHOR_REJECT_ORDER => '主播拒绝接单',
        self::ORDER_STATUS_060_ANCHOR_CANCEL => '主播取消订单',
        self::ORDER_STATUS_100_ANCHOR_AGREE_BACK => '主播同意退单',
        self::ORDER_STATUS_130_CUSTOMER_SERVICE_AGREE => '客服同意退单申请',
        self::ORDER_STATUS_050_ANCHOR_ACCEPTING_ORDER => '主播接单',
        self::ORDER_STATUS_070_TIMEOUT_FINISHED => '系统确认订单',
        self::ORDER_STATUS_080_USER_FINISHED => '用户确认订单',
        self::ORDER_STATUS_140_CUSTOMER_SERVICE_DISAGREE => '客服驳回退单申请',
        self::ORDER_STATUS_1010_ORDER_CANCELLED => '订单已取消',
        self::ORDER_STATUS_1020_ORDER_BACK => '订单已退单',
        self::ORDER_STATUS_1000_ORDER_END_AND_SETTLEMENT => '订单已完成',
        self::ORDER_STATUS_090_USER_BACK_ORDER => '用户申请退单',
        self::ORDER_STATUS_110_ANCHOR_DISAGREE_BACK => '主播驳回退单',
        self::ORDER_STATUS_120_USER_APPEAL_CUSTOMER_SERVICE => '用户申请客服退单',
        self::ORDER_STATUS_150_TIMEOUT_OVER_FINISHED_ORDER => '系统完成订单',
        self::ORDER_STATUS_160_TIMEOUT_USER_BACK_ORDER => '系统退单',
        self::ORDER_STATUS_170_TIMEOUT_USER_NOT_APPEAL_CUSTOMER_SERVICE => '系统完成结束订单',
    ];

    /**
     * 订单各状态可操作描述(客户端看的),用户操作订单的选项【可操作的动作描述】
     * @var type 
     */
    public static $order_status_action = [
        self::ORDER_STATUS_000_DEFAULT => '未知状态',
        self::ORDER_STATUS_010_CREATE_ORDER => '下单',
        self::ORDER_STATUS_020_TIMEOUT_CANCEL => '主播未接单',
        self::ORDER_STATUS_030_USER_CANCEL => '取消',
        self::ORDER_STATUS_040_ANCHOR_REJECT_ORDER => '拒单',
        self::ORDER_STATUS_060_ANCHOR_CANCEL => '取消',
        self::ORDER_STATUS_100_ANCHOR_AGREE_BACK => '同意',
        self::ORDER_STATUS_130_CUSTOMER_SERVICE_AGREE => '客服同意退单申请',
        self::ORDER_STATUS_050_ANCHOR_ACCEPTING_ORDER => '接单',
        self::ORDER_STATUS_070_TIMEOUT_FINISHED => '系统确认订单',
        self::ORDER_STATUS_080_USER_FINISHED => '确认',
        self::ORDER_STATUS_140_CUSTOMER_SERVICE_DISAGREE => '客服驳回退单申请',
        self::ORDER_STATUS_090_USER_BACK_ORDER => '退单',
        self::ORDER_STATUS_110_ANCHOR_DISAGREE_BACK => '拒绝退单',
        self::ORDER_STATUS_120_USER_APPEAL_CUSTOMER_SERVICE => '申请客服',
        self::ORDER_STATUS_150_TIMEOUT_OVER_FINISHED_ORDER => '系统完成订单',
        self::ORDER_STATUS_160_TIMEOUT_USER_BACK_ORDER => '系统退单',
        self::ORDER_STATUS_170_TIMEOUT_USER_NOT_APPEAL_CUSTOMER_SERVICE => '系统完成结束订单',
        self::ORDER_STATUS_1000_ORDER_END_AND_SETTLEMENT => '系统处理:已完成',
        self::ORDER_STATUS_1010_ORDER_CANCELLED => '系统处理:已取消',
        self::ORDER_STATUS_1020_ORDER_BACK => '系统处理:已退单',
    ];

    /**
     * 订单各状态详细描述(我自己看的)
     * @var type 
     */
    public static $order_status_detail = [
        self::ORDER_STATUS_000_DEFAULT => '默认值',
        self::ORDER_STATUS_010_CREATE_ORDER => '创建订单',
        self::ORDER_STATUS_030_USER_CANCEL => '用户取消掉订单(交易取消)',
        self::ORDER_STATUS_040_ANCHOR_REJECT_ORDER => '拒绝订单(交易取消)',
        self::ORDER_STATUS_050_ANCHOR_ACCEPTING_ORDER => '接受订单',
        self::ORDER_STATUS_060_ANCHOR_CANCEL => '主播取消掉订单(交易取消)',
        self::ORDER_STATUS_080_USER_FINISHED => '用户完成订单',
        //订单纠纷状态
        self::ORDER_STATUS_090_USER_BACK_ORDER => '用户申请退单',
        self::ORDER_STATUS_100_ANCHOR_AGREE_BACK => '主播同意退单(交易退单)',
        self::ORDER_STATUS_110_ANCHOR_DISAGREE_BACK => '主播不同意退单',
        self::ORDER_STATUS_120_USER_APPEAL_CUSTOMER_SERVICE => '用户申诉到客服',
        self::ORDER_STATUS_130_CUSTOMER_SERVICE_AGREE => '客服同意申诉(交易退单)',
        self::ORDER_STATUS_140_CUSTOMER_SERVICE_DISAGREE => '客服不同意申诉(交易完成)',
        //超时
        self::ORDER_STATUS_020_TIMEOUT_CANCEL => '创建订单后1小时不接单取消掉订单(交易取消)',
        self::ORDER_STATUS_070_TIMEOUT_FINISHED => '接单超时陪玩时间24小时后自动确认订单',
        self::ORDER_STATUS_150_TIMEOUT_OVER_FINISHED_ORDER => '订单确认超过24小时(交易完成)',
        self::ORDER_STATUS_160_TIMEOUT_USER_BACK_ORDER => '订单退单超过24小时主播未处理(交易退单)',
        self::ORDER_STATUS_170_TIMEOUT_USER_NOT_APPEAL_CUSTOMER_SERVICE => '主播拒绝退单后用户24小时没有申诉客服(交易完成)',
        //结束状态
        self::ORDER_STATUS_1000_ORDER_END_AND_SETTLEMENT => '结束订单,支付完成',
        self::ORDER_STATUS_1010_ORDER_CANCELLED => '取消订单,退款完成',
        self::ORDER_STATUS_1020_ORDER_BACK => '回退订单,退款完成',
    ];

    /**
     * 当前角色,当前状态,用户可以做的事情
     * @var type 
     */
    public static $user_order_status_action = [
        self::ORDER_USER_ROLE_TYPE_01_USER => [
            self::ORDER_STATUS_000_DEFAULT => [self::ORDER_STATUS_010_CREATE_ORDER],
            self::ORDER_STATUS_010_CREATE_ORDER => [self::ORDER_STATUS_030_USER_CANCEL],
            self::ORDER_STATUS_020_TIMEOUT_CANCEL => FALSE,
            self::ORDER_STATUS_030_USER_CANCEL => FALSE,
            self::ORDER_STATUS_040_ANCHOR_REJECT_ORDER => FALSE,
            self::ORDER_STATUS_050_ANCHOR_ACCEPTING_ORDER => [self::ORDER_STATUS_080_USER_FINISHED],
            self::ORDER_STATUS_060_ANCHOR_CANCEL => FALSE,
            self::ORDER_STATUS_070_TIMEOUT_FINISHED => [self::ORDER_STATUS_090_USER_BACK_ORDER],
            self::ORDER_STATUS_080_USER_FINISHED => [self::ORDER_STATUS_090_USER_BACK_ORDER],
            self::ORDER_STATUS_090_USER_BACK_ORDER => FALSE,
            self::ORDER_STATUS_100_ANCHOR_AGREE_BACK => FALSE,
            self::ORDER_STATUS_110_ANCHOR_DISAGREE_BACK => [self::ORDER_STATUS_120_USER_APPEAL_CUSTOMER_SERVICE],
            self::ORDER_STATUS_120_USER_APPEAL_CUSTOMER_SERVICE => FALSE,
            self::ORDER_STATUS_130_CUSTOMER_SERVICE_AGREE => FALSE,
            self::ORDER_STATUS_140_CUSTOMER_SERVICE_DISAGREE => FALSE,
            self::ORDER_STATUS_150_TIMEOUT_OVER_FINISHED_ORDER => FALSE,
            self::ORDER_STATUS_160_TIMEOUT_USER_BACK_ORDER => FALSE,
            self::ORDER_STATUS_170_TIMEOUT_USER_NOT_APPEAL_CUSTOMER_SERVICE => FALSE,
            self::ORDER_STATUS_1000_ORDER_END_AND_SETTLEMENT => FALSE,
            self::ORDER_STATUS_1010_ORDER_CANCELLED => FALSE,
            self::ORDER_STATUS_1020_ORDER_BACK => FALSE,
        ],
        self::ORDER_USER_ROLE_TYPE_02_ANCHOR => [
            self::ORDER_STATUS_000_DEFAULT => FALSE,
            self::ORDER_STATUS_010_CREATE_ORDER => [self::ORDER_STATUS_040_ANCHOR_REJECT_ORDER, self::ORDER_STATUS_050_ANCHOR_ACCEPTING_ORDER],
            self::ORDER_STATUS_020_TIMEOUT_CANCEL => FALSE,
            self::ORDER_STATUS_030_USER_CANCEL => FALSE,
            self::ORDER_STATUS_040_ANCHOR_REJECT_ORDER => FALSE,
            self::ORDER_STATUS_050_ANCHOR_ACCEPTING_ORDER => [self::ORDER_STATUS_060_ANCHOR_CANCEL],
            self::ORDER_STATUS_060_ANCHOR_CANCEL => FALSE,
            self::ORDER_STATUS_070_TIMEOUT_FINISHED => FALSE,
            self::ORDER_STATUS_080_USER_FINISHED => FALSE,
            self::ORDER_STATUS_090_USER_BACK_ORDER => [self::ORDER_STATUS_100_ANCHOR_AGREE_BACK, self::ORDER_STATUS_110_ANCHOR_DISAGREE_BACK],
            self::ORDER_STATUS_100_ANCHOR_AGREE_BACK => FALSE,
            self::ORDER_STATUS_110_ANCHOR_DISAGREE_BACK => FALSE,
            self::ORDER_STATUS_120_USER_APPEAL_CUSTOMER_SERVICE => FALSE,
            self::ORDER_STATUS_130_CUSTOMER_SERVICE_AGREE => FALSE,
            self::ORDER_STATUS_140_CUSTOMER_SERVICE_DISAGREE => FALSE,
            self::ORDER_STATUS_150_TIMEOUT_OVER_FINISHED_ORDER => FALSE,
            self::ORDER_STATUS_160_TIMEOUT_USER_BACK_ORDER => FALSE,
            self::ORDER_STATUS_170_TIMEOUT_USER_NOT_APPEAL_CUSTOMER_SERVICE => FALSE,
            self::ORDER_STATUS_1000_ORDER_END_AND_SETTLEMENT => FALSE,
            self::ORDER_STATUS_1010_ORDER_CANCELLED => FALSE,
            self::ORDER_STATUS_1020_ORDER_BACK => FALSE,
        ],
    ];

    /**
     * 状态改变映射关系
     * 想要改变成的状态 =》 【能改成想要改变的状态的前提状态】
     * @var type 
     */
    public static $do_map = [
        self::ORDER_STATUS_000_DEFAULT => false,
        self::ORDER_STATUS_010_CREATE_ORDER => true,
        self::ORDER_STATUS_020_TIMEOUT_CANCEL => [self::ORDER_STATUS_010_CREATE_ORDER],
        self::ORDER_STATUS_030_USER_CANCEL => [self::ORDER_STATUS_010_CREATE_ORDER],
        self::ORDER_STATUS_040_ANCHOR_REJECT_ORDER => [self::ORDER_STATUS_010_CREATE_ORDER],
        self::ORDER_STATUS_050_ANCHOR_ACCEPTING_ORDER => [self::ORDER_STATUS_010_CREATE_ORDER],
        self::ORDER_STATUS_060_ANCHOR_CANCEL => [self::ORDER_STATUS_050_ANCHOR_ACCEPTING_ORDER],
        self::ORDER_STATUS_070_TIMEOUT_FINISHED => [self::ORDER_STATUS_050_ANCHOR_ACCEPTING_ORDER],
        self::ORDER_STATUS_080_USER_FINISHED => [self::ORDER_STATUS_050_ANCHOR_ACCEPTING_ORDER],
        //订单纠纷状态
        self::ORDER_STATUS_090_USER_BACK_ORDER => [self::ORDER_STATUS_070_TIMEOUT_FINISHED, self::ORDER_STATUS_080_USER_FINISHED],
        self::ORDER_STATUS_100_ANCHOR_AGREE_BACK => [self::ORDER_STATUS_090_USER_BACK_ORDER],
        self::ORDER_STATUS_110_ANCHOR_DISAGREE_BACK => [self::ORDER_STATUS_090_USER_BACK_ORDER],
        self::ORDER_STATUS_120_USER_APPEAL_CUSTOMER_SERVICE => [self::ORDER_STATUS_110_ANCHOR_DISAGREE_BACK],
        self::ORDER_STATUS_130_CUSTOMER_SERVICE_AGREE => [self::ORDER_STATUS_120_USER_APPEAL_CUSTOMER_SERVICE],
        self::ORDER_STATUS_140_CUSTOMER_SERVICE_DISAGREE => [self::ORDER_STATUS_120_USER_APPEAL_CUSTOMER_SERVICE],
        //
        self::ORDER_STATUS_150_TIMEOUT_OVER_FINISHED_ORDER => [self::ORDER_STATUS_070_TIMEOUT_FINISHED, self::ORDER_STATUS_080_USER_FINISHED],
        self::ORDER_STATUS_160_TIMEOUT_USER_BACK_ORDER => [self::ORDER_STATUS_090_USER_BACK_ORDER],
        self::ORDER_STATUS_170_TIMEOUT_USER_NOT_APPEAL_CUSTOMER_SERVICE => [self::ORDER_STATUS_110_ANCHOR_DISAGREE_BACK],
        //
        self::ORDER_STATUS_1000_ORDER_END_AND_SETTLEMENT => [
            self::ORDER_STATUS_1000_ORDER_END_AND_SETTLEMENT,
            self::ORDER_STATUS_150_TIMEOUT_OVER_FINISHED_ORDER,
            self::ORDER_STATUS_170_TIMEOUT_USER_NOT_APPEAL_CUSTOMER_SERVICE,
            self::ORDER_STATUS_140_CUSTOMER_SERVICE_DISAGREE],
        self::ORDER_STATUS_1010_ORDER_CANCELLED => [
            self::ORDER_STATUS_1010_ORDER_CANCELLED,
            self::ORDER_STATUS_020_TIMEOUT_CANCEL,
            self::ORDER_STATUS_030_USER_CANCEL,
            self::ORDER_STATUS_040_ANCHOR_REJECT_ORDER,
            self::ORDER_STATUS_060_ANCHOR_CANCEL,
        ],
        self::ORDER_STATUS_1020_ORDER_BACK => [
            self::ORDER_STATUS_1020_ORDER_BACK,
            self::ORDER_STATUS_100_ANCHOR_AGREE_BACK,
            self::ORDER_STATUS_130_CUSTOMER_SERVICE_AGREE,
            self::ORDER_STATUS_160_TIMEOUT_USER_BACK_ORDER,
        ],
    ];

    const ORDER_COMMENT_STATUS_00_DEFAULT = 0;
    const ORDER_COMMENT_STATUS_01_HAVE_COMMENT = 1;
    //角色
    const ORDER_USER_ROLE_TYPE_01_USER = 1;
    const ORDER_USER_ROLE_TYPE_02_ANCHOR = 2;

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

    /**
     * 写入订单记录
     * @param type $data  订单需要内容
     * $data = [ <br />
     * //order<br />
     * 'order_id' => $this->createOrderId(),<br />
     * 'uid' => $userId,<br />
      'cert_uid' => $skillInfo['uid'],<br />
      'cert_id' => $skillInfo['cert_id'],<br />
      'num' => $num,<br />
      'price' => $skillInfo['price'],<br />
      'discount' => '',<br />
      'amount' => $money,<br />
      'real_amount' => $realMoney,<br />
      'memo' => $memo,<br />
      'status' => DueOrder::ORDER_STATUS_01_CREATE_ORDER,<br />
      //detail<br />
      'skill_id' => $skillId,<br />
      'price' => $skillInfo['uid'],<br />
      'unit' => $skillInfo['unit'],<br />
      'number' => $num,<br />
      'start_time' => $startPlayTime,<br />
      'end_time' => $endPlayTime,<br />
      'role_name' => $roleName,<br />
      'contact_type' => $contactType,<br />
      'contact' => $contact,<br />
      ];
     */
    public function insertOrder($data) {
        try {
            self::dataInFormat($data);
            //开启事务
            $db = DbHelper::getInstance(self::$dbConfig);
            $orderParamField = [
                'order_id', 'uid', 'cert_uid', 'cert_id', 'discount', 'amount', 'real_amount', 'memo', 'status', 'comment'
            ];
            $table = $this->_getOrderTable();
            $orderSql = "INSERT INTO `{$table}`(`order_id`,`uid`,`cert_uid`,`cert_id`,`discount`,`amount`,`real_amount`,`memo`,`status`,`comment`)"
                    . "VALUES(:order_id,:uid,:cert_uid,:cert_id,:discount,:amount,:real_amount,:memo,:status,:comment)";
            $orderParam = $this->_getDataFromPool($orderParamField, $data);

            $resOrder = $db->execute($orderSql, $orderParam);
            //detail
            $detailTable = $this->_getOrderDetailTable();
            $detailSql = " INSERT INTO `{$detailTable}`(`order_id`,`cert_id`,`skill_id`,`price`,`unit`,`number`,`start_time`,`end_time`,`role_name`,`contact_type`,`contact`)
                                                 VALUES(:order_id,:cert_id,:skill_id,:price,:unit,:number,:start_time,:end_time,:role_name,:contact_type,:contact)";
            $detailParamField = [
                'order_id', 'cert_id', 'skill_id', 'price', 'unit', 'number', 'start_time', 'end_time', 'role_name', 'contact_type', 'contact'
            ];
            $detailParam = $this->_getDataFromPool($detailParamField, $data);
            $resDetail = $db->execute($detailSql, $detailParam);
            if ($resOrder && $resDetail) {
                return TRUE;
            }
        } catch (Exception $e) {
            //回滚
            return FALSE;
        }
    }

    private function _getOrderTable() {
        return 'due_order';
    }
    private function _getUserCouponTable() {
        return 'due_user_coupon';
    }

    private function _getOrderDetailTable() {
        return 'due_order_detail';
    }

    private function _getOrderLogTable() {
        return 'due_order_log';
    }

    /**
     * 获取数组中数据
     * @param type $result
     * @param type $poll
     * @return type
     */
    private function _getDataFromPool($result, $poll) {
        $res = [];
        foreach ($result as $key) {
            $res[$key] = $poll[$key];
        }
        return $res;
    }

    /**
     * 订单状态位变更权限判断
     * @param int $to
     * @param int $from
     * @return bool
     */
    public static function OrderStatusFromToCheck(int $to, int $from): bool {
        $map = self::$do_map;
        if (isset($map[$to])) {
            if (is_array($map[$to]) && in_array($from, $map[$to])) {
                return TRUE;
            } else if (is_bool($map[$to]) && $map[$to]) {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * 更新订单状态
     * @param int $orderId
     * @param int $status
     * @param string $reason
     * @return int  影响行数
     */
    public function updateOrderStatusByOrderId(int $orderId, int $status, string $reason = '') {
        $table = $this->_getOrderTable();
        $time = date("Y-m-d H:i:s");
        $paramData = ['order_id' => $orderId, 'otime' => $time, 'status' => $status];
        $setArr[] = "`status` = :status ";
        $setArr[] = "`otime` = :otime ";
        if (!empty($reason)) {
            $setArr[] = "`reason` = :reason";
            $paramData['reason'] = $reason;
        }
        if ($status == self::ORDER_STATUS_1000_ORDER_END_AND_SETTLEMENT) {
            $paramData['stime'] = $time;
            $setArr[] = "`stime` = :stime ";
        }
        if ($status == self::ORDER_STATUS_1010_ORDER_CANCELLED || $status == self::ORDER_STATUS_1020_ORDER_BACK) {
            $paramData['rtime'] = $time;
            $setArr[] = "`rtime` = :rtime ";
        }
        $setStr = implode(',', $setArr);
        $orderSql = "UPDATE {$table} SET {$setStr} WHERE order_id=:order_id  LIMIT 1";
        $res = $this->db->execute($orderSql, $paramData);
        return $res;
    }

    /**
     * 更新订单原因
     * @param int $orderId
     * @param int $status
     * @param string $reason
     * @return int  影响行数
     */
    public function updateOrderReasonByOrderId(int $orderId, $reason = '') {
        $table = $this->_getOrderTable();
        $paramData = ['order_id' => $orderId, 'reason' => $reason];
        $setStr = "`reason` = :reason ";
        $orderSql = "UPDATE {$table} SET {$setStr} WHERE order_id=:order_id  LIMIT 1";
        $res = $this->db->execute($orderSql, $paramData);
        return $res;
    }

    /**
     * 更新订单状态
     * @param int $orderId
     * @param int $status
     * @param string $reason
     * @return int  影响行数
     */
    public function updateOrderOtidByOrderId(int $orderId, int $otid) {
        $table = $this->_getOrderTable();
        $paramData = ['order_id' => $orderId, 'otid' => $otid];
        $setStr = "`otid` = :otid ";
        $orderSql = "UPDATE {$table} SET {$setStr} WHERE order_id=:order_id  LIMIT 1";
        $res = $this->db->execute($orderSql, $paramData);
        return $res;
    }

    /**
     * 更新订单状态
     * @param int $orderId
     * @param int $status
     * @param string $reason
     * @return int  影响行数
     */
    public function updateOrderIncomeByOrderId(int $orderId, $income) {
        $table = $this->_getOrderTable();
        $paramData = ['order_id' => $orderId, 'income' => $income];
        $setStr = "`income` = :income ";
        $orderSql = "UPDATE {$table} SET {$setStr} WHERE order_id=:order_id  LIMIT 1";
        self::dataInFormat($paramData);
        $res = $this->db->execute($orderSql, $paramData);
        return $res;
    }

    /**
     * 更新订单评论状态
     * @param int $orderId
     * @param int $status
     * @param string $reason
     * @return int  影响行数
     */
    public function updateCommentStatusByOrderId(int $orderId, int $commenSttatus) {
        $table = $this->_getOrderTable();
        $paramData = ['order_id' => $orderId, 'comment' => $commenSttatus];
        $setStr = "`comment` = :comment ";
        $orderSql = "UPDATE {$table} SET {$setStr} WHERE order_id=:order_id  LIMIT 1";
        $res = $this->db->execute($orderSql, $paramData);
        return $res;
    }

    /**
     * 写入订单记录
     * '`order_id`, `ctime`, `status`, `uid`, `reason`, `log`'
     * @param type $data
     * @return type
     */
    public function insertLog(array $data): int {
        $table = $this->_getOrderLogTable();
        $fields = '`order_id`, `ctime`, `status`, `uid`, `reason`, `log`';
        $paramFields = explode(',', str_replace(['`', ' '], '', $fields));
        $value = array_map(function($n) {
            return ':' . $n;
        }, $paramFields);
        $valueStr = implode(',', $value);
        $sql = "INSERT INTO  {$table} ({$fields})VALUES({$valueStr})";
        $bindParam = $this->_getDataFromPool($paramFields, $data);
        $rows = $this->db->execute($sql, $bindParam);
        return $rows;
    }

    /**
     * 查看操作记录
     * '`order_id`, `ctime`, `status`, `uid`, `reason`, `log`'
     * @param type $data
     * @return type
     */
    public function getOneOrderLog($orderId, $status) {
        $table = $this->_getOrderLogTable();
        $fields = '`id`,`order_id`, `ctime`, `status`, `uid`, `reason`, `log`';
        $sql = "SELECT {$fields} FROM  {$table} WHERE `order_id`=:order_id AND `status`=:status ORDER BY id DESC LIMIT 1";
        $bindParam = [
            'order_id' => $orderId,
            'status' => $status
        ];
        $rows = $this->db->query($sql, $bindParam);
        return isset($rows[0]) ? $rows[0] : [];
    }

    /**
     * 获取指定的订单信息
     * @param int $orderId
     * @return array 
     */
    public function getOrderDataByOrderId(int $orderId): array {
        $fields = '`id`,`otid`, `order_id`, `uid`, `cert_uid`, `cert_id`,  `discount`, `amount`, `real_amount`, `income`, `memo`, `status`, `comment`,`reason`, `ctime`, `otime`, `stime`, `rtime`';
        $table = $this->_getOrderTable();
        $orderSql = "SELECT $fields FROM $table WHERE `order_id`=:order_id  LIMIT 1";
        $orderList = $this->db->query($orderSql, ['order_id' => $orderId]);
        self::dataOutFormat($orderList);
        return isset($orderList[0]) ? $orderList[0] : [];
    }

    /**
     * 获取指定的订单信息
     * @param int $orderId
     * @return array 
     */
    public function getOrderDataByUidAndCertId(int $uid, int $certId, int $limit = 1): array {
        $fields = '`id`, `order_id`, `uid`, `cert_uid`, `cert_id`, `discount`, `amount`, `real_amount`, `income`, `memo`, `status`,`comment`, `reason`, `ctime`, `otime`, `stime`, `rtime`';
        $table = $this->_getOrderTable();
        $orderSql = "SELECT $fields FROM $table WHERE `uid`=:uid  AND  `cert_uid`=:cert_uid AND `status` > 0  ORDER BY id  DESC LIMIT {$limit} ";
        $orderList = $this->db->query($orderSql, ['uid' => $uid, 'cert_uid' => $certId]);
        self::dataOutFormat($orderList);
        if ($limit == 1) {
            return isset($orderList[0]) ? $orderList[0] : [];
        } else {
            return (array) $orderList;
        }
    }

    /**
     * 获取进行中的订单
     * @param int $uid
     * @param int $certId
     * @param int $limit
     * @return type
     */
    public function getDoingOrderDataByUidAndCertId(int $uid, int $certId, int $limit = 1) {
        $fields = '`id`, `order_id`, `uid`, `cert_uid`, `cert_id`, `discount`, `amount`, `real_amount`, `income`, `memo`, `status`,`comment`, `reason`, `ctime`, `otime`, `stime`, `rtime`';
        $table = $this->_getOrderTable();
        $minStatus = self::ORDER_STATUS_000_DEFAULT;
        $maxStatus = self::ORDER_STATUS_1000_ORDER_END_AND_SETTLEMENT;

        $orderSql = "SELECT $fields FROM $table WHERE `uid`=:uid  AND  `cert_uid`=:cert_uid AND `status` > {$minStatus} AND  `status`  < {$maxStatus}  ORDER BY id  DESC LIMIT {$limit} ";
        $orderList = $this->db->query($orderSql, ['uid' => $uid, 'cert_uid' => $certId]);
        self::dataOutFormat($orderList);
        if ($limit == 1) {
            return isset($orderList[0]) ? $orderList[0] : [];
        } else {
            return $orderList;
        }
    }

    /**
     * 获取两个人之间的订单数据
     * @param int $orderId
     * @return array 
     */
    public function getOrderDataForTwoPersons(int $uid1, int $uid2, int $limit = 1): array {
        $fields = '`id`, `order_id`, `uid`, `cert_uid`, `cert_id`, `discount`, `amount`, `real_amount`, `income`, `memo`, `status`, `comment`,`reason`, `ctime`, `otime`, `stime`, `rtime`';
        $table = $this->_getOrderTable();
        $where = "  `status` > 0 AND (`uid`='{$uid1}'  AND  `cert_uid`='{$uid2}' ) OR  (`uid`='{$uid2}'  AND  `cert_uid`='{$uid1}' ) ";
        $orderSql = "SELECT $fields FROM $table WHERE {$where}  ORDER BY id  DESC LIMIT {$limit} ";
        $orderList = $this->db->query($orderSql);
        self::dataOutFormat($orderList);
        if ($limit == 1) {
            return isset($orderList[0]) ? $orderList[0] : [];
        } else {
            return (array) $orderList;
        }
    }

    /**
     * 获取指定的订单信息
     * @param int $orderId
     * @return array 
     */
    public function getOrderIdDataByStatus(int $status, int $limit = 1): array {
        $fields = '`order_id`';
        $table = $this->_getOrderTable();
        $orderSql = "SELECT $fields FROM $table WHERE `status`=:status AND `status` > 0 ORDER BY id  ASC LIMIT {$limit} ";
        $orderList = $this->db->query($orderSql, ['status' => $status]);
        self::dataOutFormat($orderList);
        if ($limit == 1) {
            return isset($orderList[0]) ? $orderList[0] : [];
        } else {
            return (array) $orderList;
        }
    }

    /**
     * 获取状态为x,时间小于y的订单记录
     * @param int $status
     * @param type $time    24小时之前的时间  时间字符串
     * @param int $limit
     * @return array
     */
    public function getOrderIdDataByStatusAndLtTime(int $status, $time = '', int $limit = 1): array {
        $fields = '`order_id`';
        $table = $this->_getOrderTable();
        $orderSql = "SELECT $fields FROM $table WHERE `status`=:status AND `otime`<:otime AND `status` > 0 ORDER BY id  ASC LIMIT {$limit} ";
        $orderList = $this->db->query($orderSql, ['status' => $status, 'otime' => $time]);
        self::dataOutFormat($orderList);
        if ($limit == 1) {
            return isset($orderList[0]) ? $orderList[0] : [];
        } else {
            return (array) $orderList;
        }
    }

    /**
     * 获取陪玩超时的数据
     * @param int $status
     * @param int $time   24小时之前的时间  时间戳
     * @param int $limit
     * @return array
     */
    public function getAcceptingOrderIdDataByStatusAndTime(int $status, int $time, int $limit = 1): array {
        $fields = 'o.`order_id`';
        $table = $this->_getOrderTable();
        $table2 = $this->_getOrderDetailTable();
        $orderSql = "SELECT $fields FROM $table  as o  ,  {$table2} as d WHERE  o.`order_id` =d.`order_id` AND  o.`status`=:status  AND o.`status` > 0 AND d.`start_time`< '{$time}' ORDER BY o.id  ASC LIMIT {$limit} ";
        $orderList = $this->db->query($orderSql, ['status' => $status]);
        self::dataOutFormat($orderList);
        return (array) $orderList;
    }

    /**
     * 获取完成订单数 按主播技能id 由于有左连接 弃用
     * @param int $skillId
     * @return array
     */
    public function getOrderTotalBySkillId($skillId): array {
        if (is_array($skillId)) {
            //绑定占位符
            $in = $this->db->buildInPrepare($skillId);
            $fields = 'COUNT(a.id) as order_total,b.skill_id';
            $table1 = $this->_getOrderTable();
            $table2 = $this->_getOrderDetailTable();
            $orderSql = "SELECT $fields FROM $table1 AS a LEFT JOIN $table2 as b ON  a.order_id = b.order_id WHERE b.skill_id in ({$in})and a.status = " . self::ORDER_STATUS_1000_ORDER_END_AND_SETTLEMENT . " GROUP BY b.skill_id";
            $bdParam = $skillId;
        } else {
            $fields = 'COUNT(a.id) as order_total';
            $table1 = $this->_getOrderTable();
            $table2 = $this->_getOrderDetailTable();
            $orderSql = "SELECT $fields FROM $table1 AS a LEFT JOIN $table2 as b ON  a.order_id = b.order_id WHERE b.skill_id =:skill_id and a.status = " . self::ORDER_STATUS_1000_ORDER_END_AND_SETTLEMENT;
            $bdParam = ['skill_id' => $skillId];
        }

        try {

            $orderList = $this->db->query($orderSql, $bdParam);
            self::dataOutFormat($orderList);
            return $orderList;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 获取所有订单号按技能id
     * @param array $skillId
     * @return bool|array
     */
    public function getAllOrderBySkillId(array $skillId)
    {
        //绑定占位符
        $in = $this->db->buildInPrepare($skillId);
        $table = $this->_getOrderDetailTable();
        $orderSql = "SELECT order_id,skill_id FROM {$table} WHERE skill_id in ({$in})";
        $bdParam = $skillId;
        try {

           return $this->db->query($orderSql, $bdParam);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 获取所有订单状态按订单号
     * @param array $orderId
     * @return bool|array
     */
    public function getOrderStatusByOrderId(array $orderId)
    {
        //绑定占位符
        $in = $this->db->buildInPrepare($orderId);
        $table = $this->_getOrderTable();
        $orderSql = "SELECT order_id,status  FROM {$table} WHERE order_id in ({$in})";
        $bdParam = $orderId;
        try {

            return $this->db->query($orderSql, $bdParam);
        } catch (Exception $e) {
            return false;
        }

    }
    /**
     * 获取订单总数按用户id
     * @param type $uid
     * @return int
     */
    public function getOrderTotalByUid($uid): int {
        $fields = ' COUNT(1)  as total ';
        $table = $this->_getOrderTable();
        $orderSql = "SELECT $fields FROM $table WHERE `uid`=:uid AND `status` > 0  LIMIT 1;";
        $bdParam = ['uid' => $uid];
        $res = $this->getDb()->query($orderSql, $bdParam);
        self::dataOutFormat($res);
        return $res[0]['total'];
    }

    /**
     * 获取指定用户的的指定状态的订单数量
     * @param type $certUid
     * @return int
     */
    public function getOrderTotalByCertUid(int $certUid, int $status = 1): int {
        $fields = ' COUNT(1)  as total ';
        $table = $this->_getOrderTable();
        if ($certUid) {
            $where[] = "`cert_uid`=:cert_uid ";
            $bdParam['cert_uid'] = $certUid;
        }
        if ($status) {
            $where[] = "`status`=:status ";
            $bdParam['status'] = $status;
        }
        $orderSql = "SELECT $fields FROM $table WHERE  " . implode(' AND ', $where) . " AND `status` > 0   LIMIT 1;";
        $res = $this->getDb()->query($orderSql, $bdParam);
        self::dataOutFormat($res);
        return $res[0]['total'];
    }

    /**
     * 获取指定的订单详细表信息
     * @param int $orderId
     * @return array
     */
    public function getOrderDetailDataByOrderId(int $orderId): array {
        $fields = '`id`, `order_id`, `cert_id`, `skill_id`, `price`, `unit`, `number`, `start_time`, `end_time`, `role_name`, `contact_type`, `contact`, `ctime`';
        $table = $this->_getOrderDetailTable();
        $orderSql = "SELECT $fields FROM $table WHERE `order_id`=:order_id  LIMIT 1";
        $orderList = $this->db->query($orderSql, ['order_id' => $orderId]);
        self::dataOutFormat($orderList);
        return isset($orderList[0]) ? $orderList[0] : [];
    }

    /**
     * 根据用户ID获取用户约玩订单列表
     * @param int $uid 用户ID
     * @param int $page 页码
     * @param int $size 数量
     * return array|boolean
     */
    public function getUserOrderList($uid, $page = 1, $size = 5) {
        $fields = '`id`, `order_id`, `uid`, `cert_uid`, `cert_id`, `discount`, `amount`, `real_amount`, `income`, `memo`, `status`, `comment`,`reason`, `ctime`, `otime`, `stime`, `rtime`';
//        $paramFields = ['uid', 'page', 'size'];
        $paramFields = ['uid'];

        $page = ($page - 1) * $size;
        $data = ['uid' => $uid, 'page' => $page, 'size' => $size];

        $table = $this->_getOrderTable();

        //$orderSql = "select $fields from $table where uid=:uid order by ctime DESC limit :page, :size";
        $orderSql = "select $fields from $table where uid=:uid  AND `status` > 0 order by ctime DESC limit $page, $size";

        $orderParam = $this->_getDataFromPool($paramFields, $data);

        $orderList = $this->db->query($orderSql, $orderParam);
        self::dataOutFormat($orderList);
        return $orderList;
    }

    /**
     * 根据用户ID获取用户订单列表
     * @param int $uid 用户ID
     * @param int $page 页码
     * @param int $size 数量
     * return array|boolean
     */
    public function getUserDetailOrderList($uid, $page = 1, $size = 5) {
        $fields1 = ' a.`order_id`, a.`uid`, a.`cert_uid`, a.`cert_id`, a.`discount`, a.`amount`, a.`real_amount`, a.`income`, a.`memo`, a.`status`,a.`comment`, a.`reason`, a.`ctime`, a.`otime`, a.`stime`, a.`rtime`';
        $fields2 = ' b.`skill_id`, b.`price`, b.`unit`, b.`number`, b.`start_time`';
        $paramFields = ['uid'];
        $page = ($page - 1) * $size;
        $data = ['uid' => $uid, 'page' => $page, 'size' => $size];
        $tableA = $this->_getOrderTable();
        $tableB = $this->_getOrderDetailTable();
        $orderSql = "select {$fields1} ,{$fields2}  from {$tableA} as a ,{$tableB} as b where a.`status` > 0  AND   a.`order_id` = b.`order_id` and  a.uid=:uid order by  a.id DESC limit $page, $size";
        $orderParam = $this->_getDataFromPool($paramFields, $data);
        $orderList = $this->db->query($orderSql, $orderParam);
        self::dataOutFormat($orderList);
        return $orderList;
    }

    /**
     * 根据用户ID获取用户约玩订单列表
     * @param int $uid 用户ID
     * @param int $page 页码
     * @param int $size 数量
     * return array|boolean
     */
    public function getCertUserOrderList($uid, $page = 1, $size = 5) {
        $fields1 = ' a.`order_id`, a.`uid`, a.`cert_uid`, a.`cert_id`, a.`discount`, a.`amount`, a.`real_amount`,a.`income`, a.`memo`, a.`status`,a.`comment`, a.`reason`, a.`ctime`, a.`otime`, a.`stime`, a.`rtime`';
        $fields2 = ' b.`skill_id`, b.`price`, b.`unit`, b.`number`, b.`start_time`';
        $paramFields = ['cert_uid'];
        $page = ($page - 1) * $size;
        $data = ['cert_uid' => $uid, 'page' => $page, 'size' => $size];
        $tableA = $this->_getOrderTable();
        $tableB = $this->_getOrderDetailTable();
        $orderSql = "select {$fields1} ,{$fields2}  from {$tableA} as a ,{$tableB} as b where  a.`status` > 0  AND  a.`order_id` = b.`order_id` and  a.cert_uid=:cert_uid order by  a.id DESC limit $page, $size";
        $orderParam = $this->_getDataFromPool($paramFields, $data);
        $orderList = $this->db->query($orderSql, $orderParam);
        self::dataOutFormat($orderList);
        return $orderList;
    }

    /**
     * 刪除支付异常的订单。防止订单计划任务一直执行
     * @return type
     */
    public function fixPayErrorOrder() {
        $fields = ' `order_id`,`status` ';
        $table = $this->_getOrderTable();
        $res = FALSE;
        /**
         * 支付异常
         */
        $orderIds = [];
        $orderSql = "SELECT $fields FROM $table WHERE `status` > 0 AND `otid` < 1  LIMIT 20;";
        $array = $this->getDb()->query($orderSql);
        foreach ($array as $value) {
            $content = $value['order_id'] . ' status: ' . $value['status'] . ' tostatus:' . self::ORDER_STATUS_NEGATIVE_001_PAY_EXCEPTION;
            $orderIds[] = $value['order_id'];
            write_log($content, 'due_order_pay_exception');
        }
        $count = count($orderIds);
        if ($count > 0) {
            $where = implode(',', $orderIds);
            $sql = "UPDATE {$table} SET `status`= " . self::ORDER_STATUS_NEGATIVE_001_PAY_EXCEPTION . " WHERE `order_id` IN({$where}) LIMIT {$count}";
            $res = $this->db->execute($sql);
        }
        return $res;
    }

    public static function sqlTest($sql, $data) {
        foreach ($data as $key => $value) {
            $sql = str_replace(':' . $key, "'" . $value . "'", $sql);
        }
        echo $sql;
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
    /**
     * 更新优惠券状态
     */
    public function _updateCouponStatus($uid,$orderId,$couponId){
        write_log(__CLASS__."第 ".__LINE__." 行，更新优惠券相关参数：uid:{$uid}；orderId:{$orderId};优惠券id：{$couponId}","due_update_coupon");
        try { 
            $table = $this->_getUserCouponTable();
            $paramData = ['orderid' => $orderId, 'coupon_id' => $couponId,'utime'=>date("Y-m-d H:i:s"),'uid'=>$uid];
            $setStr = "`orderid` = :orderid,`utime`=:utime,`status`=".DueCouponService::COUPON_STATUS_02_UNUSE;
            $couponSql = "UPDATE {$table} SET {$setStr} WHERE `uid`=:uid and `id`=:coupon_id";  
            $res = $this->db->execute($couponSql, $paramData);
            if(!$res) 
                throw new \Exception(__CLASS__."第 ".__LINE__." 行，更新优惠券状态失败 SQL：'".$couponSql."' 条件：uid:{$uid}；orderId:{$orderId};优惠券id：{$couponId}");
            return $res;
        } catch (\Exception $e) {
            write_log($e->getMessage(),"due_update_coupon");
            return false;
        }
    }
}
