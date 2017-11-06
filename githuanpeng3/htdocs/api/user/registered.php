<?php

/**
 * 注册
 * author yandong@6rooms.com
 * date 2015-12-15 10:30
 * version 0.1
 */
include '../../../include/init.php';

// ini_set('display_errors',1);            //错误信息
// ini_set('display_startup_errors',1);    //php启动错误信息
use service\common\ApiCommon;
use service\user\UserRegisterService;
use service\activity\RegisterActivityService;
use service\due\DueActivityService;
use service\due\DueCouponService;
use system\RedisHelper;
use system\DbHelper;

class registered extends ApiCommon {

    const REDIS_CONFIG = 'huanpeng';

    public $paramValue = [];
    public $param = [
        'phone'     =>  ['name' => 'mobile',        'default' => '',    'rule' => self::PARAM_RULE_01_NOT_NULL],
        'vCode'     =>  ['name' => 'mobileCode',    'default' => '',    'rule' => self::PARAM_RULE_01_NOT_NULL],
        'nick'      =>  ['name' => 'nick',          'default' => '',    'rule' => self::PARAM_RULE_01_NOT_NULL],
        'password'  =>  ['name' => 'password',      'default' => '',    'rule' => self::PARAM_RULE_01_NOT_NULL],
        'client'    =>  ['name' => 'client',        'default' => '0',   ],
        'cid'       =>  ['name' => 'cid',           'default' => ''     ],
        'channelID' =>  ['name' => 'channelID',     'default' => '0'    ],
    ];

    public function action() {
        $param = self::getParam($this->param, TRUE);
        if (empty($param['channelID'])) {
           $param['channelID'] = hp_getRequestChannelID();
        }
        $this->paramValue = $param;

        $reg = new UserRegisterService();

        $reg->setPhone($param['phone']);
        $reg->setVerificationCode($param['vCode']);
        $reg->setNick($param['nick']);
        $reg->setPassword($param['password']);
        $reg->setCid($param['cid']);
        $reg->setChannelID($param['channelID']);
        $reg->setClient($param['client']);

        $res = $reg->register();

        if (!empty($res)) {
            $this->resultData = $res;
            $this->synchrodata();
        } else {
            $this->resultData['code'] = $reg->getErrorCode();
            $this->resultData['desc'] = $reg->getErrorMessage();
        }
        $code = isset($this->resultData['code']) ? $this->resultData['code'] : 0;
        render_json($this->resultData, $code, $code != 0 ? 2 : 1);
    }

    /**
     * 同步数据
     */
    public function synchrodata() {
        #1. 同步优惠券数据
        $this->syncCoupon();
        #2. 同步登录
        $this->syncLoginCookie();
        #3. 同步任务数据
        $this->syncFirstLogin();
        #4. 同步用户来源渠道
        $this->syncPromocode();
        #5. 异步同步用户活动数据
        $this->syncActivity();
    }

    /**
     * 同步优惠券
     */
    public function syncCoupon() {

        $acti = new DueActivityService();
        $res = $acti->updateUserCouponUidByPhone($this->paramValue['phone'], $this->resultData['uid']);
    }

    /**
     * 同步用户来源渠道
     */
    public function syncPromocode() {
        $coupon = new DueCouponService();
        $res = $coupon->getPromocode($this->resultData['uid']);
        if (!empty($res)) {
            updateUserPromocode($this->resultData['uid'], $res['promocode'], DbHelper::getInstance('huanpeng'));
        }
        $promocode = hp_getRequestPromoCode();
        if($promocode){
            updateUserPromocode($this->resultData['uid'], $promocode, DbHelper::getInstance('huanpeng'));
        }
    }

    /**
     * 同步登录
     */
    public function syncLoginCookie() {

        setUserLoginCookie($this->resultData['uid'], $this->resultData['encpass']);
    }

    /**
     * 首次登录同步数据
     */
    public function syncFirstLogin() {

        if ($this->paramValue['client']) {
            $redisObj = RedisHelper::getInstance(self::REDIS_CONFIG);
            $keys = "IsFirstLoginfromApp:" . $this->resultData['uid'];
            $res = $redisObj->get($keys);
            if (!$res) {
                $redisObj->set($keys, 1); //设置标志
                synchroTask($this->resultData['uid'], 36, 0, 200, new DBHelperi_huanpeng()); //同步到task表中
            }
        }
    }
    
    /**
     * 同步活动数据
     */
    public function syncActivity() {
        $m = new RegisterActivityService();
        $m->addUser($this->resultData['uid']);
    }

}

/**
 *  检验经纪公司
 * @param int $cid 经纪公司id
 * @param $db
 * @return bool
 */
function checkCompang($cid, $db) {
    if (empty($cid)) {
        return false;
    }
    $res = $db->where("id=$cid")->limit(1)->select('company');
    if (false !== $res) {
        if (empty($res)) {
            return false;
        } else {
            return true;
        }
    } else {
        return false;
    }
}

/**
 * 添加主播到经纪公司表
 * @param int $uid 主播id
 * @param int $cid 经纪公司id
 * @param $db
 * @return bool
 */
function addCompangAnchor($uid, $cid, $db) {
    if (empty($uid) || empty($cid)) {
        return false;
    }
    $data = array(
        'uid'   => $uid,
        'cid'   => $cid,
        'utime' => date('Y-m-d H:i:s', time())
    );
    $res = $db->insert('company_anchor', $data);
    if (false !== $res) {
        return true;
    } else {
        return false;
    }
}

$reg = new registered();
$reg->action();


