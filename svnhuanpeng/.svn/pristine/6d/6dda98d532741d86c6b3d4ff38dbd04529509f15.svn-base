<?php

/**
 * 订单类
 * @encoding utf-8
 * @author liupeng@6.cn
 * @since 2017-06-02 18:01:52
 * @version 1.0
 * @Description
 * @todo 1 支付  2财务冻结  3 消息推送
 */

namespace service\due;

use system\RedisHelper;
use system\DbHelper;
use service\user\UserDataService;
use service\due\rongCloud\RongCloudService;
use lib\SiteMsgBiz;
use lib\MsgPackage;
use lib\due\DueOrder;
use lib\Finance;
use lib\SocketSend;
use lib\Anchor;
use DBHelperi_huanpeng;
use Exception;

class DueOrderService {

    const ERROR_CODE_01 = -100;
    const ERROR_CODE_02 = -101;
    const ERROR_CODE_03 = -102;
    const ERROR_CODE_04 = -103;
    const ERROR_CODE_05 = -104;
    const ERROR_CODE_06 = -105;
    const ERROR_CODE_07 = -106;
    const ERROR_CODE_08 = -107;
    const ERROR_CODE_09 = -108;
    const ERROR_CODE_10 = -109;
    const ERROR_CODE_11 = -110;
    const ERROR_CODE_12 = -111;
    const ERROR_CODE_13 = -112;
    const ERROR_CODE_14 = -113;
    const ERROR_CODE_15 = -114;
    const ERROR_CODE_16 = -115;
    const ERROR_CODE_17 = -116;
    const ERROR_CODE_18 = -117;
    const ERROR_CODE_19 = -118;
    const ERROR_CODE_20 = -119;
    const ERROR_CODE_21 = -120;
    const ERROR_CODE_22 = -121;
    const ERROR_CODE_23 = -122;
    const ERROR_CODE_24 = -123;
    const ERROR_CODE_25 = -124;
    const ERROR_CODE_26 = -125;
    const ERROR_CODE_27 = -126;
    const ERROR_CODE_28 = -127;
    const ERROR_CODE_29 = -128;
    const ERROR_CODE_30 = -129;
    const ERROR_CODE_31 = -130;
    const ERROR_CODE_32 = -131;
    //
    const PALY_MIN_TIEM = 3600;
    const PALY_MAX_TIEM = 3600 * 8;
    const PALY_MAX_NUMBER = 24;

    public static $error_info = [
        self::ERROR_CODE_01 => '约玩时间错误',
        self::ERROR_CODE_02 => '约玩时间小于最小时间',
        self::ERROR_CODE_03 => '约玩时间大于最大时间',
        self::ERROR_CODE_04 => '约玩时间不可以跨天',
        self::ERROR_CODE_05 => '约玩时间不在主播设置的时间段',
        self::ERROR_CODE_06 => '约玩支付失败',
        self::ERROR_CODE_07 => '约玩下单失败',
        self::ERROR_CODE_08 => '您点的服务不存在',
        self::ERROR_CODE_09 => '约玩时间穿越到了过去',
        self::ERROR_CODE_10 => '约玩时间太远无法预订哦',
        self::ERROR_CODE_11 => '站內信发送失败',
        self::ERROR_CODE_12 => '消息发送失败',
        self::ERROR_CODE_13 => '订单不存在',
        self::ERROR_CODE_14 => '这个订单不是用户您的,您无法修改',
        self::ERROR_CODE_15 => '订单状态修改失败',
        self::ERROR_CODE_16 => '订单交易处理失败',
        self::ERROR_CODE_17 => '未到指定时间禁止操作',
        self::ERROR_CODE_18 => '这个订单不是主播您的,您无法修改',
        self::ERROR_CODE_19 => '订单状态不允许这样改变',
        self::ERROR_CODE_20 => '请求接口不合法',
        self::ERROR_CODE_21 => '扣费失败',
        self::ERROR_CODE_22 => '您的余额不足',
        self::ERROR_CODE_23 => '新增订单数据发送失败',
        self::ERROR_CODE_24 => '金额计算错误',
        self::ERROR_CODE_25 => '订单交易失败',
        self::ERROR_CODE_26 => '您和主播之间还有订单未结束哦!',
        self::ERROR_CODE_28 => '优惠券当天使用次数达到上限',
        self::ERROR_CODE_29 => '优惠券不可用',
        self::ERROR_CODE_30 => '优惠券使用出错',
        self::ERROR_CODE_31 => '使用优惠券时,需绑定手机号',
        self::ERROR_CODE_32 => '交易失败,准备回滚',
    ];

    // 时间间隔(超时)时间定义
    /**
     * 接单超时 1小时
     */
    const TIMER_TIME_OUT_01_HOUR = 3600; //3600

    /**
     * 超时处理 24小时,如 取消 完成都是24小时
     */
    const TIMER_TIME_OUT_24_HOUR = 86400; //86400

    /**
     * 订单状态对比滞后时间:此时间要大于ORDER_PAY_ARRIVAL_TIME的三倍时间,确保在财务处理后,再对账
     */
    const TIMER_TIME_ORDER_STATUS_COMPARISON = 180; //180

    /**
     * 订单到账时间:订单状态确认后,多长时间汇款或者退款
     */
    const ORDER_PAY_ARRIVAL_TIME = 70; //60  此时间 要在所有时间中最小 不然会出现不可预知的问题

    /**
     * 订单锁定7天
     */
    const ORDER_LOCK_TIME = 86400 * 7; //3600
    //
    const CONTACT_TYPE_00_NO_WAY = 0;
    const CONTACT_TYPE_01_QQ = 1;
    const CONTACT_TYPE_02_WECHAT = 2;
    const CONTACT_TYPE_03_PHONE = 3;

    public static $contact_type = [
        self::CONTACT_TYPE_00_NO_WAY => 'null',
        self::CONTACT_TYPE_01_QQ => 'qq',
        self::CONTACT_TYPE_02_WECHAT => 'wechat',
        self::CONTACT_TYPE_03_PHONE => 'phone',
    ];

    const ORDER_NUMBER_CACHE_KEY = 'due_order_number_key_20170713_';
    //上次列表查看时间
    const ORDER_NUMBER_CACHE_KEY_FIELD_TIME = 'time';
    //订单数量
    const ORDER_NUMBER_CACHE_KEY_FIELD_NUM = 'num';

    public static $choice_push_type = self::USER_MESSAGE_PUSH_TYPE_02_RONG_CLOUD;

    /**
     * 站内信
     */
    const USER_MESSAGE_PUSH_TYPE_01_SITE_MSG = 1;

    /**
     * 站内信
     */
    const USER_MESSAGE_PUSH_TYPE_02_RONG_CLOUD = 2;

    /**
     * 给用户发消息
     */
    const USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER = 1;

    /**
     * 给主播发消息
     */
    const USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_02_ANCHOR = 2;

    /**
     * 推送标题
     * @var type 
     */
    public static $push_title = [
        self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER => '陪玩主播已接单',
        self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_02_ANCHOR => '陪玩收到新订单',
    ];

    /**
     * 订单号尝试创建3次不成功后休眠1秒防止建冲突
     */
    const ORDER_CREATE_LOOP_TIMES = 3;

    private $_uid;
    private $_luid;
    private $_redis;
    private $_page;
    private $_size;

    /**
     * 订单写入数据
     * @var type 
     */
    private $_insertData = [];
    public $redisConfig = 'huanpeng';
    public $dueOrderObj = null;
    public $financeObj = null;

    public function setUid($uid) {
        $this->_uid = $uid;
    }

    public function setLuid($luid) {
        $this->_luid = $luid;
    }

    public function setPage($page) {
        $this->_page = $page;
        return $this;
    }

    public function getPage() {
        return $this->_page ? $this->_page : 1;
    }

    public function setSize($size) {
        $this->_size = $size;
        return $this;
    }

    public function getSize() {
        return $this->_size ? $this->_size : 5;
    }

    /**
     * 获取redis资源
     * @return type
     */
    public function getRedis() {
        if (is_null($this->_redis)) {
            $this->_redis = RedisHelper::getInstance($this->redisConfig);
        }
        return $this->_redis;
    }

    /**
     * 获取DueOrder资源
     * @return type
     */
    public function getDueOrderObj() {
        if (is_null($this->dueOrderObj)) {
            $this->dueOrderObj = new DueOrder();
        }
        return $this->dueOrderObj;
    }

    /**
     * 获取DueOrder资源
     * @return type
     */
    public function getFinanceObj() {
        if (is_null($this->financeObj)) {
            $this->financeObj = new Finance();
        }
        return $this->financeObj;
    }

    /**
     * 生产初始订单号
     * 订单号10秒自检是否唯一
     * @staticvar int $times
     * @return string
     */
    public function createOrderId() {
        static $orderLoopTimes = 0;
        list($usec, $sec) = explode(" ", microtime());
        $tmp = strval($usec);
        $param1 = date('ymdHis', $sec);
        $param2 = substr($usec, strpos($tmp, '.') + 1, 3);
        $param3 = rand(100, 999);
        $orderId = $param1 . $param2 . $param3;
        $value = 'ishave';
        $redisVal = $this->getRedis()->get($orderId);
        if ($redisVal == $value && $orderLoopTimes < self::ORDER_CREATE_LOOP_TIMES) {
            $orderLoopTimes++;
            return $this->createOrderId();
        } else if ($orderLoopTimes >= self::ORDER_CREATE_LOOP_TIMES) {
            sleep(1);
            $orderLoopTimes -= 3;
            return $this->createOrderId();
        }
        $this->getRedis()->set($orderId, $value, 10);
        return $orderId;
    }

    /**
     * (银行柜台)订单的所有和钱相关的操作平台
     * @param int $orderId 订单号码
     * @param int $toStatus 操作订单状态
     * @param string $reason  操作理由
     * @return boolean  
     * @throws Exception
     */
    public function bankTeller(int $orderId, int $toStatus, string $reason,int $couponId=0) {
        $db = DbHelper::getInstance(DueOrder::$dbConfig);
        try {
            //单例 开启事物
            $db->beginTransaction();
            //判断是否是新订单
            if ($toStatus == DueOrder::ORDER_STATUS_010_CREATE_ORDER) {
                $createOrderRes = $this->_insertOrder($this->_insertData,$couponId);
                if (!$createOrderRes) {
                    throw new \Exception(self::ERROR_CODE_05);
                }
                $orderInfo = $this->_insertData;
            }else{
                $orderInfo = $this->getOrderByOrderId($orderId);
            }
            //清理原因
            if (!in_array($toStatus, [DueOrder::ORDER_STATUS_120_USER_APPEAL_CUSTOMER_SERVICE])) {
                $this->updateOrderReason($orderId);
            }
            //改状态
            $updateResult = FALSE;
            $updateResult = $this->updateOrderStatus($orderId, $toStatus, $reason);
            if (!$updateResult) {
                throw new Exception(self::ERROR_CODE_15);
            }
            //扣款(根据修改后订状态处理)
            $payResult = FALSE;
            $payResult = $this->operateOrder($orderInfo, $toStatus);
            if (!$payResult) { 
                throw new Exception(self::ERROR_CODE_32);
            }
            $db->commit();
            return TRUE;
        } catch (Exception $e) {
            $db->rollback();
            write_log("order $orderId to $toStatus error:" . self::$error_info[$e->getMessage()],'order.pay.error.rollback');
            return FALSE;
        }
    }

    /**
     * 修改订单状态
     * @param int $orderId
     * @param int $status
     * @param string $reason
     * @return bool
     */
    public function updateOrderStatus(int $orderId, int $status, string $reason) {
        return $this->getDueOrderObj()->updateOrderStatusByOrderId($orderId, $status, $reason);
    }

    /**
     * 修改订单原因
     * @param int $orderId
     * @param int $status
     * @param string $reason
     * @return bool
     */
    public function updateOrderReason(int $orderId, $reason = '') {
        return $this->getDueOrderObj()->updateOrderReasonByOrderId($orderId, $reason);
    }

    /**
     * 修改订单评论状态
     * @param int $orderId
     * @param int $status
     * @return type
     */
    public function updateOrderCommentStatus(int $orderId, int $status = DueOrder::ORDER_COMMENT_STATUS_01_HAVE_COMMENT) {
        return $this->getDueOrderObj()->updateCommentStatusByOrderId($orderId, $status);
    }

    /**
     * 根据订单号获取订单信息。
     * @param int $orderId
     * @return array
     */
    public function getOrderByOrderId(int $orderId): array {
        return $this->getDueOrderObj()->getOrderDataByOrderId($orderId);
    }

    /**
     * 根据订单号获取订单信息。
     * @param int $orderId
     * @return array
     */
    public function getOrderByUidAndCertId(int $uid, int $certUid, $limit = 1): array {
        return $this->getDueOrderObj()->getOrderDataByUidAndCertId($uid, $certUid, $limit);
    }

    /**
     * 根据订单号获取订单信息。
     * @param int $orderId
     * @return array
     */
    public function getOrderForTwoPersons(int $uid1, int $uid2, $limit = 1) {
        return $this->getDueOrderObj()->getOrderDataForTwoPersons($uid1, $uid2, $limit);
    }

    /**
     * 根据订单号获取订单详情信息。
     * @param int $orderId
     * @return array
     */
    public function getOrderDetailByOrderId(int $orderId): array {
        return $this->getDueOrderObj()->getOrderDetailDataByOrderId($orderId);
    }

    /**
     * 订单创建:用户下单
     * @param int $userId 用户ID
     * @param int $skillId 技能ID
     * @param int $num 购买数量
     * @param int $startPlayTime 开始游戏时间
     * @param int $endPlayTime 结束游玩时间
     * @param type $memo
     * @param type $roleName 角色名称
     * @param type $contactType 联系方式
     * @param type $contact 联系号码
     * @return type int 小于0是错误码  大于0 是订单号
     * @throws \Exception
     */
    public function createOrder(int $userId, int $skillId, int $num, int $startPlayTime,int $couponId=0, $endPlayTime = 0, $memo = '', $roleName = '', $contactType = self::CONTACT_TYPE_00_NO_WAY, $contact = '') {
        try {
            $skillInfo = $this->getSkillInfo($skillId);
            if (empty($skillInfo)) {
                throw new \Exception(self::ERROR_CODE_08);
            }
            //检测约定时间
            $this->_checkPlayTime($startPlayTime);
            //检测是否可以下单
            $this->_checkCreateOrder($userId, $skillInfo['uid']);
            //扣除用户费用
            $money = $num * intval($skillInfo['price']);
            //检查是否有优惠
            if($couponId!=0){
                $disMoney = $this->_discount($userId, $couponId , $money);
            }else $disMoney = 0 ;  
            $realMoney = self::bcMatch($money, $disMoney, '-');
            if($realMoney<0){
                $realMoney = 0;
                $disMoney  = $money;
            }
            //判断余额是否足够
            $this->checkBalance($userId, $realMoney);
            //生成订单号
            $orderId = $this->createOrderId();
            //初始化缓存
            $this->getOrderNumByLuid($skillInfo['uid']);
            
            $data = [
                //order
                'order_id' => $orderId,
                'uid' => $userId,
                'cert_uid' => $skillInfo['uid'],
                'cert_id' => $skillInfo['cert_id'],
                'num' => $num,
                'price' => $skillInfo['price'],
                'discount' => $disMoney,
                'amount' => $money,
                'real_amount' => $realMoney,
                'memo' => $memo,
                'status' => DueOrder::ORDER_STATUS_000_DEFAULT,
                'comment' => DueOrder::ORDER_COMMENT_STATUS_00_DEFAULT,
                //detail
                'skill_id' => $skillId,
                'unit' => $skillInfo['unit'],
                'number' => $num,
                'start_time' => $startPlayTime,
                'end_time' => $endPlayTime,
                'role_name' => $roleName,
                'contact_type' => $contactType,
                'contact' => $contact,
            ];
            $this->_insertData = $data;
            $this->addOrderLog($orderId, DueOrder::ORDER_STATUS_000_DEFAULT, $userId);
            //扣款成功后更新订单状态
            $payResult = $this->bankTeller($orderId, DueOrder::ORDER_STATUS_010_CREATE_ORDER, '',$couponId);
            if ($payResult) {
                $this->addOrderLog($orderId, DueOrder::ORDER_STATUS_010_CREATE_ORDER, $userId);
            } else {
                throw new \Exception(self::ERROR_CODE_21);
            }

            //@todo 发送推送消息
            $this->sendPush($userId, $skillInfo['uid'], DueOrder::ORDER_STATUS_010_CREATE_ORDER);
            //浮层消息推送
            $this->sendPushToAnchor($skillInfo['uid'], $userId, $this->getOrderNumByLuid($skillInfo['uid']));
            return $orderId;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 订单状态改变判断
     * @param int $from
     * @param int $to
     * @throws Exception
     */
    protected static function orderMapCheck(int $from, int $to) {
        $res = DueOrder::OrderStatusFromToCheck($to, $from);
        if (!$res) {
            throw new Exception(self::ERROR_CODE_19);
        }
        return TRUE;
    }

    /**
     * 用戶取消订单
     * @param int $uid
     * @param int $orderId
     * @param int $status
     * @param string $reason
     * @return boolean
     * @throws Exception
     */
    public function userCancleOrderByOrderId(int $uid, int $orderId, string $reason = '') {
        try {
            $status = DueOrder::ORDER_STATUS_030_USER_CANCEL;
            //判断是否是自己的订单
            $orderInfo = $this->getOrderByOrderId($orderId);
            if (empty($orderInfo)) {
                throw new Exception(self::ERROR_CODE_13);
            }
            if ($uid != $orderInfo['uid']) {
                throw new Exception(self::ERROR_CODE_14);
            }
            self::orderMapCheck($orderInfo['status'], $status);
            $this->addOrderLog($orderId, $status, $uid, $reason);

            //修改订单状态, 财务处理
            $res = $this->bankTeller($orderId, $status, $reason);
            if (!$res) {
                throw new Exception(self::ERROR_CODE_16);
            }
            //待处理订单数自减
            $this->subOrderNumByLuid($orderInfo['cert_uid']);
            $this->sendPushToAnchor($orderInfo['cert_uid'], $uid, $this->getOrderNumByLuid($orderInfo['cert_uid']));
            //send msg
            $this->sendPush($uid, $orderInfo['cert_uid'], $status);
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 下单后超过一小时,系统取消订单
     * @param int $orderId
     * @param int $status
     * @param string $reason
     * @return bool
     */
    public function systemTimeOutCancleOrderByOrderId(int $systemId, int $orderId, string $reason = '') {

        try {
            //判断订单是否存在
            $status = DueOrder::ORDER_STATUS_020_TIMEOUT_CANCEL;
            $orderInfo = $this->getOrderByOrderId($orderId);
            if (empty($orderInfo)) {
                throw new Exception(self::ERROR_CODE_13);
            }
            //判断时间是否超时
            if (time() - strtotime($orderInfo['ctime']) < self::TIMER_TIME_OUT_01_HOUR) {
                throw new Exception(self::ERROR_CODE_17);
            }
            self::orderMapCheck($orderInfo['status'], $status);
            $this->addOrderLog($orderId, $status, $systemId, $reason);
            //修改订单状态, 财务处理

            $res = $this->bankTeller($orderId, $status, $reason);
            if (!$res) {
                throw new Exception(self::ERROR_CODE_16);
            }
            //待处理订单数自减
            $this->subOrderNumByLuid($orderInfo['cert_uid']);
            $this->sendPushToAnchor($orderInfo['cert_uid'], $orderInfo['uid'], $this->getOrderNumByLuid($orderInfo['cert_uid']));
            //send msg
            RongCloudService::getInstance();//[仅仅是为了加载文件]
            $this->sendSystemPush($orderInfo['uid'], DueOrder::$order_status[$status], \service\due\rongCloud\RongCloudServiceHelp::MSG_CG_07);
            $this->sendSystemPush($orderInfo['cert_uid'], DueOrder::$order_status[$status], \service\due\rongCloud\RongCloudServiceHelp::MSG_CG_07);
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 主播拒绝订单
     * @param int $certUid
     * @param int $orderId
     * @param int $status
     * @param string $reason
     * @return bool
     * @throws Exception
     */
    public function anchorRejectOrderByOrderId(int $certUid, int $orderId, string $reason) {

        try {
            $status = DueOrder::ORDER_STATUS_040_ANCHOR_REJECT_ORDER;
            //判断是否是自己的订单
            $orderInfo = $this->getOrderByOrderId($orderId);
            if (empty($orderInfo)) {
                throw new Exception(self::ERROR_CODE_13);
            }
            if ($certUid != $orderInfo['cert_uid']) {
                throw new Exception(self::ERROR_CODE_18);
            }
            self::orderMapCheck($orderInfo['status'], $status);
            $this->addOrderLog($orderId, $status, $certUid, $reason);
            //修改订单状态, 财务处理
            $res = $this->bankTeller($orderId, $status, $reason);
            if (!$res) {
                throw new Exception(self::ERROR_CODE_16);
            }
            //待处理订单数自减
            $this->subOrderNumByLuid($orderInfo['cert_uid']);
            $this->sendPushToAnchor($orderInfo['cert_uid'], $orderInfo['uid'], $this->getOrderNumByLuid($orderInfo['cert_uid']));
            //send msg
            $this->sendPush($orderInfo['cert_uid'], $orderInfo['uid'], $status);
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 主播接受订单
     * @param int $certUid
     * @param int $orderId
     * @param int $status
     * @param string $reason
     * @return bool
     * @throws Exception
     */
    public function anchorAcceptOrderByOrderId(int $certUid, int $orderId, string $reason = '') {

        try {
            $status = DueOrder::ORDER_STATUS_050_ANCHOR_ACCEPTING_ORDER;

            //判断是否是自己的订单
            $orderInfo = $this->getOrderByOrderId($orderId);
            if (empty($orderInfo)) {
                throw new Exception(self::ERROR_CODE_13);
            }
            if ($certUid != $orderInfo['cert_uid']) {
                throw new Exception(self::ERROR_CODE_18);
            }
            self::orderMapCheck($orderInfo['status'], $status);
            $this->addOrderLog($orderId, $status, $certUid);
            //修改订单状态, 财务处理
            $res = $this->bankTeller($orderId, $status, $reason);
            if (!$res) {
                throw new Exception(self::ERROR_CODE_16);
            }
            //待处理订单数自减
            $this->subOrderNumByLuid($orderInfo['cert_uid']);
            $this->sendPushToAnchor($orderInfo['cert_uid'], $orderInfo['uid'], $this->getOrderNumByLuid($orderInfo['cert_uid']));
            //send msg
            $this->sendPush($orderInfo['cert_uid'], $orderInfo['uid'], $status);
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 主播取消订单
     * @param int $uid
     * @param int $orderId
     * @param int $status
     * @param string $reason
     * @return bool
     */
    public function anchorCancleOrderByOrderId(int $certUid, int $orderId, string $reason) {
        try {

            $status = DueOrder::ORDER_STATUS_060_ANCHOR_CANCEL;

            //判断是否是自己的订单
            $orderInfo = $this->getOrderByOrderId($orderId);
            if (empty($orderInfo)) {
                throw new Exception(self::ERROR_CODE_13);
            }
            if ($certUid != $orderInfo['cert_uid']) {
                throw new Exception(self::ERROR_CODE_14);
            }
            self::orderMapCheck($orderInfo['status'], $status);
            $this->addOrderLog($orderId, $status, $certUid, $reason);
            //修改订单状态, 财务处理
            $res = $this->bankTeller($orderId, $status, $reason);
            if (!$res) {
                throw new Exception(self::ERROR_CODE_16);
            }
            //send msg
            $this->sendPush($orderInfo['cert_uid'], $orderInfo['uid'], $status);

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 超过陪玩时间24小时,用户没有点击确定,自动完成订单
     * @param int $orderId
     * @param int $status
     * @param string $reason
     * @return bool
     */
    public function systemTimeOutFinishedOrderByOrderId(int $systemId, int $orderId, string $reason = '') {
        try {
            $status = DueOrder::ORDER_STATUS_070_TIMEOUT_FINISHED;
            //判断订单是否存在
            $orderDetailInfo = $this->getDueOrderObj()->getOrderDetailDataByOrderId($orderId);
            if (empty($orderDetailInfo)) {
                throw new Exception(self::ERROR_CODE_13);
            }
            //判断时间是否超时
            if (time() - $orderDetailInfo['start_time'] < self::TIMER_TIME_OUT_24_HOUR) {
                throw new Exception(self::ERROR_CODE_17);
            }
            $orderInfo = $this->getOrderByOrderId($orderId);
            self::orderMapCheck($orderInfo['status'], $status);
            $this->addOrderLog($orderId, $status, $systemId, $reason);
            //修改订单状态, 财务处理
            $res = $this->bankTeller($orderId, $status, $reason);
            if (!$res) {
                throw new Exception(self::ERROR_CODE_16);
            }
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 确认订单超过24小时
     * @param int $orderId
     * @param int $status
     * @param string $reason
     * @return bool
     */
    public function systemTimeOutOverFinishedOrderByOrderId(int $systemId, int $orderId, string $reason = '') {
        try {
            $status = DueOrder::ORDER_STATUS_150_TIMEOUT_OVER_FINISHED_ORDER;
            //判断订单是否存在
            $orderInfo = $this->getOrderByOrderId($orderId);
            if (empty($orderInfo)) {
                throw new Exception(self::ERROR_CODE_13);
            }
            //判断时间是否超时
            if (time() - strtotime($orderInfo['otime']) < self::TIMER_TIME_OUT_24_HOUR) {
                throw new Exception(self::ERROR_CODE_17);
            }
            self::orderMapCheck($orderInfo['status'], $status);
            $this->addOrderLog($orderId, $status, $systemId, $reason);
            //修改订单状态, 财务处理
            $res = $this->bankTeller($orderId, $status, $reason);
            if (!$res) {
                throw new Exception(self::ERROR_CODE_16);
            }
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 主播取消订单
     * @param int $uid
     * @param int $orderId
     * @param int $status
     * @param string $reason
     * @return bool
     */
    public function userFinishOrderByOrderId(int $uid, int $orderId, string $reason = '') {
        try {
            $status = DueOrder::ORDER_STATUS_080_USER_FINISHED;

            //判断是否是自己的订单  
            $orderInfo = $this->getOrderByOrderId($orderId);
            if (empty($orderInfo)) {
                throw new Exception(self::ERROR_CODE_13);
            }
            if ($uid != $orderInfo['uid']) {
                throw new Exception(self::ERROR_CODE_14);
            }
            self::orderMapCheck($orderInfo['status'], $status);
            $this->addOrderLog($orderId, $status, $uid);
            //修改订单状态, 财务处理
            $res = $this->bankTeller($orderId, $status, $reason);
            if (!$res) {
                throw new Exception(self::ERROR_CODE_16);
            }
            //send msg
            $this->sendPush($orderInfo['uid'], $orderInfo['cert_uid'], $status);
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 结束订单(最后一步)
     * @param type $systemId 操作ID
     * @param int $orderId
     * @param string $reason
     * @return boolean
     * @throws Exception
     */
    public function systemEndOrderByOrderId($systemId, int $orderId, string $reason = '') {
        try {
            $status = DueOrder::ORDER_STATUS_1000_ORDER_END_AND_SETTLEMENT;
            $orderInfo = $this->getOrderByOrderId($orderId);
            if (empty($orderInfo)) {
                throw new Exception(self::ERROR_CODE_13);
            }
            self::orderMapCheck($orderInfo['status'], $status);
            $this->addOrderLog($orderId, $status, $systemId);
            //修改订单状态, 财务处理
            $res = $this->bankTeller($orderId, $status, $reason);
            if (!$res) {
                throw new Exception(self::ERROR_CODE_16);
            }
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 订单取消(退款)
     * @param type $systemId 操作ID
     * @param int $orderId
     * @param string $reason
     * @return boolean
     * @throws Exception
     */
    public function systemRefundOrderByOrderId($systemId, int $orderId, string $reason = '') {
        try {
            $status = DueOrder::ORDER_STATUS_1010_ORDER_CANCELLED;
            $orderInfo = $this->getOrderByOrderId($orderId);
            if (empty($orderInfo)) {
                throw new Exception(self::ERROR_CODE_13);
            }
            self::orderMapCheck($orderInfo['status'], $status);
            $this->addOrderLog($orderId, $status, $systemId, $reason);
            //修改订单状态, 财务处理
            $res = $this->bankTeller($orderId, $status, $reason);
            if (!$res) {
                throw new Exception(self::ERROR_CODE_16);
            }
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 订单退单(退款)
     * @param type $systemId 操作ID
     * @param int $orderId
     * @param string $reason
     * @return boolean
     * @throws Exception
     */
    public function systemBackOrderByOrderId($systemId, int $orderId, string $reason = '') {
        try {
            $status = DueOrder::ORDER_STATUS_1020_ORDER_BACK;
            $orderInfo = $this->getOrderByOrderId($orderId);
            if (empty($orderInfo)) {
                throw new Exception(self::ERROR_CODE_13);
            }
            self::orderMapCheck($orderInfo['status'], $status);
            $this->addOrderLog($orderId, $status, $systemId, $reason);
            //修改订单状态, 财务处理
            $res = $this->bankTeller($orderId, $status, $reason);
            if (!$res) {
                throw new Exception(self::ERROR_CODE_16);
            }
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 用户退单
     * @param int $uid
     * @param int $orderId
     * @param int $status
     * @param string $reason
     * @return boolean
     * @throws Exception
     */
    public function userBackOrderByOrderId(int $uid, int $orderId, string $reason) {

        try {
            $status = DueOrder::ORDER_STATUS_090_USER_BACK_ORDER;

            //判断是否是自己的订单
            $orderInfo = $this->getOrderByOrderId($orderId);
            if (empty($orderInfo)) {
                throw new Exception(self::ERROR_CODE_13);
            }
            if ($uid != $orderInfo['uid']) {
                throw new Exception(self::ERROR_CODE_14);
            }

            self::orderMapCheck($orderInfo['status'], $status);
            $this->addOrderLog($orderId, $status, $uid, $reason);
            //修改订单状态, 财务处理
            $res = $this->bankTeller($orderId, $status, $reason);
            if (!$res) {
                throw new Exception(self::ERROR_CODE_16);
            }
            //send msg
            $this->sendPush($orderInfo['uid'], $orderInfo['cert_uid'], $status);

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 主播同意退单
     * @param int $uid
     * @param int $orderId
     * @param int $status
     * @param string $reason
     * @return bool
     */
    public function anchorAgreeBackOrderByOrderId(int $certUid, int $orderId, string $reason = '') {
        try {
            $status = DueOrder::ORDER_STATUS_100_ANCHOR_AGREE_BACK;

            //判断是否是自己的订单
            $orderInfo = $this->getOrderByOrderId($orderId);
            if (empty($orderInfo)) {
                throw new Exception(self::ERROR_CODE_13);
            }
            if ($certUid != $orderInfo['cert_uid']) {
                throw new Exception(self::ERROR_CODE_14);
            }
            self::orderMapCheck($orderInfo['status'], $status);
            $this->addOrderLog($orderId, $status, $certUid, $reason);
            //修改订单状态, 财务处理
            $res = $this->bankTeller($orderId, $status, $reason);
            if (!$res) {
                throw new Exception(self::ERROR_CODE_16);
            }
            //send msg
            $this->sendPush($orderInfo['cert_uid'], $orderInfo['uid'], $status);


            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 系统在用户申诉后,主播未处理,超过24小时自动退款
     * @param int $systemId  系统处理ID
     * @param int $orderId
     * @param string $reason
     * @return bool  
     */
    public function systemTimeOutBackOrderByOrderId(int $systemId, int $orderId, string $reason = '') {
        try {
            $status = DueOrder::ORDER_STATUS_160_TIMEOUT_USER_BACK_ORDER;
            $orderInfo = $this->getOrderByOrderId($orderId);
            if (empty($orderInfo)) {
                throw new Exception(self::ERROR_CODE_13);
            }
            self::orderMapCheck($orderInfo['status'], $status);
            //判断时间是否超时
            if (time() - strtotime($orderInfo['otime']) < self::TIMER_TIME_OUT_24_HOUR) {
                throw new Exception(self::ERROR_CODE_17);
            }
            $this->addOrderLog($orderId, $status, $systemId);
            //修改订单状态, 财务处理
            $res = $this->bankTeller($orderId, $status, $reason);
            if (!$res) {
                throw new Exception(self::ERROR_CODE_16);
            }
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 主播不同意退单
     * @param int $uid
     * @param int $orderId
     * @param int $status
     * @param string $reason
     * @return bool
     */
    public function anchorDisagreeBackOrderByOrderId(int $certUid, int $orderId, string $reason = '') {
        try {
            $status = DueOrder::ORDER_STATUS_110_ANCHOR_DISAGREE_BACK;

            //判断是否是自己的订单
            $orderInfo = $this->getOrderByOrderId($orderId);
            if (empty($orderInfo)) {
                throw new Exception(self::ERROR_CODE_13);
            }
            if ($certUid != $orderInfo['cert_uid']) {
                throw new Exception(self::ERROR_CODE_14);
            }
            self::orderMapCheck($orderInfo['status'], $status);
            $this->addOrderLog($orderId, $status, $certUid, $reason);
            //修改订单状态, 财务处理
            $res = $this->bankTeller($orderId, $status, $reason);
            if (!$res) {
                throw new Exception(self::ERROR_CODE_16);
            }
            //send msg
            $this->sendPush($orderInfo['cert_uid'], $orderInfo['uid'], $status);

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 用户申诉到客服
     * @param int $uid
     * @param int $orderId
     * @param int $status
     * @param string $reason
     * @return bool
     */
    public function userAppealOrderByOrderId(int $uid, int $orderId, string $reason) {
        try {
            $status = DueOrder::ORDER_STATUS_120_USER_APPEAL_CUSTOMER_SERVICE;

            //判断是否是自己的订单
            $orderInfo = $this->getOrderByOrderId($orderId);
            if (empty($orderInfo)) {
                throw new Exception(self::ERROR_CODE_13);
            }
            if ($uid != $orderInfo['uid']) {
                throw new Exception(self::ERROR_CODE_14);
            }
            self::orderMapCheck($orderInfo['status'], $status);
            $this->addOrderLog($orderId, $status, $uid, $reason);
            //修改订单状态, 财务处理
            $res = $this->bankTeller($orderId, $status, '');
            if (!$res) {
                throw new Exception(self::ERROR_CODE_16);
            }
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 主播不同意退单后,24小时用户申述客服 结束订单
     * @param int $uid
     * @param int $orderId
     * @param int $status
     * @param string $reason
     * @return bool  
     */
    public function systemTimeOutUserNotAppealOrderByOrderId(int $systemId, int $orderId, string $reason = '') {
        try {
            $status = DueOrder::ORDER_STATUS_170_TIMEOUT_USER_NOT_APPEAL_CUSTOMER_SERVICE;
            //判断是否是自己的订单
            $orderInfo = $this->getOrderByOrderId($orderId);
            if (empty($orderInfo)) {
                throw new Exception(self::ERROR_CODE_13);
            }
            //判断时间是否超时
            if (time() - strtotime($orderInfo['otime']) < self::TIMER_TIME_OUT_24_HOUR) {
                throw new Exception(self::ERROR_CODE_17);
            }
            self::orderMapCheck($orderInfo['status'], $status);
            //修改订单状态
            $this->addOrderLog($orderId, $status, $systemId, $reason);
            //修改订单状态, 财务处理
            $res = $this->bankTeller($orderId, $status, $reason);
            if (!$res) {
                throw new Exception(self::ERROR_CODE_16);
            }
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 客服处理申诉订单(驳回)
     * @param int $orderId
     * @param int $status
     * @param string $reason
     * @return boolean
     * @throws Exception
     */
    public function customerServiceDisagreeOrderByOrderId(int $adminId, int $orderId, string $reason = '') {
        try {
            $status = DueOrder::ORDER_STATUS_140_CUSTOMER_SERVICE_DISAGREE;

            //判断是否是自己的订单
            $orderInfo = $this->getOrderByOrderId($orderId);
            if (empty($orderInfo)) {
                throw new Exception(self::ERROR_CODE_13);
            }
            self::orderMapCheck($orderInfo['status'], $status);
            $this->addOrderLog($orderId, $status, $adminId, $reason);
            //修改订单状态, 财务处理
            $res = $this->bankTeller($orderId, $status, $reason);
            if (!$res) {
                throw new Exception(self::ERROR_CODE_16);
            }
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 客服处理申诉订单(通过)
     * @param int $orderId
     * @param int $status
     * @param string $reason
     * @return boolean
     * @throws Exception
     */
    public function customerServiceAgreeOrderByOrderId(int $adminId, int $orderId, string $reason = '') {
        try {
            $status = DueOrder::ORDER_STATUS_130_CUSTOMER_SERVICE_AGREE;
            //判断是否是自己的订单
            $orderInfo = $this->getOrderByOrderId($orderId);
            if (empty($orderInfo)) {
                throw new Exception(self::ERROR_CODE_13);
            }
            self::orderMapCheck($orderInfo['status'], $status);
            $this->addOrderLog($orderId, $status, $adminId, $reason);

            //修改订单状态, 财务处理
            $res = $this->bankTeller($orderId, $status, $reason);
            if (!$res) {
                throw new Exception(self::ERROR_CODE_16);
            }
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 写入新订单
     * @staticvar int $insertOrderNum
     * @param type $data
     * @return boolean
     */
    protected function _insertOrder($data,$couponId) {
        static $insertOrderNum = 0;
        $dueOrder = $this->getDueOrderObj();
        $res = $dueOrder->insertOrder($data);
        if (!$res && $insertOrderNum < 3) {
            $dueOrder->_insertOrder($data); 
            $insertOrderNum++;
        } else {  
            $this->addOrderNumByLuid($data['cert_uid']);  
            //优惠券使用状态更新
            if($couponId!=0){
                $updateCouponRes = $this->updateCouponStatus($data['uid'],$data['order_id'],$couponId);
                if(!$updateCouponRes){
                    throw new Exception(self::ERROR_CODE_30);
                }
            }
            return $res;
        }
        return FALSE;
    }

    /**
     * 下单前检测
     * @param type $uid
     * @param type $certUid
     */
    protected function _checkCreateOrder($uid, $certUid) {
        return true;
        #1 检测两者之间是否有未完成的订单,如果有那么无法下新的订单
        $res = $this->getDueOrderObj()->getDoingOrderDataByUidAndCertId($uid, $certUid);
        if (!empty($res)) {
            throw new Exception(self::ERROR_CODE_26);
        }
    }

    /**
     * 订单时间判断
     * @param type $startPlayTime
     * @param type $endPlayTime
     * @param type $startInfoTime
     * @param type $endInfoTime
     * @param type $week
     * @return boolean
     * @throws \Exception
     */
    protected function _checkPlayTime($startPlayTime, $endPlayTime = '', $startInfoTime = '', $endInfoTime = '', $week = '') {
        $nowTime = time();
        $maxTime = strtotime(date('Y-m-d 23:59:59', $nowTime + 86400 * 2));
        if ($startPlayTime > $maxTime) {
            throw new \Exception(self::ERROR_CODE_10);
        }
        if ($startPlayTime % 600 != 0) {
            throw new \Exception(self::ERROR_CODE_01);
        }
        if ($startPlayTime < $nowTime) {
            throw new \Exception(self::ERROR_CODE_09);
        }
        return TRUE;
        if ($startPlayTime >= $endPlayTime) {
            throw new \Exception(self::ERROR_CODE_01);
        }
        if ($endPlayTime - $startPlayTime < self::PALY_MIN_TIEM) {
            throw new \Exception(self::ERROR_CODE_02);
        }
        if ($endPlayTime - $startPlayTime > self::PALY_MAX_TIEM) {
            throw new \Exception(self::ERROR_CODE_03);
        }
        if (date('d', $startPlayTime) != date('d', $endPlayTime)) {
            throw new \Exception(self::ERROR_CODE_04);
        }
        if (!in_array(date('N', $startPlayTime), explode(',', $week))) {
            throw new \Exception(self::ERROR_CODE_05);
        }

        $spt = $startPlayTime % 86400;
        $ept = $startPlayTime % 86400;
        if (!($spt >= $startInfoTime && $ept <= $endInfoTime)) {
            throw new \Exception(self::ERROR_CODE_05);
        }
        return TRUE;
    }

    /**
     * 余额检测
     * @param type $userId
     * @param type $payHbCoin
     * @throws Exception
     */
    public function checkBalance($userId, $payHbCoin) {
        $balance = $this->getFinanceObj()->getBalance($userId);
        if ($balance['hb'] < $payHbCoin) {
            throw new \Exception(self::ERROR_CODE_22);
        }
        return true;
    }

    /**
     * 检测是否有优惠
     * @param type $uid
     * @return int  优惠金额
     */
    protected function _discount($uid, $couponId ,$money) {
        $couponService = new DueCouponService();
        $couponService->setUid($uid);
        //获取用户当天已使用 优惠券次数
        $todayNum = $couponService->todayUseCouponNum();
        $todayNum = $todayNum[0]['todayUseNum'];
        if($todayNum>=DueCouponConfig::USABLE_NIMBER)
            throw new \Exception(self::ERROR_CODE_28);
        //检查优惠券是否过期/是否已经使用
        $coupon = $couponService->getCouponUsable($uid, $couponId ,$money);
        if($coupon == false )
            throw new \Exception(self::ERROR_CODE_29);
        return $coupon[0]['price'];
    }

    /**
     * 获取技能的信息
     * @param type $skillId
     * @return type
     */
    public function getSkillInfo($skillId) {
        /**
         * @todo  DueCertService 应该提供更好的方法
         */
        $cert = new DueCertService();
        $res = $cert->getSkillBySkillId(['skillId' => $skillId]);
        $certInfo = $cert->getCertInfoByCertIds($res[0]['cert_id']);
        if (DueCertService::setStatus($certInfo[$res[0]['cert_id']]['status']) == DueCertService::CERT_STATUS_PASS && $res[0]['switch'] == 1) {
            return $res[0];
        } else {
            return [];
        }
    }

    /**
     * 发送推送
     * @param type $uid
     * @param type $type
     */
    public function sendPush($fromUid, $toUid, $orderStatus) {
        /**
         * 暂时使用站内信
         * 后期采用推送技术
         */
        switch (self::$choice_push_type) {
            case self::USER_MESSAGE_PUSH_TYPE_02_RONG_CLOUD:
                $this->rongCloudSend($fromUid, $toUid, $orderStatus);
                break;
            case self::USER_MESSAGE_PUSH_TYPE_01_SITE_MSG:
                break;
            default :
                break;
//                //消息发送
//                //to user
//                $user = new UserDataService();
//                $userInfo = $user->setUid($fromUid)->getUserInfo();
//                $msg = [
//                    'uid' => $uid,
//                    'title' => self::$push_title[$type],
//                ];
//                if ($type == self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER) {
//                    $msg['content'] = $userInfo['nick'] . '即将带你飞,请保证在线哦!';
//                } else {
//                    $msg['content'] = $userInfo['nick'] . '要你陪玩,请尽快接单哦!';
//                }
//                $this->_doSend($msg);
//                break;
        }
    }

    /**
     * site msg send 
     * @param type $msg
     * @return boolean
     * @throws Exception
     */
    private function _doSend($msg) {
        $package = MsgPackage::getSiteMsgPackage($msg['uid'], $msg['title'], $msg['content'], MsgPackage::SITEMSG_TYPE_TO_USER);
        $siteMsg = new SiteMsgBiz();
        $r = $siteMsg->sendMsg($package);
        if (!$r) {
            throw new \Exception(self::ERROR_CODE_11);
        }
        return TRUE;
    }

    /**
     * 融云消息发送
     * @param type $fromUid  
     * @param type $toUid
     * @param type $orderStatus 订单状态
     * @return boolean
     * @throws Exception
     */
    public function rongCloudSend($fromUid, $toUid, $orderStatus) {
        $user = new UserDataService();
        $send = RongCloudService::getInstance();
        $userInfo = $user->setUid($fromUid)->getUserInfo();
        switch ($orderStatus) {
            case DueOrder::ORDER_STATUS_010_CREATE_ORDER:
                $content = $userInfo['nick'] . ' 要你陪玩，请尽快接单哦！';
                $param = \service\due\rongCloud\RongCloudServiceHelp::MSG_CG_01;
                break;
            case DueOrder::ORDER_STATUS_040_ANCHOR_REJECT_ORDER:
                $content = $userInfo['nick'] . ' 主播忙其它的事情了，下次再约';
                $param = \service\due\rongCloud\RongCloudServiceHelp::MSG_CG_04;
                break;
            case DueOrder::ORDER_STATUS_050_ANCHOR_ACCEPTING_ORDER:
                $content = $userInfo['nick'] . ' 即将带你飞，请保证在线哦！';
                $param = \service\due\rongCloud\RongCloudServiceHelp::MSG_CG_03;
                break;
            case DueOrder::ORDER_STATUS_090_USER_BACK_ORDER:
                $content = $userInfo['nick'] . ' 想要退单，请处理';
                $param = \service\due\rongCloud\RongCloudServiceHelp::MSG_CG_02;
                break;
            case DueOrder::ORDER_STATUS_060_ANCHOR_CANCEL:
                $content = $userInfo['nick'] . ' 已取消接单，请查看订单详情';
                $param = \service\due\rongCloud\RongCloudServiceHelp::MSG_CG_05;
                break;
            default:
                //通知客户端更新页面,忽略消息内容
                $content = 'huanpeng:' . $orderStatus;
                $param = \service\due\rongCloud\RongCloudServiceHelp::MSG_CG_00;
                break;
        }
        //-----消息类型-----
//	const MSG_CG_01 = 10001; //用户下单 通知主播
//	const MSG_CG_02 = 10002; //用户退单 通知主播
//	const MSG_CG_03 = 10003; //主播接单 通知用户
//	const MSG_CG_04 = 10004; //主播拒单 通知用户
        if ($param > 0) {
            $r = $send->addMsgList($fromUid, $toUid, $content, \service\due\rongCloud\RongCloudServiceHelp::OBJECT_NAME_13, $param);
        } else {
            $r = TRUE;
        }
        if (!$r) {
            throw new \Exception(self::ERROR_CODE_12);
        }
        return TRUE;
    }

    /**
     * 系统消息推送
     * @param type $param
     * @return type
     */
    public function sendSystemPush($toUid, $message, $param = null) {
        $send = RongCloudService::getInstance();
        //系统消息发送
        $content = "系统通知:" . $message;
        $param = $param ? $param : \service\due\rongCloud\RongCloudServiceHelp::MSG_CG_00;
        return $send->addMsgList(\service\due\rongCloud\RongCloudServiceHelp::RONG_MSG_ADMIN_UID, $toUid, $content, \service\due\rongCloud\RongCloudServiceHelp::OBJECT_NAME_13, $param);
    }

    /**
     * 用户APP弹框消息推主播
     * @param type $luid
     * @param type $uid
     * @param type $num
     * @return boolean
     * @throws \Exception
     */
    public function sendPushToAnchor($luid, $uid, $num) {
        $msg1 = MsgPackage::getNewDueOrderNumMsgSocketPackage($luid, $uid, $num, 0);
        $msg2 = MsgPackage::getNewDueOrderNumMsgSocketPackage($luid, $uid, $num, 1);
        $r1 = SocketSend::sendMsg($msg1, (new DBHelperi_huanpeng()));
        $r2 = SocketSend::sendMsg($msg2, (new DBHelperi_huanpeng()));

        if (!$r1 || !$r2) {
            write_log(__FILE__ . __METHOD__ . "send message Fail ");
//            throw new \Exception(self::ERROR_CODE_23);
        }
        return TRUE;
    }

    /**
     * 我的订单
     */
    public function getMyOrderList() {
        $dueOrder = new DueOrder();
        $orderList = $dueOrder->getUserOrderList($this->_uid, $this->getPage(), $this->getSize());
        return $orderList;
    }

    /**
     * 我的详细订单
     */
    public function getMyOrderDetailList() {
        $dueOrder = new DueOrder();
        $orderList = $dueOrder->getUserDetailOrderList($this->_uid, $this->getPage(), $this->getSize());
        return $orderList;
    }

    /**
     * 我的接单
     */
    public function getMyCertOrderList() {
        $dueOrder = new DueOrder();
        $certOrderList = $dueOrder->getCertUserOrderList($this->_uid, $this->getPage(), $this->getSize());
        return $certOrderList;
    }

    /**
     * 获取订单总数
     * @param int $uid
     * @return int
     */
    public function getTotalNumberByUid(int $uid): int {
        return $this->getDueOrderObj()->getOrderTotalByUid($uid);
    }

    /**
     * 按状态获取主播订单总数
     * @param int $certUid
     * @return int
     */
    public function getTotalNumberByCertUid(int $certUid, $status = 0): int {
        return $this->getDueOrderObj()->getOrderTotalByCertUid($certUid, $status);
    }

    /**
     * 下单聊天状态
     * @param int $status
     * @return string
     */
    public static function getOrderRongCloudStatusMessage(int $status) {
        switch ($status) {
            case DueOrder::ORDER_STATUS_010_CREATE_ORDER:
                $message = '待接单';
                $code = 1;
                break;
            case DueOrder::ORDER_STATUS_050_ANCHOR_ACCEPTING_ORDER:
                $message = '进行中';
                $code = 2;
                break;
            case DueOrder::ORDER_STATUS_070_TIMEOUT_FINISHED:
            case DueOrder::ORDER_STATUS_080_USER_FINISHED:
                $message = '已完成';
                $code = 3;
                break;
            default :
                $code = 0;
                $message = '未知状态';
        }
        return ['code' => $code, 'desc' => $message];
    }

    /**
     * 总流程状态「详情页使用」
     * @param int $status
     * @param type $roleType
     * @param type $reason 
     * @return type  ['order' => "返回订单状态", 'pay' => "支付状态", 'resaon' => "格式化原因"]
     */
    public static function getOrderStatusMessage(int $status, $roleType = self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER, $reason = '') {
        switch ($status) {
            case DueOrder::ORDER_STATUS_010_CREATE_ORDER:
                $orderMessage = '待接单';
                $reasonMessagePre = '' . $reason;
                $pay = $roleType == self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER ? '已支付' : '未到账';
                break;
            case DueOrder::ORDER_STATUS_020_TIMEOUT_CANCEL:
                $orderMessage = '已取消';
                $reasonMessagePre = '系统自动取消，原因：主播未接单';
                $pay = $roleType == self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER ? '退款中' : '未到账';
                break;
            case DueOrder::ORDER_STATUS_030_USER_CANCEL:
                $orderMessage = '已取消';
                $reasonMessagePre = '原因：' . $reason;
                $pay = $roleType == self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER ? '退款中' : '未到账';
                break;
            case DueOrder::ORDER_STATUS_040_ANCHOR_REJECT_ORDER:
                $orderMessage = '已拒单';
                $reasonMessagePre = '原因：' . $reason;
                $pay = $roleType == self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER ? '退款中' : '未到账';
                break;
            case DueOrder::ORDER_STATUS_050_ANCHOR_ACCEPTING_ORDER:
                $orderMessage = '进行中';
                $reasonMessagePre = '';
                $pay = $roleType == self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER ? '已支付' : '未到账';
                break;
            case DueOrder::ORDER_STATUS_060_ANCHOR_CANCEL :
                $orderMessage = '已取消';
                $reasonMessagePre = '原因：' . $reason;
                $pay = $roleType == self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER ? '退款中' : '未到账';
                break;
            case DueOrder::ORDER_STATUS_070_TIMEOUT_FINISHED:
                $orderMessage = '已完成';
                $reasonMessagePre = '系统自动完成';
                $pay = $roleType == self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER ? '已支付' : '未到账 (收益将在订单完成24小时后到账)';
                break;
            case DueOrder::ORDER_STATUS_080_USER_FINISHED:
                $orderMessage = '已完成';
                $reasonMessagePre = '用户确认完成';
                $pay = $roleType == self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER ? '已支付' : '未到账 (收益将在订单完成24小时后到账)';
                break;
            case DueOrder::ORDER_STATUS_090_USER_BACK_ORDER:
                $orderMessage = '退单中';
                $reasonMessagePre = '原因：' . $reason;
                $pay = $roleType == self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER ? '退款中' : '未到账';
                break;
            case DueOrder::ORDER_STATUS_100_ANCHOR_AGREE_BACK:
                $orderMessage = '已退单';
                $reasonMessagePre = '主播同意退单';
                $pay = $roleType == self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER ? '退款中' : '未到账';
                break;
            case DueOrder::ORDER_STATUS_110_ANCHOR_DISAGREE_BACK:
                $orderMessage = '已完成';
                $reasonMessagePre = '退单未通过，原因:' . $reason;
                $pay = $roleType == self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER ? '已支付' : '未到账 (收益将在订单完成24小时后到账)';
                break;
            case DueOrder::ORDER_STATUS_120_USER_APPEAL_CUSTOMER_SERVICE:
                $orderMessage = $roleType == self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER ? '申诉中' : '已完成';
                $reasonMessagePre = $roleType == self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER ? '申诉中，请耐心等待' : '退单未通过，原因:' . $reason;
                $pay = $roleType == self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER ? '已支付' : '未到账 (收益将在订单完成24小时后到账)';
                break;
            case DueOrder::ORDER_STATUS_130_CUSTOMER_SERVICE_AGREE:
                $orderMessage = '已退单';
                $reasonMessagePre = '平台退单，原因:' . $reason;
                $pay = $roleType == self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER ? '退款中' : '未到账';
                break;
            case DueOrder::ORDER_STATUS_140_CUSTOMER_SERVICE_DISAGREE:
                $orderMessage = '已完成';
                $reasonMessagePre = $roleType == self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER ? '平台未退单，原因:' . $reason : '';
                $pay = $roleType == self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER ? '已支付' : '未到账 (收益将在订单完成24小时后到账)';
                break;
            case DueOrder::ORDER_STATUS_150_TIMEOUT_OVER_FINISHED_ORDER:
                $orderMessage = '已完成';
                $reasonMessagePre = '';
                $pay = $roleType == self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER ? '已支付' : '未到账 (收益将在订单完成24小时后到账)';
                break;
            case DueOrder::ORDER_STATUS_160_TIMEOUT_USER_BACK_ORDER:
                $orderMessage = '已退单';
                $reasonMessagePre = '系统自动退单，原因:主播超时未处理';
                $pay = $roleType == self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER ? '退款中' : '未到账';
                break;
            case DueOrder::ORDER_STATUS_170_TIMEOUT_USER_NOT_APPEAL_CUSTOMER_SERVICE:
                $orderMessage = '已完成';
                $reasonMessagePre = '用户未申诉平台';
                $pay = $roleType == self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER ? '已支付' : '未到账 (收益将在订单完成24小时后到账)';
                break;
            case DueOrder::ORDER_STATUS_1000_ORDER_END_AND_SETTLEMENT:
                $orderMessage = '已完成';
                $reasonMessagePre = '';
                $pay = $roleType == self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER ? '已支付' : '已到账';
                break;
            case DueOrder::ORDER_STATUS_1010_ORDER_CANCELLED:
                $orderMessage = '已取消';
                $reasonMessagePre = '';
                $pay = $roleType == self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER ? '已退款' : '未到账';
                break;
            case DueOrder::ORDER_STATUS_1020_ORDER_BACK:
                $orderMessage = '已退单';
                $reasonMessagePre = '';
                $pay = $roleType == self::USER_MESSAGE_PUSH_TO_PEOPLE_ROLE_01_USER ? '已退款' : '未到账';
                break;
            default :
                $orderMessage = '未知状态';
                $reasonMessagePre = '';
                $pay = '未知状态';
        }
        return ['order' => $orderMessage, 'pay' => $pay, 'reason' => $reasonMessagePre];
    }

    /**
     * 返回给用户的说明
     * @param int $status
     * @return string
     */
    public static function getOrderReturnStatusMessage(int $status) {
        return DueOrder::$order_status[$status];
    }

    /**
     * 返回角色当前可以执行的操作
     * @param int $userRole  用户角色
     * @param int $currentOrderStatus  订单当前状态
     * @return array
     */
    public function getUserOrderAction(int $userRole, int $currentOrderStatus): array {
        $res = DueOrder::$user_order_status_action[$userRole][$currentOrderStatus];
        $result = [];
        if ($res != FALSE) {
            foreach ($res as $action) {
                $result[] = ['state' => $action, 'desc' => DueOrder::$order_status_action[$action]];
            }
        }
        return $result;
    }

    #####【浮层上订单数量展示 start】 

    /**
     * 获取最近的一条数据
     * @param int $luid
     * @return array
     */
    public function getLastOrder(int $luid): array {
        $dueOrder = $this->getDueOrderObj();
        $res = $dueOrder->getCertUserOrderList($luid, 1, 1);
        return $res[0] ?? [];
    }

    /**
     * 获取主播新增订单数
     * @param int $luid
     * @return type
     */
    public function getOrderNumByLuid(int $luid) {
        $num = 0;
        $luid = intval($luid);
        if ($luid > 0) {
            $redis = $this->getRedis();
            $key = self::getOrderNumCacheKeyByLuid($luid);
            $num = $redis->hGet($key, self::ORDER_NUMBER_CACHE_KEY_FIELD_NUM);
            if ($num === false || $num < 0) {
                //待处理订单数小于0说明数据出错,重置
                $res = $this->getLastOrder($luid);
                $num = $this->CleanOrderNumByLuid($luid, isset($res['ctime']) ?? '');
            }
        }
        return (int) $num;
    }

    /**
     * 清理
     * @param int $luid  
     * @param int $increment 增量
     * @return int 新的订单数
     */
    public function CleanOrderNumByLuid(int $luid, $time = '', $num = 0) {
        $res = FALSE;
        $luid = intval($luid);
        if ($luid > 0) {
            $redis = $this->getRedis();
            $key = self::getOrderNumCacheKeyByLuid($luid);
            $time = $time ? $time : date("Y-m-d H:i:s");
            $value = [
                self::ORDER_NUMBER_CACHE_KEY_FIELD_TIME => $time,
                self::ORDER_NUMBER_CACHE_KEY_FIELD_NUM => $num ? $num : $this->getTotalNumberByCertUid($luid, DueOrder::ORDER_STATUS_010_CREATE_ORDER),
            ];

            $res = $redis->hMSet($key, $value);
            $redis->expireAt($key, time() + 86400 * 15);
        }
        return (int) $num;
    }

    /**
     * 订单数自增
     * @param int $luid
     * @param int $increment
     * @return int 
     */
    public function addOrderNumByLuid(int $luid, int $increment = 1) {
        $res = FALSE;
        if ($luid > 0) {
            $redis = $this->getRedis();
            $key = self::getOrderNumCacheKeyByLuid($luid);
            ## 保证key存在
            $this->getOrderNumByLuid($luid); //创建key值
            $res = $redis->hIncrBy($key, self::ORDER_NUMBER_CACHE_KEY_FIELD_NUM, $increment);
        } 
        return (int) $res;
    }

    /**
     * 订单数自减
     * @param int $luid
     * @param int $increment
     * @return int 
     */
    public function subOrderNumByLuid(int $luid, int $increment = -1) {
        $res = FALSE;
        if ($luid > 0) {
            $redis = $this->getRedis();
            ## 保证key存在
            $this->getOrderNumByLuid($luid); //创建key值
            $key = self::getOrderNumCacheKeyByLuid($luid);
            $res = $redis->hIncrBy($key, self::ORDER_NUMBER_CACHE_KEY_FIELD_NUM, $increment);
        }
        return (int) $res;
    }

    /**
     * 返回cache key
     * @param type $luid
     * @return type
     */
    public static function getOrderNumCacheKeyByLuid(int $luid) {
        return self::ORDER_NUMBER_CACHE_KEY . $luid;
    }

    #####【订单数量 end】####
    ###【order log start】###

    /**
     * 记录订单变更记录
     * @param type $order_id
     * @param type $status
     * @param type $uid
     * @param type $reason
     * @param type $log
     * @param type $ctime
     * @return type
     */
    public function addOrderLog($order_id, $status, $uid, $reason = '', $log = '', $ctime = '') {
        $ctime = $ctime ? $ctime : date("Y-m-d H:i:s");
        $trace = debug_backtrace();
        $key = 0;
        $log = '';
        while (isset($trace[$key])) {
//            $log .= "[$key]";
            $log = "[$key]";
            $log .= $trace[$key]['file'] . "(line:" . $trace[$key]['line'] . ")\n";
            //            $log .= "param:".json_encode($trace[$key]['args']) ."\n";
            $key++;
        }
        return $this->getDueOrderObj()->insertLog(['order_id' => $order_id, 'ctime' => $ctime, 'status' => $status, 'uid' => $uid, 'reason' => $reason, 'log' => $log]);
    }

    /**
     * 记录订单变更记录
     * @param type $order_id
     * @param type $status
     * @param type $uid
     * @param type $reason
     * @param type $log
     * @param type $ctime
     * @return type
     */
    public function getOrderLog($order_id, $status) {
        return $this->getDueOrderObj()->getOneOrderLog($order_id, $status);
    }

    ###【order log end】###
    ### [ 扣费 ]  

    /**
     * 扣费财务处理:根据当前的订单状态进行财务处理,所以要扣费请先修改订单状态
     * @param int $orderId  
     * @return bool
     */
    private function operateOrder( $orderInfo , int $status): bool {
        $orderId =$orderInfo['order_id'];
        $orderlog = $this->getOrderLog($orderInfo['order_id'], $orderInfo['status']);
        $finance = new Finance();
        $res = -100; //负数默认是扣费失败
        $error = '';
//        $status = $orderInfo['status'];
        switch ($status) {
            //首先建立默认订单(状态为0),订单扣费成功后变为状态1
//            case DueOrder::ORDER_STATUS_00_DEFAULT:
            case DueOrder::ORDER_STATUS_010_CREATE_ORDER:
                $runtime = date("Y-m-d H:i:s", time() + self::ORDER_LOCK_TIME);
                $otidArray = $res1 = $finance->createDueOrder($orderInfo['uid'], $orderInfo['cert_uid'], $orderInfo['amount'], $orderInfo['real_amount'], '用户下单', $orderlog['id'], $runtime, Finance::GUARANTEE_CRON_ACTION_REFUND);
                if($res1 <0 ){
                    write_log("{$orderId},order:{$orderInfo['amount']} ,real:{$orderInfo['real_amount']},下单失败,错误码:{$res1}", 'order.pay.error.log');
                } 
                $res2 = FALSE;
                $error = 'create fail';
                $orderInfo['otid'] = -1;
                if ($finance->checkBizResult($res1) && $otidArray['tid'] > 0) {
                    $this->getDueOrderObj()->updateOrderOtidByOrderId($orderInfo['order_id'], $otidArray['tid']);
                    $orderInfo['otid'] = $otidArray['tid'];
                    $res2 = $finance->lockDueOrder($otidArray['tid'], DueOrder::$order_status_detail[$orderInfo['status']], $orderlog['id'], $orderInfo['uid']);
                    $error = 'create lock fail';
                }
                $this->updateUserBalance($orderInfo['uid']);
                $bool = $res2;
                break;
            //订单已经被锁--不处理
            case DueOrder::ORDER_STATUS_050_ANCHOR_ACCEPTING_ORDER:
            case DueOrder::ORDER_STATUS_070_TIMEOUT_FINISHED:
            case DueOrder::ORDER_STATUS_080_USER_FINISHED:
            case DueOrder::ORDER_STATUS_090_USER_BACK_ORDER:
            case DueOrder::ORDER_STATUS_110_ANCHOR_DISAGREE_BACK:
                $bool = true;
                break;
            //锁定延长    
            case DueOrder::ORDER_STATUS_120_USER_APPEAL_CUSTOMER_SERVICE:
                $bool = true;
                break;
            //待取消订单:通知财务5分钟后进行将欢朋币退回到用户的账上(先改状态,再解锁)
            ### uid
            case DueOrder::ORDER_STATUS_030_USER_CANCEL:
            ### cert_uid   
            case DueOrder::ORDER_STATUS_040_ANCHOR_REJECT_ORDER:
            case DueOrder::ORDER_STATUS_060_ANCHOR_CANCEL:
            case DueOrder::ORDER_STATUS_100_ANCHOR_AGREE_BACK:
            ### systhem
            case DueOrder::ORDER_STATUS_020_TIMEOUT_CANCEL:
            case DueOrder::ORDER_STATUS_130_CUSTOMER_SERVICE_AGREE:
            case DueOrder::ORDER_STATUS_160_TIMEOUT_USER_BACK_ORDER:
                if (in_array($status, [DueOrder::ORDER_STATUS_030_USER_CANCEL])) {
                    $handlUid = $orderInfo['uid'];
                } elseif (in_array($status, [DueOrder::ORDER_STATUS_040_ANCHOR_REJECT_ORDER, DueOrder::ORDER_STATUS_060_ANCHOR_CANCEL, DueOrder::ORDER_STATUS_100_ANCHOR_AGREE_BACK])) {
                    $handlUid = $orderInfo['cert_uid'];
                } elseif (in_array($status, [DueOrder::ORDER_STATUS_020_TIMEOUT_CANCEL, DueOrder::ORDER_STATUS_130_CUSTOMER_SERVICE_AGREE, DueOrder::ORDER_STATUS_160_TIMEOUT_USER_BACK_ORDER])) {
                    $handlUid = 0;
                }
                $runtime = date("Y-m-d H:i:s", time() + self::ORDER_PAY_ARRIVAL_TIME);
                $res1 = $finance->refundDueOrder($orderInfo['otid'], $runtime, $orderlog['id'], $handlUid);
                $errorCode = $res1;
                $error = 'refund fail';
                $res2 = FALSE;
                if ($finance->checkBizResult($res1)) {
                    $res2 = $finance->unlockDueOrder($orderInfo['otid'], DueOrder::$order_status_detail[$orderInfo['status']], $orderlog['id'], $handlUid);
                    $errorCode = $res2;
                    $error = 'refund unlock fail';
                }
                $bool = $res2;
                break;
            //待完成订单:通知财务5分钟后进行将欢朋币达到主播的账上(先改状态,再解锁)
            case DueOrder::ORDER_STATUS_140_CUSTOMER_SERVICE_DISAGREE:
            case DueOrder::ORDER_STATUS_150_TIMEOUT_OVER_FINISHED_ORDER:
            case DueOrder::ORDER_STATUS_170_TIMEOUT_USER_NOT_APPEAL_CUSTOMER_SERVICE:
                $runtime = date("Y-m-d H:i:s", time() + self::ORDER_PAY_ARRIVAL_TIME);
                $res1 = $finance->finishDueOrder($orderInfo['otid'], $runtime, $orderlog['id'], 0);
                $errorCode = $res1;
                $error = 'finish fail';
                $res2 = FALSE;
                if ($finance->checkBizResult($res1)) {
                    $res2 = $finance->unlockDueOrder($orderInfo['otid'], DueOrder::$order_status_detail[$orderInfo['status']], $orderlog['id'], 0);
                    $errorCode = $res2;
                    $error = 'finish unlock fail';
                }
                $bool = $res2;
                break;
            //取消订单检测
            case DueOrder::ORDER_STATUS_1010_ORDER_CANCELLED:
            case DueOrder::ORDER_STATUS_1020_ORDER_BACK:
                $financeOrder = $finance->getGuaranteeOrderInfo($orderInfo['otid']);
                if ($financeOrder['status'] == Finance::GUARANTEE_STATUS_BACKED) {
                    $this->updateUserBalance($orderInfo['uid']);
                    $bool = TRUE;
                } else {
                    $error = 'guarantee status neq';
                    $errorCode = 'status neq '.Finance::GUARANTEE_STATUS_BACKED;
                    $bool = FALSE;
                }
                break;
            //付款订单检测    
            case DueOrder::ORDER_STATUS_1000_ORDER_END_AND_SETTLEMENT:
                $financeOrder = $finance->getGuaranteeOrderInfo($orderInfo['otid']);
                if ($financeOrder['status'] == Finance::GUARANTEE_STATUS_FINISHED) {
                    $this->getDueOrderObj()->updateOrderIncomeByOrderId($orderInfo['order_id'], $financeOrder['income'] / 1000);
                    $this->updateUserBalance($orderInfo['cert_uid'], 2);
                    $bool = TRUE;
                } else {
                    $error = 'guarantee status neq';
                    $errorCode = 'status neq '.Finance::GUARANTEE_STATUS_FINISHED;
                    $bool = FALSE;
                }
                break;

            default:
                $bool = FALSE;
                break;
        }
        if (!$bool) {
            $message = "error {$orderId} | {$orderInfo['otid']} tostatus $status ,errorCode:{$errorCode}, errorMessage:" . $error;
            write_log($message, 'order.pay.error.log');
            return FALSE;
        }



        return $bool;
    }

    /**
     * 更新用户的信息
     * 
     * @param type $uid
     * @param type $type  1: 更新用户欢朋币;2更新用户金币
     */
    public function updateUserBalance($uid, $type = 1) {
        $user = new Anchor($uid);
        $finance = new Finance();
        $res = $finance->getBalance($uid);
        if ($type == 1) {
            $user->updateUserHpCoin($res['hb']);
        } else {
            $user->updateAnchorCoin($res['gb']);
        }
        return;
    }

    /**
     * 金钱运算
     * @param type $left_operand   左边数
     * @param type $right_operand  右边数
     * @param type $bc     运算法则  + - * / %
     * @param type $scale  精确位数
     * @return type
     * @throws Exception
     */
    public static function bcMatch(&$left_operand, &$right_operand, $bc, $scale = 3) {
        //和钱相关的运算保留3位小数
        $left_operand = round($left_operand, $scale);
        $right_operand = round($right_operand, $scale);
        switch ($bc) {
            case '+':
                $res = bcadd($left_operand, $right_operand, $scale);
                break;
            case '-':
                $res = bcsub($left_operand, $right_operand, $scale);
                break;
            case '*':
                $res = bcmul($left_operand, $right_operand, $scale);
                break;
            case '/':
                $res = bcdiv($left_operand, $right_operand, $scale);
                break;
            case '%':
                $res = bcmod($left_operand, $right_operand, $scale);
                break;
            default :
                throw new Exception(self::ERROR_CODE_24);
        }
        return $res;
    }

    /**
     * 是否可以评论
     * @param type $uid  用户ID
     * @param type $orderInfo  ['uid'=> '' , 'status'=> '' ...]   用户信息
     * @return int   <br />
     * 1 有评论权限  现在无评论  可以评论
     * 2 有评论权限  现在有评论  不可以评论  
     * 3 无评论权限  现在无评论  不可以评论
     * 4 无评论权限  现在有评论  不可以评论
     */
    public function getCanComment($uid = 0, $orderInfo = '') {
        if (empty($uid) || empty($orderInfo)) {
            return 3; //默认订单不存在
        }
        ### 1 有评论权限  现在无评论  可以评论
        ### 2 有评论权限  现在有评论  不可以评论 
        ### 3 无评论权限  现在无评论  不可以评论
        ### 4 无评论权限  现在有评论  不可以评论
        if ($orderInfo['uid'] == $uid && $orderInfo['status'] >= DueOrder::ORDER_STATUS_070_TIMEOUT_FINISHED && $orderInfo['status'] != DueOrder::ORDER_STATUS_1010_ORDER_CANCELLED) {
            if ($orderInfo['comment'] == 0) {
                return 1;
            } else {
                return 2;
            }
        } else {
            if ($orderInfo['comment'] == 0) {
                return 3;
            } else {
                return 4;
            }
        }
    }

    /**
     * 我的收益
     * @param type $orderInfo
     * @return type
     */
    public function myIncome($orderInfo) {
        $income = 0;
        if ($orderInfo['status'] == DueOrder::ORDER_STATUS_1000_ORDER_END_AND_SETTLEMENT) {
            $income = $orderInfo['income'];
        }
        return $income;
    }
    /**
     * 更新优惠券使用状态
     * -------------
     * @return bool
     */
    protected function updateCouponStatus($uid,$orderId,$couponId){
        $res = $this->getDueOrderObj()->_updateCouponStatus($uid,$orderId,$couponId); 
        return $res ? true : false; 
    }
}
