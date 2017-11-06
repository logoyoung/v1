<?php
/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017-7-21
 * Time: 下午1:58:53
 * Desc: 下订单时|可用优惠券列表
 */
 
namespace api\due;
include '../../../include/init.php';
// ini_set('display_errors',1);            //错误信息
// ini_set('display_startup_errors',1);    //php启动错误信息
use service\common\ApiCommon;
use service\due\DueCouponService;
use service\due\DueCouponConfig;

class usableCoupon extends ApiCommon
{
    const PARAM_ERROR_CODE_01 = -1001;
    private $errMsg = [
        self::PARAM_ERROR_CODE_01=>'缺少订单金额参数'
    ];
    private $amount; 
    private function _init(){
        if(!isset($_POST['amount']) || !is_numeric($_POST['amount'])){
            render_error_json($this->errMsg[self::PARAM_ERROR_CODE_01],self::PARAM_ERROR_CODE_01);
        } 
        $this->amount = intval($_POST['amount']);
        $this->checkIsLogin(true);
    }
    private function getUsableCouponList(){
        $usableCoupon = new DueCouponService();
        $usableCoupon->setUid($this->uid);
        return $usableCoupon->getUsableCouponList($this->amount);
    }
    /**
     * 接口参数 过滤
     * @param unknown $data
     * @return array
     */
    private function paramFilter($data){
        foreach($data['list'] as $ko=>$vo){ 
            $condition = json_decode($vo['condition'],true);
            $data['list'][$ko]["basePrice"] = $condition['basePrice'][1];
            $data['list'][$ko]["couponId"] = $vo['id'];//此id 为due_user_coupon的主键id
            $data['list'][$ko]["orderId"] = $vo['orderid'];
            $data['list'][$ko]["price"] = intval($vo['price']);
            $data['list'][$ko]["activityId"] = $vo['activity_id'];
            $data['list'][$ko]["status"] = $vo['isUse'];
            $data['list'][$ko]["ctimeStamp"] = strtotime($vo['ctime']);
            $data['list'][$ko]["stimeStamp"] = strtotime($vo['stime']);
            $data['list'][$ko]["etimeStamp"] = strtotime($vo['etime']);
            unset(
                $data['list'][$ko]['cid'],
                $data['list'][$ko]['id'],
                $data['list'][$ko]['condition'],
                $data['list'][$ko]['max_number'],
                $data['list'][$ko]['send_number'],
                $data['list'][$ko]['expire'],
                $data['list'][$ko]['coupon_id'],
                $data['list'][$ko]['activity_id'],
                $data['list'][$ko]['orderid'],
                $data['list'][$ko]['stime'],
                $data['list'][$ko]['etime'],
                $data['list'][$ko]['ctime'],
                $data['list'][$ko]['isUse']
            );  
        }
        return $data;
    }
    public function display(){
        $this->_init();
        $data = $this->getUsableCouponList();
        if(!empty($data))
            $data = $this->paramFilter($data); 
        $data['list'] = ArraySort($data['list'], 'status', 'price',SORT_ASC,SORT_DESC); 
        render_json($data);
    }
}
$usableCoupon = new usableCoupon();
$usableCoupon->display();
?>