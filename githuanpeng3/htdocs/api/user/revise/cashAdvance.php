<?php

/**
 * 金币兑换人民币,提现
 * @since 2017-05-02 11:52:51
 */
include '../../../../include/init.php';

use lib\Finance;
use lib\Anchor;
use lib\AnchorExchange;

/**
 * 1 每个用户每个月只能体现一次
 * 2 体现分为四个状态,  未提现,提现中,提现失败,提现成功
 */
class cashAdvance {

    private $_uid;
    private $_encpass;
    private $_number;
    private $_finance;
    private $_db;
    private $_anchor;
    private $_exchange;

    public function __construct($uid = 0, $encpass = '') {
        $this->_uid = intval($uid);
        $this->_encpass = trim($encpass);
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
        self::checkCode($this->_checkLogin());
        self::checkCode($this->_checkIsAnchor());
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
        if ($this->_uid == 0 || empty($this->_encpass)) {
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
     * (主播)金币兑换欢朋币
     */
    public function display() {
        //初始记录
        $step = 0; //无提现记录
        $res = $this->_getRecord();
        if ($res !== false && isset($res['status'])) {
            $step = $res['status'];
            $result = $this->_finance->getWithdrawRecordInfo($res['otid']);
        }
        $res['utime'] = $res['utime'] == '0000-00-00 00:00:00' ? '' : substr($res['utime'], 0, 10);
        $result = [
            'step' => $step,
            'coin' => isset($result['gb']) ? abs($result['gb']) : "0",
            'cny' => isset($result['rmb']) ? $result['rmb'] : "0",
            'time' => isset($res['ctime']) ? $res['ctime'] : "",
            'finishTime' => isset($res['utime']) ? $res['utime'] : "",
        ];
        succ($result);
    }

    /**
     * 当月提现记录
     * @return type
     */
    protected function _getRecord() {
        $res = $this->_exchange->getRowByUidAndType($this->_uid, Finance::EXC_GB_RMB);
        if (is_array($res) && isset($res[0])) {
            return $res[0];
        } else {
            return FALSE;
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

$obj = new cashAdvance($uid, $encpass);
$obj->display();
