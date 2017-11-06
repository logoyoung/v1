<?php

namespace service\due;

use system\RedisHelper;
use service\common\AbstractService;
use lib\due\DueActivity;
use lib\due\DueOrder;
use lib\due\DueCoupon;
use service\due\DueActivityConfigService;
use service\due\DueCouponService;
use service\due\DueOrderService;
use service\due\DueCertService;
use Exception;

/**
 * 分享要素:分享类型,分享源
 * 
 * 客户端请求服务端 分享源:分享类型
 * 
 * 
 */
class DueActivityService extends AbstractService {

    //        1	登录活动类型	只领取一次	1	2017-07-20 11:20:13
//2	用户下单类型		1	2017-07-20 11:20:08
//3	资质审核通过类型		1	2017-07-20 11:20:05
//4	内部发放	审核人员或系统发放	1	2017-07-20 11:20:06

    const ACTIVITY_TYPE_01_LOGIN = 1;
    const ACTIVITY_TYPE_02_ORDER = 2;
    const ACTIVITY_TYPE_03_CERT = 3;
    const ACTIVITY_TYPE_04_ADMIN = 4;
    const ACTIVITY_TYPE_05_PROMOTION = 5;

    public $activityModel = null;
    public $activityConfigServiceModel = null;
    public $couponServiceModel = null;
    public $redisConfig = 'huanpeng';
    public $_redis = null;
    public $phone = '';

    public function getActivityModel() {
        if (is_null($this->activityModel)) {
            $this->activityModel = new DueActivity();
        }
        return $this->activityModel;
    }

    public function getActivityConfigServiceModel() {
        if (is_null($this->activityConfigServiceModel)) {
            $this->activityConfigServiceModel = new DueActivityConfigService();
        }
        return $this->activityConfigServiceModel;
    }

    public function getCouponServiceModel() {
        if (is_null($this->couponServiceModel)) {
            $this->couponServiceModel = new DueCouponService();
        }
        return $this->couponServiceModel;
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

    public function __set($name, $value) {
        $this->$name = $value;
    }

    /**
     * [检测]检测是否存在活动,如果有返回活动信息
     * @param type $typeId  活动类型
     * @param type $uid     
     * @param type $isAnchor
     * @param type $sourceId   源
     * @param type $time
     * @return array
     */
    public function checkActivity($typeId, $uid = null, $isAnchor = false, $sourceId = 0, $time = null) {

        do {
            $row = $this->getActivity($typeId, $time);

            ## 查看是否还有剩余的优惠券可以领取;
            $data = [];
            foreach ($row as $value) {
                try {
                    $this->getActivityConfigServiceModel()->activityAnalysis($value, $uid, $isAnchor);
                } catch (Exception $exc) {
                    continue;
                }
                if (empty($this->getActivityConfigServiceModel()->haveCoupons)) {
                    ## 无优惠券可领取
                    continue;
                }

                if (empty($uid)) {
                    $data[] = $value;
                    continue;
                }
                ## 检查用户是否有领取记录
                ## 检查领取方式
                ## 1 直接领取查看我的优惠券
                if ($this->getActivityConfigServiceModel()->rule[DueActivityConfigService::CONFIG_RECEIVE_TYPE] == DueActivityConfigService::RECEIVE_TYPE_01_RECEIVE) {
                    $coupon = $this->getCouponServiceModel()->getCouponInfoByActivityId($uid, $this->phone, $value['aid']);
                    if (count($coupon) >= $this->getActivityConfigServiceModel()->rule[DueActivityConfigService::CONFIG_ACTIVITY_RECEIVE_LIMIT]) {
                        continue;
                    }
                    # 如果用户领取了推广优惠券,那么无法领取登录优惠券,修改需求:2017-08-22 14:24:17
                    $res = [];
                    $row = $this->getActivity(self::ACTIVITY_TYPE_05_PROMOTION);
                    foreach ($row as $r) {
                        $res = $this->getCouponServiceModel()->getCouponInfoByActivityId($uid, $this->phone, $r['aid']);
                        if (!empty($res)) {
                            ## 
                            continue 2;
                        }
                    }
                }
                ## 2 分享领取是否领取完了
                if ($this->getActivityConfigServiceModel()->rule[DueActivityConfigService::CONFIG_RECEIVE_TYPE] == DueActivityConfigService::RECEIVE_TYPE_02_SHARE) {
                    $record = $this->getActivitySendRecord($uid, $sourceId, $value);
                    if (!empty($record)) {
                        ## 有记录
                        if ($record['receive_number'] >= $record['share_number']) {
                            ## 已领取完
                            continue;
                        }
                    }
                }
                $value['share_number'] = $this->getActivityConfigServiceModel()->rule[DueActivityConfigService::CONFIG_ACTIVITY_RECEIVE_LIMIT];
                $data[] = $value;
            }
        } while (FALSE);

        return $data;
    }

    /**
     * [分享]获取分享的相关信息
     * @param type $uid
     * @param type $sourceId
     * @param type $typeId
     * @param type $isAnchor
     */
    public function shareCoupon($uid, $sourceId, $activityId, $isAnchor = false) {

        do {
            $record = [];
            ## 活动检查
            $activityData = $this->getActivityDataById($activityId);

            if (empty($activityData)) {
                throw new Exception(errorDesc(-8011), -8011);
            }

            $data = $activityData[0];
            $typeId = $data['type'];
            ## 查看分享记录
            ## 检查是否有发放记录
            $record = $this->getActivitySendRecord($uid, $sourceId, $data);
            # 1 有记录  返回分享数据
            if (!empty($record)) {
                break;
            } else {
                ## 判断源是否是真的存在
                $check = $this->checkSourceId($uid, $sourceId, $typeId, $data);
                if (!$check) {
                    throw new Exception(errorDesc(-8012), -8012);
                }
                ### 创建记录前一定要分析规则
                $this->getActivityConfigServiceModel()->activityAnalysis($data, 0, 0);

                if (empty($this->getActivityConfigServiceModel()->haveCoupons)) {
                    throw new Exception(errorDesc(-8027), -8027);
                } else {
                    $record = $this->getActivityConfigServiceModel()->createActivityShareRecord($uid, $sourceId, $typeId, $data);
                }
            }
        } while (FALSE);

        if (!empty($record)) {
            $result = $data;
            $result['share_uuid'] = $record['share_uuid'];
            $result['share_number'] = $record['receive_number'];
        } else {
            throw new Exception(errorDesc(-8013), -8013);
        }
        return $result;
    }

    /**
     * 检测源ID是否合法
     * @param type $uid
     * @param type $sourceId
     * @param type $typeId
     * @param type $activityData
     * @return boolean
     */
    public function checkSourceId($uid, $sourceId, $typeId, $activityData) {
//        1	登录活动类型	只领取一次	1	2017-07-20 11:20:13
//2	用户下单类型		1	2017-07-20 11:20:08
//3	资质审核通过类型		1	2017-07-20 11:20:05
//4	内部发放	审核人员或系统发放	1	2017-07-20 11:20:06
        if (empty($activityData)) {
            return false;
        }
        ## 原使用记录查询
        $sourceData = $this->getActivityModel()->getShareRecordByTypeAndSourceId($typeId, $sourceId);


        if (!empty($sourceData) && $sourceData[0]['aid'] != $activityData['aid']) {
            throw new Exception(errorDesc(-8032), -8032);
        }

        if (!empty($sourceData)) {
            # 查询分享是否领取完了
            $sharedCoupons = $this->getCouponServiceModel()->getCouponInfoByShareId($sourceData[0]['share_uuid']);
            $sharedNumber = count($sharedCoupons);
            if ($sharedNumber >= $sourceData[0]['receive_number']) {
                throw new Exception(errorDesc(-8029), -8029);
            }
        }
        $result = FALSE;
        switch ($typeId) {
            case self::ACTIVITY_TYPE_01_LOGIN:
                $result = FALSE;
                break;
            case self::ACTIVITY_TYPE_02_ORDER:
                $orderModel = new DueOrderService();
                $orderInfo = $orderModel->getOrderByOrderId($sourceId);
                $mapArray = array_merge(DueOrder::$do_map[DueOrder::ORDER_STATUS_1020_ORDER_BACK], DueOrder::$do_map[DueOrder::ORDER_STATUS_1010_ORDER_CANCELLED]);
                if (!empty($orderInfo) && $orderInfo['uid'] == $uid && $orderInfo['ctime'] >= $activityData['stime'] && $orderInfo['ctime'] <= $activityData['etime'] && !in_array($orderInfo['status'], $mapArray)) {
                    $result = TRUE;
                }
                break;
            case self::ACTIVITY_TYPE_03_CERT:
                $certModel = new DueCertService();
                $certModel->setUid($uid);
                $certList = $certModel->getCertByUid();
                $diff = [];
                $certInfos = [];
                foreach ($certList as $value) {
                    $certInfos[$value['certId']] = $value;
                    if ($value['certId'] != $sourceId) {
                        $diff[] = $value['certId'];
                    }
                }
                ## 是否是自己源
                if (!array_key_exists($sourceId, $certInfos)) {
                    $result = FALSE;
                    break;
                }
                ## 判断是否重复领取  1.唯一
                foreach ($diff as $diffId) {
                    $record = $this->getActivitySendRecord($uid, $diffId, $activityData);
                    if (!empty($record)) {
                        $result = FALSE;
                        break 2;
                    }
                }
                ##  2.时间正确 3.状态正确 
                if ($certInfos[$sourceId]['ctime'] >= $activityData['stime'] && $certInfos[$sourceId]['ctime'] <= $activityData['etime'] && $certModel->setStatus($certInfos[$sourceId]['status']) == DueCertService::CERT_STATUS_PASS) {
                    $result = TRUE;
                }
                break;
            case self::ACTIVITY_TYPE_04_ADMIN:
                $result = FALSE;

                break;
            case self::ACTIVITY_TYPE_05_PROMOTION:
                $result = TRUE;

                break;

            default:
                $result = FALSE;
                break;
        }

        return $result;
    }

    /**
     * redis list
     * [领取优惠券]活动检查点,查看是否有活动,并返回活动  
     * 1 检查是否有活动
     * 2 检查是否领取了
     * 3 如果没有进行发放,并展示(当前展示或者分享出去)
     * 4 领取 
     */
    public function getActivityCoupon($uid, $isAnchor = false, $activityId = 0, $shareUuid = 0, $phone = 0) {
        do {
            ## 创建参数后台处理
            $param = [
                'uid'        => $uid,
                'isAnchor'   => $isAnchor,
                'activityId' => $activityId,
                'shareUuid'  => $shareUuid,
                'phone'      => $phone,
            ];
            $returnKey = md5(implode('_', $param) . uniqid());
            $param['returnKey'] = $returnKey;
            $cacheValue = json_encode($param);
            $redis = $this->getRedis();
            $redis->lPush(DueActivityConfigService::ACTIVITY_CACHE_KEY, $cacheValue);
            $num = 0;
            usleep(500000);
            do {
                $result = $redis->get($param['returnKey']);
                if ($result != FALSE) {
                    break;
                }
                usleep(100000);
                $num++;
            } while ($num < 10);
            $result = intval($result);

            if ($result < 0) {
                throw new Exception(errorDesc($result), $result);
            } elseif ($result > 0) {
                $coupon = new DueCoupon();
                $result = $coupon->_returnCouponList($uid, 1, 1);

                if (isset($result[0])) {
                    $res = $result[0];
                } else {
                    throw new Exception(errorDesc(-8021), -8021);
                }
            } else {
                throw new Exception(errorDesc(-8021), -8021);
            }
        } while (false);
        return $res;
    }

    /**
     * redis list
     * [领取优惠券]活动检查点,查看是否有活动,并返回活动  
     * 1 检查是否有活动
     * 2 检查是否领取了
     * 3 如果没有进行发放,并展示(当前展示或者分享出去)
     * 4 领取 
     */
    public function getActivityCoupon2($uid, $isAnchor = false, $activityId = 0, $shareUuid = 0, $phone = '') {
        do {
            ## 活动检查

            $this->getActivityConfigServiceModel()->clear();
            $this->phone = $phone;
            $this->getActivityConfigServiceModel()->phone = $phone;
            $activityData = $this->getActivityDataById($activityId);

            if (!empty($activityData)) {
                $data = $activityData[0];
                ## add lock 
                $lockCacheKey = 'hpcp_' . md5(sprintf('%s_%s_%s_%s', $uid, $phone, $data['aid'], $shareUuid));
                $lock = $this->getRedis()->get($lockCacheKey);
                if (!$lock) {
                    $this->getRedis()->set($lockCacheKey, '1', 5);
                } else {
                    throw new Exception(errorDesc(-8035), -8035);
                }
                ## 活动规则分析
                $this->getActivityConfigServiceModel()->activityAnalysis($data, $uid, $isAnchor, $shareUuid);

                if (empty($this->getActivityConfigServiceModel()->haveCoupons)) {
                    throw new Exception(errorDesc(-8024), -8024);
                }
                $this->getActivityConfigServiceModel()->getCouponId();
                if ($this->getActivityConfigServiceModel()->selectedId == 0) {
                    throw new Exception(errorDesc(-8024), -8024);
                }

                ## 根据具体领取类型进行发放优惠券
                $receiveType = $this->getActivityConfigServiceModel()->rule[DueActivityConfigService::CONFIG_RECEIVE_TYPE];
                if ($receiveType == DueActivityConfigService::RECEIVE_TYPE_02_SHARE) {
                    if (empty($shareUuid)) {
                        throw new Exception(errorDesc(-8017), -8017);
                    }
                }

                if (in_array($receiveType, [DueActivityConfigService::RECEIVE_TYPE_01_RECEIVE, DueActivityConfigService::RECEIVE_TYPE_02_SHARE])) {
                    if ($data['type'] == self::ACTIVITY_TYPE_01_LOGIN) {
                        # 如果用户领取了推广优惠券,那么无法领取登录优惠券,修改需求:2017-08-22 14:24:17
                        $res = [];
                        $row = $this->getActivity(self::ACTIVITY_TYPE_05_PROMOTION);
                        foreach ($row as $r) {
                            $res = $this->getCouponServiceModel()->getCouponInfoByActivityId($uid, $this->phone, $r['aid']);
                            if (!empty($res)) {
                                throw new Exception(errorDesc(-8033), -8033);
                            }
                        }
                    }

                    if ($data['type'] == self::ACTIVITY_TYPE_05_PROMOTION) {
                        $res = $this->getCouponServiceModel()->getCouponByIdDesc(0, $phone);
                        # 推广:手机号码已存在,无法再次领取  2017-08-22 14:57:19
                        if (!empty($res)) {
                            throw new Exception(errorDesc(-8034), -8034);
                        }
                    }
                    if ($receiveType == DueActivityConfigService::RECEIVE_TYPE_01_RECEIVE && !$this->getActivityConfigServiceModel()->isInActivityTime) {
                        throw new Exception(errorDesc(-8030), -8030);
                    }
                    if ($receiveType == DueActivityConfigService::RECEIVE_TYPE_02_SHARE) {
                        $time = date("Y-m-d H:i:s");
                        $sharelog = $this->getActivityModel()->getShareRecordByUuid($shareUuid);
                        if (isset($sharelog[0]) && $time >= $sharelog[0]['stime'] && $time <= $sharelog[0]['etime']) {
                            
                        } else {
                            throw new Exception(errorDesc(-8031), -8031);
                        }
                    }
                    $res = $this->getActivityConfigServiceModel()->receiveActivityCoupon($uid, $phone);
                } else {
                    throw new Exception(errorDesc(-8015), -8015);
                }
            } else {
                throw new Exception(errorDesc(-8011), -8011);
            }
        } while (false);
        return $res;
    }

    /**
     * 
     * @param type $config
     */
    public function setConfig($config) {
        
    }

    /**
     * 获取(查找)活动
     * @param type $typeId
     * @param type $time
     */
    public function getActivity($typeId, $time = NULL) {
        if (is_null($time)) {
            $time = date("Y-m-d H:i:s");
        }
        $row = $this->getActivityModel()->searchActivity($typeId, $time);
        return $row;
    }

    /**
     * 获取(查找)活动
     * @param type $typeId
     * @param type $time
     */
    public function getActivityDataById($activityId) {
        $row = $this->getActivityModel()->getActivityById($activityId);
        return $row;
    }

    /**
     * 检测活动是否发放过过
     * @param type $uid
     * @param type $typeId
     */
    public function getActivitySendRecord($uid, $sourceId, $activityInfo) {
        $record = $this->getActivityModel()->getShareRecord($activityInfo['type'], $uid, $sourceId, $activityInfo['stime'], $activityInfo['etime']);
        $res = [];
        foreach ($record as $value) {
            if ($value['aid'] == $activityInfo['aid']) {
                $res = $value;
                break;
            }
        }
        return $res;
    }

    /**
     * 数据写入
     * @param type $data
     * @return type
     */
    public function InsertShareRecordData($data) {
        return $this->getActivityModel()->insertShareRecord($data);
    }

    /**
     * 同步优惠券的数据
     * 1 用户注册或者登陆的时候
     * 2 用户绑定手机的时候
     * @param type $phone
     * @param type $uid
     */
    public function updateUserCouponUidByPhone($phone, $uid) {
        return $this->getCouponServiceModel()->updateCouponUidByPhone($phone, $uid);
    }

    /**
     * 
     * @param type $uid
     * @param type $sourceId
     * @param type $typeId
     * @param type $activityData
     * @return boolean  ['activityId'=> '' ,'sourceId'=>'']
     */
    public function publicCheckSourceId($uid, $sourceId, $typeId, $activityData = null) {
        try {
            $activityList = $this->getActivity($typeId);
            if (empty($activityList)) {
                return FALSE;
            }
            $activityData = $activityList[0];
            if ($this->checkSourceId($uid, $sourceId, $typeId, $activityData)) {
                return ['activityId' => $activityData['aid'], 'sourceId' => $sourceId];
            }
            return FALSE;
        } catch (Exception $exc) {
            return FALSE;
        }
    }

}
