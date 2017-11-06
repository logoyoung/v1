<?php

/**
 * 金币兑换欢朋币
 * @since 2017-05-02 11:52:51
 */
include '../../../../include/init.php';

use lib\Finance;
use lib\Anchor;
use lib\AnchorExchange;
use lib\User;

class beanToCoin {

    private $_uid;
    private $_encpass;
    private $_number;
    private $_finance;
    private $_db;
    private $_desc = [];
    private $_anchor;
    private $_user;
    private $_exchange;

    public function __construct($uid = 0, $encpass = '', $number = 0) {
        $this->_uid = intval($uid);
        $this->_encpass = trim($encpass);
        $this->_number = intval($number);
        $this->_init();
    }

    /**
     * init
     */
    private function _init() {
        $this->_db = new DBHelperi_huanpeng();

        $this->_anchor = new Anchor($this->_uid, $this->_db);
        $this->_user = new User($this->_uid, $this->_db);
        $this->_finance = new Finance($this->_db);
        $this->_exchange = new AnchorExchange($this->_uid, '', $this->_db);
        $this->_check();
    }

    /**
     * 如果错误,则退出
     * @param type $code
     * @return boolean
     */
    public static function checkCode($code) {
        if ($code !== TRUE) {
            error2($code);
        }
        return TRUE;
    }

    /**
     * check
     * @return type void
     */
    protected function _check() {
        self::checkCode($this->_checkParam());
        self::checkCode($this->_checkNumber());
        self::checkCode($this->_checkLogin());
        self::checkCode($this->_checkIsAnchor());
        self::checkCode($this->_checkBalance());
        self::checkCode($this->_checkCompany());
        if ($GLOBALS['env'] != 'DEV' && $GLOBALS['env'] != 'PRE') {
            self::checkCode($this->_checkOnlineTime());
        }
        return;
    }

    /**
     * check param
     * @return  mixed:int or bool 
     */
    protected function _checkParam() {
        if ($this->_uid == 0 || empty($this->_encpass) || $this->_number <= 0) {
            return -4013;
        } else {
            return TRUE;
        }
    }

    /**
     * 修改上线时间
     * @return boolean
     */
    protected function _checkOnlineTime() {
        // 1496246400 2017-06-01 00:00:00
        if (time() < 1496246400) {
            return -4108;
        } else {
            return TRUE;
        }
    }

    /**
     * _checkNumber  播收益“金豆”可以兑换为“金币”用于提现，兑换最低金额、最高金额（单位：金豆）；20金豆、500金豆
     * @return  mixed:int or bool 
     */
    protected function _checkNumber() {
        if ($this->_number >= 15 && $this->_number <= 500) {
            return TRUE;
        } else {
            return -4106;
        }
    }

    /**
     * checkLogin
     * @return  mixed:int or bool 
     */
    protected function _checkLogin() {
        return CheckUserIsLogIn($this->_uid, $this->_encpass, $this->_db);
    }

    /**
     * check is  anchor
     * @return  mixed:int or bool 
     */
    protected function _checkIsAnchor() {
        return Anchor::isAnchor($this->_uid, $this->_db);
    }

    /**
     * 主播签约渠道检验   渠道15是平台的,渠道0是未签约的,其他的是经纪公司的
     * @todo 待确认
     * @return boolean
     */
    protected function _checkCompany() {
        $anchorChannel = $this->_anchor->getAnchorCertInfo();
        if (isset($anchorChannel['cid']) && in_array($anchorChannel['cid'], [0, 15])) {
            return TRUE;
        }
        $res = getCompanyTypeByCid($anchorChannel['cid'], $this->_db);
        if (isset($res[$anchorChannel['cid']]) && $res[$anchorChannel['cid']] == 2) {
            return TRUE;
        }
        return -4097;
    }

    /**
     * 判断余额是否足够
     * <pre>
     *  array(4) {
      ["hb"]=>
      int(324)
      ["gb"]=>
      float(770.32)
      ["hd"]=>
      int(196)
      ["gd"]=>
      float(0.9)
     */
    protected function _checkBalance() {
        $res = $this->_finance->getBalance($this->_uid);
        if (is_array($res) && isset($res['gd']) && $this->_number <= $res['gd']) {
            return TRUE;
        } else {
            return -5023;
        }
    }

    /**
     * (主播)金币兑换欢朋币
     */
    public function exchange() {
        $doBool = $this->_exchange->beanToCoin($this->_uid, $this->_number);
        if ($doBool) {
            $res = $this->_finance->getBalance($this->_uid);
            succ($res);
        } else {
            error2(-4100);
        }
    }

    /**
     * 清理相应的缓存
     */
    public function __destruct() {
        
    }

}

$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : 0;
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$number = isset($_POST['number']) ? trim($_POST['number']) : 0;

$obj = new beanTocoin($uid, $encpass, $number);
$obj->exchange();
