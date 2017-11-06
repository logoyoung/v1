<?php

namespace service\due;

use system\RedisHelper;
use service\common\AbstractService;
use lib\due\DueActivityConfig;
use lib\due\DueCoupon;
use service\due\DueActivityService;
use service\due\DueCouponService;
use service\user\UserCenterCountService;
use Exception;

class DueActivityConfigService extends AbstractService {

    const RECEIVE_TYPE_01_RECEIVE = 1;
    const RECEIVE_TYPE_02_SHARE = 2;
    const ACTIVITY_CACHE_KEY = 'due_ActivityList';

    /**
     * 描述
     * @var type 
     */
    public static $describe = [
        self::RECEIVE_TYPE_01_RECEIVE => '直接领取优惠券',
        self::RECEIVE_TYPE_02_SHARE => '分享优惠券',
    ];

    /**
     * 分享中优惠券设置
     */
    const CONFIG_PACKAGE_CONFIG = 'configPackageConfig';

    /**
     * 优惠券领取设置
     */
    const CONFIG_RECEIVE_TYPE = 'configReceiveType';

    /**
     * 用户领取优惠券Id限制
     */
    const CONFIG_USER_RECEIVE_LIMIT = 'configUserReceiveLimit';

    /**
     * 主播优惠券领取限制
     */
    const CONFIG_ANCHOR_RECEIVE_LIMIT = 'configAnchorReceiveLimit';

    /**
     * 活动时间
     */
    const CONFIG_ACTIVITY_TIME = 'configActivityTime';

    /**
     * 活动链接有效期
     */
    const CONFIG_EXPIRE_TIME = 'configExpireTime';

    /**
     * 活动(链接)领取总数量限制
     */
    const CONFIG_ACTIVITY_RECEIVE_LIMIT = 'configActivityReceiveLimit';

    /**
     * 活动(链接)每个人可以领取的数量
     */
    const CONFIG_ACTIVITY_EVERYONE_RECEIVE_LIMIT = 'configActivityEveryoneReceiveLimit';

    /**
     * 同一活动领取数量限制
     */
    const CONFIG_ACTIVITY_SAME_RECEIVE_LIMIT = 'configActivitySameReceiveLimit';

    /**
     * 优惠券使用门槛
     */
    const USE_COUPON_BASEPRICE = 'basePrice';

    /**
     * 每个活动的配置列表
     * 表达式	含义
      EQ	等于（=）
      NEQ	不等于（<>）
      GT	大于（>）
      EGT	大于等于（>=）
      LT	小于（<）
      ELT	小于等于（<=）
      [NOT] BETWEEN	（不在）区间查询
      [NOT] IN	（不在）IN 查询
     * @var type 
     */
    public static $configure = [
        self::CONFIG_PACKAGE_CONFIG => ['JSON', NULL, "活动包含的优惠券以及数量"],
        self::CONFIG_USER_RECEIVE_LIMIT => ['IN', NULL, "此活动用户允许领取的优惠券ID|例如:123,124,125"],
        self::CONFIG_ANCHOR_RECEIVE_LIMIT => ['IN', NULL, "此活动用户允许领取的优惠券ID|例如:123,124,125"],
        self::CONFIG_ACTIVITY_TIME => ['BETWEEN', NULL, "活动时间|例如:2017-07-19 10:16:46,2017-07-19 10:16:52"],
        self::CONFIG_EXPIRE_TIME => ['EQ', NULL, "有效期(天)|例如:7"],
        self::CONFIG_RECEIVE_TYPE => ['EQ', NULL, "领取方式|例如:1直接领取,2分享领取"],
        self::CONFIG_ACTIVITY_RECEIVE_LIMIT => ['EQ', NULL, "活动链接领取数量限制|例如:4"],
        self::CONFIG_ACTIVITY_SAME_RECEIVE_LIMIT => ['EQ', NULL, "同一活动领取数量限制|例如:4"],
        self::CONFIG_ACTIVITY_EVERYONE_RECEIVE_LIMIT => ['EQ', NULL, "活动(链接)每个人可以领取的数量|例如:1"],
    ];
    public static $useCouponConfigure = [
        self::USE_COUPON_BASEPRICE => ['EGT', NULL, "活动包含的优惠券以及数量"],
    ];
    public $activityConfigModel = null;
    public $activityServiceModel = null;
    public $couponServiceModel = null;
    public $_redis = null;

    public function getActivityConfigModel() {
        if (is_null($this->activityConfigModel)) {
            $this->activityConfigModel = new DueActivityConfig();
        }
        return $this->activityConfigModel;
    }

    public function getActivityServiceModel() {
        if (is_null($this->activityServiceModel)) {
            $this->activityServiceModel = new DueActivityService();
        }
        return $this->activityServiceModel;
    }

    public function getCouponServiceModel() {
        if (is_null($this->couponServiceModel)) {
            $this->couponServiceModel = new DueCouponService();
        }
        return $this->couponServiceModel;
    }

    public $redisConfig = 'huanpeng';

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
     * 验证信息与结果
     * @var type 
     */
    public static $rulePool = [];
    public $currentActivityInfo = null;
    public $currentTypeId = null;
    public $currentActivityId = null;
    public $rule = [null];
    public $ruleResult = null;
    public $selectedId = null;
    public $isInActivityTime = false;
    public $couponNumber = [];

    /**
     * 辅助验证信息
     * @var type 
     */
    public $uid = null;
    public $isAnchor = false;
    public $shareUuid = null;
    public $haveCoupons = [];
    public $poolCoupons = [];
    private $phone = '';

    /**
     * 辅助结果集
     */
    public $shareCoupons = null;

    /**
     * 获取规则
     * @param type $key
     * @return type
     */
    public function getRule($id = 1) {
        $config = [];
        $dbConfig = $this->getActivityConfigModel()->getConfig($id);
        if (isset($dbConfig['config'])) {
            $config = json_decode($dbConfig['config']);
        }
        return $config;
    }

    /**
     * 更新配置
     * @return type
     */
    public function updateRule($id = 1) {
        $rule = self::$configure;
        $res = $this->getActivityConfigModel()->setConfig($rule, $id);
        return $res;
    }

    /**
     * 优惠券使用配置更新
     */
    public function updateUseCouponRule($id = 2) {
        $rule = self::$useCouponConfigure;
        return $this->getActivityConfigModel()->setUseCouponConfig($rule, $id);
    }

    public function clear() {
        $this->currentActivityInfo = null;
        $this->currentTypeId = null;
        $this->currentActivityId = null;
        $this->rule = [];
        $this->ruleResult = null;
        $this->selectedId = null;
        $this->isInActivityTime = false;
        $this->couponNumber = [];

        /**
         * 辅助验证信息
         * @var type 
         */
        $this->uid = null;
        $this->isAnchor = false;
        $this->shareUuid = null;

        /**
         * 辅助结果集
         */
        $this->shareCoupons = [];
        $this->poolCoupons  = [];
        $this->haveCoupons  = [];
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    /**
     * 活动分析:
     * @param type $config
     * @return type
     */
    public function activityAnalysis($currentActivityInfo, $uid = null, $isAnchor = null, $shareUuid = null) {
        $this->clear();
        if ($uid) {
            $this->uid = $uid;
        }
        if ($isAnchor) {
            $this->isAnchor = $isAnchor;
        }
        if ($shareUuid) {
            $this->shareUuid = $shareUuid;
        }
        if ($currentActivityInfo) {
            $this->currentActivityInfo = $currentActivityInfo;
        }

        $this->shareCoupons = null;

        if ($this->currentActivityInfo) {
            $this->currentTypeId = $this->currentActivityInfo['type'];
            $this->currentActivityId = $this->currentActivityInfo['aid'];
        }
        $this->rule = null;
        $this->selectedId = null;
        $config = json_decode($currentActivityInfo['configure'], TRUE);
        foreach ($config as $key => $value) {
            $this->rule[$key] = $value[1];
        }
        foreach ($this->rule as $method => $param) {
            if (method_exists($this, $method)) {
                $this->$method($param);
            }
        }
//        if (empty($this->selectedId)) {
//            throw new Exception(errorDesc(-8016), -8016);
//        }
        return $this->rule;
    }

    ###【规则】###

    /**
     * 活动包含的优惠券以及数量
     * ## 活动的优惠券还有就行
     * @param type $value
     */
    protected function configPackageConfig($value) {
        //'packageConfig' => ['JSON', NULL, "活动包含的优惠券以及数量"],
        $config = json_decode($value, TRUE);
        $couponIds = array_keys($config);
        $coupons = $this->getCouponServiceModel()->getCouponInfoById($couponIds);
        $total = 0;
        foreach ($coupons as $row) {
            $tmp = $row['max_number'] - $row['send_number'];
            if ($tmp > 0) {
                $haveCoupons[] = $row['cid'];
            }
            $total += $tmp;
        }
        if ($total > $this->rule[self::CONFIG_ACTIVITY_RECEIVE_LIMIT]) {
            $this->poolCoupons  = $haveCoupons;
        }
    }

    /**
     * 此活动用户允许领取的优惠券ID
     * 领取限制
     * @param type $exp
     */
    protected function configUserReceiveLimit($value) {
        if (!empty($value) && !$this->isAnchor) {
            $couponIds = explode(',', $value);
            $coupons = $this->getCouponServiceModel()->getCouponInfoById($couponIds);
            $total = 0;
            foreach ($coupons as $row) {
                $tmp = $row['max_number'] - $row['send_number'];
                if ($tmp > 0) {
                    $haveCoupons[] = $row['cid'];
                }
                $total += $tmp;
            }
            $haveCoupons = array_intersect($this->poolCoupons, $haveCoupons);
            $this->haveCoupons = array_merge($this->haveCoupons, $haveCoupons);
            $this->haveCoupons = array_unique($this->haveCoupons);
            
        }
    }

    /**
     * 此活动主播允许领取的优惠券ID
     * @param type $value
     */
    protected function configAnchorReceiveLimit($value) {
        if (!empty($value) && $this->isAnchor) {
            $couponIds = explode(',', $value);
            $coupons = $this->getCouponServiceModel()->getCouponInfoById($couponIds);
            $total = 0;
            foreach ($coupons as $row) {
                $tmp = $row['max_number'] - $row['send_number'];
                if ($tmp > 0) {
                    $haveCoupons[] = $row['cid'];
                }
                $total += $tmp;
            }
            $haveCoupons = array_intersect($this->poolCoupons, $haveCoupons);
            $this->haveCoupons = array_merge($this->haveCoupons, $haveCoupons);
            $this->haveCoupons = array_unique($this->haveCoupons);
        }
    }

    /**
     * 活动时间
     * @param type $exp
     */
    protected function configActivityTime($value) {
        //'activityTime' => ['BETWEEN', NULL, "活动时间|例如:2017-07-19 10:16:46,2017-07-19 10:16:52"],
        if (!empty($value)) {
            list($stime, $etime) = explode(',', $value);
            $time = date("Y-m-d H:i:s");
            if ($time >= $stime && $time <= $etime) {
                $this->isInActivityTime = TRUE;
            }
        }
    }

    /**
     * 链接有效期
     * @param type $value
     */
    protected function configExpireTime($value) {
        //'expire' => ['EQ', NULL, "有效期(天)|例如:7"],
        if ($this->currentActivityInfo) {
            /**
             * @todo 验证 
             */
        }
    }

    /**
     * 领取类型,已经处理了
     * @param type $value
     * @return $this
     */
    protected function configReceiveType($value) {
        if (!in_array($value, [self::RECEIVE_TYPE_01_RECEIVE, self::RECEIVE_TYPE_02_SHARE])) {
            throw new Exception(errorDesc(-8025), -8025);
        }
//        if ($this->currentActivityInfo && $value == self::RECEIVE_TYPE_02_SHARE && empty($this->shareUuid)) {
//            throw new Exception(errorDesc(-8019), -8019);
//        }
        return $this;
    }

    /**
     * 一个链接可领取优惠券数量
     * @param type $exp
     */
    protected function configActivityReceiveLimit($value) {
        // CONFIG_ACTIVITY_RECEIVE_LIMIT=> ['EQ', NULL, "活动链接领取限制|例如:4"],



        if ($this->currentActivityInfo && ( $this->uid || $this->phone )) {
            $shareCoupons = [];

            if ($this->rule[self::CONFIG_RECEIVE_TYPE] == self::RECEIVE_TYPE_01_RECEIVE) {

                $shareCoupons = $this->getCouponServiceModel()->getCouponInfoByActivityId($this->uid, $this->phone, $this->currentActivityInfo['aid']);
            } elseif ($this->rule[self::CONFIG_RECEIVE_TYPE] == self::RECEIVE_TYPE_02_SHARE) {
                if (!empty($this->shareUuid)) {
                    $shareCoupons = $this->_getShareCoupons($this->shareUuid);
                }
            } else {
                throw new Exception(errorDesc(-8020), -8020);
            }

            $shareCount = count($shareCoupons);
            if ($shareCount >= $this->rule[DueActivityConfigService::CONFIG_ACTIVITY_RECEIVE_LIMIT]) {
                throw new Exception(errorDesc(-8023), -8023);
            }
        }
    }

    /**
     * 一个链接一个用户领取限制
     * @param type $exp
     */
    protected function configActivityEveryoneReceiveLimit($paramValue) {
        //'activityLinkReceiveLimit' => ['EQ', NULL, "活动链接领取限制|例如:4"],
        if ($this->currentActivityInfo && ( $this->uid || $this->phone )) {
            $shareCoupons = 0;
            if ($this->rule[self::CONFIG_RECEIVE_TYPE] == self::RECEIVE_TYPE_01_RECEIVE) {
                $shareCoupons = $this->getCouponServiceModel()->getCouponInfoByActivityId($this->uid, $this->phone, $this->currentActivityInfo['aid']);
            } elseif ($this->rule[self::CONFIG_RECEIVE_TYPE] == self::RECEIVE_TYPE_02_SHARE) {
                if (!empty($this->shareUuid)) {
                    $shareCoupons = $this->_getShareCoupons($this->shareUuid);
                }
            } else {
                throw new Exception(errorDesc(-8020), -8020);
            }

            if (count($shareCoupons) == 0) {
                return TRUE;
            }
            $shareCouponsInfoByUid = [];
            $shareCouponsInfoByPhone = [];
            foreach ($shareCoupons as $value) {
                if (!isset($shareCouponsInfoByUid[$value['uid']])) {
                    $shareCouponsInfoByUid[$value['uid']] = 0;
                }
                $shareCouponsInfoByUid[$value['uid']] ++;
                if (!isset($shareCouponsInfoByPhone[$value['phone']])) {
                    $shareCouponsInfoByPhone[$value['phone']] = 0;
                }
                $shareCouponsInfoByPhone[$value['phone']] ++;
            }
            if (isset($shareCouponsInfoByUid[$this->uid]) && $shareCouponsInfoByUid[$this->uid] >= $paramValue) {
                throw new Exception(errorDesc(-8022), -8022);
            }
            if (isset($shareCouponsInfoByPhone[$this->phone]) && $shareCouponsInfoByPhone[$this->phone] >= $paramValue) {
                throw new Exception(errorDesc(-8022), -8022);
            }
        }
    }

    /**
     * 同一活动用户可领取的优惠券数量  =》 同一活动  每天可以领取的优惠券限制
     * @param type $value
     * @throws Exception
     */
    protected function configActivitySameReceiveLimit($value) {
        //'activityLinkReceiveLimit' => ['EQ', NULL, "活动链接领取限制|例如:4"],
        if ($this->currentActivityInfo && $this->shareUuid) {
            $coupon = $this->getCouponServiceModel()->getCouponInfoByActivityId($this->uid, $this->phone, $this->currentActivityInfo['aid']);
            $count = 0;
            //2017年08月15日14:41:33  总限制改为每天限制
            foreach ($coupon as  $row) {
                if($row['stime'] >= date("Y-m-d 00:00:00") &&   $row['stime'] <= date("Y-m-d 23:59:59") ){
                    $count++;
                }
            }
            if ($count >= $value) {
                throw new Exception('您的约玩优惠券领取已达今日上限'.$value.'张', -8014);
            }
        }
    }

    #### 【 check end 】 ####

    /**
     * 创建分享记录(发放记录)
     * @param type $uid
     * @param type $typeId
     */
    public function createActivityShareRecord($shareUid, $sourceId, $typeId, $activityData = null) {
        if (is_null($activityData)) {
            $activityList = $this->getActivityServiceModel()->getActivity($typeId);
            //多个选取一个发放
            $activityData = isset($activityList[0]) ? $activityList[0] : [];
        }
        if (empty($activityData)) {
            return FALSE;
        }
        $res = $this->createRecord($shareUid, $sourceId, $activityData);
        if ($res) {
            ## 预添加优惠券
            $this->createPool($this->haveCoupons);
        }
        return $res;
    }

    /**
     * 创建记录
     * @param type $shareUid
     * @param type $sourceId
     * @param type $activityData
     * @return type
     */
    protected function createRecord($shareUid, $sourceId, $activityData) {
        $stime = strtotime(date('Y-m-d 00:00:00', time()));
        $etime = $stime + $this->rule[self::CONFIG_EXPIRE_TIME] * 86400 - 1;
        $this->shareUuid = $this->createUUID();
        $data = [
            'aid' => $activityData['aid'],
            'type' => $activityData['type'],
            'source_id' => $sourceId,
            'share_uuid' => $this->shareUuid,
            'uid' => $shareUid,
            'configure' => $activityData['configure'],
            'share_number' => $this->rule[self::CONFIG_ACTIVITY_RECEIVE_LIMIT],
            'receive_number' => 0,
            'status' => 0,
            'stime' => date("Y-m-d H:i:s", $stime),
            'etime' => date("Y-m-d H:i:s", $etime),
        ];

        // `share_number`, `receive_number`, `status`, `stime`, `etime`
        $res = $this->getActivityServiceModel()->InsertShareRecordData($data);
        if ($res) {
            return $data;
        } else {
            return [];
        }
    }

    /**
     * 生产初始订单号
     * 订单号10秒自检是否唯一
     * @staticvar int $times
     * @return string
     */
    public function createUUID() {
        list($usec, $sec) = explode(" ", microtime());
        $tmp = strval($usec);
        $param1 = date('ymdHis', $sec);
        $param2 = substr($usec, strpos($tmp, '.') + 1, 3);
        $param3 = rand(10000000, 99999999);
        $param4 = isset($_COOKIE['PHPSESSID']) ? $_COOKIE['PHPSESSID'] : uniqid();
        $uuid = $param1 . $param2 . $param3 . $param4;
        return md5($uuid);
    }

    public function createCode() {
        list($usec, $sec) = explode(" ", microtime());
        $tmp = strval($usec);
        $param1 = date('ymdHis', $sec);
        $param2 = substr($usec, strpos($tmp, '.') + 1, 3);
        $param3 = rand(100, 999);
        $uuid = $param1 . $param2 . $param3;
        return $uuid;
    }

    /**
     * 领取活动中的优惠券
     * @param type $uid
     * @param type $typeId
     */
    public function receiveActivityCoupon($receiveUid, $phone) {

        if (empty($this->currentActivityInfo)) {
            throw new Exception(errorDesc(-8011), -8011);
        }
        $dateLine = date("Y-m-d H:i:s");
        $startTime = date("Y-m-d 00:00:00");
        $coupons = $this->getCouponServiceModel()->getCouponInfoById($this->selectedId);
        if (!empty($coupons)) {
            $couponInfo = $coupons[0];
        } else {
            throw new Exception(errorDesc(-8017), -8017);
        }


        $addTime = $couponInfo['expire'] * 86400;
        $userCouponEtime = date("Y-m-d H:i:s", strtotime($startTime) + $addTime - 1);

        $promoCode = isset($_COOKIE['promo_code']) ?$_COOKIE['promo_code'] : '' ;
        if ($this->rule[self::CONFIG_RECEIVE_TYPE] == self::RECEIVE_TYPE_01_RECEIVE) {
            $data = [
                'code' => $this->createCode(),
                'uid' => $receiveUid,
                'phone' => $phone,
                'price' => $couponInfo['price'],
                'status' => DueCouponService::COUPON_STATUS_01_USE,
//            'orderid' => '',
                'share_uuid' => '',
                'coupon_id' => $this->selectedId,
//            'channel' => '',
                'type' => $this->currentActivityInfo['type'],
                'ctime' => $dateLine,
                'stime' => $startTime,
                'etime' => $userCouponEtime,
                'utime' => date("Y-m-d H:i:s"),
                'activity_id' => $this->currentActivityInfo['aid'],
                'promocode' => $promoCode,
            ];
            $res = $this->getCouponServiceModel()->insertCoupon($data);
            if (!$res) {
                throw new Exception(errorDesc(-8021), -8021);
            }
            ### 添加用户的新优惠券状态
            UserCenterCountService::addValue($receiveUid, UserCenterCountService::HASH_TABLE_FIELD_COUPON_NUM, 1);
        } elseif ($this->rule[self::CONFIG_RECEIVE_TYPE] == self::RECEIVE_TYPE_02_SHARE) {
            $data = [
                'uid' => $receiveUid,
                'phone' => $phone,
                'status' => DueCouponService::COUPON_STATUS_01_USE,
                'stime' => $startTime,
                'etime' => $userCouponEtime,
                'share_uuid' => $this->shareUuid,
                'coupon_id' => $this->selectedId,
                'utime' => $dateLine,
                'promocode' => $promoCode,
            ];
            $res = $this->getCouponServiceModel()->updateCoupon($data);
            $this->updatePool();
        }
        if ($res) {
            $result = $this->getCouponServiceModel()->getCouponByIdDesc($receiveUid, $phone, 1);
        } else {
            throw new Exception(errorDesc(-8021), -8021);
        }
        $data = $result[0];
        return $data;
    }

    /**
     * 创建分享池子
     */
    public function createPool($couponIds) {
        $coupons = $this->getCouponServiceModel()->getCouponInfoById($couponIds);
        /**
         * @todo 同一类型同一活动  限制处理
         */
        foreach ($coupons as $row) {
            $tmp = $row['max_number'] - $row['send_number'];
            if ($tmp > 0) {
                $randPool[$row['cid']] = $tmp;
            }
            $couponInfo[$row['cid']] = $row;
        }
        //creat pool
        $randHash = [];
        for ($i = 0; $i < $this->rule[self::CONFIG_ACTIVITY_RECEIVE_LIMIT]; $i++) {
            $randCouponId = $this->get_rand($randPool);
            if (!isset($randHash[$randCouponId])) {
                $randHash[$randCouponId] = 1;
            } else {
                $randHash[$randCouponId] ++;
            }
            $randPool[$randCouponId] --;

            ##[ insert ]
            $dateLine = date("Y-m-d H:i:s");
            $data = [
                'code' => $this->createCode(),
                'uid' => 0,
                'phone' => '',
                'price' => $couponInfo[$randCouponId]['price'],
                'share_uuid' => $this->shareUuid,
                'coupon_id' => $randCouponId,
                'type' => $this->currentActivityInfo['type'],
                'ctime' => $dateLine,
                'activity_id' => $this->currentActivityInfo['aid'],
            ];
            $this->getCouponServiceModel()->insertCoupon($data);
            $this->getActivityServiceModel()->getActivityModel()->updateShareRecord($this->currentActivityInfo['aid'], $this->shareUuid);
        }
        $cacheKey = $this->getCacheKey();
        $redis = $this->getRedis();
        $redis->hMSet($cacheKey, $randHash);
        return $randHash;
    }

    public function addlock($cacheKey) {
        $cacheKey = 'lock_' . $cacheKey;
        $this->getRedis()->set($cacheKey, 1, 10);
    }

    public function dellock($cacheKey) {
        $cacheKey = 'lock_' . $cacheKey;
        $this->getRedis()->delete($cacheKey);
    }

    public function checklock($cacheKey) {
        $times = 0;
        $redis = $this->getRedis();
        $cacheKey = 'lock_' . $cacheKey;
        while ($redis->get($cacheKey)) {
            usleep(100000);
            $times++;
            if ($times > 5) {
                throw new Exception(errorDesc(-8028), -8028);
            }
        }
    }

    /**
     * 获取优惠券
     * @param type $value
     * @return type
     */
    public function getCouponId() {
        $this->selectedId = 0;
        if ($this->rule[self::CONFIG_RECEIVE_TYPE] == self::RECEIVE_TYPE_02_SHARE) {
            if ($this->currentActivityInfo) {

                $cacheKey = $this->getCacheKey();
                $this->addlock($cacheKey);
                $redis = $this->getRedis();
                $randPool = $redis->hGetAll($cacheKey);
                ## 初始化
                if ($randPool && !empty($randPool)) {
                    $randHash = $randPool;
                } else {
                    $userCoupons = $this->getCouponServiceModel()->getCouponInfoByShareIdAndStatus($this->shareUuid, null, 0);

                    $randPool = [];
                    foreach ($userCoupons as $row) {
                        if (!isset($randPool[$row['coupon_id']])) {
                            $randPool[$row['coupon_id']] = 1;
                        } else {
                            $randPool[$row['coupon_id']] ++;
                        }
                    }
                    $randHash = $randPool;
                    $redis->hMSet($cacheKey, $randHash);
                }

                if (empty($randHash)) {
                    throw new Exception(errorDesc(-8027), -8027);
                }
            } else {
                throw new Exception(errorDesc(-8011), -8011);
            }
        } else {

            $coupons = $this->getCouponServiceModel()->getCouponInfoById($this->haveCoupons);
            foreach ($coupons as $row) {
                $tmp = $row['max_number'] - $row['send_number'];
                if ($tmp > 0) {
                    $randPool[$row['cid']] = $tmp;
                }
            }
        }
        $id = $this->selectedId = $this->get_rand($randPool);


        return $id;
    }

    /**
     * $proArr =array('a'=>20,'b'=>30,'c'=>50);
     * @param type $proArr
     * @return type
     */
    protected function get_rand($proArr) {
        $result = '';
        //概率数组的总概率精度 
        $proSum = array_sum($proArr);
        //概率数组循环 
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            //抽取随机数
            if ($randNum <= $proCur) {
                $result = $key;
                //得出结果
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        return $result;
    }

    public function getCacheKey() {
        if ($this->currentActivityInfo) {
            $cacheKey = md5($this->currentActivityId . '_' . $this->shareUuid . '_' . $this->currentActivityInfo['utime']);
//            $cacheKey = md5('huanpeng_' . $this->currentActivityId . '_' . $this->currentActivityInfo['utime']);
            return $cacheKey;
        } else {
            throw new Exception(errorDesc(-8026), -8026);
        }
    }

    public function updatePool() {
        if ($this->rule[self::CONFIG_RECEIVE_TYPE] == self::RECEIVE_TYPE_02_SHARE && $this->currentActivityInfo) {
            $cacheKey = $this->getCacheKey();
            $redis = $this->getRedis();
            $proArr = $redis->hGetAll($cacheKey);
            $proArr[$this->selectedId] --;
            if ($proArr[$this->selectedId] <= 0) {
                unset($proArr[$this->selectedId]);
                $redis->hDel($cacheKey, $this->selectedId);
            } else {
                $redis->hIncrBy($cacheKey, $this->selectedId, -1);
            }
            $this->dellock($cacheKey);
        }
    }

    /**
     * 
     * @param type $shareUuid
     * @return type
     */
    protected function _getShareCoupons($shareUuid) {
        if (is_null($this->shareCoupons)) {
            $this->shareCoupons = $this->getCouponServiceModel()->getCouponInfoByShareId($shareUuid);
        }
        return $this->shareCoupons;
    }

}
