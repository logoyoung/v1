<?php
namespace service\due;

class DueCouponConfig
{
    //后台设置优惠券规则格式
    const BASE_PRICE = 'basePrice';
    const COUPON_CONFIG = [
        self::BASE_PRICE =>['EGT',NULL,'优惠券使用门槛金额']
    ];
    //用户使用优惠券规则
    const USABLE_NIMBER = 5; //用户每天 使用优惠券下单次数
    const ISUSE_CODE_01 = 3001; //当日下单已经达到上限 5次
    const ISUSE_CODE_02 = 3000; //下单次数可用 < 5次
    //状态客户端返回码
    const CHECK_CODE_01 = 8000; //订单消费金额满足 优惠券使用门槛
    const CHECK_CODE_02 = 8001; //订单消费金额不满足优惠券使用门槛
    const CHECK_CODE_03 = 8002; //已过期
    const CHECK_CODE_06 = 8005; //不在有效期
    const CHECK_CODE_04 = 8003; //可使用
    const CHECK_CODE_05 = 8004; //已使用
    //程序内部校验码
    const COUPON_USE_STATUS_01 = 1; //已使用
    const COUPON_USE_STATUS_02 = 0; //未使用
}

?>