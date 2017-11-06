<?php

/**
 * 金币兑换人民币,提现
 * @since 2017-05-02 11:52:51
 */
include '../../../../include/init.php';

use \lib\Finance;
use \lib\Anchor;
use \lib\AnchorExchange;

class coinToCNY {

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
//        $this->_user = new User($this->_uid, $this->_db);
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
        self::checkCode($this->_checkBindBank());
        self::checkCode($this->_checkBalance());
        self::checkCode($this->_checkCompany());
        if ($GLOBALS['env'] != 'DEV' && $GLOBALS['env'] != 'PRE') {
            self::checkCode($this->_checkOnlineTime());
        }
        self::checkCode($this->_checkTime());
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
     * _checkNumber  申请提现，金币数额判断大于等于100小于等于800之间；
     * @return  mixed:int or bool 
     */
    protected function _checkNumber() {
        if ($this->_number >= 100 && $this->_number <= 800) {
            return TRUE;
        } else {
            return -4104;
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
     * 检查是否绑定了银行卡
     * @return boolean
     */
    protected function _checkBindBank() {
        $res = $this->_db->field("uid")->where("uid={$this->_uid}")->limit(1)->select('bank_card');
        if (empty($res)) {
            return -4086;
        } else {
            return TRUE;
        }
    }

    /**
     * 每月的1日到5日可以体现,并且每月只能体现一次
     * @version 2017-06-21 调整到 25日到月末
     */
    protected function _checkTime() {
        $today = date("d");
        if ($today >= 25) {
            $res = $this->_exchange->getRowByUidAndType($this->_uid, Finance::EXC_GB_RMB);

            if (!empty($res)) {
                return -4103;
            } else {
                return TRUE;
            }
        } else {
            return -4102;
        }
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
        if (is_array($res) && isset($res['gb']) && $this->_number <= $res['gb']) {
            return TRUE;
        } else {
            return -5023;
        }
    }

    /**
     * (主播)金币兑换欢朋币
     */
    public function exchange() {
        $doBool = $this->_exchange->coinToCNY($this->_uid, $this->_number);
        if ($doBool) {
            $res = $this->_finance->getBalance($this->_uid);
            succ($res);
        } else {
            error2(-4101);
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

$obj = new coinToCNY($uid, $encpass, $number);
$obj->exchange();
