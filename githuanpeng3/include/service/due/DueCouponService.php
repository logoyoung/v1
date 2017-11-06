<?php
/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017-7-20
 * Time: 上午9:42:23
 * Desc: 下单、资质 等活动优惠券服务类
 */
 
namespace service\due;

use system\RedisHelper;
use lib\due\DueCoupon;
use service\user\UserCenterCountService;


class DueCouponService
{
    const COUPON_STATUS_00_DEFAULT = 0;//默认
    const COUPON_STATUS_01_USE = 1;//可使用
    const COUPON_STATUS_02_UNUSE = 2;//已使用
    const COUPON_STATUS_03_EXPIRE = 3;//已过期

    public $uid;
    public $couponObj;
    public function __construct(){
        $this->couponObj = new DueCoupon();
    }
    public function setUid(int $uid){
        $this->uid = $uid;
    }
    /**
     * 获取我的优惠券列表（用户所有的优惠券）
     * @return array
     */
    public function returnCouponList($page,$size){
        //我的优惠券
        $data = $this->couponObj->_returnCouponList($this->uid,$page,$size);
        if(!empty($data)){
            $data = $this->getCouponInfoByCouponId($data,$this->uid); 
        }else $data = [];
        return $data;
    } 
    /**
     * 获取我的优惠券记录数
     * @return int
     */
    public function returnCouponCount(){
        //我的优惠券
        $data = $this->couponObj->_returnCouponCount($this->uid);
        return $data;
    } 
    /**
     * 下单获取可用优惠券列表 
     * @param $real_amount 订单交易总金额
     * @rule 有效期内、未使用、当天使用次数未达到上限、
     * @return array
     */
    public function getUsableCouponList($real_amount){
        //获取 未使用、未过期的用户优惠券
        $datas = $this->couponObj->_getUsableCouponList($this->uid);
        if(!empty($datas)){
            //通过优惠券id获取 优惠券信息
            $data = $this->getCouponInfoByCouponId($datas,$this->uid);
            //规则过滤优惠券 做出是否可用标志
            $data = $this->couponRuleFilter($data,$real_amount);
        }else $data = []; //没有优惠券
        return $data;
    }
    /**
     * 通过优惠券id 获取优惠券信息
     * @return array
     */
    private function getCouponInfoByCouponId($data,$uid){
        $datas = [];
        //优惠券信息
        $couponId = array_column($data, "coupon_id"); 
        //获取优惠券信息
        $couponInfo = $this->couponObj->_getCouponInfo($couponId,$uid);
        foreach($data as $ko=>$vo){
            foreach($couponInfo as $v){
                if($vo['coupon_id'] == $v['cid']){
                    unset($v['ctime'],$v['stime'],$v['etime']);
                    $v['etime'] = $vo['etime'];
                    $datas[] = array_merge($data[$ko] , $v);
                }
            }
        } 
        return $datas;
    }
    /**
     * 优惠券 可用规则过滤()
     * --------------
     * @param $data : 可用优惠券列表list
     * @param $real_amount 订单交易金额
     * @rule : 每天最多用5张、校验门槛 
     * @return array
     */
    public function couponRuleFilter($data,$real_amount){
        //以下开始审核 本次订单信息符合使用的门槛优惠券列表
        foreach ($data as $ko => $vo){
            $condition = json_decode($vo['condition'],true);
            $res = $this->checkCouponUseRule($condition['basePrice'][0], $condition['basePrice'][1], $real_amount);
            $data[$ko]['isUse'] = $res ? DueCouponConfig::CHECK_CODE_01 : DueCouponConfig::CHECK_CODE_02;
        }
        //获取用户每天 使用优惠券下单次数
        $usableNumber = DueCouponConfig::USABLE_NIMBER;
        $todayUseNum  = $this->todayUseCouponNum();
        $check_num = 
        $todayUseNum[0]['todayUseNum'] == $usableNumber 
        ?  DueCouponConfig::ISUSE_CODE_01
        :  DueCouponConfig::ISUSE_CODE_02;
        $data = [
            'list'=>$data,
//             'checkNum'=>$check_num, //使用次数是否满足条件
//             'todayUseNum'=>$todayUseNum[0]['todayUseNum']
        ];
        return $data;
    }
    /**
     * 获取用户当天已使用优惠券次数
     * @return number
     */
    public function todayUseCouponNum(){
        //统计 用户当天使用优惠券次数
        return $this->couponObj->_todayUseCouponNum($this->uid);
    }
    
    
    /**
     * 获取优惠券的信息
     * @param type $couponId
     */
    public function getCouponInfoById($couponId) {
        return $this->couponObj->_getCouponInfo($couponId);
    }
    
    
    /**
     * 获取用户某个活动的优惠券的信息
     * @param type $uid
     * @param type $typeId
     * @return type
     */
    public function getCouponInfoByType($uid,$typeId) {
        return $this->couponObj->getCouponsByType($uid,$typeId);
    }
    
    
     /**
      * 获取用户某个活动的优惠券的信息
      * @param type $uid
      * @param type $activityId
      * @return type
      */
    public function getCouponInfoByActivityId($uid,$phone,$activityId) {
        return $this->couponObj->getCouponsByActivityId($uid,$phone,$activityId);
    }
    
   
     /**
      * 获取用户某个分享链接的优惠券的信息
      * @param type $shareUuid   分享码
      * @param type $uid   指定用户 非必须
      * @return type
      */
    public function getCouponInfoByShareId($shareUuid,$uid = null) {
        return $this->couponObj->getCouponsByShareId($shareUuid,$uid);
    }
    
     /**
      * 获取用户某个分享链接的优惠券的信息
      * @param type $shareUuid
      * @param type $uid
      * @param type $status
      * @return type
      */
    public function getCouponInfoByShareIdAndStatus($shareUuid,$uid = null,$status = 1) {
        return $this->couponObj->getCouponsByShareIdAndStatus($shareUuid,$uid,$status);
    }
    
    
    
    /**
     * 检查 优惠券的有效期、用户优惠券使用状态、门槛
     * @return bool/array
     */
    public function getCouponUsable($uid,$couponId,$real_amount){
        //获取 我的优惠券属性
        $data = $this->couponObj->_getCouponInfoByCouponId($uid, $couponId);
        //校验优惠券是否已使用
        if($data[0]['status'] > 1) return false;
        //优惠券是否过期或不在使用期
        if($data[0]['stime'] > date("Y-m-d H:i:s") || date("Y-m-d H:i:s")> $data[0]['etime']) return false;
        //获取优惠券的信息
        $couponInfo = $this->getCouponInfoById($couponId);
        //校验优惠券使用门槛
        if(!empty($couponInfo)){
            $condition = json_decode($couponInfo[0]['condition'],true);
            $res = $this->checkCouponUseRule($condition['basePrice'][0],$condition['basePrice'][1],$real_amount);
            if($res == false) return false;
        }
        return $data;
    }
    /**
     * 获取未被使用|未过期的优惠券列表
     * @return Ambigous <multitype:, unknown>
     */
    public function getUnusedCouponList(){
        $data = $this->couponObj->_getUnusedCouponList($this->uid);
        return !empty($data) ? $data : [];
    }
    
    
     /**
     * 获取最近的几条数据
     * @return Ambigous <multitype:, unknown>
     */
    public function getCouponByIdDesc($uid,$phone,$limit){
        $data = $this->couponObj->getCouponsByUidOrPhone( $uid,$phone, 1, $limit) ;
        return !empty($data) ? $data : [];
    }
    
     
     /**
     * 获取来源码
     * @return Ambigous <multitype:, unknown>
     */
    public function getPromocode($uid){
        $data = $this->couponObj->getRowPromocodeByUid( $uid) ;
        return !empty($data) ? $data : [];
    }
    
     /**
      * 优惠券数据写入
      * @param type $data
      * @return type
      */
    public function insertCoupon($data){
        $row = $this->couponObj->insertCouponData($data);
        $this->updateCouponNumberById($data['coupon_id']);
        return $row;
    }
    
    
    /**
      * 优惠券数据写入
      * @param type $data
      * @return type
      */
    public function updateCoupon($data){
        $row = $this->couponObj->updateCouponData($data);
        return $row;
    }
    
     /**
      * 优惠券数据写入
      * @param type $data
      * @return type
      */
    public function updateCouponNumberById($id){
        $row = $this->couponObj->updateCouponNumberById($id);
        return $row;
    }
    
    
    
     /**
      * 优惠券信息更新
      * @param type $data
      * @return type
      */
    public function updateCouponUidByPhone($phone,$uid){
        $row = $this->couponObj->updateCouponUidByPhone($phone,$uid);
        UserCenterCountService::addValue($uid, UserCenterCountService::HASH_TABLE_FIELD_COUPON_NUM, $row);
        return $row;
    }
    
    
    
    
    /**
     * 验证优惠券门槛规则
     * @return bool
     */
    protected function checkCouponUseRule($operator,$basePrice,$money){
        switch ($operator){
            case 'EGT':
                return $money >=$basePrice ? true : false;
                break;
        }
    }
    
    
    
    public static function getCouponTagNumCacheKey($uid) {
        return 'center_coupon_tag_0914_' . $uid;
    }

    public static function addNewCouponTagNum($uid,$num = 1) {
        $redis = RedisHelper::getInstance('huanpeng');
        $key = self::getCouponTagNumCacheKey($uid);
        $redis->incr($key,$num);
    }

    public static function getNewCouponTagNum($uid) {
        $redis = RedisHelper::getInstance('huanpeng');
        $key = self::getCouponTagNumCacheKey($uid);
        return $redis->get($key);
    }

    public static function delNewCouponTagNum($uid) {
        $redis = RedisHelper::getInstance('huanpeng');
        $key = self::getCouponTagNumCacheKey($uid);
        return $redis->delete($key);
    }

}

?>