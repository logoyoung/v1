<?php

/**
 * Created by Zend Studio.
 * User: yalongSun <yalong_2017@6.cn>
 * Date: 2017-7-21
 * Time: 下午1:58:53
 * Desc: 我的优惠券列表
 */

namespace api\due\coupon;

include '../../../include/init.php';

// ini_set('display_errors',1);            //错误信息
// ini_set('display_startup_errors',1);    //php启动错误信息
use service\common\ApiCommon;
use service\due\DueCouponService;
use service\due\DueCouponConfig;

class myCouponList extends ApiCommon {

    private $page;
    private $size;
    private $couponService;

    private function _init() {
        $this->page = !isset($_POST['page']) ? 1 : intval($_POST['page']);
        $this->size = !isset($_POST['size']) ? 10 : intval($_POST['size']);
        $this->checkIsLogin(true);
        $this->couponService = new DueCouponService();
    }

    private function getCouponList() {
        $this->couponService->setUid($this->uid);
        return $this->couponService->returnCouponList($this->page, $this->size);
    }

    private function getCouponCount() {
        $this->couponService->setUid($this->uid);
        return $this->couponService->returnCouponCount();
    }

    /**
     * 接口参数 过滤
     * @param unknown $data
     * @return array
     */
    private function paramFilter($data) {
        foreach ($data as $ko => $vo) {
            $condition = json_decode($vo['condition'], true);
            $data[$ko]["basePrice"] = $condition['basePrice'][1];
            if ($vo['isuse'] == DueCouponService::COUPON_STATUS_01_USE) {
                $now = date("Y-m-d H:i:s");
                if ($now < $vo['stime']) {
                    $data[$ko]['status'] = DueCouponConfig::CHECK_CODE_06; //不在有效期（未 以后做 预发放预留）
                } elseif ($now > $vo['etime']) {
                    $data[$ko]['status'] = DueCouponConfig::CHECK_CODE_03; //已过期
                } else {
                    $data[$ko]['status'] = DueCouponConfig::CHECK_CODE_04; //可使用
                }
            } else
                $data[$ko]['status'] = DueCouponConfig::CHECK_CODE_05; //已使用
            $data[$ko]["couponId"] = $vo['id'];  //此id 为due_user_coupon的主键id
            $data[$ko]["activityId"] = $vo['activity_id'];
            $data[$ko]["orderId"] = $vo['orderid'];
            $data[$ko]["price"] = intval($vo['price']);
            $data[$ko]["ctimeStamp"] = strtotime($vo['ctime']);
            $data[$ko]["stimeStamp"] = strtotime($vo['stime']);
            $data[$ko]["etimeStamp"] = strtotime($vo['etime']);
            unset(
                    $data[$ko]['cid'], $data[$ko]['id'], $data[$ko]['cstime'], $data[$ko]['condition'], $data[$ko]['max_number'], $data[$ko]['send_number'], $data[$ko]['expire'], $data[$ko]['coupon_id'], $data[$ko]['activity_id'], $data[$ko]['orderid'], $data[$ko]['ctime'], $data[$ko]['etime'], $data[$ko]['stime'], $data[$ko]['isuse']
            );
        }
        return $data;
    }

    public function display() {
        $this->_init();
        $data = $this->getCouponList();
        if (!empty($data))
            $data = $this->paramFilter($data);
        $num = $this->getCouponCount();
        $data = [
            'total' => $num[0]['myCouponCount'],
            'page_size' => $this->size,
            'list' => $data,
        ];
        render_json($data);
    }

}

$couponObj = new myCouponList();
$couponObj->display();
?>